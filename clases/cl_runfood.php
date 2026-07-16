<?php
class cl_runfood
{
    public $URL = "";
    public $userId = "";
    public $apiKey = "";
    public $session, $msgError = "";
	public $cod_empresa;

    public function __construct(){
        $this->session = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    public function getCredentials(){

    }

    public function getSucursales(){
        $cod_empresa = $this->cod_empresa;
        $query = "SELECT s.cod_sucursal, s.nombre, s.direccion, rs.cod_runfood_sucursal, rs.dominio, rs.usuario_id, rs.facturar
                FROM tb_sucursales s
                LEFT JOIN tb_runfood_sucursal rs ON s.cod_sucursal = rs.cod_sucursal
                WHERE s.cod_empresa = $cod_empresa
                AND s.estado IN ('A', 'I')";
        return Conexion::buscarVariosRegistro($query);       
    }
    
    public function getSucursal($cod_sucursal){
        $cod_empresa = $this->cod_empresa;
        $query = "SELECT s.cod_sucursal, s.nombre, s.direccion, rs.cod_runfood_sucursal, rs.dominio, rs.usuario_id, rs.api_key, rs.facturar
                FROM tb_sucursales s
                LEFT JOIN tb_runfood_sucursal rs ON s.cod_sucursal = rs.cod_sucursal
                WHERE s.cod_sucursal = $cod_sucursal
                AND s.estado IN ('A', 'I')";
        $sucursal = Conexion::buscarRegistro($query);
        if($sucursal){
            $this->URL = $sucursal['dominio'];
            $this->userId = $sucursal['usuario_id'];
            $this->apiKey = $sucursal['api_key'];
        }
        return $sucursal;
    }

    public function setSucursal($cod_sucursal, $dominio, $usuario_id){
        $query = "INSERT INTO tb_runfood_sucursal(cod_sucursal, dominio, usuario_id, facturar)
                VALUES($cod_sucursal, '$dominio', $usuario_id, 0)";
        return Conexion::ejecutar($query,NULL);
    }

    public function setApiKey($cod_sucursal, $api_key){
        $query = "UPDATE tb_runfood_sucursal SET api_key = '$api_key' WHERE cod_sucursal = $cod_sucursal";
        return Conexion::ejecutar($query,NULL);
    }
    
    public function getAllProductsByOffices($cod_sucursal){
        global $session;
        $cod_empresa = $this->cod_empresa;
        $dir = url_sistema.'assets/empresas/'.$session['alias'].'/';
        $query = "SELECT p.cod_producto, p.alias, p.nombre, p.precio, p.image_min, p.cod_producto_padre, pf.id, pf.sku, pf.name_in_contifico, pf.cod_sistema_facturacion,
                (SELECT COUNT(*) FROM tb_productos_opciones po WHERE po.cod_producto = p.cod_producto) as num_opciones
                FROM tb_productos p
                LEFT JOIN tb_productos_facturacion pf ON p.cod_producto = pf.cod_producto AND pf.cod_contifico_empresa = $cod_sucursal
                WHERE p.cod_empresa = $cod_empresa
                AND p.estado IN ('A', 'I')";
        $resp = Conexion::buscarVariosRegistro($query);
        foreach($resp as $key => $item){
            $resp[$key]['image_min'] = $dir.$item['image_min'];
            $resp[$key]['tiene_opciones'] = ((int)$item['num_opciones'] > 0);
        }
        return $resp;
    }
    
    function getAllIngredientes($id){
        $cod_empresa = $this->cod_empresa;
        $query = "SELECT i.cod_ingrediente, i.ingrediente as nombre, i.precio, i.cod_unidad_medida, igf.id, igf.name_in_contifico, igf.cod_sistema_facturacion 
                FROM tb_ingredientes i
                LEFT JOIN tb_ingredientes_facturacion igf ON i.cod_ingrediente = igf.cod_ingrediente AND igf.cod_contifico_empresa = $id
                WHERE i.cod_empresa = $cod_empresa
                AND i.estado IN ('A', 'I')";
        return Conexion::buscarVariosRegistro($query);
    }
    
    function getAllRecipientes($id){
        $cod_empresa = $this->cod_empresa;
        $query = "SELECT i.cod_recipiente, i.nombre, i.precio, igf.id, igf.name_in_contifico, igf.cod_sistema_facturacion 
                FROM tb_recipientes i
                LEFT JOIN tb_recipientes_facturacion igf ON i.cod_recipiente = igf.cod_recipiente AND igf.cod_contifico_empresa = $id
                WHERE i.cod_empresa = $cod_empresa
                AND i.estado IN ('A', 'I')";
        return Conexion::buscarVariosRegistro($query);
    }
    
    function getAllFormasPago($id){
        $cod_empresa = $this->cod_empresa;
        $query = "SELECT fp.*, ff.id, ff.name_in_contifico, ff.cod_contifico_empresa 
                    FROM tb_formas_pago fp
                    INNER JOIN tb_empresa_forma_pago ef ON fp.cod_forma_pago = ef.cod_forma_pago AND ef.cod_empresa = $cod_empresa
                    LEFT JOIN tb_formas_pago_facturacion ff ON ff.cod_forma_pago = fp.cod_forma_pago AND ff.cod_contifico_empresa = $id";
        return Conexion::buscarVariosRegistro($query);
    }
    
    function saveIngrediente($office_id, $cod_empresa, $unidad, $precio, $id_contifico, $contifico_name){
        $query = "INSERT INTO tb_ingredientes(cod_empresa, cod_unidad_medida, ingrediente, precio, estado)
                    VALUES($cod_empresa, '$unidad', '$contifico_name', $precio, 'A')";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp){
            $cod_ingrediente = Conexion::lastId();
            $this->setIngrediente($office_id, $cod_ingrediente, $id_contifico, $contifico_name);
        }
        return $resp;
    }
    
    function saveRecipiente($office_id, $cod_empresa, $precio, $id_contifico, $contifico_name){
        $query = "INSERT INTO tb_recipientes(cod_empresa, nombre, precio, estado)
                    VALUES($cod_empresa, '$contifico_name', $precio, 'A')";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp){
            $cod_recipiente = Conexion::lastId();
            $this->setRecipiente($office_id, $cod_recipiente, $id_contifico, $contifico_name);
        }
        return $resp;
    }
    
    
    function setProduct($office_id, $cod_producto, $id_contifico, $contifico_name, $sku = null){
        // Un SKU de Runfood solo puede estar ligado a UN producto nuestro. Si ya estaba
        // ligado a otro, se desliga (y se le borra el sku) para que el verificador no lo
        // vuelva a re-ligar despues.
        if ($sku) {
            $query = "SELECT cod_producto FROM tb_productos_facturacion
                        WHERE sku = '$sku' AND cod_contifico_empresa = $office_id AND cod_producto != $cod_producto";
            $previo = Conexion::buscarRegistro($query);
            if ($previo) {
                $this->desligarProducto($previo['cod_producto'], $office_id);
            }
        }

        $query = "DELETE FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);

        $skuValue = $sku ? "'" . $sku . "'" : "NULL";
        $query = "INSERT INTO tb_productos_facturacion(id, sku, cod_producto, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', $skuValue, $cod_producto, '$contifico_name', 3, $office_id)";
    	$resp = Conexion::ejecutar($query,NULL);

        // Sincronizar el SKU de nuestro producto con el de Runfood.
        if ($resp && $sku) {
            $query = "UPDATE tb_productos SET sku = '$sku' WHERE cod_producto = $cod_producto";
            Conexion::ejecutar($query,NULL);
        }

        return $resp;
    }

    function desligarProducto($cod_producto, $office_id){
        $query = "DELETE FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);

        $query = "UPDATE tb_productos SET sku = '' WHERE cod_producto = $cod_producto";
        return Conexion::ejecutar($query,NULL);
    }

    /**
     * Recorre el catalogo de Runfood y liga automaticamente los productos cuyo SKU
     * ya coincide con tb_productos.sku y que aun no tienen asignacion para esta sucursal.
     */
    function verificarProductos($office_id){
        $productosRunfood = $this->LstProductos();
        if (!$productosRunfood || !isset($productosRunfood['data'])) {
            return ['matched' => 0, 'productos' => []];
        }

        $cod_empresa = $this->cod_empresa;
        $matched = [];

        foreach ($productosRunfood['data'] as $pRunfood) {
            $sku = $pRunfood['sku'] ?? null;
            if (!$sku) continue;

            $query = "SELECT cod_producto FROM tb_productos
                        WHERE cod_empresa = $cod_empresa AND sku = '$sku' AND estado IN ('A','I')";
            $producto = Conexion::buscarRegistro($query);
            if (!$producto) continue;

            $query = "SELECT cod_producto_facturacion FROM tb_productos_facturacion
                        WHERE cod_producto = {$producto['cod_producto']} AND cod_contifico_empresa = $office_id";
            $yaLigado = Conexion::buscarRegistro($query);
            if ($yaLigado) continue;

            $this->setProduct($office_id, $producto['cod_producto'], $sku, $pRunfood['name'] ?? $sku, $sku);
            $matched[] = ['cod_producto' => $producto['cod_producto'], 'sku' => $sku, 'name' => $pRunfood['name'] ?? $sku];
        }

        return ['matched' => count($matched), 'productos' => $matched];
    }
    
    function setIngrediente($office_id, $cod_ingrediente, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_ingredientes_facturacion WHERE cod_ingrediente = $cod_ingrediente AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_ingredientes_facturacion(id, cod_ingrediente, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', $cod_ingrediente, '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
    }
    
    function setRecipiente($office_id, $cod_recipiente, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_recipientes_facturacion WHERE cod_recipiente = $cod_recipiente AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_recipientes_facturacion(id, cod_recipiente, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', $cod_recipiente, '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
    }
    
    function setFormaPago($office_id, $cod_forma_pago, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_formas_pago_facturacion WHERE cod_forma_pago = '$cod_forma_pago' AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_formas_pago_facturacion(id, cod_forma_pago, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', '$cod_forma_pago', '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
    }

    function setOpcion($office_id, $cod_producto, $id_contifico, $contifico_name, $es_principal = 0, $sku = null){
        $query = "DELETE FROM tb_productos_opciones_detalle_facturacion WHERE cod_producto_opciones_detalle = $cod_producto AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);

        $es_principal = $es_principal ? 1 : 0;
        $skuValue = $sku ? "'" . $sku . "'" : "NULL";
        $query = "INSERT INTO tb_productos_opciones_detalle_facturacion(id_runfood, sku, cod_producto_opciones_detalle, nombre_runfood, cod_contifico_empresa, es_principal)
    		        VALUES('$id_contifico', $skuValue, $cod_producto, '$contifico_name', $office_id, $es_principal)";
    	return Conexion::ejecutar($query,NULL);
    }
    
    function setDomicilioAdicional($office_id, $alias, $cod_empresa, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_productos_envio_facturacion WHERE alias = '$alias' AND cod_contifico_empresa = $office_id AND cod_empresa = $cod_empresa";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_productos_envio_facturacion(id, alias, cod_empresa, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', '$alias', $cod_empresa, '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
    }
    
    
    

    /*PRODUCTOS*/
	public function LstProductos(){
		$ch = curl_init($this->URL."/products");
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'X-Api-Key: ' . $this->apiKey;

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);


        $msg = "";
        if($this->curlErrors($ch, $response, $msg)){
            curl_close($ch);
		    return json_decode($response,true);
        }else{
            $this->msgError = $msg;
            return false;
        }
	}

	public function lstFormasPago(){
		$ch = curl_init($this->URL."/payment-methods");
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'X-Api-Key: ' . $this->apiKey;

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

        
        $msg = "";
        if($this->curlErrors($ch, $response, $msg)){
            curl_close($ch);
		    return json_decode($response,true);
        }else{
            $this->msgError = $msg;
            return false;
        }
	}

    public function armarTrama($data = null){
        $trama = null;
        if($data !== null){
            $trama['data'] = $data;
        }
        $trama['tablet']['usuario'] = $this->userId;
        return json_encode($trama);
    }

    public function curlErrors($ch, $response, &$msgError){
        if($response === false){
            $msgError = "Curl error: " . curl_error($ch);
            return false;
        }else{
            $info = curl_getinfo($ch);
            $httpcode = $info['http_code'];
            $codeInt = intval($httpcode / 100);
            if($codeInt === 2)
                return true;
            else{
                $msgError = "Error ".$httpcode;
                return false;
            }    
        }
    }
}