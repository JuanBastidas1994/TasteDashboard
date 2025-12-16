<?php

class cl_frontscript
{
		var $session = null;
		var $cod_empresa = 0;
		
		public function __construct()
		{
			if(infoLogin()){
				$this->session = getSession();
				$this->cod_empresa = $this->session['cod_empresa'];	
			}
		}
		
		public function lista(){
			$query = "SELECT * FROM  tb_front_scripts WHERE estado IN ('A','I') AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function crear($nombre, $posicion, $codigo){
		    $cod_empresa = $this->cod_empresa;
			$query = "INSERT INTO tb_front_scripts(cod_empresa, nombre, ubicacion, codigo, estado)
              VALUES(:cod_empresa, :nombre, :posicion, :codigo, 'A')";
            $params = [
                ':cod_empresa' => $cod_empresa,
                ':nombre' => $nombre,
                ':posicion' => $posicion,
                ':codigo' => htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8')
            ];
            $resp = Conexion::ejecutar($query, $params);
            return $resp;
		}
		
		public function editar($id, $nombre, $posicion, $codigo){
            $cod_empresa = $this->cod_empresa;
            
            $query = "UPDATE tb_front_scripts SET nombre=:nombre, ubicacion=:posicion, codigo=:codigo WHERE id = :id";
            $params = [
                ':nombre' => $nombre,
                ':posicion' => $posicion,
                ':codigo' => htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'),
                ':id' => $id
            ];
            $resp = Conexion::ejecutar($query, $params);
            return $resp;
        }
        
        public function eliminar($id){
            $query = "UPDATE tb_front_scripts SET estado='D' WHERE id = $id";
            echo $query;
            return Conexion::ejecutar($query, $params);
        }
		
		
}