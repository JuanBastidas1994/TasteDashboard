<?php
/**
 * Cron único de notificaciones push (Expo). Reemplaza a cron/regalo_cumpleanios.php y a
 * api_gestion_ordenes/cron/recordatorio_calificacion.php.
 *
 * Ejecutar cada 15 minutos (mínimo del servidor):
 *   *\/15 * * * * php /ruta/a/taste/cron/notificaciones_push.php >> /ruta/a/logs/notificaciones_push.log 2>&1
 *
 * En cada corrida:
 *   1. Programadas: envía las campañas de notificaciones.php cuya hora ya llegó.
 *   2. Calificación: pedidos entregados hace entre 30 min y 24 h (llega a los 30-45 min).
 *   3. Cumpleaños (desde las 10:00): aviso 3 días antes y saludo el día. Ese día acredita el
 *      regalo si la empresa lo configuró (configuraciones.php > Cumpleaños) y el cliente cumple
 *      el mínimo de compras. El texto cambia si hay regalo o no.
 *   4. Recompra (desde las 10:00): clientes con N días sin pedir, 1 vez por ciclo (config por
 *      empresa en notificaciones.php).
 * Lo diario usa tb_notificaciones_auto_log, así que correr el cron varias veces al día no duplica.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Acceso denegado');
}

$rootPath = dirname(__DIR__);
chdir($rootPath);
require_once $rootPath . '/funciones.php';
require_once $rootPath . '/clases/cl_notificaciones_expo.php';

date_default_timezone_set('America/Guayaquil');

const HORA_ENVIOS_DIARIOS = 10;
const DIAS_AVISO_CUMPLEANOS = 3;

function logCron($texto)
{
    echo date('[Y-m-d H:i:s] ') . $texto . PHP_EOL;
}

function primerNombre($nombre)
{
    $partes = explode(' ', trim(html_entity_decode($nombre)));
    return mb_convert_case(mb_strtolower($partes[0]), MB_CASE_TITLE, 'UTF-8');
}

function reemplazarNombre($texto, $nombre)
{
    return str_replace('{nombre}', primerNombre($nombre), $texto);
}

function generarTracking($cod_orden)
{
    $sig = substr(hash_hmac('sha256', (string)$cod_orden, TRACKING_SECRET), 0, 12);
    return rtrim(base64_encode($cod_orden . '.' . $sig), '=');
}

function tokensDe($cl, $cod_usuario)
{
    return array_column($cl->tokensUsuario($cod_usuario), 'token');
}

logCron('Inicio');

/* =========================================================================
   1. PROGRAMADAS
   ========================================================================= */
try {
    $programadas = cl_notificaciones_expo::procesarProgramadas();
    foreach ($programadas as $id => $resultado) {
        logCron("Programada #$id: " . $resultado['mensaje']);
    }
} catch (Exception $ex) {
    logCron('ERROR programadas: ' . $ex->getMessage());
}

/* =========================================================================
   2. CALIFICACIÓN
   La fecha de entrega sale de tb_orden_historial (api_flotas y api_gestion_ordenes escriben
   ahí al pasar a ENTREGADA). El tope de 24 h evita notificar pedidos viejos.
   ========================================================================= */
try {
    if (TRACKING_SECRET === '') {
        throw new Exception('Falta TRACKING_SECRET en el .env');
    }

    $ordenes = Conexion::buscarVariosRegistro(
        "SELECT oc.cod_orden, oc.cod_usuario, oc.cod_empresa
            FROM tb_orden_cabecera oc
            INNER JOIN (
                SELECT cod_orden, MAX(fecha) AS fecha_entrega
                FROM tb_orden_historial
                WHERE estado = 'ENTREGADA' AND fecha >= :desde_historial
                GROUP BY cod_orden
            ) h ON h.cod_orden = oc.cod_orden
            WHERE oc.estado = 'ENTREGADA'
            AND oc.recordatorio_calificacion_enviado = 0
            AND h.fecha_entrega <= :hasta
            AND h.fecha_entrega >= :desde",
        [
            ':desde_historial' => date('Y-m-d H:i:s', strtotime('-24 hours')),
            ':desde' => date('Y-m-d H:i:s', strtotime('-24 hours')),
            ':hasta' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
        ]
    ) ?: [];

    $porEmpresa = [];
    foreach ($ordenes as $orden) {
        $porEmpresa[$orden['cod_empresa']][] = $orden;
    }

    $titulo = '¿Qué tal estuvo todo? ⭐';
    $mensaje = 'Cuéntanos cómo fue tu experiencia con este pedido';
    foreach ($porEmpresa as $cod_empresa => $ordenesEmpresa) {
        $cl = new cl_notificaciones_expo($cod_empresa);
        $envios = [];
        foreach ($ordenesEmpresa as $orden) {
            Conexion::ejecutar(
                "UPDATE tb_orden_cabecera SET recordatorio_calificacion_enviado = 1 WHERE cod_orden = :cod_orden",
                [':cod_orden' => $orden['cod_orden']]
            );
            $tokens = tokensDe($cl, $orden['cod_usuario']);
            if (!$tokens) continue;
            // order_tracking: la app abre el pedido y, al estar ENTREGADA, muestra la calificación
            $envios[] = ['tokens' => $tokens, 'titulo' => $titulo, 'mensaje' => $mensaje, 'data' => [
                'type' => 'order_tracking',
                'orden_id' => generarTracking($orden['cod_orden']),
                'estado' => 'ENTREGADA',
            ]];
        }
        $resultado = $cl->enviarAutomatica('calificacion', $titulo, $mensaje, $envios);
        logCron("Calificación empresa $cod_empresa: " . count($ordenesEmpresa) . " pedidos, {$resultado['ok']} push entregados");
    }
} catch (Exception $ex) {
    logCron('ERROR calificación: ' . $ex->getMessage());
}

/* Lo que sigue es diario: arranca a las 10:00 y el log evita repetir en las corridas siguientes */
if ((int)date('G') < HORA_ENVIOS_DIARIOS) {
    logCron('Fin (antes de las ' . HORA_ENVIOS_DIARIOS . ':00 no se envían cumpleaños ni recompra)');
    exit(0);
}

/* =========================================================================
   3. CUMPLEAÑOS
   ========================================================================= */

/* Configuración del regalo por empresa (cacheada en la corrida) */
function regaloEmpresa($cod_empresa)
{
    static $cache = [];
    if (!isset($cache[$cod_empresa])) {
        $cache[$cod_empresa] = Conexion::buscarRegistro(
            "SELECT e.nombre, e.fidelizacion, f.valor_regalo_cumple, f.compra_minimo_regalo_cumple, f.dias_regalo_cumple, f.cant_dias_caducidad_dinero
                FROM tb_empresas e
                LEFT JOIN tb_empresa_fidelizacion_puntos f ON f.cod_empresa = e.cod_empresa
                WHERE e.cod_empresa = :cod_empresa",
            [':cod_empresa' => $cod_empresa]
        );
    }
    return $cache[$cod_empresa];
}

/* Devuelve el cod_cliente si al usuario le corresponde regalo, o null */
function clienteConRegalo($cod_usuario, $cod_empresa)
{
    $conf = regaloEmpresa($cod_empresa);
    if (!$conf || $conf['fidelizacion'] != 1 || $conf['valor_regalo_cumple'] <= 0) return null;

    $cliente = Conexion::buscarRegistro(
        "SELECT cod_cliente FROM tb_clientes WHERE cod_usuario = :cod_usuario AND cod_empresa = :cod_empresa",
        [':cod_usuario' => $cod_usuario, ':cod_empresa' => $cod_empresa]
    );
    if (!$cliente) return null;

    if ($conf['compra_minimo_regalo_cumple'] > 0) {
        $compras = Conexion::buscarRegistro(
            "SELECT COALESCE(SUM(total), 0) as total FROM tb_orden_cabecera WHERE cod_usuario = :cod_usuario AND cod_empresa = :cod_empresa AND estado = 'ENTREGADA'",
            [':cod_usuario' => $cod_usuario, ':cod_empresa' => $cod_empresa]
        );
        if ($compras['total'] < $conf['compra_minimo_regalo_cumple']) return null;
    }
    return $cliente['cod_cliente'];
}

function acreditarRegalo($cod_cliente, $cod_empresa)
{
    $conf = regaloEmpresa($cod_empresa);
    $dias = $conf['dias_regalo_cumple'] > 0 ? $conf['dias_regalo_cumple'] : $conf['cant_dias_caducidad_dinero'];
    $caducidad = date('Y-m-d', strtotime("+$dias days"));
    $ok = Conexion::ejecutar(
        "INSERT INTO tb_cliente_dinero (cod_cliente, cod_tipo_pago, dinero, saldo, fecha, fecha_caducidad, estado)
            VALUES (:cod_cliente, 2, :dinero, :saldo, :fecha, :caducidad, 'A')",
        [':cod_cliente' => $cod_cliente, ':dinero' => $conf['valor_regalo_cumple'], ':saldo' => $conf['valor_regalo_cumple'], ':fecha' => date('Y-m-d'), ':caducidad' => $caducidad]
    );
    return $ok ? $caducidad : null;
}

try {
    $hoy = date('m-d');
    $diasCumple = [$hoy];
    // 29 de febrero: en años no bisiestos se celebra el 28
    if ($hoy === '02-28' && !checkdate(2, 29, (int)date('Y'))) {
        $diasCumple[] = '02-29';
    }
    $fechaPrevio = strtotime('+' . DIAS_AVISO_CUMPLEANOS . ' days');
    $diaPrevio = date('m-d', $fechaPrevio);

    $placeholders = [];
    $params = [':previo' => $diaPrevio];
    foreach ($diasCumple as $i => $d) {
        $placeholders[] = ":hoy$i";
        $params[":hoy$i"] = $d;
    }

    $cumpleaneros = Conexion::buscarVariosRegistro(
        "SELECT u.cod_usuario, u.cod_empresa, u.nombre, DATE_FORMAT(u.fecha_nacimiento, '%m-%d') as dia
            FROM tb_usuarios u
            INNER JOIN tb_empresas e ON e.cod_empresa = u.cod_empresa AND e.estado = 'A'
            WHERE u.cod_rol = 4 AND u.estado = 'A'
            AND DATE_FORMAT(u.fecha_nacimiento, '%m-%d') IN (" . implode(',', $placeholders) . ", :previo)",
        $params
    ) ?: [];

    $porEmpresa = [];
    foreach ($cumpleaneros as $u) {
        $porEmpresa[$u['cod_empresa']][] = $u;
    }

    foreach ($porEmpresa as $cod_empresa => $usuarios) {
        $cl = new cl_notificaciones_expo($cod_empresa);
        $empresa = html_entity_decode(regaloEmpresa($cod_empresa)['nombre']);
        $conf = regaloEmpresa($cod_empresa);
        $enviosDia = [];
        $enviosPrevio = [];
        $regalos = 0;

        foreach ($usuarios as $u) {
            $nombre = primerNombre($u['nombre']);
            $esHoy = in_array($u['dia'], $diasCumple);

            if ($esHoy) {
                if (!$cl->marcarAutomatica($u['cod_usuario'], 'cumpleanos', date('Y'))) continue;

                $caducidad = null;
                $cod_cliente = clienteConRegalo($u['cod_usuario'], $cod_empresa);
                if ($cod_cliente) {
                    $caducidad = acreditarRegalo($cod_cliente, $cod_empresa);
                    if ($caducidad) $regalos++;
                }

                $tokens = tokensDe($cl, $u['cod_usuario']);
                if (!$tokens) continue;
                if ($caducidad) {
                    $valor = '$' . number_format($conf['valor_regalo_cumple'], 2);
                    $mensaje = "$empresa te regala $valor para celebrar 🎁 Úsalo en tu próximo pedido antes del " . date('d/m/Y', strtotime($caducidad)) . '.';
                } else {
                    $mensaje = "Todo el equipo de $empresa te desea un día increíble 🎂";
                }
                $enviosDia[] = ['tokens' => $tokens, 'titulo' => "¡Feliz cumpleaños, $nombre! 🎉", 'mensaje' => $mensaje, 'data' => ['type' => 'cumpleanos', 'regalo' => $caducidad ? 1 : 0]];
            } else {
                if (!$cl->marcarAutomatica($u['cod_usuario'], 'cumpleanos_previo', date('Y', $fechaPrevio))) continue;

                $tokens = tokensDe($cl, $u['cod_usuario']);
                if (!$tokens) continue;
                if (clienteConRegalo($u['cod_usuario'], $cod_empresa)) {
                    $mensaje = "$nombre, en " . DIAS_AVISO_CUMPLEANOS . " días es tu cumpleaños y $empresa tiene un regalo esperándote 🎁";
                } else {
                    $mensaje = "$nombre, en " . DIAS_AVISO_CUMPLEANOS . " días es tu cumpleaños. ¡Celébralo con $empresa! 🥳";
                }
                $enviosPrevio[] = ['tokens' => $tokens, 'titulo' => '🎈 Tu cumpleaños se acerca', 'mensaje' => $mensaje, 'data' => ['type' => 'cumpleanos_previo']];
            }
        }

        $rDia = $cl->enviarAutomatica('cumpleanos', '¡Feliz cumpleaños! 🎉', 'Saludo del día del cumpleaños', $enviosDia);
        $rPrevio = $cl->enviarAutomatica('cumpleanos_previo', '🎈 Tu cumpleaños se acerca', 'Aviso ' . DIAS_AVISO_CUMPLEANOS . ' días antes del cumpleaños', $enviosPrevio);
        logCron("Cumpleaños empresa $cod_empresa: $regalos regalos, {$rDia['ok']} saludos y {$rPrevio['ok']} avisos entregados");
    }
} catch (Exception $ex) {
    logCron('ERROR cumpleaños: ' . $ex->getMessage());
}

/* =========================================================================
   4. RECOMPRA
   Clave del log = última orden entregada: 1 recordatorio por ciclo de inactividad.
   ========================================================================= */
try {
    $configs = Conexion::buscarVariosRegistro(
        "SELECT c.* FROM tb_notificaciones_auto_config c
            INNER JOIN tb_empresas e ON e.cod_empresa = c.cod_empresa AND e.estado = 'A'
            WHERE c.recompra_activo = 1",
        null
    ) ?: [];

    foreach ($configs as $config) {
        $cod_empresa = $config['cod_empresa'];
        $cl = new cl_notificaciones_expo($cod_empresa);

        $clientes = Conexion::buscarVariosRegistro(
            "SELECT u.cod_usuario, u.nombre, o.ultima_orden
                FROM tb_usuarios u
                INNER JOIN (
                    SELECT cod_usuario, MAX(cod_orden) AS ultima_orden, MAX(fecha) AS ultima_fecha
                    FROM tb_orden_cabecera
                    WHERE cod_empresa = :cod_empresa_ord AND estado = 'ENTREGADA'
                    GROUP BY cod_usuario
                ) o ON o.cod_usuario = u.cod_usuario
                WHERE u.cod_empresa = :cod_empresa
                AND u.cod_rol = 4 AND u.estado = 'A'
                AND o.ultima_fecha <= :limite
                AND u.cod_usuario IN (SELECT cod_usuario FROM tb_push_tokens)",
            [
                ':cod_empresa_ord' => $cod_empresa,
                ':cod_empresa' => $cod_empresa,
                ':limite' => date('Y-m-d H:i:s', strtotime('-' . (int)$config['recompra_dias'] . ' days')),
            ]
        ) ?: [];

        $envios = [];
        foreach ($clientes as $c) {
            if (!$cl->marcarAutomatica($c['cod_usuario'], 'recompra', 'orden' . $c['ultima_orden'])) continue;
            $tokens = tokensDe($cl, $c['cod_usuario']);
            if (!$tokens) continue;
            $envios[] = [
                'tokens' => $tokens,
                'titulo' => reemplazarNombre($config['recompra_titulo'], $c['nombre']),
                'mensaje' => reemplazarNombre($config['recompra_mensaje'], $c['nombre']),
                'data' => ['type' => 'recompra'],
            ];
        }

        // En el historial va el texto sin personalizar
        $quitarNombre = function ($texto) { return trim(preg_replace('/,?\s*\{nombre\}/u', '', $texto)); };
        $resultado = $cl->enviarAutomatica('recompra', $quitarNombre($config['recompra_titulo']), $quitarNombre($config['recompra_mensaje']), $envios);
        logCron("Recompra empresa $cod_empresa: " . count($envios) . " clientes, {$resultado['ok']} push entregados");
    }
} catch (Exception $ex) {
    logCron('ERROR recompra: ' . $ex->getMessage());
}

logCron('Fin');
