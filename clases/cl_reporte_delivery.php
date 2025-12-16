<?php

class cl_reporte_delivery
{
		var $session;

		public function __construct()
		{
			$this->session = getSession();
		}

		
		public function total_delivery($cod_sucursal, $fecha_ini, $fecha_fin){
            $where = "WHERE o.cod_sucursal = {$cod_sucursal}";
            if($cod_sucursal == 0) {
                $sucursales = $this->getListaSucursales();
                $where = "WHERE o.cod_sucursal IN ({$sucursales})";
            }

		    $query = "SELECT u.cod_usuario, u.nombre, u.apellido, u.imagen, u.correo, u.telefono, SUM(o.envio) as total, COUNT(o.cod_orden) as num_items
                        FROM tb_orden_cabecera o
                        INNER JOIN tb_motorizado_asignacion m ON o.cod_orden = m.cod_orden
                        INNER JOIN tb_usuarios u ON m.cod_motorizado = u.cod_usuario
                        {$where}
                        AND o.is_envio = 1
                        -- AND o.estado = 'ENTREGADA'
                        AND o.cod_courier = 99
                        AND o.fecha >='$fecha_ini 00:00:00'
                        AND o.fecha <='$fecha_fin 23:59:00'
                        GROUP BY m.cod_motorizado";
            $row = Conexion::buscarVariosRegistro($query);
            return $row;
		}
		
		public function orders_motorizado($cod_motorizado, $cod_sucursal, $fecha_ini, $fecha_fin){
            $where = "WHERE o.cod_sucursal = {$cod_sucursal}";
            if($cod_sucursal == 0) {
                $sucursales = $this->getListaSucursales();
                $where = "WHERE o.cod_sucursal IN ({$sucursales})";
            }
		    $query = "SELECT o.cod_orden, o.fecha, o.envio, o.total, o.estado
                        FROM tb_orden_cabecera o
                        INNER JOIN tb_motorizado_asignacion m ON o.cod_orden = m.cod_orden
                        INNER JOIN tb_usuarios u ON m.cod_motorizado = u.cod_usuario
                        {$where}
                        AND o.is_envio = 1
                        -- AND o.estado = 'ENTREGADA'
                        AND o.cod_courier = 99
                        AND o.fecha >='$fecha_ini 00:00:00'
                        AND o.fecha <='$fecha_fin 23:59:00'
                        AND u.cod_usuario = $cod_motorizado";
            $row = Conexion::buscarVariosRegistro($query);
            return $row;
		}
		
		function getListaSucursales() {
            require_once "cl_sucursales.php";
            $Clsucursales = new cl_sucursales();
            $sucursales = $Clsucursales->lista();
            $_sucursales = [];
            foreach ($sucursales as $key => $sucursal) {
                $_sucursales[] = $sucursal["cod_sucursal"]; 
            }
            return implode(',', $_sucursales);
        } 

}
?>