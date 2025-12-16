<?php

class cl_personalizar_web
{
    var $posicion, $titulo;
	var $session;
	
	public function __construct()
	{
		$this->session = getSession();
	}

	public function lista(){
		$query = "SELECT a.*, c.categoria
		            FROM  tb_web_adicionales a, tb_categorias c
		            WHERE a.cod_categoria = c.cod_categoria
		            AND c.cod_empresa = ".$this->session['cod_empresa'];
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	public function listaByCategoria($cod_categoria, &$resp){
	    $query = "SELECT a.*, c.categoria
		            FROM  tb_web_adicionales a, tb_categorias c
		            WHERE a.cod_categoria_items = c.cod_categoria
	                AND a.cod_categoria = $cod_categoria
	                ORDER BY a.posicion";
	   $resp = Conexion::buscarVariosRegistro($query);
	   if($resp)
	        return true;
	   return false;
	}
	
	public function eliminar_adicionales($cod_categoria){
	    $query = "DELETE FROM tb_web_adicionales WHERE cod_categoria = $cod_categoria";
	    $resp = Conexion::ejecutar($query, NULL);
	    if($resp)
	            return true;
	       return false;
	}
	
	public function insert_adicionales($cod_categoria, $cod_item){
	   $query = "INSERT INTO tb_web_adicionales(cod_categoria, titulo, posicion, cod_categoria_items, estado) ";
        $query.= "VALUES($cod_categoria, '$this->titulo', '$this->posicion', $cod_item, 'A')";
        $resp = Conexion::ejecutar($query, NULL);
        if($resp)
            return true;
       return false;
	}
	
	public function update_position($cod_categoria, $cod_items){
	    $query = "UPDATE tb_web_adicionales SET posicion = '$this->posicion' WHERE cod_categoria = $cod_categoria AND cod_categoria_items = $cod_items";
	    $resp = Conexion::ejecutar($query, NULL);
	    if($resp)
	        return true;
	   return false;
	}
}	

?>