<?php
class cl_contifico
{
	var $URL = "https://api.contifico.com/sistema/api/v1";
	var $session;
	var $cod_empresa;
	public $API, $pos, $ambiente, $categoria, $cod_contifico_empresa;
	public function __construct($pcod_empresa = null)
	{
		if($pcod_empresa != null)
				$this->cod_empresa = $pcod_empresa;
		$this->getCredentials();
	}

	public function getCredentials(){
		$cod_empresa = $this->cod_empresa;
		$query = "SELECT * 
			FROM tb_contifico_empresa 
			WHERE estado='A' 
			AND cod_empresa = $cod_empresa";
		$resp = Conexion::buscarRegistro($query);
		if($resp){
			$this->API = $resp['api'];
			$this->categoria = $resp['categoria'];
			$this->ambiente = $resp['ambiente'];
			$this->cod_contifico_empresa = $resp['cod_contifico_empresa'];
		}
	}
	
	public function getPoscode($cod_sucursal){
		$query = "SELECT p.pos, p.ambiente, p.emisor, p.ptoemision, p.secuencial
				FROM tb_contifico_empresa_postokens p, tb_contifico_sucursal s
				WHERE p.cod_postoken = s.cod_postoken
				AND s.cod_sucursal = $cod_sucursal
				AND p.ambiente = '$this->ambiente'";
	    return Conexion::buscarRegistro($query);
	}

	public function incrementSecuencial($cod_sucursal){
		$query = "UPDATE tb_contifico_empresa_postokens p, tb_contifico_sucursal s
				SET p.secuencial = p.secuencial + 1
				WHERE p.cod_postoken = s.cod_postoken
				AND s.cod_sucursal = $cod_sucursal
				AND p.ambiente = '$this->ambiente'";
		return Conexion::ejecutar($query,NULL);		
	}
	
	/*PRODUCTOS*/
	public function LstCategories(){
		$ch = curl_init($this->URL."/categoria/");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	}
	
	/*BODEGAS*/
	public function LstBodegas(){
		$ch = curl_init($this->URL."/bodega/");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	}

    /*PRODUCTOS*/
	public function LstProductos(){
		//$ch = curl_init($this->URL."/producto/?filtro=JC");
		$ch = curl_init($this->URL."/producto/");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	}
	
	
	public function LstProductosByCategory($category_id, $page, &$numRows){
		$ch = curl_init("https://api.contifico.com/sistema/api/v2/producto/?categoria_id=$category_id&page=$page");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		curl_close($ch);
		//return json_decode($response,true);
		$resp = json_decode($response,true);
		if($resp){
			$numRows = $resp['count'];
		    return $resp['results'];
		}
	}

	public function GetProducto($id){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->URL."/producto/".$id,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization:".$this->API
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response,true);
	}

	public function CreateProducto($iva, $pvp, $nombre, $id){
	    $producto['codigo_barra'] = NULL;
	    $producto['porcentaje_iva'] = $iva;
	    $producto['categoria_id'] = $this->categoria;
	    $producto['minimo'] = $pvp;
	    $producto['pvp2'] = $pvp;
	    $producto['pvp3'] = $pvp;
	    $producto['pvp1'] = $pvp;
	    $producto['pvp_manual'] = false;
	    $producto['descripcion'] = $nombre;
	    $producto['nombre'] = $nombre;
	    $producto['codigo'] = $id;
	    $producto['estado'] = "A";
	    $json = json_encode($producto);
	    
	    $ch = curl_init($this->URL."/producto/");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'Authorization: '.$this->API;
		
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
	    $response = curl_exec($ch);
		curl_close($ch);
	    return json_decode($response,true);
	}
	
	/*DOCUMENTOS*/
	public function CreateFactura($esquema){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado Contifico";
	        return false;
	    }
	    $json = json_encode($esquema);

		$ch = curl_init($this->URL."/documento/");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'Authorization: '.$this->API; //<-- Key Contifico.
		
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
	    $response = curl_exec($ch);
	    if($response === false){
            $this->msgError = "Curl error: " . curl_error($ch);
            return false;
        }else{
            $respCurl = curl_getinfo($ch);
            if($respCurl['http_code'] === 500){
                $this->msgError .= "Curl httpcode error: ".$respCurl['http_code'];
                return false;
            }
        }
        
		curl_close($ch);
	    return json_decode($response,true);
	}

	public function getDocumentos($ruc, $fechaInicio, $fechaFin) {
		$cod_contifico_empresa = $ruc;
		$query = "SELECT ofe.*, o.fecha, u.nombre as cliente
					FROM tb_orden_factura_electronica ofe, tb_orden_cabecera o, tb_usuarios u
					WHERE ofe.cod_orden = o.cod_orden
					AND o.cod_usuario = u.cod_usuario
					AND o.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
					AND ofe.cod_contifico_empresa = $cod_contifico_empresa";
		return Conexion::buscarVariosRegistro($query);
	}
	
	public function getOrdenesNoFActuradas($ruc, $fechaInicio, $fechaFin) {
		$cod_contifico_empresa = $ruc;
		$query = "SELECT o.cod_orden, o.fecha, o.estado, u.nombre as cliente
					FROM tb_contifico_sucursal cs, tb_orden_cabecera o, tb_usuarios u
					WHERE cs.cod_sucursal = o.cod_sucursal
					AND o.cod_usuario = u.cod_usuario
					AND o.cod_orden NOT IN(SELECT cod_orden FROM tb_orden_factura_electronica WHERE cod_contifico_empresa = $cod_contifico_empresa)
					AND o.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
					AND cs.cod_contifico_empresa = $cod_contifico_empresa
					AND o.estado = 'ENTREGADA'";
		return Conexion::buscarVariosRegistro($query);
	}

	// Listado unificado: ordenes facturadas y no facturadas en un mismo query, con filtros opcionales
	public function getFacturasUnificadas($cod_empresa, $fechaInicio, $fechaFin, $sucursal = '', $cliente = '', $documento = '', $estado = '') {
		$params = [
			':cod_empresa' => $cod_empresa,
			':fecha_inicio' => $fechaInicio,
			':fecha_fin' => $fechaFin
		];

		// Una orden puede tener varias filas en tb_orden_factura_electronica si se anuló y
		// se reenvió (ExistFacturaToOrden ignora las ANULADA al reenviar), por eso siempre
		// se toma solo la última fila (MAX id) por orden, no un join directo por cod_orden.
		$query = "SELECT oc.cod_orden, oc.fecha, oc.estado AS estado_orden, u.nombre AS cliente,
						s.nombre AS sucursal,
						ofe.tipo, ofe.num_factura, ofe.estado AS estado_factura, ofe.fecha AS fecha_envio,
						IF(ofe.estado IN ('CREADA','EMITIDA_SRI'), 'ENVIADA', 'NO_ENVIADA') AS estado_envio,
						IF(ofe.estado IN ('CREADA','EMITIDA_SRI') AND ofe.fecha IS NOT NULL AND DATE(ofe.fecha) = CURDATE(), 1, 0) AS puede_anular,
						err.motivo AS ultimo_error
					FROM tb_orden_cabecera oc
					JOIN tb_usuarios u ON oc.cod_usuario = u.cod_usuario
					JOIN tb_sucursales s ON oc.cod_sucursal = s.cod_sucursal
					LEFT JOIN tb_contifico_sucursal cs ON cs.cod_sucursal = oc.cod_sucursal
					LEFT JOIN tb_orden_factura_electronica ofe
						ON ofe.cod_orden_factura_electronica = (
							SELECT MAX(ofe2.cod_orden_factura_electronica)
							FROM tb_orden_factura_electronica ofe2
							WHERE ofe2.cod_orden = oc.cod_orden AND ofe2.cod_contifico_empresa = cs.cod_contifico_empresa
						)
					LEFT JOIN tb_orden_errores err
						ON err.cod_orden = oc.cod_orden AND err.tipo = 'FACTURA'
						AND err.fecha = (SELECT MAX(e2.fecha) FROM tb_orden_errores e2
											WHERE e2.cod_orden = oc.cod_orden AND e2.tipo = 'FACTURA')
					WHERE oc.cod_empresa = :cod_empresa
					AND oc.estado = 'ENTREGADA'
					AND oc.fecha BETWEEN :fecha_inicio AND :fecha_fin";

		if($sucursal !== '' && $sucursal !== null){
			$query .= " AND oc.cod_sucursal = :sucursal";
			$params[':sucursal'] = $sucursal;
		}
		if($cliente !== '' && $cliente !== null){
			$query .= " AND u.nombre LIKE :cliente";
			$params[':cliente'] = "%$cliente%";
		}
		if($documento !== '' && $documento !== null && $documento !== 'TODOS'){
			$query .= " AND ofe.tipo = :documento";
			$params[':documento'] = $documento;
		}
		if($estado === 'ENVIADA'){
			$query .= " AND ofe.estado IN ('CREADA','EMITIDA_SRI')";
		}else if($estado === 'NO_ENVIADA'){
			$query .= " AND (ofe.cod_orden_factura_electronica IS NULL OR ofe.estado NOT IN ('CREADA','EMITIDA_SRI'))";
		}

		$query .= " ORDER BY oc.fecha DESC";

		return Conexion::buscarVariosRegistro($query, $params);
	}

	/*RUCS*/
	//Guardar
	public function addRuc($razon_social, $ruc, $ambiente, $api, $cod_empresa){
		$query = "INSERT INTO tb_contifico_empresa(cod_empresa, ambiente, razon_social, ruc, api, estado)
		        VALUES($cod_empresa, '$ambiente', '$razon_social', '$ruc', '$api', 'A')";
		return Conexion::ejecutar($query,NULL);		
	}
	
	//Get
	public function getRuc($id, $cod_empresa){
	    $query = "SELECT * FROM tb_contifico_empresa WHERE cod_contifico_empresa = $id AND cod_empresa = $cod_empresa";
	    return Conexion::buscarRegistro($query);
	}
	
	//Productos
	
}