<?php
class cl_promociones
{
	public $session;
	public $cod_producto_descuento, $cod_producto, $cod_sucursal, $cod_empresa, $fecha_inicio, $fecha_fin, $is_porcentaje, $valor, $cantidad, $texto;
	
	public function __construct($pcod_sucursal=null)
	{
		if($pcod_sucursal != null)
			$this->pcod_sucursal = $pcod_sucursal;
		$this->session = getSession();
		$this->cod_empresa = $this->session['cod_empresa'];
	}

	public function lista(){//OJOOOOOOOOOOOO NOW
		$query = "SELECT p.cod_producto, p.alias, p.nombre, p.precio, p.precio_no_tax, p.image_min, p.image_max ,pd.*, s.nombre as sucursal
					FROM tb_productos p, tb_producto_descuento pd, tb_sucursales s
					WHERE p.cod_producto = pd.cod_producto
                    AND pd.cod_sucursal = s.cod_sucursal
					AND pd.fecha_fin >= curdate() 
					AND pd.estado = 'A'
					AND p.cod_empresa = ".$this->cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}
	
	public function crear(&$id){
		$query = "INSERT INTO tb_producto_descuento(cod_producto, cod_sucursal, fecha_inicio, fecha_fin, is_porcentaje, valor, cantidad, texto) ";
    	$query.= "VALUES($this->cod_producto, $this->cod_sucursal, '$this->fecha_inicio', '$this->fecha_fin', $this->is_porcentaje, $this->valor, $this->cantidad, '$this->texto')";

    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}

	public function editar(){
		$query = "UPDATE tb_producto_descuento SET cod_producto='$this->cod_producto', cod_sucursal='$this->cod_sucursal', fecha_inicio='$this->fecha_inicio', fecha_fin='$this->fecha_fin', is_porcentaje='$this->is_porcentaje', valor='$this->valor', cantidad='$this->cantidad', texto='$this->texto' WHERE cod_producto_descuento = $this->cod_producto_descuento";
    	if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}

	public function set_estado($cod_producto_descuento, $estado){
		$usuario = $this->session['cod_usuario'];
		$empresa = $this->cod_empresa;
		$query = "UPDATE tb_producto_descuento SET estado='$estado' WHERE cod_producto_descuento = $cod_producto_descuento";
    	if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}

	public function getArray($cod_producto_descuento, &$array)
	{
		$query = "select * from tb_producto_descuento where cod_producto_descuento = ".$cod_producto_descuento;
		$row = Conexion::buscarRegistro($query);
		if($row)
		{
			$array = $row;
			return true;
		}
		else
		{
			return false;
		}
	}
}


class cl_marketing_envios{

	public $session;
	public $cod_marketing_envio, $cod_sucursal, $cod_empresa, $fecha_inicio, $fecha_fin, $porcentaje, $monto, $solo_horario, $dias;
	
	public function __construct($pcod_sucursal=null)
	{
		if($pcod_sucursal != null)
			$this->pcod_sucursal = $pcod_sucursal;
		$this->session = getSession();
		$this->cod_empresa = $this->session['cod_empresa'];
	}

	public function lista(){
		$query = "SELECT me.*, s.nombre as sucursal
					FROM tb_marketing_envios me, tb_sucursales s
					WHERE me.cod_sucursal = s.cod_sucursal
					AND me.fecha_fin >= NOW() 
					AND me.estado = 'A'
					AND me.cod_empresa = ".$this->cod_empresa;
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}

	public function crear(&$id){
		$query = "INSERT INTO tb_marketing_envios(cod_empresa, cod_sucursal, fecha_inicio, fecha_fin, porcentaje, monto, solo_horario, dias, estado) ";
    	$query.= "VALUES($this->cod_empresa, $this->cod_sucursal, '$this->fecha_inicio', '$this->fecha_fin', $this->porcentaje, $this->monto, $this->solo_horario, '$this->dias', 'A')";

    	if(Conexion::ejecutar($query,NULL)){
    		$id = Conexion::lastId();
    		return true;
    	}else{
    		return false;
    	}
	}

	public function set_estado($cod_marketing_envio, $estado){
		$usuario = $this->session['cod_usuario'];
		$empresa = $this->cod_empresa;
		$query = "UPDATE tb_marketing_envios SET estado='$estado' WHERE cod_marketing_envio = $cod_marketing_envio";
    	if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function eliminar($cod_marketing_envio){
		$query = "DELETE FROM tb_marketing_envios WHERE cod_marketing_envio = $cod_marketing_envio";
    	if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}
	
	public function eliminarVarios($cod_sucursal){
		$query = "DELETE FROM tb_marketing_envios WHERE cod_sucursal = $cod_sucursal";
    	if(Conexion::ejecutar($query,NULL)){
    		return true;
    	}else{
    		return false;
    	}
	}

	public function getArray($cod_marketing_envio, &$array)
	{
		$query = "SELECT * from tb_marketing_envios where cod_marketing_envio = ".$cod_producto_descuento;
		$row = Conexion::buscarRegistro($query);
		if($row)
		{
			$array = $row;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getExistente($cod_sucursal, &$row){
	    $query = "SELECT *
	                FROM tb_marketing_envios
	                WHERE cod_sucursal = $cod_sucursal
                    AND estado = 'A'
	                AND fecha_inicio >= '$this->fecha_inicio' AND fecha_inicio <= '$this->fecha_fin'
	                AND fecha_fin >= '$this->fecha_inicio' AND fecha_inicio <= '$this->fecha_fin'";
	   $row = Conexion::buscarRegistro($query);
	   if($row)
	        return true;
	   return false;
	}
}
?>