<?php
/* El token de Expo del celular del motorizado (así le llegan las carreras nuevas). Variables heredadas de index.php: $method, $request, $input, $motorizado. */

require_once __DIR__ . '/../clases/cl_riders_notificaciones.php';

$ClNotificaciones = new cl_riders_notificaciones();
$accion = $request[1] ?? '';

if ($method === 'POST' && $accion === 'suscribir') {
    $datos = validar($input, ['token' => 'required|str:255', 'plataforma' => 'nullable|str:20', 'sonido' => 'nullable|str:40']);
    // Solo sirven los tokens de Expo: el envío no entiende otros
    if (!preg_match('/^Expo(nent)?PushToken\[[^\]]+\]$/', $datos['token'])) throw new RidersException('El token de notificaciones no es válido');

    $plataforma = strtolower($datos['plataforma'] ?? getHeader('Device-Type') ?? 'android');
    $ClNotificaciones->suscribir($motorizado['cod_usuario'], $datos['token'], $plataforma, $datos['sonido'] ?? null);
    responderOk([], 'Token registrado');
}

if ($method === 'POST' && $accion === 'desuscribir') {
    $datos = validar($input, ['token' => 'required|str:255']);
    $ClNotificaciones->desuscribir($motorizado['cod_usuario'], $datos['token']);
    responderOk([], 'Token eliminado');
}
