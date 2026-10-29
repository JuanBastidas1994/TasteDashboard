<?php
/* Las carreras del motorizado y sus pasos. Variables heredadas de index.php: $method, $request, $input, $motorizado. */

require_once __DIR__ . '/../clases/cl_riders_ordenes.php';
require_once __DIR__ . '/../clases/cl_riders_usuarios.php';

$Clordenes = new cl_riders_ordenes();
$Clusuarios = new cl_riders_usuarios();
$accion = $request[1] ?? '';

/** Una carrera ajena responde igual que una inexistente, para no revelar cuál es cuál. */
function propia($Clordenes, $motorizado, $cod_orden) {
    return $Clordenes->asignacion($cod_orden, $motorizado['cod_usuario'])
        ?? throw new RidersException('Este pedido no está asignado a tu cuenta');
}

/**
 * Posición para medir la distancia: la que mandó la app (que también se guarda como la última del motorizado) o, si no
 * vino, la última conocida si es reciente.
 */
function posicion($Clusuarios, $motorizado, $datos) {
    if (isset($datos['lat'], $datos['lon'])) {
        $Clusuarios->setUbicacion($motorizado['cod_usuario'], $datos['lat'], $datos['lon']);
        return [(float) $datos['lat'], (float) $datos['lon']];
    }
    return $Clusuarios->ubicacionReciente($motorizado['cod_usuario']);
}

/** Lejos y sin confirmar: la app muestra el diálogo y vuelve a enviar con `confirmar: true`. No es un error de sesión (HTTP 200). */
function pedirConfirmacion($proximidad, $datos, $lugar, $pregunta) {
    if (!$proximidad['far'] || !empty($datos['confirmar'])) return;
    jsonResponse([
        'success' => 0,
        'mensaje' => 'Estás a ' . formatoDistancia($proximidad['distance']) . " $lugar. $pregunta",
        'requiere_confirmacion' => true,
        'distancia_m' => $proximidad['distance'],
    ]);
}

$reglasProximidad = ['lat' => 'nullable|num:-90,90', 'lon' => 'nullable|num:-180,180', 'confirmar' => 'nullable|bool'];

/** ?tab=activos|completados|cancelados · ?rango=hoy|semana|mes (solo los dos últimos) · ?page= */
if ($method === 'GET' && $accion === 'lista') {
    $tab = (string) ($_GET['tab'] ?? 'activos');
    if (!isset(cl_riders_ordenes::TABS[$tab])) throw new RidersException("Tab '$tab' no válido");
    $pagina = max(1, (int) ($_GET['page'] ?? 1));
    $rango = (string) ($_GET['rango'] ?? 'hoy');

    responderOk(['tab' => $tab, 'page' => $tab === 'activos' ? 1 : $pagina, 'data' => $Clordenes->lista($motorizado['cod_usuario'], $tab, $rango, $pagina)]);
}

if ($method === 'GET' && $accion === 'detalle') {
    $cod_orden = (int) ($_GET['cod_orden'] ?? 0);
    propia($Clordenes, $motorizado, $cod_orden);
    responderOk(['data' => $Clordenes->detalle($cod_orden, $motorizado)]);
}

/**
 * Acepta la carrera ("Empezar"). Si la app manda lat/lon se guardan antes de evaluar el geofence: así, si el motorizado
 * ya está en el local, "Llegué al local" se marca en este mismo momento aunque no se mueva.
 */
if ($method === 'POST' && $accion === 'iniciar') {
    $datos = validar($input, ['cod_orden' => 'required|int'] + $reglasProximidad);
    $asignacion = propia($Clordenes, $motorizado, $datos['cod_orden']);

    if ($asignacion['fecha_aceptacion']) responderOk([], 'Ya habías iniciado esta carrera');

    if (isset($datos['lat'], $datos['lon'])) $Clusuarios->setUbicacion($motorizado['cod_usuario'], $datos['lat'], $datos['lon']);
    $Clordenes->aceptar($motorizado, $datos['cod_orden']);

    $marcados = [];
    if ($ultima = $Clusuarios->ubicacionReciente($motorizado['cod_usuario'])) {
        $marcados = $Clordenes->geofence($motorizado, $ultima[0], $ultima[1]);
    }
    responderOk(['geofence' => $marcados[0] ?? null], 'Carrera iniciada');
}

/** Botón manual de respaldo para cuando el GPS no lo marcó solo. */
if ($method === 'POST' && $accion === 'llegue-al-local') {
    $datos = validar($input, ['cod_orden' => 'required|int'] + $reglasProximidad);
    $asignacion = propia($Clordenes, $motorizado, $datos['cod_orden']);

    if ($asignacion['fecha_llegada_local']) responderOk([], 'Ya avisaste que llegaste al local');

    $proximidad = $Clordenes->proximidad($asignacion, 'local', posicion($Clusuarios, $motorizado, $datos));
    pedirConfirmacion($proximidad, $datos, 'del local', '¿Seguro que ya llegaste?');
    $Clordenes->llegarAlLocal($motorizado, $datos['cod_orden'], $proximidad);

    responderOk([], 'Listo, avisamos que llegaste al local');
}

/** ENVIANDO = recogió y sale hacia el cliente · ENTREGADA = entregó. El motorizado no puede anular ni dar por no entregada. */
if ($method === 'POST' && $accion === 'actualizar-estado') {
    $datos = validar($input, ['cod_orden' => 'required|int', 'estado' => 'required|str'] + $reglasProximidad);
    $asignacion = propia($Clordenes, $motorizado, $datos['cod_orden']);

    if ($datos['estado'] === 'ENVIANDO') {
        // Si el aviso de llegada falló (sin GPS, sin señal) no se le bloquea la salida: se marca al salir
        if (!$asignacion['fecha_llegada_local'] && $asignacion['fecha_aceptacion']) {
            $proximidad = $Clordenes->proximidad($asignacion, 'local', posicion($Clusuarios, $motorizado, $datos));
            pedirConfirmacion($proximidad, $datos, 'del local', '¿Seguro que ya recogiste el pedido?');
            $Clordenes->llegarAlLocal($motorizado, $datos['cod_orden'], $proximidad);
        }
        $Clordenes->recoger($motorizado, $datos['cod_orden']);
    } elseif ($datos['estado'] === 'ENTREGADA') {
        $proximidad = $Clordenes->proximidad($asignacion, 'cliente', posicion($Clusuarios, $motorizado, $datos));
        pedirConfirmacion($proximidad, $datos, 'del cliente', '¿Seguro que ya entregaste el pedido?');
        $Clordenes->entregar($motorizado, $datos['cod_orden'], $proximidad);
    } else {
        throw new RidersException('Estado no válido');
    }

    responderOk([], 'Pedido actualizado correctamente');
}

if ($method === 'POST' && $accion === 'reportar-problema') {
    $datos = validar($input, ['cod_orden' => 'required|int', 'motivo' => 'required|str', 'comentario' => 'nullable|str:255']);
    $asignacion = propia($Clordenes, $motorizado, $datos['cod_orden']);

    if (!in_array($datos['motivo'], cl_riders_ordenes::MOTIVOS, true)) throw new RidersException('Motivo no válido');
    if ($asignacion['estado'] !== 'ENVIANDO') throw new RidersException('Solo puedes reportar un problema mientras vas camino al cliente');

    if (!$Clordenes->reportarProblema($motorizado, $datos['cod_orden'], $datos['motivo'], $datos['comentario'] ?? null)) {
        responderOk([], 'Ya habías reportado un problema con este pedido, está pendiente de revisión');
    }
    responderOk([], 'Problema reportado, un administrador lo va a revisar');
}

/** El propio motorizado dice "ya se resolvió, sigo" (p. ej. el cliente volvió a llamar). Anular sigue siendo de la flota. */
if ($method === 'POST' && $accion === 'resolver-problema') {
    $datos = validar($input, ['cod_orden' => 'required|int', 'comentario' => 'nullable|str:255']);
    propia($Clordenes, $motorizado, $datos['cod_orden']);

    if (!$Clordenes->hayIncidenciaPendiente($datos['cod_orden'])) throw new RidersException('Este pedido no tiene un problema pendiente');
    $Clordenes->resolverProblema($motorizado, $datos['cod_orden'], $datos['comentario'] ?? null);

    responderOk([], 'Listo, puedes continuar');
}
