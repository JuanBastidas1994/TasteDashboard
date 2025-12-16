<?php

class cl_web_anuncios
{
	var $session;
	var $cod_producto, $cod_producto_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $fecha_create, $user_create, $estado, $codigo, $categorias;
	var $precio, $precio_no_tax, $iva_valor, $iva_porcentaje, $precio_anterior, $costo;
	var $titulo, $subtitulo, $descripcion, $text_boton, $imagen, $url_boton, $cod_cabecera, $posicion, $categoriaAnuncio, $accion;
	
	public function __construct()
	{
		$this->session = getSession();
	}

	public function lista(){
		$query = "SELECT * FROM  tb_anuncio_cabecera WHERE cod_empresa = ".$this->session['cod_empresa'];
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	/*--NUEVO--*/
	
	public function listabyempresa($cod_empresa){
		$query = "SELECT * FROM tb_anuncio_cabecera WHERE cod_empresa = ".$cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
		public function listabyId($cod_anuncio){
		$query = "SELECT * FROM tb_anuncio_cabecera WHERE cod_anuncio_cabecera = ".$cod_anuncio;
        $resp = Conexion::buscarRegistro($query);
        return $resp;
	}
	/*--NUEVO--*/
	
	public function contadorModulos($cod_modulo){
		$query = "SELECT * FROM  tb_anuncio_detalle WHERE cod_anuncio_cabecera = ".$cod_modulo;
        $resp = Conexion::buscarVariosRegistro($query);
        $cantidad=count($resp);
        return $cantidad;
	}

	public function listaByModulo($cod_modulo){
		$query = "SELECT * 
				 FROM tb_anuncio_detalle ad
				 WHERE ad.estado IN ('A','I') 
                 AND ad.cod_anuncio_cabecera = $cod_modulo
				 AND ad.cod_empresa=".$this->session['cod_empresa']." ORDER BY ad.posicion ASC";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}

	public function deleteByModulo($cod_modulo){
		$query = "DELETE FROM  tb_anuncio_cabecera_detalle WHERE cod_anuncio_cabecera = $cod_modulo";
		$resp = Conexion::ejecutar($query,NULL);
		return $resp;
	}

	public function addDetalleInModulo($cod_modulo, $cod_producto, $posicion){
		$query = "INSERT INTO tb_anuncio_cabecera_detalle(cod_anuncio_cabecera, cod_anuncio_detalle, posicion) ";
    	$query.= "VALUES($cod_modulo, $cod_producto, $posicion)";
    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}
	
	
	public function crear(&$id){
		/*$usuario = $this->session['cod_usuario'];*/
		$empresa = $this->session['cod_empresa'];
		$query = "INSERT INTO tb_anuncio_detalle(cod_anuncio_cabecera, cod_empresa, titulo, subtitulo, descripcion, imagen, text_boton, url_boton, accion_id, categorias, estado, posicion) ";
    	$query.= "VALUES($this->cod_cabecera, $empresa, '$this->titulo', '$this->subtitulo', '$this->descripcion', '$this->imagen', '$this->text_boton','$this->url_boton', '$this->accion', '$this->categoriaAnuncio', 'A', $this->posicion)";
    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function getDetalleAnuncio($cod_anuncio_detalle){
	    $query = "SELECT * FROM tb_anuncio_detalle WHERE cod_anuncio_detalle = $cod_anuncio_detalle";
    	$row = Conexion::buscarRegistro($query);
		return $row;
	}
	
	public function editar($cod_anuncio_detalle){
	    $query = "UPDATE tb_anuncio_detalle 
					SET titulo = '$this->titulo', subtitulo = '$this->subtitulo', descripcion = '$this->descripcion', text_boton = '$this->text_boton', url_boton = '$this->url_boton', accion_id='$this->accion', categorias = '$this->categoriaAnuncio', estado = '$this->estado'
	                WHERE cod_anuncio_detalle = $cod_anuncio_detalle";
	    if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function setImage($name, $scale, $cod_anuncio_detalle){
		    $option = "imagen='$name'";
		    if($scale=="min")
		        $option = "image_min='$name'";
		    $query = "UPDATE tb_anuncio_detalle SET $option WHERE cod_anuncio_detalle = $cod_anuncio_detalle";
		    return Conexion::ejecutar($query,NULL);
		}
	
	public function eliminar($cod_anuncio_detalle){
	    $query = "DELETE FROM tb_anuncio_detalle WHERE cod_anuncio_detalle = $cod_anuncio_detalle";
	    if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function actPosicion($cod_anuncio_detalle, $posicion){
	    $query = "UPDATE tb_anuncio_detalle SET posicion = $posicion WHERE cod_anuncio_detalle = $cod_anuncio_detalle";
	    if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}

	public function listaCategorias($cod_anuncio_cabecera){
		$query = "SELECT DISTINCT categorias, titulo
					FROM tb_anuncio_detalle 
					WHERE cod_anuncio_cabecera = $cod_anuncio_cabecera
					AND estado = 'A'";
		$resp = Conexion::buscarVariosRegistro($query);
		return $resp;
	}
}	

?>