<?php
class cl_promociones_nueva
{
    // 1 = Lunes
    // 2 = Martes
    // 3 = Miércoles
    // 4 = Jueves
    // 5 = Viernes
    // 6 = Sábado
    // 7 = Domingo
    public $session;
    public $cod_promocion, $descripcion, $is_porcentaje, $cantidad, $valor, $texto, $fecha_inicio, $fecha_fin, $is_recurrente, $estado;
    public $cod_sucursal, $cod_producto, $cod_empresa, $imagen;

    public function __construct()
    {
        $this->session = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    // Método para obtener la lista de promociones activas
    public function lista()
    {
        $query = "SELECT 
            p.cod_promocion, 
            p.descripcion, 
            p.is_porcentaje,
            p.valor, 
            p.texto, 
            p.fecha_inicio, 
            p.fecha_fin, 
            p.estado, 
            GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre ASC) AS sucursales
        FROM 
            promociones p
            JOIN promocion_sucursal ps ON p.cod_promocion = ps.cod_promocion
            JOIN tb_sucursales s ON ps.cod_sucursal = s.cod_sucursal
        WHERE 
            p.fecha_fin >= CURDATE() 
            AND p.cod_empresa = :cod_empresa
        GROUP BY 
            p.cod_promocion, p.descripcion, p.valor, p.texto, p.fecha_inicio, p.fecha_fin";
        
        $params = array(':cod_empresa' => $this->cod_empresa);
        $resp = Conexion::buscarVariosRegistro($query, $params); // Asegúrate de que el método 'buscarVariosRegistro' soporte parámetros
        return $resp;
    }

    public function obtener($cod_promocion)
    {
        // 1. Datos principales de la promoción
        $query = "SELECT
                    cod_promocion,
                    descripcion,
                    is_porcentaje,
                    valor,
                    texto,
                    fecha_inicio,
                    fecha_fin,
                    is_recurrente,
                    estado,
                    imagen
                FROM promociones
                WHERE cod_promocion = :cod_promocion
                    AND cod_empresa = :cod_empresa
                LIMIT 1";

        $params = [
            ':cod_promocion' => $cod_promocion,
            ':cod_empresa'   => $this->cod_empresa
        ];

        $promocion = Conexion::buscarRegistro($query, $params);

        if (!$promocion) {
            return false; // No existe
        }

        // 2. Sucursales asociadas
        $query = "SELECT cod_sucursal
                FROM promocion_sucursal
                WHERE cod_promocion = :cod";

        $sucursales = Conexion::buscarVariosRegistro($query, [
            ':cod' => $cod_promocion
        ]);

        $promocion['sucursales'] = array_column($sucursales, 'cod_sucursal');

        // 3. Productos asociados
        $query = "SELECT cod_producto
                FROM promocion_producto
                WHERE cod_promocion = :cod";

        $productos = Conexion::buscarVariosRegistro($query, [
            ':cod' => $cod_promocion
        ]);

        $promocion['productos'] = array_column($productos, 'cod_producto');

        // 4. Recurrencia (si aplica)
        $promocion['recurrencia'] = [];

        
        $query = "SELECT dia_semana, hora_inicio, hora_fin
                FROM promocion_recurrente
                WHERE cod_promocion = :cod";
        $recurrencia = Conexion::buscarVariosRegistro($query, [
            ':cod' => $cod_promocion
        ]);

        foreach ($recurrencia as $r) {
            $promocion['recurrencia'][$r['dia_semana']] = [
                'inicio' => $r['hora_inicio'],
                'fin'    => $r['hora_fin']
            ];
        }

        //5. Tipos de entrega (si aplica)
        $query = "SELECT tipo_entrega
            FROM promocion_tipo_entrega
            WHERE cod_promocion = :cod";

        $tipos_entrega = Conexion::buscarVariosRegistro($query, [
            ':cod' => $cod_promocion
        ]);

        $promocion['tipo_entrega'] = array_column($tipos_entrega, 'tipo_entrega');

        // 6. Producto regalo (si aplica)
        $query = "SELECT cod_producto_regalo
                FROM promocion_recompensa
                WHERE cod_promocion = :cod
                LIMIT 1";

        $recompensa = Conexion::buscarRegistro($query, [':cod' => $cod_promocion]);
        $promocion['producto_regalo'] = $recompensa ? $recompensa['cod_producto_regalo'] : null;

        return $promocion;
    }


    // Método para crear una nueva promoción
    public function crear(&$id)
    {
        $query = "INSERT INTO promociones (cod_empresa, descripcion, is_porcentaje, cantidad, valor, texto, fecha_inicio, fecha_fin, is_recurrente, estado, imagen)
                  VALUES (:cod_empresa, :descripcion, :is_porcentaje, :cantidad, :valor, :texto, :fecha_inicio, :fecha_fin, :is_recurrente, :estado, :imagen)";

        $params = array(
            ':cod_empresa' => $this->cod_empresa,
            ':descripcion' => $this->descripcion,
            ':is_porcentaje' => $this->is_porcentaje,
            ':cantidad' => $this->cantidad,
            ':valor' => $this->valor,
            ':texto' => $this->texto,
            ':fecha_inicio' => $this->fecha_inicio,
            ':fecha_fin' => $this->fecha_fin,
            ':is_recurrente' => $this->is_recurrente,
            ':estado' => $this->estado,
            ':imagen' => $this->imagen ?? '',
        );

        if (Conexion::ejecutar($query, $params)) {
            $id = Conexion::lastId();
            return true;
        } else {
            return false;
        }
    }

    // Método para editar una promoción existente
    public function editar()
    {
        $query = "UPDATE promociones
                  SET descripcion = :descripcion, is_porcentaje = :is_porcentaje, cantidad = :cantidad, valor = :valor, texto = :texto,
                      fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, is_recurrente = :is_recurrente, estado = :estado, imagen = :imagen
                  WHERE cod_promocion = :cod_promocion";

        $params = array(
            ':descripcion' => $this->descripcion,
            ':is_porcentaje' => $this->is_porcentaje,
            ':cantidad' => $this->cantidad,
            ':valor' => $this->valor,
            ':texto' => $this->texto,
            ':fecha_inicio' => $this->fecha_inicio,
            ':fecha_fin' => $this->fecha_fin,
            ':is_recurrente' => $this->is_recurrente,
            ':estado' => $this->estado,
            ':imagen' => $this->imagen ?? '',
            ':cod_promocion' => $this->cod_promocion
        );

        if (Conexion::ejecutar($query, $params)) {
            return true;
        } else {
            return false;
        }
    }

    // Método para asociar sucursales con una promoción
    public function asociar_sucursales($cod_promocion, $sucursales)
    {
        $query = "DELETE FROM promocion_sucursal WHERE cod_promocion = :cod_promocion"; // Elimina las asociaciones anteriores

        $params = array(':cod_promocion' => $cod_promocion);
        Conexion::ejecutar($query, $params);

        // Insertar nuevas asociaciones
        foreach ($sucursales as $cod_sucursal) {
            $query = "INSERT INTO promocion_sucursal (cod_promocion, cod_sucursal) VALUES (:cod_promocion, :cod_sucursal)";
            $params = array(
                ':cod_promocion' => $cod_promocion,
                ':cod_sucursal' => $cod_sucursal
            );
            Conexion::ejecutar($query, $params);
        }

        return true;
    }

    // Método para asociar productos con una promoción
    public function asociar_productos($cod_promocion, $productos)
    {
        $query = "DELETE FROM promocion_producto WHERE cod_promocion = :cod_promocion"; // Elimina las asociaciones anteriores

        $params = array(':cod_promocion' => $cod_promocion);
        Conexion::ejecutar($query, $params);

        // Insertar nuevas asociaciones
        foreach ($productos as $cod_producto) {
            $query = "INSERT INTO promocion_producto (cod_promocion, cod_producto) VALUES (:cod_promocion, :cod_producto)";
            $params = array(
                ':cod_promocion' => $cod_promocion,
                ':cod_producto' => $cod_producto
            );
            Conexion::ejecutar($query, $params);
        }

        return true;
    }


    // Asociar recurrencia por día con horario propio
    public function asociar_recurrencia($cod_promocion, $recurrencia)
    {
        // Borrar reglas anteriores
        Conexion::ejecutar(
            "DELETE FROM promocion_recurrente WHERE cod_promocion = :cod",
            [':cod' => $cod_promocion]
        );

        // Si no hay recurrencia → aplica siempre
        if (empty($recurrencia)) {
            return true;
        }

        foreach ($recurrencia as $dia => $horario) {

            $hora_inicio = $horario['inicio'] ?? '00:00:00';
            $hora_fin    = $horario['fin']    ?? '23:59:59';

            if (strlen($hora_inicio) === 5) $hora_inicio .= ':00';
            if (strlen($hora_fin) === 5)    $hora_fin    .= ':00';

            if ($hora_fin === '00:00:00') {
                $hora_fin = '23:59:59';
            }else if ($hora_fin <= $hora_inicio) {
                $hora_fin = '23:59:59';
            }

            $query = "INSERT INTO promocion_recurrente
                    (cod_promocion, dia_semana, hora_inicio, hora_fin)
                    VALUES (:cod, :dia, :inicio, :fin)";

            $params = [
                ':cod'    => $cod_promocion,
                ':dia'    => $dia,
                ':inicio' => $hora_inicio,
                ':fin'    => $hora_fin
            ];

            Conexion::ejecutar($query, $params);
        }

        return true;
    }

    // Asociar tipos de entrega con una promoción
    public function asociar_tipo_entrega($cod_promocion, $tipos_entrega)
    {
        // Borrar asociaciones anteriores
        Conexion::ejecutar(
            "DELETE FROM promocion_tipo_entrega WHERE cod_promocion = :cod",
            [':cod' => $cod_promocion]
        );

        // Si no hay tipos definidos → aplica a todos
        if (empty($tipos_entrega)) {
            return true;
        }

        // Insertar nuevas asociaciones
        foreach ($tipos_entrega as $tipo) {

            $query = "INSERT INTO promocion_tipo_entrega
                    (cod_promocion, tipo_entrega)
                    VALUES (:cod, :tipo)";

            $params = [
                ':cod'  => $cod_promocion,
                ':tipo' => $tipo
            ];

            Conexion::ejecutar($query, $params);
        }

        return true;
    }

    //Asociar las condiciones - Advances
    public function asociar_condiciones($cod_promocion, $condiciones)
    {
        // 1. Eliminar condiciones anteriores
        Conexion::ejecutar(
            "DELETE FROM promocion_condicion WHERE cod_promocion = :cod",
            [':cod' => $cod_promocion]
        );

        // 2. Si no hay condiciones, salir
        if (empty($condiciones)) {
            return true;
        }

        // 3. Insertar nuevas condiciones
        foreach ($condiciones as $condicion) {

            $tipo = $condicion['tipo_condicion'] ?? null;

            if (!$tipo) continue;

            $cod_producto = $condicion['cod_producto'] ?? null;
            $cantidad     = $condicion['cantidad_necesaria'] ?? null;
            $monto        = $condicion['monto_minimo'] ?? null;

            $query = "INSERT INTO promocion_condicion
                        (cod_promocion, tipo_condicion, cod_producto, cantidad_necesaria, monto_minimo)
                    VALUES
                        (:cod_promocion, :tipo_condicion, :cod_producto, :cantidad, :monto)";

            $params = [
                ':cod_promocion'   => $cod_promocion,
                ':tipo_condicion'  => $tipo,
                ':cod_producto'    => $cod_producto,
                ':cantidad'        => $cantidad,
                ':monto'           => $monto
            ];

            Conexion::ejecutar($query, $params);
        }

        return true;
    }

    //Asociar las recompensas - Advances
    public function asociar_recompensas($cod_promocion, $recompensas)
    {
        // 1. Eliminar recompensas anteriores
        Conexion::ejecutar(
            "DELETE FROM promocion_recompensa WHERE cod_promocion = :cod",
            [':cod' => $cod_promocion]
        );

        // 2. Si no hay recompensas, salir
        if (empty($recompensas)) {
            return true;
        }

        // 3. Insertar nuevas recompensas
        foreach ($recompensas as $recompensa) {

            $cod_producto = $recompensa['cod_producto_regalo'] ?? null;
            $cantidad     = $recompensa['cantidad_regalo'] ?? 1;

            if (!$cod_producto) continue;

            $query = "INSERT INTO promocion_recompensa
                        (cod_promocion, cod_producto_regalo, cantidad_regalo)
                    VALUES
                        (:cod_promocion, :cod_producto, :cantidad)";

            $params = [
                ':cod_promocion' => $cod_promocion,
                ':cod_producto'  => $cod_producto,
                ':cantidad'      => $cantidad
            ];

            Conexion::ejecutar($query, $params);
        }

        return true;
    }



    //Eliminar las recurrencias
    public function eliminar_recurrencia($cod_promocion){
        // Borrar reglas anteriores
        Conexion::ejecutar(
            "DELETE FROM promocion_recurrente WHERE cod_promocion = :cod",
            [':cod' => $cod_promocion]
        );
    }

    //Eliminar de manera segura una promocion
    public function eliminar($cod_promocion)
    {
        try {
            Conexion::beginTransaction();

            // 1. Validar que la promoción exista y pertenezca a la empresa
            $existe = Conexion::buscarRegistro(
                "SELECT cod_promocion 
                FROM promociones 
                WHERE cod_promocion = :cod 
                AND cod_empresa = :empresa
                LIMIT 1",
                [
                    ':cod'     => $cod_promocion,
                    ':empresa' => $this->cod_empresa
                ]
            );

            if (!$existe) {
                throw new Exception('La promoción no existe o no pertenece a la empresa');
            }

            // 2. Eliminar relaciones
            Conexion::ejecutar(
                "DELETE FROM promocion_producto WHERE cod_promocion = :cod",
                [':cod' => $cod_promocion]
            );

            Conexion::ejecutar(
                "DELETE FROM promocion_sucursal WHERE cod_promocion = :cod",
                [':cod' => $cod_promocion]
            );

            Conexion::ejecutar(
                "DELETE FROM promocion_recurrente WHERE cod_promocion = :cod",
                [':cod' => $cod_promocion]
            );

            Conexion::ejecutar(
                "DELETE FROM promocion_tipo_entrega WHERE cod_promocion = :cod",
                [':cod' => $cod_promocion]
            );

            // 3. Eliminar promoción
            Conexion::ejecutar(
                "DELETE FROM promociones 
                WHERE cod_promocion = :cod 
                AND cod_empresa = :empresa",
                [
                    ':cod'     => $cod_promocion,
                    ':empresa' => $this->cod_empresa
                ]
            );

            Conexion::commit();
            return true;

        } catch (Exception $e) {
            Conexion::rollback();
            return false;
        }
    }

}
?>