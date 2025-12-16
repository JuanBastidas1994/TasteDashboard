<?php

class cl_web_esquema
{
	var $session;
	var $titulo, $forma, $tipo, $detalle, $iddetalle, $plataforma, $numColumnas;
	
	public function __construct()
	{
		$this->session = getSession();
	}
	
	public function lista($idEmpresa, $plataforma){
		$query = "SELECT * 
					FROM  tb_web_esquema 
					WHERE cod_empresa = $idEmpresa
					AND plataforma = '$plataforma'
					ORDER BY posicion ASC";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}

    public function get($id){
        $query = "SELECT * FROM tb_web_esquema WHERE cod_web_esquema = $id";
        return Conexion::buscarRegistro($query);
    }

	public function crear(&$id,$idEmpresa){
		/*$usuario = $this->session['cod_usuario'];*/
		//$empresa = $this->session['cod_empresa'];
		$empresa =$idEmpresa;
		$query = "INSERT INTO tb_web_esquema(cod_empresa, titulo, forma, tipo, detalle, cod_detalle, plataforma, num_columnas) ";
    	$query.= "VALUES($empresa, '$this->titulo', '$this->forma','$this->tipo', '$this->detalle', '$this->iddetalle', '$this->plataforma', '$this->numColumnas')";
    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
    	//return $query;
	}

    public function update($id, $titulo, $forma, $columna){
        $query = "UPDATE tb_web_esquema SET titulo='$titulo', forma='$forma', num_columnas='$columna' WHERE cod_web_esquema = $id";
        return Conexion::ejecutar($query,NULL);
    }
	
	public function eliminar($cod_esquema){
	    $query = "DELETE FROM tb_web_esquema WHERE cod_web_esquema = $cod_esquema";
	    return Conexion::ejecutar($query,NULL);
	}
	
	public function actPosicion($cod_esquema, $posicion){
	    $query = "UPDATE tb_web_esquema SET posicion = $posicion WHERE cod_web_esquema = $cod_esquema";
	    return Conexion::ejecutar($query,NULL);
	}
}	

?>