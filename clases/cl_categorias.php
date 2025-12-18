<?php
class cl_categorias
{
		var $session;
		var $cod_categoria, $cod_categoria_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado;
		
		public function __construct($pcod_categoria=null)
		{
			if($pcod_categoria != null)
				$this->cod_categoria = $pcod_categoria;
			$this->session = getSession();
		}

		public function listaNueva($cod_empresa, $end = 300){
			$query = "SELECT cod_categoria, alias, categoria, image_min, image_max, estado, desc_corta, fecha_modificacion
					FROM tb_categorias WHERE estado IN ('A','I') AND cod_categoria_padre = 0 AND cod_empresa = ".$cod_empresa." LIMIT 0,$end";
            $resp = Conexion::buscarVariosRegistro($query);
            foreach ($resp as $key => $categoria) {
				$resp[$key]['subcategorias'] = $this->subcategorias($categoria['cod_categoria']); 
            }
            return $resp;
		}

		public function subcategorias($cod_categoria_padre, $end = 100){
			$query = "SELECT c.cod_categoria, c.alias, c.categoria, c.image_min, c.image_max, c.estado 
			FROM tb_categorias_dependientes cd, tb_categorias c 
			WHERE cd.cod_categoria = c.cod_categoria
			AND cd.cod_categoria_padre = $cod_categoria_padre
			AND c.estado = 'A'
			LIMIT 0,$end";
		    $resp = Conexion::buscarVariosRegistro($query);
			foreach ($resp as $key => $categoria) {
				$resp[$key]['subcategorias'] = $this->subcategorias($categoria['cod_categoria']); 
            }
            return $resp;
		}

		public function lista(){
			$query = "SELECT * FROM tb_categorias WHERE estado IN('A','I') AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaPadre(){
			$query = "SELECT * FROM tb_categorias
                    WHERE cod_categoria_padre=0
                    AND estado='A'
                    AND cod_empresa=".$this->session['cod_empresa']."
                    ORDER BY posicion";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaHijos($cod_padre){
			$query = "SELECT * FROM `tb_categorias` 
                    WHERE cod_categoria_padre=$cod_padre
                    AND estado='A'
                    AND cod_empresa=".$this->session['cod_empresa']."
                    ORDER BY posicion";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function lista_extras(){
			$query = "SELECT * FROM  tb_categorias WHERE estado IN('A','I') AND cod_categoria>=44 AND cod_categoria<=48 AND cod_empresa =".$this->session['cod_empresa']." ORDER BY posicion";
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
			$query = "SELECT * FROM tb_categorias WHERE alias = '$alias' AND estado IN ('A','I')";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}

		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_categorias(cod_categoria_padre, cod_empresa, alias, categoria, desc_corta, desc_larga, image_min, image_max, estado) ";
        	$query.= "VALUES($this->cod_categoria_padre, $empresa, '$this->alias', '$this->nombre', '$this->desc_corta', '$this->desc_larga', '$this->image_min', '$this->image_max','A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$fecha = fecha();
        	$query= "UPDATE tb_categorias SET cod_categoria_padre=$this->cod_categoria_padre, categoria='$this->nombre', desc_corta='$this->desc_corta', desc_larga='$this->desc_larga', estado='$this->estado', fecha_modificacion='$fecha' WHERE cod_categoria = $this->cod_categoria";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function setImage($name, $scale, $cod_categoria){
		    $option = "image_max='$name'";
		    if($scale=="min")
		        $option = "image_min='$name'";
		    $query = "UPDATE tb_categorias SET $option WHERE cod_categoria = $cod_categoria";
		    return Conexion::ejecutar($query,NULL);
		}

		public function setImages($image_max, $image_min, $cod_categoria){
		    $query = "UPDATE tb_categorias 
						SET image_max='$image_max', image_min='$image_min' 
						WHERE cod_categoria = $cod_categoria";
		    return Conexion::ejecutar($query,NULL);
		}

		public function set_estado($cod_categoria, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_categorias SET estado='$estado' WHERE cod_categoria = $cod_categoria";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function get($cod_categoria){
			$query = "SELECT * FROM tb_categorias where cod_categoria = $cod_categoria AND estado IN('A','I')";
			return Conexion::buscarRegistro($query);
		}

		public function getArray($cod_categoria, &$array)
		{
			$query = "SELECT * FROM tb_categorias where cod_categoria = $cod_categoria AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$array = $row;
				return true;
			}
			else
			{
				return false;
			}
		}

		public function getArrayByAlias($alias, &$array)
		{
			$query = "SELECT * FROM tb_categorias WHERE alias = '$alias' AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$array = $row;
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function moverProductosCategorias($cod_producto, $posicion){
		   
		 	$query = "UPDATE tb_productos SET posicion=$posicion WHERE cod_producto =".$cod_producto;
		 	//echo $query;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}
		
		public function moverCategorias($cod_categoria, $posicion){
		   
		 	$query = "UPDATE tb_categorias SET posicion=$posicion WHERE cod_categoria =".$cod_categoria;
		 	//echo $query;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}

		public function guardarVariasCategorias($cod_categoria, $cod_categoria_padre){
			$query = "INSERT INTO tb_categorias_dependientes(cod_categoria, cod_categoria_padre)
						VALUES($cod_categoria, $cod_categoria_padre)";
			return Conexion::ejecutar($query, null);
		}

		public function editarVariasCategorias($cod_categoria, $cod_categoria_padre){
			$query = "INSERT INTO tb_categorias_dependientes(cod_categoria, cod_categoria_padre)
						VALUES($cod_categoria, $cod_categoria_padre)";
			return Conexion::ejecutar($query, null);	
		}

		public function eliminarVariasCategorias($cod_categoria){
			$query = "DELETE FROM tb_categorias_dependientes WHERE cod_categoria = $cod_categoria";
			return Conexion::ejecutar($query, null);
		}

		public function crear2(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_categorias(cod_empresa, cod_categoria_padre, alias, categoria, desc_corta, desc_larga, image_min, image_max, estado) 
						VALUES($empresa, '$this->cod_categoria_padre', '$this->alias', '$this->nombre', '$this->desc_corta', '$this->desc_larga', '$this->image_min', '$this->image_max','A')";
			if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar2(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$fecha = fecha();
        	$query= "UPDATE tb_categorias SET cod_categoria_padre = $this->cod_categoria_padre, categoria='$this->nombre', desc_corta='$this->desc_corta', desc_larga='$this->desc_larga', estado='$this->estado', fecha_modificacion='$fecha' WHERE cod_categoria = $this->cod_categoria";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getVariasCategorias($cod_categoria){
			$cod_categorias = [];
			$query = "SELECT * 
						FROM tb_categorias_dependientes 
						WHERE cod_categoria = $cod_categoria";
			$resp = Conexion::buscarVariosRegistro($query);
			foreach ($resp as $categoriaPadre) {
				$cod_categorias[] = $categoriaPadre['cod_categoria_padre'];
			}
			return $cod_categorias;
		}
}
?>