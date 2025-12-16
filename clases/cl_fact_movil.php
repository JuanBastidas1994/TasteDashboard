<?php
class cl_fact_movil
{
    var $URL = "http://apptest.factureromovil.com/api";
	var $cod_empresa;
	var $msgError = "";
	var $errorToken = false;

	var $username, $password, $API;
	
	public function __construct($pcod_empresa = null)
	{
		if($pcod_empresa != null)
			$this->cod_empresa = $pcod_empresa;

		$this->getCredentials();
	}

	public function getCredentials()
	{
		$cod_empresa = $this->cod_empresa;
		$query = "SELECT * FROM tb_factmovil_empresa WHERE cod_empresa = ".$cod_empresa;
		$resp = Conexion::buscarRegistro($query);
		if($resp){
			$this->username = $resp['username'];
			$this->password = $resp['password'];
			if($resp['ambiente']=="production")
			    $this->URL = "https://app.factureromovil.com/api";
			$this->getToken();
		}
	}
	
	public function getToken(){
	    $data['_username'] = $this->username;
	    $data['_password'] = $this->password;
        $json = json_encode($data);
        
        $link = $this->URL.'/login_check';
        $ch = curl_init($link);
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        $response = curl_exec($ch);
        if($response === false){
            $this->msgError = "Curl error: " . curl_error($ch);
            return false;
        }
        curl_close($ch);
        $info = json_decode($response,true);
        if(isset($info['token']))
            $this->API = $info['token'];
		else{
			$this->errorToken = "Hay un problema con los tokens, por favor contactar con soporte."; 
			if(isset($info['message'])){
				$this->errorToken = "Hay un problema con los tokens, Explicación: ".$info['message'];
			}	
		}

        return true;
	}


    /*PRODUCTOS*/
	public function LstProductos(){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
		$ch = curl_init($this->URL."/productos");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'authorization: Bearer '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		if($response === false){
            $this->msgError = "Curl error: " . curl_error($ch);
            return false;
        }else{
            $respCurl = curl_getinfo($ch);
            if($respCurl['http_code'] != 200){
                $this->msgError = "Curl httpcode error: " . $respCurl['http_code'];
                var_dump($response);
                return false;
            }
        }
		
		curl_close($ch);
		return json_decode($response,true);
	}
	
	public function GetProducto($id){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
		$ch = curl_init($this->URL."/productos/".$id);
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'authorization: Bearer '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		if($response === false){
            $this->msgError = "Curl error: " . curl_error($ch);
            return false;
        }else{
            $respCurl = curl_getinfo($ch);
            if($respCurl['http_code'] != 200){
                if($respCurl['http_code'] == 204){
                    $return['message'] = "Producto no existe";
                    return $return;
                }    
                $this->msgError = "Curl httpcode error: " . $respCurl['http_code'];
                return false;
            }
        }
		
		curl_close($ch);
		return json_decode($response,true);
	}

	public function CreateProducto($iva, $pvp, $nombre, $sku, $bien, $id)
	{
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
	    $producto['tipoProducto'] = $bien;
	    $producto['tarifaIva'] = $iva;
	    $producto['codigoPrincipal'] = $sku; //SKU
	    $producto['descripcion'] = $nombre;
	    $producto['valorUnitario'] = $pvp;
	    $json = json_encode($producto);
	    
	    $ch = curl_init($this->URL."/productos");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'authorization: Bearer '.$this->API;
		
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
            if($respCurl['http_code'] == 500){
                $this->msgError = "Curl httpcode error: 500 ";
                return false;
            }
        }
        
		curl_close($ch);
	    return json_decode($response,true);
	}
	
	public function CreateCliente($cedula,$nombre,$email=""){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
	    $tipo = 5;
	    if(strlen($cedula)==10)
	        $tipo = 2;
	    else if(strlen($cedula)==13)
	        $tipo = 1;     
	        
	    
	    $cliente['identificacion'] = $cedula;
	    $cliente['tipoIdentificacion'] = $tipo;
	    $cliente['razonSocial'] = $nombre;
	    $cliente['email'] = $email;
	    $json = json_encode($cliente);
	    
	    $ch = curl_init($this->URL."/clientes");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'authorization: Bearer '.$this->API;
		
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
            if($respCurl['http_code'] == 500){
                $this->msgError = "Curl httpcode error: 500 ";
                return false;
            }
        }
        
		curl_close($ch);
	    return json_decode($response,true);
	}
	
	public function CreateFactura($esquema){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
	    $json = json_encode($esquema);
	    
	    $ch = curl_init($this->URL."/documentos/facturas");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'authorization: Bearer '.$this->API;
		
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
            if($respCurl['http_code'] == 500){
                $this->msgError = "Curl httpcode error: 500 ";
                return false;
            }
        }
        
		curl_close($ch);
	    return json_decode($response,true);
	}

	public function AnularFactura($esquema){
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
	    $json = json_encode($esquema);
	    
	    $ch = curl_init($this->URL."/documentos/notas/creditos");
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'authorization: Bearer '.$this->API;
		
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
            if($respCurl['http_code'] == 500){
                $this->msgError = "Curl httpcode error: 500 ";
                return false;
            }
        }
        
		curl_close($ch);
	    return json_decode($response,true);
	}
	
	public function GetFacturaByAutorizacion($id)
	{
	    if($this->API == ""){
	        $this->msgError = "Empresa no tiene configurado facturero movil";
	        return false;
	    }
	    
		$ch = curl_init($this->URL."/documentos/".$id."/clave/acceso");
		$json = NULL;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'authorization: Bearer '.$this->API;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		$response = curl_exec($ch);
		if($response === false){
            $this->msgError = "Curl error: " . curl_error($ch);
            return false;
        }else{
            $respCurl = curl_getinfo($ch);
            if($respCurl['http_code'] != 200){
                if($respCurl['http_code'] == 204){
                    $return['message'] = "Documento no existe";
                    return $return;
                }    
                $this->msgError = "Curl httpcode error: " . $respCurl['http_code'];
                return false;
            }
        }
		
		curl_close($ch);
		return json_decode($response,true);
	}
	
}
?>