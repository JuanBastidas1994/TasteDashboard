<?php
require_once __DIR__ . '/cl_riders_usuarios.php';
require_once __DIR__ . '/cl_riders_notificaciones.php';

/**
 * Las carreras del motorizado y sus pasos: aceptar -> llegó al local -> recogió y sale -> cerca del cliente -> entregó.
 * Cada paso exige el anterior y no se repite. No cambia tb_orden_cabecera.estado salvo al salir (ENVIANDO) y al entregar
 * (ENTREGADA): aceptar, llegar al local y estar cerca solo marcan la hora en tb_motorizado_asignacion (la orden sigue ASIGNADA).
 * El motorizado no puede anular ni dar por no entregada una orden: eso es de la flota (reportar-problema + resolución).
 */
class cl_riders_ordenes {

    const TABS = [
        'activos' => ['ASIGNADA', 'ENVIANDO'],
        'completados' => ['ENTREGADA'],
        'cancelados' => ['NO_ENTREGADA', 'ANULADA', 'CANCELADA'],
    ];

    const MOTIVOS = ['NO_CONTESTA', 'DIRECCION_INCORRECTA', 'CLIENTE_RECHAZO', 'NO_QUISO_PAGAR', 'OTRO'];
    const POR_PAGINA = 20;
    const FINALES = ['ENTREGADA' => 'entregada', 'NO_ENTREGADA' => 'no entregada', 'ANULADA' => 'anulada', 'CANCELADA' => 'cancelada'];

    private $usuarios;
    private $notif;

    public function __construct() {
        $this->usuarios = new cl_riders_usuarios();
        $this->notif = new cl_riders_notificaciones();
    }

    /** Estados que entiende la app. */
    private static function estadoApp($estado) {
        if ($estado === 'CANCELADA') return 'ANULADA';
        return in_array($estado, ['ENVIANDO', 'ENTREGADA', 'NO_ENTREGADA', 'ANULADA'], true) ? $estado : 'ASIGNADA';
    }

    private static function tiempos($f) {
        return [
            'fecha_asignacion' => fechaApp($f['fecha_asignacion']),
            'fecha_aceptacion' => fechaApp($f['fecha_aceptacion']),
            'fecha_llegada_local' => fechaApp($f['fecha_llegada_local']),
            'fecha_salida' => fechaApp($f['fecha_salida']),
            'fecha_cerca' => fechaApp($f['fecha_cerca']),
            'fecha_llegada' => fechaApp($f['fecha_llegada']),
        ];
    }

    // ---------------------------------------------------------------- lectura

    /**
     * Carreras del motorizado para un tab de "Mis pedidos", de todas las empresas/flotas en las que trabaja.
     * "activos" no pagina; "completados"/"cancelados" van por rango (hoy|semana|mes) y de 20 en 20.
     */
    public function lista($cod_motorizado, $tab, $rango = 'hoy', $pagina = 1) {
        $estados = self::TABS[$tab];
        $marcas = implode(',', array_fill(0, count($estados), '?'));
        // Cuándo "ocurrió" la carrera para ubicarla en el rango: entrega, o aceptación, o asignación
        $cuando = "COALESCE(ma.fecha_llegada, ma.fecha_aceptacion, ma.fecha_asignacion)";

        $sql = "SELECT oc.cod_orden, oc.estado, oc.total, oc.envio, oc.envio_iva, oc.telefono, oc.referencia, oc.referencia2,
                       oc.observacion, oc.latitud, oc.longitud, oc.cod_sucursal, oc.cod_empresa,
                       ma.fecha_asignacion, ma.fecha_aceptacion, ma.fecha_llegada_local, ma.fecha_salida, ma.fecha_cerca, ma.fecha_llegada,
                       e.nombre AS empresa_nombre, e.alias AS empresa_alias, e.logo AS empresa_logo, e.logo_min AS empresa_logo_min,
                       s.nombre AS sucursal_nombre,
                       (SELECT COUNT(*) FROM tb_orden_detalle d WHERE d.cod_orden = oc.cod_orden) AS num_items
                FROM tb_orden_cabecera oc
                INNER JOIN tb_motorizado_asignacion ma ON ma.cod_orden = oc.cod_orden
                INNER JOIN tb_empresas e ON e.cod_empresa = oc.cod_empresa
                LEFT JOIN tb_sucursales s ON s.cod_sucursal = oc.cod_sucursal
                WHERE ma.cod_motorizado = ? AND oc.estado IN ($marcas)";
        $params = array_merge([$cod_motorizado], $estados);

        if ($tab === 'activos') {
            $sql .= " ORDER BY ma.fecha_asignacion DESC";
        } else {
            $dias = $rango === 'semana' ? 6 : ($rango === 'mes' ? 29 : 0);
            $sql .= " AND $cuando >= ? ORDER BY $cuando DESC LIMIT " . self::POR_PAGINA . " OFFSET " . (max(1, (int) $pagina) - 1) * self::POR_PAGINA;
            $params[] = date('Y-m-d', strtotime("-$dias days")) . ' 00:00:00';
        }

        $filas = Conexion::buscarVariosRegistro($sql, $params) ?: [];
        $pagos = $this->pagosDe(array_column($filas, 'cod_orden'));

        return array_map(function ($f) use ($pagos) {
            $logo = urlAssetEmpresa($f['empresa_alias'], $f['empresa_logo']);
            return [
                'cod_orden' => (int) $f['cod_orden'],
                'numero' => (int) $f['cod_orden'],
                'estado' => self::estadoApp($f['estado']),
                'total' => (float) $f['total'],
                'is_envio' => 1,
                // Envío y forma de pago a la vista en la lista (lo que gana y si debe llevar efectivo)
                'pago' => self::pago($pagos[$f['cod_orden']] ?? [], (float) $f['envio'] + (float) $f['envio_iva']),
                'telefono' => $f['telefono'],
                'referencia' => $f['referencia'],
                'referencia2' => $f['referencia2'],
                'observacion' => $f['observacion'],
                'latitud' => coordenada($f['latitud']),
                'longitud' => coordenada($f['longitud']),
                'cod_sucursal' => (int) $f['cod_sucursal'],
                'cod_empresa' => (int) $f['cod_empresa'],
            ] + self::tiempos($f) + [
                'empresa_nombre' => $f['empresa_nombre'],
                'empresa_logo' => $logo,
                'empresa_logo_min' => urlAssetEmpresa($f['empresa_alias'], $f['empresa_logo_min']) ?: $logo,
                'sucursal_nombre' => $f['sucursal_nombre'],
                'num_items' => (int) $f['num_items'],
            ];
        }, $filas);
    }

    /** La asignación de la orden a este motorizado con lo necesario para validar pasos; null si no es suya (o no existe). */
    public function asignacion($cod_orden, $cod_motorizado) {
        $fila = Conexion::buscarRegistro(
            "SELECT ma.*, oc.estado, oc.cod_empresa, oc.cod_sucursal, oc.cod_usuario AS cod_cliente,
                    oc.latitud AS cliente_lat, oc.longitud AS cliente_lon,
                    s.latitud AS local_lat, s.longitud AS local_lon, ofe.cod_flota
             FROM tb_motorizado_asignacion ma
             INNER JOIN tb_orden_cabecera oc ON oc.cod_orden = ma.cod_orden
             LEFT JOIN tb_sucursales s ON s.cod_sucursal = oc.cod_sucursal
             LEFT JOIN tb_ordenes_flota ofe ON ofe.cod_orden = oc.cod_orden
             WHERE ma.cod_orden = :cod_orden AND ma.cod_motorizado = :cod_motorizado",
            ['cod_orden' => $cod_orden, 'cod_motorizado' => $cod_motorizado]
        );
        return $fila ?: null;
    }

    /** Pantalla de la carrera. */
    public function detalle($cod_orden, $usuario) {
        $f = Conexion::buscarRegistro(
            "SELECT oc.cod_orden, oc.estado, oc.total, oc.envio, oc.envio_iva, oc.telefono, oc.referencia, oc.referencia2,
                    oc.observacion, oc.latitud AS cliente_lat, oc.longitud AS cliente_lon,
                    u.nombre AS cliente_nombre, u.apellido AS cliente_apellido,
                    s.nombre AS sucursal_nombre, s.direccion AS sucursal_direccion,
                    s.latitud AS sucursal_lat, s.longitud AS sucursal_lon, s.telefono AS sucursal_telefono,
                    e.nombre AS empresa_nombre, e.alias AS empresa_alias, e.logo AS empresa_logo,
                    ofe.cod_flota,
                    ma.fecha_asignacion, ma.fecha_aceptacion, ma.fecha_llegada_local, ma.fecha_salida, ma.fecha_cerca, ma.fecha_llegada
             FROM tb_orden_cabecera oc
             INNER JOIN tb_motorizado_asignacion ma ON ma.cod_orden = oc.cod_orden
             INNER JOIN tb_empresas e ON e.cod_empresa = oc.cod_empresa
             LEFT JOIN tb_sucursales s ON s.cod_sucursal = oc.cod_sucursal
             LEFT JOIN tb_usuarios u ON u.cod_usuario = oc.cod_usuario
             LEFT JOIN tb_ordenes_flota ofe ON ofe.cod_orden = oc.cod_orden
             WHERE ma.cod_motorizado = :cod_motorizado AND oc.cod_orden = :cod_orden",
            ['cod_motorizado' => $usuario['cod_usuario'], 'cod_orden' => $cod_orden]
        );
        if (!$f) return null;

        $incidencias = $this->incidencias($cod_orden);
        $pendiente = null;
        foreach ($incidencias as $i) {
            if ($i['estado'] === 'PENDIENTE') { $pendiente = $i; break; }
        }
        $pagos = $this->pagosDe([$cod_orden]);
        $flota = $f['cod_flota'] ?: $usuario['cod_empresa'];

        return [
            'cod_orden' => (int) $f['cod_orden'],
            'numero' => (int) $f['cod_orden'],
            'estado' => self::estadoApp($f['estado']),
            'total' => (float) $f['total'],
            'is_envio' => 1,
            'telefono' => $f['telefono'],
            'referencia' => $f['referencia'],
            'referencia2' => $f['referencia2'],
            'observacion' => $f['observacion'],
            'cliente_lat' => coordenada($f['cliente_lat']),
            'cliente_lon' => coordenada($f['cliente_lon']),
            'cliente_nombre' => $f['cliente_nombre'],
            'cliente_apellido' => $f['cliente_apellido'] ?? '',
            'sucursal_nombre' => $f['sucursal_nombre'],
            'sucursal_direccion' => $f['sucursal_direccion'],
            'sucursal_lat' => coordenada($f['sucursal_lat']),
            'sucursal_lon' => coordenada($f['sucursal_lon']),
            'sucursal_telefono' => $f['sucursal_telefono'],
            'empresa_nombre' => $f['empresa_nombre'],
            'empresa_logo' => urlAssetEmpresa($f['empresa_alias'], $f['empresa_logo']),
            'cod_flota' => $f['cod_flota'] ? (int) $f['cod_flota'] : null,
        ] + self::tiempos($f) + [
            'pago' => self::pago($pagos[$cod_orden] ?? [], (float) $f['envio'] + (float) $f['envio_iva']),
            'items' => $this->items($cod_orden),
            'soporte_admins' => $flota ? $this->adminsFlota($flota) : [],
            'incidencia_pendiente' => $pendiente,
            'incidencias' => $incidencias,
        ];
    }

    /** @return array<int, array> pagos de efectivo/transferencia/tarjeta por orden */
    private function pagosDe(array $cods) {
        if (!$cods) return [];
        $marcas = implode(',', array_fill(0, count($cods), '?'));
        $filas = Conexion::buscarVariosRegistro("SELECT cod_orden, forma_pago, monto FROM tb_orden_pagos WHERE cod_orden IN ($marcas) AND forma_pago IN ('E','TB','T')", array_values($cods)) ?: [];
        $porOrden = [];
        foreach ($filas as $f) $porOrden[$f['cod_orden']][] = $f;
        return $porOrden;
    }

    /**
     * Qué cobra y qué paga el motorizado. Si es efectivo, al recoger deja al local `total - envío` de su bolsillo y cobra
     * el `total` al cliente: así ya no le debe nada al local después (`entregar_al_local` = 0.00). Si no es efectivo,
     * todo en cero: el pedido ya está pagado.
     */
    private static function pago(array $pagos, $envio) {
        $efectivo = null;
        $otro = null;
        foreach ($pagos as $p) {
            if ($p['forma_pago'] === 'E' && !$efectivo) $efectivo = $p;
            if ($p['forma_pago'] !== 'E' && !$otro) $otro = $p;
        }
        $cobrar = $efectivo ? round((float) $efectivo['monto'], 2) : 0.0;
        $pagoLocal = $efectivo ? max(0.0, round($cobrar - $envio, 2)) : 0.0;
        $pago = $efectivo ?: $otro;

        return [
            'forma_pago' => $pago ? $pago['forma_pago'] : null,
            'monto' => $pago ? round((float) $pago['monto'], 2) : 0.0,
            'cobrar_cliente' => $cobrar,
            'pagar_al_local' => $pagoLocal,
            'entregar_al_local' => 0.0,
            'envio' => round($envio, 2),
        ];
    }

    /** Ítems para que el motorizado verifique que el local le entregó todo. */
    private function items($cod_orden) {
        $items = Conexion::buscarVariosRegistro(
            "SELECT p.nombre, d.cantidad, d.precio_final FROM tb_orden_detalle d
             INNER JOIN tb_productos p ON p.cod_producto = d.cod_producto WHERE d.cod_orden = :cod_orden",
            ['cod_orden' => $cod_orden]
        ) ?: [];
        return array_map(function ($i) {
            return ['nombre' => html_entity_decode($i['nombre']), 'cantidad' => (int) $i['cantidad'], 'precio_final' => round((float) $i['precio_final'], 2)];
        }, $items);
    }

    /** Quién atiende en la flota: administradores con teléfono. */
    private function adminsFlota($cod_flota) {
        $filas = Conexion::buscarVariosRegistro(
            "SELECT cod_usuario, nombre, apellido, telefono, cod_rol FROM tb_usuarios
             WHERE cod_empresa = :cod_flota AND cod_rol IN (2, 3) AND estado = 'A' AND telefono IS NOT NULL AND telefono != ''
             ORDER BY cod_rol ASC, nombre ASC",
            ['cod_flota' => $cod_flota]
        ) ?: [];
        return array_map(function ($a) {
            return ['cod_usuario' => (int) $a['cod_usuario'], 'nombre' => $a['nombre'], 'apellido' => $a['apellido'] ?? '', 'telefono' => $a['telefono'], 'cod_rol' => (int) $a['cod_rol']];
        }, $filas);
    }

    // ---------------------------------------------------------------- pasos

    /** Que sea su carrera, esté en el estado esperado, no repita el paso y haya hecho el anterior. */
    private function verificar($cod_orden, $cod_motorizado, $esperado, $columna, $repetido, $requiere = null, $falta = null) {
        $a = $this->asignacion($cod_orden, $cod_motorizado);
        if (!$a) throw new RidersException('Esa carrera no está a cargo de este motorizado.');
        if (isset(self::FINALES[$a['estado']])) throw new RidersException('La carrera ya terminó: ' . self::FINALES[$a['estado']] . '.');
        if ($a[$columna]) throw new RidersException($repetido);
        if ($requiere && !$a[$requiere]) throw new RidersException($falta);
        if ($a['estado'] !== $esperado) {
            throw new RidersException($a['estado'] === 'ENVIANDO' ? 'Ya saliste con el pedido: este paso ya pasó.' : 'La carrera aún no está lista para este paso.');
        }
        return $a;
    }

    private function historial($cod_orden, $estado) {
        return Conexion::ejecutar(
            "INSERT INTO tb_orden_historial (cod_orden, estado, fecha, observacion, order_token) VALUES (:cod_orden, :estado, :fecha, '', '')",
            ['cod_orden' => $cod_orden, 'estado' => $estado, 'fecha' => fecha()]
        );
    }

    private function marcar($cod_orden, array $columnas) {
        $sets = [];
        $params = ['cod_orden' => $cod_orden];
        foreach ($columnas as $col => $valor) {
            $sets[] = "$col = :$col";
            $params[$col] = $valor;
        }
        if (!Conexion::ejecutar("UPDATE tb_motorizado_asignacion SET " . implode(', ', $sets) . " WHERE cod_orden = :cod_orden", $params)) {
            throw new RidersException('Error al actualizar el pedido');
        }
    }

    private function setEstadoOrden($cod_orden, $estado) {
        if (!Conexion::ejecutar("UPDATE tb_orden_cabecera SET estado = :estado WHERE cod_orden = :cod_orden", ['estado' => $estado, 'cod_orden' => $cod_orden])) {
            throw new RidersException('Error al actualizar el pedido');
        }
    }

    /** Carreras aceptadas y sin entregar (las que cuentan para el máximo a la vez), sin contar una. */
    public function contarEnCurso($cod_motorizado, $excepto = 0) {
        $fila = Conexion::buscarRegistro(
            "SELECT COUNT(*) AS total FROM tb_orden_cabecera oc
             INNER JOIN tb_motorizado_asignacion ma ON ma.cod_orden = oc.cod_orden
             WHERE ma.cod_motorizado = :cod_motorizado AND oc.cod_orden != :excepto AND ma.fecha_llegada IS NULL
             AND (oc.estado = 'ENVIANDO' OR (oc.estado = 'ASIGNADA' AND ma.fecha_aceptacion IS NOT NULL))",
            ['cod_motorizado' => $cod_motorizado, 'excepto' => $excepto]
        );
        return (int) ($fila['total'] ?? 0);
    }

    /**
     * Acepta la carrera ("Empezar"): va camino al local. Respeta el máximo de carreras a la vez del motorizado (o de la flota
     * que lleva la orden). Se valida aquí y no al asignar: el despachador puede asignarle varias, pero solo acepta las que puede llevar.
     */
    public function aceptar($usuario, $cod_orden) {
        $a = $this->verificar($cod_orden, $usuario['cod_usuario'], 'ASIGNADA', 'fecha_aceptacion', 'Ya aceptaste esta carrera.');
        $limite = $this->usuarios->limiteCarreras($usuario, $a['cod_flota']);
        if ($limite > 0 && $this->contarEnCurso($usuario['cod_usuario'], $cod_orden) >= $limite) {
            throw new RidersException($limite === 1
                ? 'Ya tienes una carrera en curso: entrégala antes de aceptar otra.'
                : "Ya tienes $limite carreras en curso (el máximo): entrega una antes de aceptar otra.");
        }
        $this->marcar($cod_orden, ['fecha_aceptacion' => fecha()]);
        $this->historial($cod_orden, 'ORDEN_ACEPTADA');
    }

    /** Llegó al local. $proximidad: distancia medida al marcar a mano (null si fue el GPS). */
    public function llegarAlLocal($usuario, $cod_orden, $proximidad = null) {
        $a = $this->verificar($cod_orden, $usuario['cod_usuario'], 'ASIGNADA', 'fecha_llegada_local', 'Ya avisaste que llegaste al local.', 'fecha_aceptacion', 'Primero acepta la carrera.');
        $this->marcar($cod_orden, ['fecha_llegada_local' => fecha()] + ($proximidad ? ['llegada_local_distancia_m' => $proximidad['distance'], 'llegada_local_lejos' => $proximidad['far'] ? 1 : 0] : []));
        $this->historial($cod_orden, 'PUNTO_RECOGIDA');
        $this->notif->pushSeguimiento($a['cod_cliente'], $cod_orden, '¡Tu repartidor llegó al restaurante! 🛵', 'Tu motorizado ya está en el local para recoger tu pedido.');
    }

    /** Recogió el pedido y sale hacia el cliente: la orden pasa a ENVIANDO y se le avisa al cliente. */
    public function recoger($usuario, $cod_orden) {
        $a = $this->verificar($cod_orden, $usuario['cod_usuario'], 'ASIGNADA', 'fecha_salida', 'Ya saliste con el pedido.', 'fecha_llegada_local', 'Primero avisa que llegaste al local.');
        $this->setEstadoOrden($cod_orden, 'ENVIANDO');
        $this->marcar($cod_orden, ['fecha_salida' => fecha()]);
        $this->historial($cod_orden, 'ENVIANDO');
        $this->notif->firebaseEstado($a['cod_empresa'], $cod_orden, $a['cod_sucursal'], 'ENVIANDO');
        $this->notif->pushSeguimiento($a['cod_cliente'], $cod_orden, '¡Tu pedido va en camino! 🛵', 'El motorizado ya salió y se dirige a tu ubicación.');
    }

    /** Está cerca de la dirección del cliente (no cambia el estado). */
    public function cercaDelCliente($usuario, $cod_orden) {
        $a = $this->verificar($cod_orden, $usuario['cod_usuario'], 'ENVIANDO', 'fecha_cerca', 'Ya avisaste que estás cerca del cliente.', 'fecha_salida', 'Primero sal del local con el pedido.');
        $this->marcar($cod_orden, ['fecha_cerca' => fecha(), 'push_cercania_enviado' => 1]);
        $this->historial($cod_orden, 'PUNTO_ENTREGA');
        $this->notif->pushSeguimiento($a['cod_cliente'], $cod_orden, '¡Ya casi llega! 👀', 'Tu motorizado está muy cerca. Prepárate para recibir tu pedido.');
    }

    /** Entregó el pedido. Avisa a Firebase: el panel de Gestión de Órdenes factura solo al llegar a ENTREGADA. */
    public function entregar($usuario, $cod_orden, $proximidad = null) {
        $a = $this->verificar($cod_orden, $usuario['cod_usuario'], 'ENVIANDO', 'fecha_llegada', 'Esta carrera ya fue entregada.', 'fecha_salida', 'Primero sal del local con el pedido.');
        $this->setEstadoOrden($cod_orden, 'ENTREGADA');
        $this->marcar($cod_orden, ['fecha_llegada' => fecha()] + ($proximidad ? ['entrega_distancia_m' => $proximidad['distance'], 'entrega_lejos' => $proximidad['far'] ? 1 : 0] : []));
        $this->historial($cod_orden, 'ENTREGADA');
        $this->notif->firebaseEstado($a['cod_empresa'], $cod_orden, $a['cod_sucursal'], 'ENTREGADA');
        $this->notif->pushSeguimiento($a['cod_cliente'], $cod_orden, '¡Pedido entregado! 🎉', 'Esperamos que lo disfrutes. ¡Buen provecho!');
    }

    // ---------------------------------------------------------------- ubicación

    /** Carreras aceptadas y sin entregar, con las coordenadas del local y del cliente, para el geofence. */
    private function enCurso($cod_motorizado) {
        return Conexion::buscarVariosRegistro(
            "SELECT oc.cod_orden, oc.estado, ma.fecha_llegada_local, ma.fecha_cerca,
                    oc.latitud AS cliente_lat, oc.longitud AS cliente_lon, s.latitud AS local_lat, s.longitud AS local_lon
             FROM tb_orden_cabecera oc
             INNER JOIN tb_motorizado_asignacion ma ON ma.cod_orden = oc.cod_orden
             LEFT JOIN tb_sucursales s ON s.cod_sucursal = oc.cod_sucursal
             WHERE ma.cod_motorizado = :cod_motorizado AND ma.fecha_llegada IS NULL
             AND (oc.estado = 'ENVIANDO' OR (oc.estado = 'ASIGNADA' AND ma.fecha_aceptacion IS NOT NULL))
             ORDER BY ma.fecha_asignacion DESC",
            ['cod_motorizado' => $cod_motorizado]
        ) ?: [];
    }

    /**
     * Marca "llegó al local" y "cerca del cliente" cuando la posición cae dentro del radio. A diferencia de api_flotas no exige
     * dos pings seguidos dentro del radio: basta uno, y también se evalúa al aceptar con la última ubicación (un motorizado
     * parado en la base no genera pings y nunca activaba "Llegué al local").
     *
     * @return string[] pasos marcados: 'llego_al_local' y/o 'cerca_del_cliente'
     */
    public function geofence($usuario, $lat, $lon) {
        $marcados = [];
        foreach ($this->enCurso($usuario['cod_usuario']) as $c) {
            try {
                if ($c['estado'] === 'ASIGNADA' && !$c['fecha_llegada_local']) {
                    $destino = [coordenada($c['local_lat']), coordenada($c['local_lon'])];
                    if ($destino[0] !== null && $destino[1] !== null && distanciaMetros($lat, $lon, $destino[0], $destino[1]) <= RIDERS_GEOFENCE_STORE_METERS) {
                        $this->llegarAlLocal($usuario, $c['cod_orden']);
                        $marcados[] = 'llego_al_local';
                    }
                } elseif ($c['estado'] === 'ENVIANDO' && !$c['fecha_cerca']) {
                    $destino = [coordenada($c['cliente_lat']), coordenada($c['cliente_lon'])];
                    if ($destino[0] !== null && $destino[1] !== null && distanciaMetros($lat, $lon, $destino[0], $destino[1]) <= RIDERS_GEOFENCE_CUSTOMER_METERS) {
                        $this->cercaDelCliente($usuario, $c['cod_orden']);
                        $marcados[] = 'cerca_del_cliente';
                    }
                }
            } catch (RidersException $e) {
                // Otro proceso (el botón manual, la flota) pudo marcar el paso antes: no es un error para quien reporta la ubicación
            }
        }
        return $marcados;
    }

    /** Qué tan lejos está del local o del cliente al marcar a mano. Advertir y no bloquear. @return array{distance: ?int, far: bool} */
    public function proximidad($asignacion, $destino, $posicion) {
        $lat = coordenada($asignacion[$destino === 'local' ? 'local_lat' : 'cliente_lat']);
        $lon = coordenada($asignacion[$destino === 'local' ? 'local_lon' : 'cliente_lon']);
        if (!$posicion || $lat === null || $lon === null) return ['distance' => null, 'far' => false];

        $metros = (int) round(distanciaMetros($posicion[0], $posicion[1], $lat, $lon));
        return ['distance' => $metros, 'far' => $metros > ($destino === 'local' ? RIDERS_FAR_STORE_METERS : RIDERS_FAR_CUSTOMER_METERS)];
    }

    // ---------------------------------------------------------------- problemas

    private function incidenciaApp($i) {
        return [
            'cod_incidencia' => (int) $i['cod_incidencia'],
            'motivo' => $i['motivo'],
            'comentario' => $i['comentario'],
            'fecha_reporte' => fechaApp($i['fecha_reporte']),
            'estado' => $i['estado'],
            'resuelto_por' => $i['estado'] === 'PENDIENTE' ? null : $i['resuelto_por'],
            'comentario_resolucion' => $i['comentario_resolucion'],
            'fecha_resolucion' => fechaApp($i['fecha_resolucion']),
        ];
    }

    private function incidencias($cod_orden) {
        $filas = Conexion::buscarVariosRegistro(
            "SELECT cod_incidencia, motivo, comentario, fecha_reporte, estado, resuelto_por, comentario_resolucion, fecha_resolucion
             FROM tb_orden_incidencia WHERE cod_orden = :cod_orden ORDER BY fecha_reporte ASC, cod_incidencia ASC",
            ['cod_orden' => $cod_orden]
        ) ?: [];
        return array_map([$this, 'incidenciaApp'], $filas);
    }

    public function hayIncidenciaPendiente($cod_orden) {
        return (bool) Conexion::buscarRegistro("SELECT cod_incidencia FROM tb_orden_incidencia WHERE cod_orden = :cod_orden AND estado = 'PENDIENTE'", ['cod_orden' => $cod_orden]);
    }

    /**
     * El motorizado reporta que no puede entregar. NO cambia el estado de la orden: sigue en ENVIANDO hasta que la flota lo
     * resuelva (así no puede "auto-cancelar", p. ej. quedarse con el efectivo y decir que no contestaron).
     */
    public function reportarProblema($usuario, $cod_orden, $motivo, $comentario) {
        $a = $this->asignacion($cod_orden, $usuario['cod_usuario']);
        if (!$a) throw new RidersException('Esa carrera no está a cargo de este motorizado.');
        if ($this->hayIncidenciaPendiente($cod_orden)) return false;

        $ok = Conexion::ejecutar(
            "INSERT INTO tb_orden_incidencia (cod_orden, cod_motorizado, motivo, comentario, fecha_reporte) VALUES (:cod_orden, :cod_motorizado, :motivo, :comentario, :fecha)",
            ['cod_orden' => $cod_orden, 'cod_motorizado' => $usuario['cod_usuario'], 'motivo' => $motivo, 'comentario' => trim((string) $comentario), 'fecha' => fecha()]
        );
        if (!$ok) throw new RidersException('Error al reportar');

        // Solo "no contesta": el push puede hacer que el cliente llame de vuelta; los otros motivos no se resuelven así
        if ($motivo === 'NO_CONTESTA') {
            $this->notif->pushSeguimiento($a['cod_cliente'], $cod_orden, 'No hemos podido contactarte', 'Tu motorizado ha intentado contactarte sin respuesta. Contáctalo para coordinar la entrega.');
        }
        return true;
    }

    /** El propio motorizado dice "ya se resolvió, sigo". No requiere admin; anular sigue siendo de la flota. */
    public function resolverProblema($usuario, $cod_orden, $comentario) {
        return Conexion::ejecutar(
            "UPDATE tb_orden_incidencia
             SET estado = 'DESCARTADA', resuelto_por = 'MOTORIZADO', cod_admin_resuelve = :cod_usuario, comentario_resolucion = :comentario, fecha_resolucion = :fecha
             WHERE cod_orden = :cod_orden AND estado = 'PENDIENTE'",
            ['cod_usuario' => $usuario['cod_usuario'], 'comentario' => $comentario ? trim($comentario) : null, 'fecha' => fecha(), 'cod_orden' => $cod_orden]
        );
    }
}
