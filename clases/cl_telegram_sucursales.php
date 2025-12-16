<?php

class cl_telegram_sucursales
{
        var $session;
		var $cod_empresa;

		public function __construct()
		{
            $this->session = getSession();
            $this->cod_empresa = $this->session['cod_empresa'];
		}
		
		public function getConfig($cod_sucursal){
            $query = "SELECT * 
                        FROM tb_telegram_sucursal ts
                        WHERE ts.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }

        public function create($cod_sucursal, &$hash){
            do {
                $hash = $cod_sucursal."-".fechaSignos();
                $hash = hash("crc32b", $hash);
                $query = "SELECT * 
                        FROM tb_telegram_sucursal
                        WHERE code = '$hash'";
                $resp = Conexion::buscarRegistro($query);
            } while ($resp);
            
            $query = "INSERT INTO tb_telegram_sucursal
                        SET cod_sucursal = $cod_sucursal, code = '$hash', estado = 'PENDIENTE'";
            return Conexion::ejecutar($query, null);
        }
}
?>