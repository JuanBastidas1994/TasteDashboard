<?php
class cl_noticias
{
		var $session;
		var $cod_noticia,$cod_empresa,$alias,$nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado; 
		/*$cod_categoria_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado;*/
		
		public function __construct($pcod_noticia=null)
		{
			if($pcod_noticia != null)
				$this->cod_noticia= $pcod_noticia;
			    $this->session = getSession();
		}

		public function lista(){
			$query = "SELECT * FROM tb_noticias WHERE estado IN('A','I') AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function GetProductos($cod_categoria)
		{
			$query = "select * from tb_productos where cod_producto = ".$cod_producto;
			$row = Conexion::buscarRegistro($query);
			if(count($row)>0)
			{
				$this->cod_empresa = $row['cod_empresa'];
				$this->cod_rol = $row['cod_rol'];
				$this->nombre = $row['nombre'];
				$this->apellido = $row['apellido'];
				$this->imagen = $row['imagen'];
				$this->correo = $row['correo'];
				$this->usuario = $row['usuario'];
				$this->password= $row['password'];
				$this->estado = $row['estado'];
				return true;
			}
			else
			{
				return false;
			}
		}

		public function aliasDisponible($alias){
			$empresa = $this->session['cod_empresa'];
			$query = "SELECT * FROM tb_noticias WHERE alias = '$alias' AND estado IN ('A','I') AND cod_empresa = $empresa";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}

		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_noticias(cod_empresa,titulo, desc_corta, desc_larga, image_min, imagen_max, fecha_create, estado,alias) ";
        	$query.= "VALUES($empresa, '$this->nombre', '$this->desc_corta', '$this->desc_larga', '$this->image_min', '$this->image_max', NOW(), 'A','$this->alias')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		$this->set_categorias($id);
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
        	$query= "UPDATE tb_noticias SET titulo='$this->nombre', desc_corta='$this->desc_corta', desc_larga='$this->desc_larga', estado='$this->estado',fecha_modificacion=NOW() WHERE cod_noticia = $this->cod_noticia";
        	//echo $query;
        	if(Conexion::ejecutar($query,NULL)){
        		$this->set_categorias($this->cod_noticia);
        		return true;
        	}else{
        		return false;
        	}
		}

		public function setImage($name, $scale, $cod_noticia){
		    $option = "imagen_max='$name'";
		    if($scale=="min")
		        $option = "image_min='$name'";
		    $query = "UPDATE tb_noticias SET $option WHERE cod_noticia = $cod_noticia";
		    return Conexion::ejecutar($query,NULL);
		}

		public function set_categorias($cod_noticia){
			$query = "DELETE FROM tb_noticias_categoria WHERE cod_noticia = $cod_noticia";
			Conexion::ejecutar($query,NULL);

			$categorias = $this->categorias;
			foreach ($categorias as $cat) {
				$query = "INSERT INTO tb_noticias_categoria(cod_noticia, cod_categoria) VALUES($cod_noticia, $cat)";
				if(!Conexion::ejecutar($query,NULL)){
	        		return false;
	        	}
			}
		}

		public function get_categorias($cod_noticia = null){
			if($cod_noticia == null){
				$cod_noticia = $this->cod_noticia;
			}
			$return = null;
			$query = "SELECT cod_categoria FROM tb_noticias_categoria WHERE cod_noticia = $cod_noticia";
            $resp = Conexion::buscarVariosRegistro($query);
            foreach ($resp as $data) {
            	$return[] = $data['cod_categoria'];
            }
            return $return;
		}

		public function set_estado($cod_noticia, $estado){
			$query = "UPDATE tb_noticias SET estado='$estado' WHERE cod_noticia = $cod_noticia";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getArray($cod_noticia, &$array)
		{
			$query = "SELECT * FROM  tb_noticias where cod_noticia = $cod_noticia AND estado IN('A','I')";
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

		public function getArrayByAlias($alias, &$array)
		{
			$empresa = $this->session['cod_empresa'];
			$query = "SELECT * FROM tb_noticias WHERE alias = '$alias' AND cod_empresa = $empresa AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			//echo $query;
			if($row){
				$array = $row;
				return true;
			}
			else
			{
				return false;
			}
		}

		public function lista_imagenes($cod_noticia){
			$query = "SELECT * FROM tb_noticias_imagenes WHERE cod_noticia = $cod_noticia";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function add_img_noticia($cod_noticia,$nombre,&$id){
			$query = "INSERT INTO tb_noticias_imagenes(cod_noticia, nombre_img, posicion) ";
        	$query.= "VALUES($cod_noticia, '$nombre', 1)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function delete_imagen($cod_imagen){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;

			$query = "SELECT * FROM tb_noticias_imagenes WHERE cod_imagen = $cod_imagen";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$query = "DELETE FROM tb_noticias_imagenes WHERE cod_imagen = $cod_imagen";
	        	if(Conexion::ejecutar($query,NULL)){
					$files = url_upload.'/assets/empresas/'.$this->session['alias'].'/';
	        		$urlImg = $files.$row['nombre_img'];
	        		unlink($urlImg);
	        		return true;
	        	}else{
	        		return false;
	        	}
			}else
				return false;
		}

}
?>