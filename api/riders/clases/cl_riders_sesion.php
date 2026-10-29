<?php

/**
 * Sesión de la app de motorizados: el token que reemplaza al `User-Id` crudo de api_flotas (con el que cualquiera que
 * supiera un id podía ubicar a otro motorizado). Solo se guarda el sha256 del token. También lleva el límite de intentos del login.
 */
class cl_riders_sesion {

    /** Crea una sesión y devuelve el token en claro (única vez que existe). */
    public function emitir($cod_usuario, $dispositivo = null) {
        $token = bin2hex(random_bytes(32));
        Conexion::ejecutar(
            "INSERT INTO tb_motorizado_token (cod_usuario, token_hash, dispositivo, ultimo_uso, fecha_creacion)
             VALUES (:cod_usuario, :hash, :dispositivo, :fecha, :fecha2)",
            [
                'cod_usuario' => $cod_usuario, 'hash' => hash('sha256', $token),
                'dispositivo' => $dispositivo ? mb_substr($dispositivo, 0, 60) : null,
                'fecha' => fecha(), 'fecha2' => fecha(),
            ]
        );
        return $token;
    }

    /** El motorizado dueño del token (sin la clave) o null: token desconocido, revocado, o cuenta dada de baja. */
    public function autenticar($token) {
        $hash = hash('sha256', $token);
        $usuario = Conexion::buscarRegistro(
            "SELECT u.* FROM tb_motorizado_token t
             INNER JOIN tb_usuarios u ON u.cod_usuario = t.cod_usuario
             WHERE t.token_hash = :hash AND t.fecha_revocado IS NULL AND u.cod_rol = :rol AND u.estado = 'A'",
            ['hash' => $hash, 'rol' => ROL_MOTORIZADO]
        );
        if (!$usuario) return null;

        Conexion::ejecutar(
            "UPDATE tb_motorizado_token SET ultimo_uso = :fecha WHERE token_hash = :hash AND (ultimo_uso IS NULL OR ultimo_uso < :limite)",
            ['fecha' => fecha(), 'hash' => $hash, 'limite' => date('Y-m-d H:i:s', time() - 60)]
        );
        unset($usuario['password']);
        return $usuario;
    }

    public function cerrar($token) {
        return Conexion::ejecutar(
            "UPDATE tb_motorizado_token SET fecha_revocado = :fecha WHERE token_hash = :hash AND fecha_revocado IS NULL",
            ['fecha' => fecha(), 'hash' => hash('sha256', $token)]
        );
    }

    /** Cierra todas las sesiones del motorizado (al darlo de baja o desactivarlo). */
    public function cerrarTodas($cod_usuario) {
        return Conexion::ejecutar(
            "UPDATE tb_motorizado_token SET fecha_revocado = :fecha WHERE cod_usuario = :cod_usuario AND fecha_revocado IS NULL",
            ['fecha' => fecha(), 'cod_usuario' => $cod_usuario]
        );
    }

    /** Lanza el error de "demasiados intentos" si la clave ya llegó al máximo en la ventana. */
    public function limitar($clave) {
        $desde = date('Y-m-d H:i:s', time() - RIDERS_LOGIN_VENTANA);
        $fila = Conexion::buscarRegistro(
            "SELECT COUNT(*) AS total, MIN(fecha) AS primero FROM tb_motorizado_login_intento WHERE clave = :clave AND fecha > :desde",
            ['clave' => $clave, 'desde' => $desde]
        );
        if ($fila && (int) $fila['total'] >= RIDERS_LOGIN_MAX_INTENTOS) {
            $espera = max(1, strtotime($fila['primero']) + RIDERS_LOGIN_VENTANA - time());
            throw new RidersException("Demasiados intentos. Espera $espera segundos.");
        }
    }

    public function golpear($clave) {
        Conexion::ejecutar("INSERT INTO tb_motorizado_login_intento (clave, fecha) VALUES (:clave, :fecha)", ['clave' => $clave, 'fecha' => fecha()]);
        // Limpieza barata de lo que ya no cuenta
        if (mt_rand(1, 50) === 1) {
            Conexion::ejecutar("DELETE FROM tb_motorizado_login_intento WHERE fecha < :limite", ['limite' => date('Y-m-d H:i:s', time() - 3600)]);
        }
    }

    public function limpiar($clave) {
        Conexion::ejecutar("DELETE FROM tb_motorizado_login_intento WHERE clave = :clave", ['clave' => $clave]);
    }
}
