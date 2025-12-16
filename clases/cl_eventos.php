<?php

class cl_eventos
{
    var $session;
    var $cod_empresa;
    var $cod_categoria, $titulo, $imagen, $archivo, $fecha, $hora_inicio, $hora_fin, $user_create, $color, $descripcion, $estado;
    public function __construct()
	{
		$this->session = getSession();
		$this->cod_empresa = $this->session['cod_empresa'];
	}
	
	public function listaBetweenFechas($start, $end){
	    $query = "SELECT a.cod_agenda, a.titulo, a.imagen, a.fecha, a.color, a.hora_inicio, a.hora_fin, a.descripcion, a.estado, ac.nombre as categoria 
                FROM tb_agenda a, tb_agenda_categorias ac
                WHERE a.cod_categoria = ac.cod_agenda_categoria
                AND a.fecha >= '$start'
                AND a.fecha <= '$end'
                AND a.estado = 'A'
                AND ac.cod_empresa = ".$this->cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	public function getCategorias(){
	    $query = "SELECT *
                FROM tb_agenda_categorias
                WHERE estado = 'A'
                AND cod_empresa = ".$this->cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	public function get($id){
	    $query = "SELECT *
                FROM tb_agenda
                WHERE estado = 'A'
                AND cod_agenda = ".$id;
        $resp = Conexion::buscarRegistro($query);
        return $resp;
	}
	
	public function crear(&$id){
	    $query = "INSERT INTO tb_agenda(cod_categoria, titulo, imagen, archivo, fecha, hora_inicio, hora_fin, user_create, color, descripcion, estado) ";
	    $query.= "VALUES($this->cod_categoria, '$this->titulo','$this->imagen', '$this->archivo','$this->fecha','$this->hora_inicio','$this->hora_fin','$this->user_create','$this->color','$this->descripcion','A')";
	    if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function editHoras($id){
	    $query = "UPDATE tb_agenda SET fecha='$this->fecha', hora_inicio='$this->hora_inicio', hora_fin='$this->hora_fin' WHERE cod_agenda = ".$id;
	    return Conexion::ejecutar($query,NULL);
	}
	
	public function delete($id){
	    $query = "UPDATE tb_agenda SET estado='D' WHERE cod_agenda = ".$id;
	    return Conexion::ejecutar($query,NULL);
	}
}