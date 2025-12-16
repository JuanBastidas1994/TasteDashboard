<?php
class cl_cards
{
		public $session;
		public $cod_cliente, $codigo, $estado;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        public function get($cod_empresa, $codigo){
			$query = "SELECT * 
                        FROM  tb_card_fidelizacion
                        WHERE cod_empresa = $cod_empresa
                        AND codigo = '$codigo'";
			return Conexion::buscarRegistro($query);
        }

        public function lista($cod_empresa){
			$query = "SELECT * 
                        FROM  tb_card_fidelizacion
                        WHERE cod_empresa = $cod_empresa";
			return Conexion::buscarVariosRegistro($query);
        }

        public function crear($cod_empresa){
            $fecha = fecha();
            $query = "INSERT INTO tb_card_fidelizacion 
                        SET cod_empresa = $cod_empresa, cod_cliente = '$this->cod_cliente', codigo = '$this->codigo', estado = '$this->estado'";
            //echo $query;
            return Conexion::ejecutar($query, null);
        }

        public function eliminar($cod_empresa_version){
            $query = "DELETE 
                        FROM tb_empresas_versiones_app 
                        WHERE cod_empresa_version = $cod_empresa_version";
            return Conexion::ejecutar($query, null);
        }

        public function vaciarTarjeta($cod_tarjeta){
            $query = "UPDATE tb_card_fidelizacion
                        SET cod_cliente = 0, estado = 'I'
                        WHERE cod_card_fidelizacion = $cod_tarjeta";
            return Conexion::ejecutar($query, null);
        }
}
?>