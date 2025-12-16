<?php
class cl_codigos_promocionales
{
		public $session;
		public $cod_codigo_promocional, $cod_empresa, $codigo, $tipo, $por_o_din, $monto, $cantidad, $usos_restantes, $restriccion, $fecha_create, $fecha_expiracion, $estado, $usoIlimitado;

		//CUPONES A CLIENTES
		var $cod_cupon, $titulo, $cantidad_dias_disponibles, $imagen, $descripcion;
		
		public function __construct($pcod_codigo=null)
		{
			if($pcod_codigo != null)
				$this->cod_codigo_promocional = $pcod_codigo;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function lista(){
			$query = "SELECT * FROM tb_codigo_promocional WHERE estado ='A' AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "INSERT INTO tb_codigo_promocional(cod_empresa, codigo, tipo, por_o_din, monto, cantidad, usos_restantes, restriccion, fecha_create, fecha_expiracion, estado, ilimitado) ";
        	$query.= "VALUES($this->cod_empresa, '$this->codigo', '$this->tipo', '$this->por_o_din', '$this->monto', '$this->cantidad', '$this->usos_restantes', '$this->restriccion', NOW(), '$this->fecha_expiracion', '$this->estado', $this->usoIlimitado)";
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
			$query = "UPDATE tb_codigo_promocional SET tipo='$this->tipo', por_o_din='$this->por_o_din', monto='$this->monto', cantidad='$this->cantidad', usos_restantes='$this->usos_restantes', restriccion='$this->restriccion',fecha_expiracion='$this->fecha_expiracion', estado='$this->estado',
			ilimitado = $this->usoIlimitado  
			WHERE cod_codigo_promocional = $this->cod_codigo_promocional";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function set_estado($cod_codigo_promocional, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_codigo_promocional SET estado='$estado' WHERE cod_codigo_promocional = $cod_codigo_promocional";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function get($cod_codigo_promocional)
		{
			$query = "select * from tb_codigo_promocional where cod_codigo_promocional = ".$cod_codigo_promocional;
			$row = Conexion::buscarRegistro($query);
			if($row)
			{
				$this->cod_codigo_promocional = $row['cod_codigo_promocional'];
				$this->cod_empresa = $row['cod_empresa'];
				$this->codigo = $row['codigo'];
				$this->tipo = $row['tipo'];
				$this->por_o_din = $row['por_o_din'];
				$this->monto = $row['monto'];
				$this->cantidad = $row['cantidad'];
				$this->usos_restantes = $row['usos_restantes'];
				$this->fecha_create = $row['fecha_create'];
				$this->fecha_expiracion = $row['fecha_expiracion'];
				$this->estado = $row['estado'];
				$this->usoIlimitado = $row['ilimitado'];
				return true;
			}
			else
			{
				return false;
			}
		}

		public function getArray($cod_codigo_promocional, &$array)
		{
			$query = "select * from tb_codigo_promocional where cod_codigo_promocional = ".$cod_codigo_promocional;
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

		function getOrdersByCoupon($cupon){
			$cod_empresa = $this->session["cod_empresa"];
			$query = "SELECT * FROM tb_orden_cabecera WHERE cod_empresa = $cod_empresa AND cod_descuento = '$cupon'";
			return Conexion::buscarVariosRegistro($query);
		}
		
		// CUPONES A CLIENTES
		function getCoupons(){
			$cod_empresa = $this->session["cod_empresa"];
			$query = "SELECT * 
						FROM tb_cupones
						WHERE cod_empresa = $cod_empresa
						AND estado IN ('A','I')";
			return Conexion::buscarVariosRegistro($query);
		}

		function createCoupon(){
			$cod_empresa = $this->session["cod_empresa"];
			$query = "INSERT INTO tb_cupones
						SET cod_empresa = $cod_empresa,
							titulo = '$this->titulo',
							imagen = '$this->imagen',
							estado = '$this->estado',
							tipo = '$this->tipo',
							cantidad_dias_disponibles = '$this->cantidad_dias_disponibles',
							descripcion = '$this->descripcion'";
			return Conexion::ejecutar($query, null);
		}

		function editCoupon(){
			$query = "UPDATE tb_cupones
						SET titulo = '$this->titulo',
							estado = '$this->estado',
							tipo = '$this->tipo',
							cantidad_dias_disponibles = '$this->cantidad_dias_disponibles',
							descripcion = '$this->descripcion'
						WHERE cod_cupon = $this->cod_cupon";
			return Conexion::ejecutar($query, null);
		}

		function getCoupon(){
			$query = "SELECT *
						FROM tb_cupones
						WHERE cod_cupon = $this->cod_cupon";
			return Conexion::buscarRegistro($query);
		}
}
?>