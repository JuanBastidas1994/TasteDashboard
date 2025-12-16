<?php

class cl_web_pagina
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
	
	public function getHomeByBusiness($cod_empresa){
		$query = "SELECT * FROM tb_front_paginas WHERE cod_empresa = $cod_empresa AND home = 1;";
		$page = Conexion::buscarRegistro($query);
		if(!$page){
		    $queryInsert = "INSERT INTO tb_front_paginas SET cod_empresa = $cod_empresa, titulo = 'Home', alias = 'home', estado = 'A', home=1";
		    Conexion::ejecutar($queryInsert, null);
		    return Conexion::buscarRegistro($query);
		}
		return $page;
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
					num_columnas = '$this->numColumnas',
					md = '$this->md',
					sm = '$this->sm',
					detalle = '$this->detalle',
					detalle2 = '$this->detalle2',
					cod_detalle = '$this->cod_detalle',
					html = '$this->html',
					fecha = '$fecha',
					classname='$this->classname',
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
					num_columnas = '$this->numColumnas',
					md = '$this->md',
					sm = '$this->sm',
					detalle = '$this->detalle',
					detalle2 = '$this->detalle2',
					html = '$this->html',
					classname='$this->classname',
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
		return Conexion::buscarRegistro($query);
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
}	

?>