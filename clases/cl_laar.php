<?php

class cl_laar
{
		var $URL = "https://api.laarcourier.com:9727/";
		var $cod_empresa = "";
		var $cod_sucursal = "";
		var $msgError = "";
		
		var $username, $password, $API;
		
		public function __construct($pcod_empresa=null, $pcod_sucursal=null)
		{
		    if($pcod_empresa != null)
			    $this->cod_empresa = $pcod_empresa;
			    
			if($pcod_sucursal != null)
			    $this->cod_sucursal = $pcod_sucursal;
			
			$this->URL = "https://api.laarcourier.com:9727/";
			if($this->cod_empresa != "" && $this->cod_sucursal != ""){
			    $this->getCredentials();
			}
		}
		
		public function getCredentials()
		{
    		$cod_empresa = $this->cod_empresa;
    		$cod_sucursal = $this->cod_sucursal;
    		$query = "SELECT * FROM tb_laar_sucursal WHERE cod_empresa = $cod_empresa AND cod_sucursal = $cod_sucursal";
    		$resp = Conexion::buscarRegistro($query);
    		if($resp){
    			$this->username = $resp['username'];
    			$this->password = $resp['password'];
    			$this->getToken();
    		}
    	}
    	
    	public function getToken(){
    	    $data['username'] = $this->username;
    	    $data['password'] = $this->password;
            $json = json_encode($data);
            
            $link = $this->URL.'/authenticate';
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
                $this->msgError = $info['Message'];
                return false;
            }    
            return true;
    	}
		
		public function crearGuia($orden,$sucursal){
		    if($this->API == ""){
    	        $this->msgError = "Empresa no tiene configurado Laar o esta fuera de servicio";
    	        return false;
    	    }
	    
		    require_once "cl_sucursales.php";
		    require_once "cl_ordenes.php";
            $Clsucursales = new cl_sucursales();
            $Clordenes = new cl_ordenes();
            
		    $InfoCiu=$Clsucursales->getInfoByCiudad($sucursal['cod_ciudad']);
            $codigo=$InfoCiu['codigo'];
            
		    //DATOS ORIGEN
		    $origen['identificacionO']="0950771907";
		    $origen['ciudadO']=$codigo;
		    $origen['nombreO']=$sucursal['nombre'];
		    $origen['direccion']=$sucursal['direccion'];
		    $origen['referencia']="";
		    $origen['numeroCasa']="";
		    $origen['postal']="";
		    $origen['telefono']="";
		    $origen['celular']=$sucursal['telefono'];
		    
		    $InfoDestino=$Clordenes->get_destino_orden($orden['cod_orden']);
		    
		    //DATOS DESTINO
		    $destino['identificacionD']=$orden['num_documento']; // opcional
		    $destino['ciudadD']=$InfoDestino['codigo'];
		    $destino['nombreD']=$orden['nombre'].' '.$orden['apellido'];
		    $destino['direccion']=$orden['referencia'];
		    $destino['referencia']= $orden['referencia2']; // opcional
		    $destino['numeroCasa']=$InfoDestino['num_casa'];
		    $destino['postal']=$InfoDestino['cod_postal'];
		    $destino['telefono']=""; // opcional
		    $destino['celular']=$orden['telefono_user'];
		    
		    $peso = 0;
		    $nPieza=0;
		    foreach ($orden['detalle'] as $d) {
                $peso = $d['peso']+$peso;
                $nPieza++;
            }
		    
		    $nPieza = 1;
		    //DATOS A ENVIAR
			$data['origen'] = $origen;
			$data['destino'] = $destino;
			$data['numeroGuia'] = ""; // opcional
			$data['tipoServicio'] ="201202002002013"; // carga 
			$data['noPiezas'] = $nPieza;
			$data['peso'] = $peso;
			$data['valorDeclarado'] =0; //opcional
			$data['contiene'] ="DECODIFICADOR";
			$data['tamanio'] ="";//opcional
			$data['cod'] = false;//opcional
			$data['costoflete'] = 0;//"si tiene valor de cod true el campo obligatorio"
			$data['costoproducto'] = 0;//"si tiene valor de cod true el campo obligatorio"
			$data['tipocobro'] = 0;//opcional
			$data['comentario'] = "";//opcional
			$data['fechaPedido'] = "";//opcional
			$json = json_encode($data);
            
			$ch = curl_init($this->URL."guias/contado");
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Authorization: Bearer '.$this->API;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($ch);
		    if($response === false){
                $this->msgError = "Curl error: " . curl_error($ch);
                return false;
            }else{
                //echo 'Operacion correcta';
            }
		    curl_close($ch);
		    
		    //var_dump($response);
		    
		    file_put_contents("LogsLaar.log", PHP_EOL . $json . PHP_EOL . $response, FILE_APPEND);
		    
		    return json_decode($response);
		    
		}
		
		public function crearGuiaPrueba(){
		        $curl = curl_init();
                
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.laarcourier.com:9727/guias/contado',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                    "origen": {
                        "identificacionO": "0950771907",
                        "ciudadO": "201001002001",
                        "nombreO": "Norte",
                        "direccion": "Guayacanes",
                        "referencia": "",
                        "numeroCasa": "",
                        "postal": "",
                        "telefono": "",
                        "celular": "0995322319"
                    },
                    "destino": {
                        "identificacionD": "0950771907",
                        "ciudadD": "201001002001",
                        "nombreD": "Lizbeth nacipucha",
                        "direccion": "sauces 3",
                        "referencia": "",
                        "numeroCasa": "",
                        "postal": "",
                        "telefono": "",
                        "celular": "0995322319"
                    },
                    "numeroGuia": "",
                    "tipoServicio": "2012020020091",
                    "noPiezas": 1,
                    "peso": 5,
                    "valorDeclarado": 0,
                    "contiene": "DECODIFICADOR",
                    "tamanio": "",
                    "cod": false,
                    "costoflete": 0,
                    "costoproducto": 0,
                    "tipocobro": 0,
                    "comentario": "",
                    "fechaPedido": ""
                }',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..HRkW6vONvyXNUWuuAVDzgxPg31kSjGppEKpme7tKo9Y'
                  ),
                ));
                
                $response = curl_exec($curl);
                if($response === false)
                    {
                        echo 'Curl errorc: ' . curl_error($curl);
                    }
                    else
                    {
                        echo 'Operacion completada sin errores '.$this->API;
                    }
                curl_close($curl);
                echo $response;
		}
		
		public function DataGuia($token){
			$curl = curl_init();
			
		

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.laarcourier.com:9727/guias/v2/'.$token,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json;charset=utf-8','User-Agent: PostmanRuntime/7.26.8'
              ),
            ));
            $response = curl_exec($curl);
            if($response === false)
            {
                echo 'Curl error: ' . curl_error($curl);
            }
            var_dump($response);
            curl_close($curl);
            
		    return $response;
		}
		
		public function costoEnvio($latitud, $longitud){
		    if($this->API == ""){
    	        $this->msgError = "Empresa no tiene configurado Laar o esta fuera de servicio";
    	        return false;
    	    }
    	    
			$data['codigoServicio'] = 201202002002013;
			$data['codigoCiudadOrigen'] = $latitud;
			$data['codigoCiudadDestino'] = $longitud;
			$data['piezas'] = $longitud;
			$data['peso'] = $longitud;
			$json = json_encode($data);

			$ch = curl_init($this->URL."cotizadores/tarifa/normal");
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Authorization: Bearer '.$this->API;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($ch);
		    if($response === false){
                $this->msgError = "Curl error: " . curl_error($ch);
                return false;
            }else{
                //echo 'Operacion correcta';
            }
		    curl_close($ch);
		    return json_decode($response);
		}
		
		public function webhooks_info(){
			$data['api_token_gacela'] = $this->API;
			
			$json = json_encode($data);

			$ch = curl_init($this->URL."webhooks/info");
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Authorization: Bearer '.$this->apiKey;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                 
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response);
		}


		

		public function informacion($cedula)
		{
		    $ch = curl_init("http://localhost/miecommerce/runfood/puntos/".$cedula);
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Api-Key: '.$this->api;

		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response);
		}

		public function calcular($cedula)
		{
		    $ch = curl_init("http://localhost/miecommerce/runfood/puntos/calcular/".$cedula);
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Api-Key: '.$this->api;

		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response);
		}
}
?>