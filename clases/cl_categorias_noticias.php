<?php
class cl_categorias_noticias
{
		public $session;

		/*public $cod_sucursal, $cod_empresa, $nombre, $direccion, $latitud, $longitud, $hora_ini, $hora_fin, $intervalo, $emisor, $telefono, $correo, $estado, $distancia_km;*/
		
		public function __construct($pcategorias_noticias=null)
		{
			if($pcategorias_noticias != null)
				$this->pcategorias_noticias = $pcategorias_noticias;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function lista(){
			$query = "SELECT * FROM tb_categorias_noticias WHERE estado ='A' AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listabyempresa($cod_empresa){
			$query = "SELECT * FROM tb_categorias_noticias WHERE estado ='A' AND cod_empresa = ".$cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "INSERT INTO tb_categorias_noticias(cod_empresa, nombre, cod_categoria_padre, estado) ";
        	$query.= "VALUES($this->cod_empresa, '$this->nombre', '$this->cod_categoria_padre', '$this->estado')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_categorias_noticias SET nombre='$this->nombre', cod_categoria_padre='$this->cod_categoria_padre' WHERE cod_categorias_noticias = $this->cod_categoria_noticia";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		public function getArray($cod_categoria, &$array)
		{
			$query = "SELECT * from tb_categorias_noticias where cod_categorias_noticias = ".$cod_categoria;
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

		public function set_estado($cod_categoria_noticia, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE  tb_categorias_noticias SET estado='$estado' WHERE cod_categorias_noticias = $cod_categoria_noticia";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		

}
?>