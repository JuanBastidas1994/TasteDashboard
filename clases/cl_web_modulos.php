<?php

class cl_web_modulos
{
	var $session;
	var $cod_producto, $cod_producto_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $fecha_create, $user_create, $estado, $codigo, $categorias;
	var $precio, $precio_no_tax, $iva_valor, $iva_porcentaje, $precio_anterior, $costo;
	
	public function __construct()
	{
		$this->session = getSession();
	}

	public function lista(){
		$query = "SELECT * FROM tb_web_modulos_productos WHERE cod_empresa = ".$this->session['cod_empresa'];
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	/*--NUEVO--*/
	
	public function listabyempresa($cod_empresa){
		$query = "SELECT * FROM tb_web_modulos_productos WHERE cod_empresa = ".$cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	/*--NUEVO--*/

	public function listaByModulo($cod_modulo){
		$query = "SELECT * 
				FROM tb_productos p, tb_web_modulos_productos_detalle d 
				WHERE p.cod_producto = d.cod_producto
				AND p.estado ='A' 
				AND d.cod_web_modulos_producto = $cod_modulo
				AND cod_empresa = ".$this->session['cod_empresa']." ORDER BY d.posicion ASC";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}

	public function deleteByModulo($cod_modulo){
		$query = "DELETE FROM tb_web_modulos_productos_detalle WHERE cod_web_modulos_producto = $cod_modulo";
		$resp = Conexion::ejecutar($query,NULL);
		return $resp;
	}

	public function addDetalleInModulo($cod_modulo, $cod_producto, $posicion){
		$query = "INSERT INTO tb_web_modulos_productos_detalle(cod_web_modulos_producto, cod_producto, posicion) ";
    	$query.= "VALUES($cod_modulo, $cod_producto, $posicion)";
    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}
}	

?>