<?php
class cl_app_versiones
{
		public $session;
		public $cod_empresa, $name, $code, $texto, $obligatorio, $aplicacion, $descripción;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        public function get($cod_empresa, $cod_empresa_version){
			$query = "SELECT * 
                        FROM  tb_empresas_versiones_app
                        WHERE cod_empresa = $cod_empresa
                        AND cod_empresa_version = $cod_empresa_version";
			return Conexion::buscarRegistro($query);
        }

        public function lista($cod_empresa){
			$query = "SELECT * 
                        FROM  tb_empresas_versiones_app
                        WHERE cod_empresa = $cod_empresa
                        ORDER BY fecha_modificacion DESC";
			return Conexion::buscarVariosRegistro($query);
        }

        public function crear($cod_empresa){
            $fecha = fecha();
            $query = "INSERT INTO tb_empresas_versiones_app 
                        SET cod_empresa = $cod_empresa, name = '$this->name', code = $this->code, texto = '$this->texto',
                        obligatorio = $this->obligatorio, aplicacion = '$this->aplicacion', 
                        descripcion = '$this->descripcion', fecha_modificacion = '$fecha'";
            //echo $query;
            return Conexion::ejecutar($query, null);
        }

        public function eliminar($cod_empresa_version){
            $query = "DELETE 
                        FROM tb_empresas_versiones_app 
                        WHERE cod_empresa_version = $cod_empresa_version";
            return Conexion::ejecutar($query, null);
        }
}
?>