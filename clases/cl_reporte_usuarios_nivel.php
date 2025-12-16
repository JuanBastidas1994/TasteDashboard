<?php

class cl_reporte_usuarios_nivel
{
		var $session;
		var $cod_nivel;
		var $cod_reporte;
        
		public function __construct($pcod_reporte=null)
		{
			if($pcod_reporte != null)
				$this->cod_reporte= $pcod_reporte;
			$this->session = getSession();
		}

		public function getNivelesByEmpresa($cod_empresa){
            $query = "SELECT * 
                        FROM tb_niveles 
                        WHERE cod_empresa = $cod_empresa
                        ORDER BY posicion ASC";
            return Conexion::buscarVariosRegistro($query);
        }
		
        public function getClientesByNivel($cod_empresa, $cod_nivel){
            $filtro = "";
            if($cod_nivel <> "")
                $filtro = "AND c.cod_nivel = $cod_nivel";
            $query = "SELECT c.*, n.nombre as nom_nivel
                        FROM tb_clientes c, tb_niveles n
                        WHERE c.cod_nivel = n.posicion
                        AND c.cod_empresa = $cod_empresa
                        AND n.cod_empresa = $cod_empresa
                        AND c.estado = 'A'
                        $filtro
                        ORDER BY n.posicion ASC";
            //echo $query;
            return Conexion::buscarVariosRegistro($query);
        }

        public function cantidadByNivel($cod_empresa){
            $query = "SELECT COUNT(*) as cantidad, n.nombre 
                        FROM tb_clientes c, tb_niveles n 
                        WHERE c.cod_nivel = n.posicion 
                        AND c.cod_empresa = $cod_empresa 
                        AND n.cod_empresa = $cod_empresa 
                        AND c.estado = 'A' 
                        AND c.nombre <> ''
                        GROUP BY n.cod_nivel";
            return Conexion::buscarVariosRegistro($query);
        }
}
?>