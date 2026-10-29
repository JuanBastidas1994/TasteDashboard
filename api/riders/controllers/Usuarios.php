<?php
/* Sesión, perfil, ubicación y disponibilidad. Variables heredadas de index.php: $method, $request, $input, $motorizado (null en rutas públicas). */

require_once __DIR__ . '/../clases/cl_riders_usuarios.php';
require_once __DIR__ . '/../clases/cl_riders_ordenes.php';
require_once __DIR__ . '/../clases/cl_riders_sesion.php';
require_once __DIR__ . '/../clases/cl_riders_google.php';

$Clusuarios = new cl_riders_usuarios();
$Clordenes = new cl_riders_ordenes();
$Clsesion = new cl_riders_sesion();
$accion = $request[1] ?? '';
$dispositivo = getHeader('Device-Type');
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

/** Usuario y clave (MD5 del sistema anterior). Limita los intentos por usuario e IP. */
if ($method === 'POST' && $accion === 'login') {
    $datos = validar($input, ['usuario' => 'required|str:190', 'password' => 'required|str:190']);
    $clave = 'rider-login:' . mb_strtolower($datos['usuario']) . '|' . $ip;

    $Clsesion->limitar($clave);
    $perfil = $Clusuarios->login($datos['usuario'], $datos['password']);
    if (!$perfil) {
        $Clsesion->golpear($clave);
        throw new RidersException('Usuario o contraseña incorrectos');
    }
    $Clsesion->limpiar($clave);

    $token = $Clsesion->emitir($perfil['cod_usuario'], $dispositivo);
    responderOk(['token' => $token, 'data' => $Clusuarios->perfil($perfil, $Clordenes->contarEnCurso($perfil['cod_usuario']) > 0)], 'Login correcto');
}

/**
 * Con Google. Si la cuenta aún no está en una flota responde `requiere_codigo` (sin token) y la app pide el código
 * de invitación y lo canjea con el mismo id_token (canjear-codigo). A diferencia de api_flotas, no crea cuentas sueltas.
 */
if ($method === 'POST' && $accion === 'login_google') {
    $datos = validar($input, ['id_token' => 'required|str']);
    $clave = 'rider-google:' . $ip;
    $Clsesion->limitar($clave);
    $Clsesion->golpear($clave);

    $google = (new cl_riders_google())->verificar($datos['id_token']);

    // Solo se empareja por un correo que Google confirmó; la cuenta más reciente que esté activa
    $usuario = null;
    $inactiva = false;
    if ($google['email'] && $google['email_verified']) {
        foreach ($Clusuarios->getPorCorreo($google['email']) as $candidato) {
            if ($candidato['estado'] === 'A') { $usuario = $candidato; break; }
            $inactiva = true;
        }
    }
    if (!$usuario && $inactiva) throw new RidersException('Tu cuenta está desactivada. Habla con tu flota.');
    if (!$usuario || !$Clusuarios->estaEnFlota($usuario)) {
        responderOk(['requiere_codigo' => true, 'data' => null], 'Ingresa el código que te dio tu flota');
    }

    $token = $Clsesion->emitir($usuario['cod_usuario'], $dispositivo);
    responderOk(['token' => $token, 'requiere_codigo' => false, 'data' => $Clusuarios->perfil($usuario, $Clordenes->contarEnCurso($usuario['cod_usuario']) > 0)], 'Login correcto');
}

/**
 * Canjea el código de invitación (GENERAL: se une a la flota como invitado) o de reactivación (REACTIVACION: liga su
 * Google a una cuenta ya creada) con el mismo id_token de Google. Es público: todavía no tiene sesión.
 */
if ($method === 'POST' && $accion === 'canjear-codigo') {
    $datos = validar($input, ['id_token' => 'required|str', 'codigo' => 'required|str:20']);
    $clave = 'rider-code:' . $ip;
    $Clsesion->limitar($clave);
    $Clsesion->golpear($clave);

    $google = (new cl_riders_google())->verificar($datos['id_token']);
    if (!$google['email'] || !$google['email_verified']) throw new RidersException('Tu cuenta de Google no tiene correo.');

    $fila = $Clusuarios->getCodigoVigente(strtoupper(trim($datos['codigo'])));
    if (!$fila) throw new RidersException('El código no existe o ya venció. Pide uno nuevo a tu flota.');

    if ($fila['tipo'] === 'GENERAL') {
        $usuario = null;
        foreach ($Clusuarios->getPorCorreo($google['email']) as $candidato) {
            if ($candidato['estado'] === 'A') { $usuario = $candidato; break; }
        }
        if (!$usuario) $usuario = $Clusuarios->crearDesdeGoogle($google['email'], $google['name'], $google['picture']);
        if (!$usuario || !$Clusuarios->vincularAFlota($usuario['cod_usuario'], $fila['cod_empresa'])) {
            throw new RidersException('No se pudo vincular a la flota');
        }
    } else {
        $usuario = $Clusuarios->reactivarCuenta($fila['cod_usuario_objetivo'], $google['email'], $google['picture'])
            ?? throw new RidersException('Este código ya no es válido. Pide uno nuevo a tu flota.');
        if ($usuario['estado'] !== 'A') throw new RidersException('Tu cuenta está desactivada. Habla con tu flota.');
    }
    $Clusuarios->marcarCodigoUsado($fila['cod_codigo']);

    $token = $Clsesion->emitir($usuario['cod_usuario'], $dispositivo);
    responderOk(['token' => $token, 'data' => $Clusuarios->perfil($usuario, $Clordenes->contarEnCurso($usuario['cod_usuario']) > 0)], 'Te uniste correctamente');
}

// A partir de aquí todo requiere sesión (index.php ya la validó y dejó $motorizado)

if ($method === 'POST' && $accion === 'logout') {
    $Clsesion->cerrar(bearerToken());
    responderOk([], 'Sesión cerrada');
}

if ($method === 'GET' && $accion === 'perfil') {
    responderOk(['data' => $Clusuarios->perfil($motorizado, $Clordenes->contarEnCurso($motorizado['cod_usuario']) > 0)]);
}

/** Guarda la última ubicación y revisa si ya llegó al local o está cerca del cliente (`geofence`). */
if ($method === 'POST' && $accion === 'ubicacion') {
    $datos = validar($input, ['lat' => 'required|num:-90,90', 'lon' => 'required|num:-180,180']);

    $Clusuarios->setUbicacion($motorizado['cod_usuario'], $datos['lat'], $datos['lon']);
    $marcados = $Clordenes->geofence($motorizado, $datos['lat'], $datos['lon']);

    responderOk(['fecha' => fecha(), 'geofence' => $marcados[0] ?? null, 'geofences' => $marcados], 'Ubicación actualizada correctamente');
}

if ($method === 'POST' && $accion === 'disponibilidad') {
    $datos = validar($input, ['isActive' => 'required']);
    $disponible = filter_var($datos['isActive'], FILTER_VALIDATE_BOOLEAN);

    if (!$disponible && $Clordenes->contarEnCurso($motorizado['cod_usuario']) > 0) {
        throw new RidersException('No puedes desconectarte, tienes un pedido en camino.');
    }
    $Clusuarios->setDisponibilidad($motorizado['cod_usuario'], $disponible);
    responderOk(['isActive' => $disponible], 'Disponibilidad actualizada');
}

if ($method === 'GET' && $accion === 'estadisticas-hoy') {
    responderOk([
        'pedidos_hoy' => $Clusuarios->pedidosHoy($motorizado['cod_usuario']),
        'tiempo_conectado_segundos' => $Clusuarios->tiempoConectadoHoySegundos($motorizado['cod_usuario']),
    ]);
}
