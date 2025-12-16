<?php

class cl_front_pagina
{
	var $session;
	var $cod_front_pagina, $tipo, $titulo, $forma, $numColumnas, $md, $sm, $detalle, $detalle2, $html, $alias, $showTitle, $classname;
	var $cod_detalle = 0;
	
	public function __construct(){
		$this->session = getSession();
	}
	
	public function getSecciones($cod_empresa){
		$query = "SELECT * FROM tb_front_paginas WHERE cod_empresa = $cod_empresa";
		return Conexion::buscarVariosRegistro($query);
	}

	public function getByAlias($alias, $cod_empresa){
		$query = "SELECT * FROM tb_front_paginas WHERE alias = '$alias' AND cod_empresa = $cod_empresa";
		return Conexion::buscarRegistro($query);
	}
	
	public function getEmpresa($alias){
		$query = "SELECT e.* 
					FROM tb_front_paginas fp, tb_empresas e
					WHERE fp.cod_empresa = e.cod_empresa
					AND fp.alias = '$alias'";
		return Conexion::buscarRegistro($query);
	}
	
	public function getTipos(){
		$query = "SELECT * FROM tb_front_pagina_tipos WHERE estado = 'A' ORDER BY posicion";
		return Conexion::buscarVariosRegistro($query);
	}

	public function crear(&$id){
		$fecha = fecha_only();
		$query = "INSERT INTO tb_front_pagina_detalle
					SET cod_front_pagina = '$this->cod_front_pagina',
					cod_tipo = '$this->tipo',
					titulo = '$this->titulo',
					forma = '$this->forma',
					detalle = '$this->detalle',
					detalle2 = '$this->detalle2',
					cod_detalle = '$this->cod_detalle',
					html = '$this->html',
					fecha = '$fecha',
					showTitle='$this->showTitle'";
		$resp = Conexion::ejecutar($query, null);
		$id = Conexion::lastId();
		if($resp)
			return true;
		return false;
	}

	public function editar($cod_front_pagina_detalle){
		$query = "UPDATE tb_front_pagina_detalle
					SET titulo = '$this->titulo',
					forma = '$this->forma',
					detalle = '$this->detalle',
					detalle2 = '$this->detalle2',
					html = '$this->html',
					showTitle='$this->showTitle'
					WHERE cod_front_pagina_detalle = $cod_front_pagina_detalle";
		return Conexion::ejecutar($query, null);
	}

	public function eliminarDetalle($cod_front_pagina_detalle){
		$query = "DELETE FROM tb_front_pagina_detalle WHERE cod_front_pagina_detalle = $cod_front_pagina_detalle";
		return Conexion::ejecutar($query, null);
	}

	public function setPosition($cod_front_pagina_detalle, $posicion){
		$query = "UPDATE tb_front_pagina_detalle SET posicion = $posicion WHERE cod_front_pagina_detalle = $cod_front_pagina_detalle";
		return Conexion::ejecutar($query, null);
	}

	public function cargarDatosSeccion($seccion){
		$query = "SELECT *
					FROM tb_front_pagina_detalle
					WHERE cod_front_pagina = $seccion";
		return Conexion::buscarVariosRegistro($query);
	}

	public function get($id){
		$query = "SELECT * FROM tb_front_pagina_detalle WHERE cod_front_pagina_detalle = $id";
		$detalle = Conexion::buscarRegistro($query);
		if($detalle['cod_tipo'] == 'ordenar'){
		    $detalle['items'] = $this->getProductos($detalle['cod_front_pagina_detalle']);
		}else if($detalle['cod_tipo'] == 'anuncios'){
		    $detalle['items'] = $this->getPromociones($detalle['cod_front_pagina_detalle']);
		}else{
		    $detalle['items'] = [];
		}
		
		return $detalle;
	}	

	public function crearPage($cod_empresa){
		$query = "INSERT INTO tb_front_paginas
					SET cod_empresa = $cod_empresa, 
					titulo = '$this->titulo',
					alias = '$this->alias',
					estado = 'A'";
		return Conexion::ejecutar($query, null);
	}

	public function setHome($cod_front_pagina){
		$query = "SELECT * FROM tb_front_paginas WHERE cod_front_pagina = $cod_front_pagina";
		$resp = Conexion::buscarRegistro($query);
		if($resp){
			$query = "UPDATE tb_front_paginas SET home = 0 WHERE cod_empresa = ".$resp['cod_empresa'];
			Conexion::ejecutar($query, null);

			$query = "UPDATE tb_front_paginas SET home = 1 WHERE cod_front_pagina = $cod_front_pagina";
			return Conexion::ejecutar($query, null);
		}else
			return false;
	}
	
	//CONTENIDO PAGINA
	
	public function getContenidoItem($contenido_id){
	    $query = "SELECT * FROM tb_front_pagina_detalle_contenido WHERE id = $contenido_id";
	    return Conexion::buscarRegistro($query);
	}
	
	public function getProductos($detalle_id){
	    $query = "SELECT p.cod_producto, p.alias, p.nombre, p.desc_corta, p.image_min, p.image_max, p.precio_no_tax, p.precio, p.precio_anterior, p.open_detalle, p.estado
					FROM tb_productos p, tb_front_pagina_detalle_contenido fc 
					WHERE p.cod_producto = fc.accion_desc 
					AND p.estado ='A' 
					AND fc.cod_front_pagina_detalle = $detalle_id
					AND p.cod_empresa = ".$this->session['cod_empresa']." ORDER BY fc.posicion ASC";
		$resp = Conexion::buscarVariosRegistro($query);
		foreach($resp as $key => $items){
		    $resp[$key]['image_min'] = getdirfile($items['image_min']);
		    $resp[$key]['image_max'] = getdirfile($items['image_max']);
		}
		return $resp;
	}
	
	public function getPromociones($detalle_id){
	    $query = "SELECT *
	                FROM tb_front_pagina_detalle_contenido WHERE cod_front_pagina_detalle = $detalle_id ORDER BY posicion";
	    return Conexion::buscarVariosRegistro($query);
	}
	
	public function actualizarProductos($detalle_id, $productosIds){
	    $query = "DELETE FROM tb_front_pagina_detalle_contenido WHERE cod_front_pagina_detalle = $detalle_id";
		$resp = Conexion::ejecutar($query, null);
		if($resp){
    		$posicion = 0;
    		foreach($productosIds as $id){
    		    
    		    $query = "INSERT INTO tb_front_pagina_detalle_contenido(cod_front_pagina_detalle, accion_desc, posicion, estado) VALUES($detalle_id, $id, $posicion, 'A')";
    		    Conexion::ejecutar($query, null);
    		    $posicion++;
    		}
    		return true;
		}
		return false;
	}
	
	public function agregarPromo($detalle_id, $accion_id, $accion_desc, $image){
	   $query = "INSERT INTO tb_front_pagina_detalle_contenido
                    (cod_front_pagina_detalle, imagen, accion_id, accion_desc, estado, posicion) 
                    SELECT $detalle_id, '$image', '$accion_id', '$accion_desc', 'A', (COALESCE(MAX(posicion), 0) + 1)
                    FROM tb_front_pagina_detalle_contenido
                WHERE cod_front_pagina_detalle = $detalle_id";
        return Conexion::ejecutar($query, null);
	}
	
	public function eliminarContenidoPagina($detalle_id){
	    $query = "DELETE FROM tb_front_pagina_detalle_contenido WHERE id = $detalle_id";
	    return Conexion::ejecutar($query, null);
	}
}	

?>