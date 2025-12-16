<?php
class cl_couriers
{
        var $session;
        
        public function __construct($pcouriers=null)
        {
            if($pcouriers != null)
                $this->cod= $pcouriers;
                $this->session = getSession();
        }

        public function sucursalGacela($cod_sucursal){
            $query = "SELECT s.nombre, gs.* 
                        FROM tb_gacela_sucursal gs, tb_sucursales s
                        WHERE gs.cod_sucursal = s.cod_sucursal
                        AND gs.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }

        public function sucursalLaar($cod_sucursal){
            $query = "SELECT s.nombre, ls.* 
                        FROM tb_laar_sucursal ls, tb_sucursales s
                        WHERE ls.cod_sucursal = s.cod_sucursal
                        AND ls.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }

        public function sucursalPicker($cod_sucursal){
            $query = "SELECT s.nombre, ps.* 
                        FROM tb_picker_sucursal ps, tb_sucursales s
                        WHERE ps.cod_sucursal = s.cod_sucursal
                        AND ps.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }

        public function getLista() {
            $query = "SELECT * 
                        FROM tb_courier
                        WHERE estado = 'A'";
            return Conexion::buscarVariosRegistro($query);
        }
}
?>