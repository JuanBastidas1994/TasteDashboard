<?php
class cl_runfood
{
    public $URL = "";
    public $userId = "";
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
        $query = "SELECT s.cod_sucursal, s.nombre, s.direccion, rs.cod_runfood_sucursal, rs.dominio, rs.usuario_id, rs.facturar
                FROM tb_sucursales s
                LEFT JOIN tb_runfood_sucursal rs ON s.cod_sucursal = rs.cod_sucursal
                WHERE s.cod_sucursal = $cod_sucursal
                AND s.estado IN ('A', 'I')";
        $sucursal = Conexion::buscarRegistro($query);       
        if($sucursal){
            $this->URL = $sucursal['dominio'];
            $this->userId = $sucursal['usuario_id'];
        }
        return $sucursal;
    }

    public function setSucursal($cod_sucursal, $dominio, $usuario_id){
        $query = "INSERT INTO tb_runfood_sucursal(cod_sucursal, dominio, usuario_id, facturar)
                VALUES($cod_sucursal, '$dominio', $usuario_id, 0)";
        return Conexion::ejecutar($query,NULL);	
    }
    
    public function getAllProductsByOffices($cod_sucursal){
        global $session;
        $cod_empresa = $this->cod_empresa;
        $dir = url_sistema.'assets/empresas/'.$session['alias'].'/';
        $query = "SELECT p.cod_producto, p.nombre, p.precio, p.image_min, p.cod_producto_padre, pf.id, pf.name_in_contifico, pf.cod_sistema_facturacion 
                FROM tb_productos p
                LEFT JOIN tb_productos_facturacion pf ON p.cod_producto = pf.cod_producto AND pf.cod_contifico_empresa = $cod_sucursal
                WHERE p.cod_empresa = $cod_empresa
                AND p.estado IN ('A', 'I')";
        $resp = Conexion::buscarVariosRegistro($query);
        foreach($resp as $key => $item){
            $resp[$key]['image_min'] = $dir.$item['image_min'];
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
    
    
    function setProduct($office_id, $cod_producto, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_contifico_empresa = $office_id";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_productos_facturacion(id, cod_producto, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', $cod_producto, '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
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
    
    function setDomicilioAdicional($office_id, $alias, $cod_empresa, $id_contifico, $contifico_name){
        $query = "DELETE FROM tb_productos_envio_facturacion WHERE alias = '$alias' AND cod_contifico_empresa = $office_id AND cod_empresa = $cod_empresa";
        Conexion::ejecutar($query,NULL);	
        
        $query = "INSERT INTO tb_productos_envio_facturacion(id, alias, cod_empresa, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
    		        VALUES('$id_contifico', '$alias', $cod_empresa, '$contifico_name', 3, $office_id)";
    	return Conexion::ejecutar($query,NULL);	
    }
    
    
    

    /*PRODUCTOS*/
	public function LstProductos(){
		$ch = curl_init($this->URL."/Articulo/Fetch");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->armarTrama());
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
		$ch = curl_init($this->URL."/FORMAPAGO/Fetch");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->armarTrama());
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