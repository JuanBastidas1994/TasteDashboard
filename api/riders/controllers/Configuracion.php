<?php
/*
 * Datos de contacto de Taste y versión de la app (para el aviso de actualización). Es pública: la app la pide antes de iniciar
 * sesión. Sin `app_version` la app no muestra el aviso. La versión sale de tb_empresas_versiones_app (pantalla "Versiones de app").
 * Variables heredadas de index.php: $method, $request, $input.
 */

if ($method === 'GET' && ($request[1] ?? '') === '') {
    $plataforma = strtoupper(getHeader('Device-Type') ?: 'ANDROID');
    $respuesta = [
        'informacion' => ['phone' => RIDERS_APP_PHONE, 'email' => RIDERS_APP_EMAIL, 'address' => RIDERS_APP_ADDRESS, 'color' => '#e1380f'],
    ];

    $version = Conexion::buscarRegistro(
        "SELECT name, code, texto, obligatorio, url FROM tb_empresas_versiones_app
         WHERE cod_empresa = :cod_empresa AND aplicacion = :aplicacion ORDER BY code DESC LIMIT 1",
        ['cod_empresa' => RIDERS_APP_COD_EMPRESA, 'aplicacion' => $plataforma]
    );
    if ($version && (int) $version['code'] > 0) {
        $respuesta['app_version'] = [
            'name' => $version['name'],
            'code' => (int) $version['code'],
            'text' => $version['texto'],
            'required' => (bool) $version['obligatorio'],
            'url' => $version['url'] ?: ($plataforma === 'ANDROID' ? RIDERS_APP_ANDROID_URL : ''),
            // Llaves que ya lee el aviso de actualización de la app
            'texto' => $version['texto'],
            'obligatorio' => (int) $version['obligatorio'],
        ];
    }
    responderOk($respuesta, 'Correcto');
}
