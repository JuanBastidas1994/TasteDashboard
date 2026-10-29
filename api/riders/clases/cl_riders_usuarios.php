<?php

/** Motorizados (tb_usuarios rol 17): cuenta, perfil, ubicación, disponibilidad y vinculación a flotas por código. */
class cl_riders_usuarios {

    const CAMPOS = "cod_usuario, cod_empresa, nombre, apellido, correo, usuario, telefono, num_documento, imagen, placa, is_active, estado, max_carreras";

    /** Login clásico. `usuario` acepta el usuario o el correo (el dashboard nuevo identifica por correo). */
    public function login($usuario, $password) {
        $fila = Conexion::buscarRegistro(
            "SELECT " . self::CAMPOS . " FROM tb_usuarios
             WHERE (usuario = :usuario OR correo = :correo) AND password = MD5(:password) AND cod_rol = :rol AND estado = 'A'
             ORDER BY cod_usuario DESC",
            ['usuario' => $usuario, 'correo' => $usuario, 'password' => $password, 'rol' => ROL_MOTORIZADO]
        );
        return $fila ?: null;
    }

    public function get($cod_usuario) {
        $fila = Conexion::buscarRegistro(
            "SELECT " . self::CAMPOS . " FROM tb_usuarios WHERE cod_usuario = :cod_usuario AND cod_rol = :rol",
            ['cod_usuario' => $cod_usuario, 'rol' => ROL_MOTORIZADO]
        );
        return $fila ?: null;
    }

    /**
     * Cuentas de motorizado con ese correo, la más reciente primero. Puede haber más de una si el correo quedó repetido
     * en cuentas legacy: se usa la más reciente que esté activa.
     */
    public function getPorCorreo($correo) {
        return Conexion::buscarVariosRegistro(
            "SELECT " . self::CAMPOS . " FROM tb_usuarios WHERE correo = :correo AND cod_rol = :rol ORDER BY cod_usuario DESC",
            ['correo' => $correo, 'rol' => ROL_MOTORIZADO]
        ) ?: [];
    }

    /** Tiene flota: afiliado y activo en tb_motorizado_empresa, o con empresa propia (cuenta creada desde el dashboard). */
    public function estaEnFlota($usuario) {
        if (!empty($usuario['cod_empresa'])) return true;
        $fila = Conexion::buscarRegistro(
            "SELECT cod_motorizado_empresa FROM tb_motorizado_empresa WHERE cod_usuario = :cod_usuario AND estado = 'A'",
            ['cod_usuario' => $usuario['cod_usuario']]
        );
        return (bool) $fila;
    }

    /** Cuenta nueva a partir de Google, aún sin flota (se vincula con un código). */
    public function crearDesdeGoogle($correo, $nombre, $imagen) {
        $partes = explode(' ', trim((string) $nombre), 2);
        Conexion::ejecutar(
            "INSERT INTO tb_usuarios (cod_rol, nombre, apellido, correo, imagen, telefono, num_documento, latitud, longitud, placa, estado)
             VALUES (:rol, :nombre, :apellido, :correo, :imagen, '', '', '', '', '', 'A')",
            ['rol' => ROL_MOTORIZADO, 'nombre' => $partes[0], 'apellido' => $partes[1] ?? '', 'correo' => $correo, 'imagen' => $imagen]
        );
        return $this->get(Conexion::lastId());
    }

    /** Código vigente: no vencido y, si es de reactivación, que no se haya usado (un solo uso). */
    public function getCodigoVigente($codigo) {
        $fila = Conexion::buscarRegistro(
            "SELECT * FROM tb_flota_codigo
             WHERE codigo = :codigo AND fecha_expiracion > NOW() AND (tipo != 'REACTIVACION' OR fecha_uso IS NULL)",
            ['codigo' => $codigo]
        );
        return $fila ?: null;
    }

    public function marcarCodigoUsado($cod_codigo) {
        return Conexion::ejecutar("UPDATE tb_flota_codigo SET fecha_uso = :fecha WHERE cod_codigo = :cod_codigo", ['fecha' => fecha(), 'cod_codigo' => $cod_codigo]);
    }

    /** GENERAL: lo une a la flota del código como invitado. */
    public function vincularAFlota($cod_usuario, $cod_empresa) {
        $existente = Conexion::buscarRegistro(
            "SELECT cod_motorizado_empresa FROM tb_motorizado_empresa WHERE cod_usuario = :cod_usuario AND cod_empresa = :cod_empresa",
            ['cod_usuario' => $cod_usuario, 'cod_empresa' => $cod_empresa]
        );
        if ($existente) {
            return Conexion::ejecutar("UPDATE tb_motorizado_empresa SET estado = 'A' WHERE cod_motorizado_empresa = :id", ['id' => $existente['cod_motorizado_empresa']]);
        }
        return Conexion::ejecutar(
            "INSERT INTO tb_motorizado_empresa (cod_usuario, cod_empresa, estado, es_invitado, fecha_afiliacion) VALUES (:cod_usuario, :cod_empresa, 'A', 1, :fecha)",
            ['cod_usuario' => $cod_usuario, 'cod_empresa' => $cod_empresa, 'fecha' => fecha()]
        );
    }

    /**
     * REACTIVACION: el código apunta a una cuenta que ya existe; se le pone el correo de Google (y la foto solo si no tenía).
     * Si ya hay otra cuenta de motorizado con ese correo y no tiene historia, se borra; si la tiene, se avisa.
     */
    public function reactivarCuenta($cod_usuario_objetivo, $correo, $imagen) {
        $objetivo = $this->get($cod_usuario_objetivo);
        if (!$objetivo) return null;

        foreach ($this->getPorCorreo($correo) as $otra) {
            if ((int) $otra['cod_usuario'] === (int) $cod_usuario_objetivo) continue;
            $historia = Conexion::buscarRegistro(
                "SELECT (SELECT COUNT(*) FROM tb_motorizado_asignacion WHERE cod_motorizado = :a)
                      + (SELECT COUNT(*) FROM tb_motorizado_empresa WHERE cod_usuario = :b) AS total",
                ['a' => $otra['cod_usuario'], 'b' => $otra['cod_usuario']]
            );
            if ($historia && (int) $historia['total'] > 0) {
                throw new RidersException('Ya existe otra cuenta con ese correo. Habla con tu flota.');
            }
            Conexion::ejecutar("DELETE FROM tb_usuarios WHERE cod_usuario = :id AND cod_rol = :rol", ['id' => $otra['cod_usuario'], 'rol' => ROL_MOTORIZADO]);
        }

        $conImagen = empty($objetivo['imagen']) && !empty($imagen);
        $ok = Conexion::ejecutar(
            "UPDATE tb_usuarios SET correo = :correo" . ($conImagen ? ", imagen = :imagen" : "") . " WHERE cod_usuario = :id",
            ['correo' => $correo, 'id' => $cod_usuario_objetivo] + ($conImagen ? ['imagen' => $imagen] : [])
        );
        return $ok ? $this->get($cod_usuario_objetivo) : null;
    }

    /** Empresas/flotas a las que está afiliado, con logo para mostrar en la app. */
    public function getEmpresasAfiliadas($usuario) {
        $filas = Conexion::buscarVariosRegistro(
            "SELECT e.cod_empresa, e.nombre, e.alias, e.logo, e.logo_min
             FROM tb_motorizado_empresa me INNER JOIN tb_empresas e ON e.cod_empresa = me.cod_empresa
             WHERE me.cod_usuario = :cod_usuario AND me.estado = 'A'",
            ['cod_usuario' => $usuario['cod_usuario']]
        ) ?: [];

        // Cuenta creada desde el dashboard con empresa propia pero sin fila de afiliación
        if (!$filas && !empty($usuario['cod_empresa'])) {
            $filas = Conexion::buscarVariosRegistro(
                "SELECT cod_empresa, nombre, alias, logo, logo_min FROM tb_empresas WHERE cod_empresa = :cod_empresa",
                ['cod_empresa' => $usuario['cod_empresa']]
            ) ?: [];
        }

        return array_map(function ($e) {
            $logo = urlAssetEmpresa($e['alias'], $e['logo']);
            return ['cod_empresa' => (int) $e['cod_empresa'], 'nombre' => $e['nombre'], 'logo' => $logo, 'logo_min' => urlAssetEmpresa($e['alias'], $e['logo_min']) ?: $logo];
        }, $filas);
    }

    /** Alias de la empresa a la que pertenece el motorizado (de ahí sale su foto). */
    private function aliasDe($usuario, $empresas) {
        $cod = !empty($usuario['cod_empresa']) ? $usuario['cod_empresa'] : ($empresas[0]['cod_empresa'] ?? null);
        if (!$cod) return null;
        $fila = Conexion::buscarRegistro("SELECT alias FROM tb_empresas WHERE cod_empresa = :cod", ['cod' => $cod]);
        return $fila ? $fila['alias'] : null;
    }

    /**
     * Carreras que puede llevar a la vez (0 = sin límite): la suya si la tiene; si no, la de la flota que lleva la orden
     * ($cod_flota) o, sin ella, la de su empresa. Por defecto 1, como siempre en api_flotas.
     */
    public function limiteCarreras($usuario, $cod_flota = null) {
        if ($usuario['max_carreras'] !== null && $usuario['max_carreras'] !== '') return (int) $usuario['max_carreras'];
        $cod = $cod_flota ?: ($usuario['cod_empresa'] ?: null);
        if (!$cod) {
            $fila = Conexion::buscarRegistro("SELECT cod_empresa FROM tb_motorizado_empresa WHERE cod_usuario = :c AND estado = 'A' ORDER BY cod_motorizado_empresa LIMIT 1", ['c' => $usuario['cod_usuario']]);
            $cod = $fila ? $fila['cod_empresa'] : null;
        }
        if (!$cod) return 1;
        $fila = Conexion::buscarRegistro("SELECT max_carreras FROM tb_empresas WHERE cod_empresa = :cod", ['cod' => $cod]);
        return $fila ? (int) $fila['max_carreras'] : 1;
    }

    /** El perfil como lo lee la app (las llaves de siempre). */
    public function perfil($usuario, $tieneEntregaEnProgreso) {
        $empresas = $this->getEmpresasAfiliadas($usuario);
        return [
            'cod_usuario' => (int) $usuario['cod_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'] ?? '',
            'correo' => $usuario['correo'],
            'telefono' => $usuario['telefono'],
            'num_documento' => $usuario['num_documento'] ?: null,
            'imagen' => urlAssetEmpresa($this->aliasDe($usuario, $empresas), $usuario['imagen']),
            'placa' => $usuario['placa'],
            'is_active' => (int) $usuario['is_active'] === 1 ? 1 : 0,
            'tiene_pedido_en_progreso' => (bool) $tieneEntregaEnProgreso,
            'max_carreras' => $this->limiteCarreras($usuario),
            'empresas' => $empresas,
        ];
    }

    /** Actualiza la posición actual y agrega un punto al recorrido. */
    public function setUbicacion($cod_usuario, $lat, $lon) {
        // latitud/longitud son varchar(15): el GPS manda hasta 17 decimales y el insert fallaría con "Data too long"
        $lat = round((float) $lat, 7);
        $lon = round((float) $lon, 7);
        $fecha = fecha();
        $ok = Conexion::ejecutar(
            "UPDATE tb_usuarios SET latitud = :lat, longitud = :lon, fecha_ubicacion = :fecha WHERE cod_usuario = :cod_usuario",
            ['lat' => $lat, 'lon' => $lon, 'fecha' => $fecha, 'cod_usuario' => $cod_usuario]
        );
        if ($ok) {
            Conexion::ejecutar(
                "INSERT INTO tb_motorizado_seguimiento (cod_motorizado, latitud, longitud, fecha) VALUES (:cod_usuario, :lat, :lon, :fecha)",
                ['cod_usuario' => $cod_usuario, 'lat' => $lat, 'lon' => $lon, 'fecha' => $fecha]
            );
        }
        return $ok;
    }

    /** Última ubicación guardada si es reciente (así se decide "ya llegó" sin esperar un ping nuevo); si no, null. */
    public function ubicacionReciente($cod_usuario) {
        $fila = Conexion::buscarRegistro("SELECT latitud, longitud, fecha_ubicacion FROM tb_usuarios WHERE cod_usuario = :c", ['c' => $cod_usuario]);
        if (!$fila || !$fila['fecha_ubicacion'] || coordenada($fila['latitud']) === null || coordenada($fila['longitud']) === null) return null;
        if (strtotime($fila['fecha_ubicacion']) < time() - RIDERS_LOCATION_FRESH_MINUTES * 60) return null;
        return [coordenada($fila['latitud']), coordenada($fila['longitud'])];
    }

    public function setDisponibilidad($cod_usuario, $disponible) {
        $ok = Conexion::ejecutar("UPDATE tb_usuarios SET is_active = :a WHERE cod_usuario = :c", ['a' => $disponible ? 1 : 0, 'c' => $cod_usuario]);
        if ($ok) {
            if ($disponible) {
                $abierta = Conexion::buscarRegistro("SELECT cod_conexion FROM tb_motorizado_conexion WHERE cod_usuario = :c AND fecha_fin IS NULL", ['c' => $cod_usuario]);
                if (!$abierta) Conexion::ejecutar("INSERT INTO tb_motorizado_conexion (cod_usuario, fecha_inicio) VALUES (:c, :f)", ['c' => $cod_usuario, 'f' => fecha()]);
            } else {
                Conexion::ejecutar("UPDATE tb_motorizado_conexion SET fecha_fin = :f WHERE cod_usuario = :c AND fecha_fin IS NULL", ['c' => $cod_usuario, 'f' => fecha()]);
            }
        }
        return $ok;
    }

    /** Carreras entregadas hoy (la tarjeta "Pedidos hoy" del inicio). */
    public function pedidosHoy($cod_usuario) {
        $fila = Conexion::buscarRegistro(
            "SELECT COUNT(*) AS total FROM tb_motorizado_asignacion WHERE cod_motorizado = :c AND fecha_llegada >= :ini",
            ['c' => $cod_usuario, 'ini' => date('Y-m-d') . ' 00:00:00']
        );
        return (int) ($fila['total'] ?? 0);
    }

    /** Segundos conectado hoy: la sesión abierta cuenta hasta ahora y una que empezó ayer cuenta desde medianoche. */
    public function tiempoConectadoHoySegundos($cod_usuario) {
        $ini = date('Y-m-d') . ' 00:00:00';
        $filas = Conexion::buscarVariosRegistro(
            "SELECT fecha_inicio, fecha_fin FROM tb_motorizado_conexion WHERE cod_usuario = :c AND (fecha_fin IS NULL OR fecha_fin >= :ini)",
            ['c' => $cod_usuario, 'ini' => $ini]
        ) ?: [];
        $total = 0;
        foreach ($filas as $f) {
            $desde = max(strtotime($f['fecha_inicio']), strtotime($ini));
            $hasta = $f['fecha_fin'] ? strtotime($f['fecha_fin']) : time();
            $total += max(0, $hasta - $desde);
        }
        return $total;
    }
}
