<?php
class cl_promociones_nueva
{
    public $session;
    public $cod_promocion, $descripcion, $tipo_descuento, $valor, $texto, $fecha_inicio, $fecha_fin, $is_recurrente, $frecuencia, $horario_inicio, $horario_fin;
    public $cod_sucursal, $cod_producto;

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
            p.valor, 
            p.texto, 
            p.fecha_inicio, 
            p.fecha_fin, 
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

    // Método para crear una nueva promoción
    public function crear(&$id)
    {
        $query = "INSERT INTO promociones (descripcion, tipo_descuento, valor, texto, fecha_inicio, fecha_fin, is_recurrente, frecuencia, horario_inicio, horario_fin) 
                  VALUES (:descripcion, :tipo_descuento, :valor, :texto, :fecha_inicio, :fecha_fin, :is_recurrente, :frecuencia, :horario_inicio, :horario_fin)";

        $params = array(
            ':descripcion' => $this->descripcion,
            ':tipo_descuento' => $this->tipo_descuento,
            ':valor' => $this->valor,
            ':texto' => $this->texto,
            ':fecha_inicio' => $this->fecha_inicio,
            ':fecha_fin' => $this->fecha_fin,
            ':is_recurrente' => $this->is_recurrente,
            ':frecuencia' => $this->frecuencia,
            ':horario_inicio' => $this->horario_inicio,
            ':horario_fin' => $this->horario_fin
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
                  SET descripcion = :descripcion, tipo_descuento = :tipo_descuento, valor = :valor, texto = :texto, 
                      fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, is_recurrente = :is_recurrente, 
                      frecuencia = :frecuencia, horario_inicio = :horario_inicio, horario_fin = :horario_fin
                  WHERE cod_promocion = :cod_promocion";

        $params = array(
            ':descripcion' => $this->descripcion,
            ':tipo_descuento' => $this->tipo_descuento,
            ':valor' => $this->valor,
            ':texto' => $this->texto,
            ':fecha_inicio' => $this->fecha_inicio,
            ':fecha_fin' => $this->fecha_fin,
            ':is_recurrente' => $this->is_recurrente,
            ':frecuencia' => $this->frecuencia,
            ':horario_inicio' => $this->horario_inicio,
            ':horario_fin' => $this->horario_fin,
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
}
?>