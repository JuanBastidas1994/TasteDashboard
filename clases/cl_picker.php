<?php

class cl_picker
{
		var $URL = "https://dev-api.pickerexpress.com/api/";
		var $apiKey = "";
		var $tokenSucursal = "";
		var $msgError = "";
		public function __construct($pcod_sucursal=null)
		{
			$this->getTokens($pcod_sucursal);
		}

		public function getTokens($cod_sucursal){
		    $query = "SELECT ps.cod_empresa, ps.cod_sucursal, ps.api, ps.ambiente, s.nombre
						FROM tb_picker_sucursal ps, tb_sucursales s
						WHERE ps.cod_sucursal = s.cod_sucursal
						AND ps.cod_sucursal = $cod_sucursal
						AND ps.estado = 'A'";
			$resp = Conexion::buscarRegistro($query);
			if($resp){
			    $this->apiKey = $resp['api'];
			    if($resp['ambiente'] == "development")
			        $this->URL = "https://dev-api.pickerexpress.com/api/";
    			else    
    			    $this->URL = "https://api.pickerexpress.com/api/";
			}else{
			    
			}
		}
		
		public function crearOrder($orden){
		    $nombre_completo = trim($orden['nombre']." ".$orden['apellido']);
		    $arrayNombre = explode(" ", $nombre_completo, 2);
		    
			$data['customerName'] = $arrayNombre[0];
			$data['customerLastName'] = ($arrayNombre[1]!=="" && $arrayNombre[1]!==null) ? $arrayNombre[1] : "Desconocido";
			$data['customerEmail'] = $orden['correo'];
			$data['customerCountryCode'] = "+593";
			$data['customerMobile'] =  substr($orden['telefono_user'], 1);
			//$data['customerMobile'] =  substr($orden['telefono_user'], 1);
			//$data['customerMobile'] =  $orden['telefono_user'];
			$data['address'] = $orden['referencia']." ".$orden['referencia2'];
			$data['latitude'] = $orden['latitud'];
			$data['longitude'] = $orden['longitud'];
			$data['zipCode'] = "0245";
			$total = number_format($orden['total'],2);
			$data['sendSMR'] = true;
			$data['orderAmount'] =$total;
			$data['businessDeliveryFee'] = number_format($orden['envio'],2);

			if($orden['pago'] == "E" || $orden['pago'] == "TC"){
				$envio = number_format($orden['envio'],2);
				$totalSinEnvio = $total - $envio;
				$data['orderAmount'] = number_format($totalSinEnvio,2);
				$data['paymentMethod'] = "CASH";
			}else{
				$data['paymentMethod'] = "CARD";
			}
			$json = json_encode($data);

			$ch = curl_init($this->URL."createBooking");
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Authorization: Bearer '.$this->apiKey;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                 
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    
		    file_put_contents("LogsPicker.log", PHP_EOL . $json . PHP_EOL . $response, FILE_APPEND);
		    
		    return json_decode($response);
		}
		
		public function cancelarOrder($token){
			$data['api_token_gacela'] = $this->tokenSucursal;
			
			$data['order_token'] = $token;
			$json = json_encode($data);

			$ch = curl_init($this->URL."cancel_order");
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
		
		public function trackingOrder($tokenOrden){
			$ch = curl_init($this->URL."getBookingDetails?bookingID=".$tokenOrden);
            $headers = array();
		    $headers[] = 'Content-Type: application/json';
            $headers[] = 'content-language: es';
		    $headers[] = 'Authorization: '.$this->apiKey;

		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");    
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response, true);
		}
		

		/*WEBHOOKS*/
		public function webhooks_info(){
			$ch = curl_init($this->URL."webhooks");
		    $headers = array();
		    $headers[] = 'Content-Type: application/json';
            $headers[] = 'content-language: es';
		    $headers[] = 'Authorization: '.$this->apiKey;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response);
		}

		public function order_status_update($api, $URL, $type){
			//"UPDATE_BOOKING_STATUS"
			//"DRIVER_ASSIGNED"
			$data['url'] = $URL;
			$data['type'] = $type;
			
			$json = json_encode($data);

			echo $this->URL;
			$ch = curl_init($this->URL."webhooks");
		    $headers[] = 'Content-Type: application/json';
            $headers[] = 'content-language: es';
		    $headers[] = 'Authorization: Bearer '.$api;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                 
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return json_decode($response);
		}

		public function order_status_update_ambiente($api, $URL, $type, $ambiente){
			if($ambiente == "development")
				$this->URL = "https://dev-api.pickerexpress.com/api/";
			else    
				$this->URL = "https://api.pickerexpress.com/api/";

			$data['url'] = $URL;
			$data['type'] = $type;
			
			$json = json_encode($data);

			$ch = curl_init($this->URL."webhooks");
		    $headers[] = 'Content-Type: application/json';
            $headers[] = 'content-language: es';
		    $headers[] = 'Authorization: Bearer '.$api;
		  
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                 
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    
		    return json_decode($response);
		}
}
?>