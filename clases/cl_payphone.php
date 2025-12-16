<?php

class cl_payphone
{
		var $API = "Bearer dO6TxX4ITekNCN_acPIjcCSIRvrvB7LhozJBrrUsgFAM3g8ORZlCNH3s-Nb1mtZz496Y7-T2tFTqqDS82-A967fDrQ9GkekO9lG9izu7voehmAROq4C5uFh72i3R6dw_dkL7UYF4GMNSKZWZoctgZKvepp0MHbjQApE1ToDEej8mJ6Af01XyWneVybtPucNtv27dCRll2TkY11GPqPydTSskCeNDxIAZoePGJ8nvv5RXhQ_mBmQehB8EscxwF5gESPig6ki-C6KiBSfGA_3KyrsAEjLAc1hFGBr4_yOlBIKZqYCCrgQ2r6iKeIEjlUshAyjuxw";

		var $session;
		var $cod_empresa;

		var $cod_usuario, $cedula, $titular, $cardNumber, $mes, $year, $CVV, $impuestos, $subtotal, $total, $phone, $email;
		var $clientTransactionId;
		
		public function __construct($pcod_producto=null)
		{
			if($pcod_producto != null)
				$this->cod_producto = $pcod_producto;
			$this->session = getSession();
		}

		public function lst_regiones(){
			$ch = curl_init("https://pay.payphonetodoesposible.com/api/Regions");
		    $json = NULL;
		    $headers = array();
		    $headers[] = 'Accept: application/json';
		    $headers[] = 'Authorization: '.$this->API; // key here
			
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $response = curl_exec($ch);
		    curl_close($ch);
		    return $response;
		}

		public function PayCart(&$mensaje){
			$clientTransactionId = 0;
			if(!$this->createTransaction($clientTransactionId)){
				$mensaje = "No se pudo crear un codigo de transaccion";
				return false;
			}
			$this->clientTransactionId = $clientTransactionId;
			$this->impuestos = $this->impuestos * 100;
			$this->subtotal = $this->subtotal*100;
    		$this->total = $this->total*100;

    		$pago = $this->ArraySendPay();
			$ch = curl_init("https://pay.payphonetodoesposible.com/api/transaction/Create");
		    $json = json_encode($pago);
		    $this->Logs('ENVIO','Datos Enviados', $json, 'S');

		    $headers = array();
		    $headers[] = 'Accept: application/json';
		    $headers[] = 'Content-Type: application/json';
		    $headers[] = 'Authorization: '.$this->API; // key here
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		    $payphoneJson = curl_exec($ch);
		    curl_close($ch);

		    $payphoneResp = json_decode($payphoneJson,true);
    		if (JSON_ERROR_NONE !== json_last_error()){
    			$this->ErrorPayCart("No hubo JSON de respuesta");
    			return false;
    		}else{
    			if(isset($payphoneResp['errors']) || isset($payphoneResp['message'])){
    				if(isset($payphoneResp['errors']))
	    			{
	    				if(count($payphoneResp['errors'])>0)
	    					for($x=0; $x<count($payphoneResp['errors']); $x++){
	    						$mensaje .= $payphoneResp['errors'][$x]['message']." - ".$payphoneResp['errors'][$x]['errorDescriptions'][0];
	    					}
	    				else
	    					$mensaje = "Error en querer realizar el pago, por favor intentelo mas tarde.";	
	    			}
	    			else if(isset($payphoneResp['message']))
	    			{
	    				$mensaje = $payphoneResp['message'];
	    			}
	    			else
	    			{
	    				$mensaje = "Error al pagar";
	    			}

    				$this->ErrorPayCart($payphoneJson);
    				return false;
    			}else{
    				if($payphoneResp['status']=="Approved")
	    			{
	    				$this->SuccessPayCar($payphoneJson);
	    				return true;
	    			}else{
	    				$this->ErrorPayCart($payphoneJson);
	    				return false;
	    			}
    			}
    		}

		    return false;
		}

		function SuccessPayCar($json){
			$this->Logs('RESPUESTA','Payphone SI cobro', $json, 'S');
		}

		function ErrorPayCart($json){
			$this->Logs('RESPUESTA','Error cobro payphone', $json, 'E');
		}

		function createTransaction(&$cod_transaccion){
			$cod_transaccion = $this->code().$this->cod_usuario;
			$query = "insert into tb_transaccion(cod_transaccion, cod_usuario, fecha, estado, monto) values('$cod_transaccion','$this->cod_usuario',NOW(), 'NEGADO', 0)";
			if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		function Logs($tipo,$detalle,$respJson,$estado){
			$query = "INSERT INTO log_pagos(cod_usuario,tipo,detalle,mensaje_resp,estado_resp,fecha,cod_transaccion)";
			$query.= "VALUES($this->cod_usuario,'$tipo','$detalle','$respJson','$estado',NOW(),'$this->clientTransactionId')";
			if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		function ArraySendPay(){
			$pago['number'] = base64_encode($this->cardNumber);			//(Número de tarjeta codificado en base64)
			$pago['expirationMonth'] = base64_encode($this->mes);		     //(Mes de expiración de la tarjeta codificado en base64) 
			$pago['expirationYear'] = base64_encode($this->year);		     //(Año de expiración de la tarjeta codificado en base64) 
			$pago['verificationCode'] = base64_encode ($this->CVV);		//(Código de seguridad de la tarjeta codificado en base64) 
			$pago['documentId'] = base64_encode($this->cedula);			//(Número de cedula codificado en base64)
			$pago['cardHolder'] = base64_encode($this->titular);			//(Nombre del cedula codificado en base64)
			
			$pago['amount'] = $this->total;								//(Valor con impuestos y todo.)
			$pago['amountWithTax'] = $this->subtotal;      				//(productos que si cobran impuesto pero con el valor sin el impuesto) 
			//$pago['amountWithoutTax'] = 0;						//(productos que no cobran impuestos) 
			$pago['tax'] = $this->impuestos;     							//(Valor total de los impuestos)
			//$pago['service'] = 0;									//(Valor total del servicio) 
			//$pago['tip'] = 0;										//(Valor total de la propina)
			$pago['clientTransactionId'] = $this->clientTransactionId;	//(Identificador de la transacción en la aplicación del cliente)	Lo generamos de nuestro lado para darle seguimiento.
			//$pago['storeId'] = $StoreId;							//(Id del Store que va a cobrar) No es Obligatorio
		    //$pago['storeId'] = 'fd8bd908-3325-455a-842c-7632de2565a8';
			//$pago['terminalId'] = "";								//(Id del terminal que va a cobrar) No es obligatorio
			$pago['currency'] = "USD";								//(Moneda con la que se cobra, ejemplo “USD”)
			$pago['phoneNumber'] = $this->phone;							//(Número de teléfono del cliente en formato internacional) 
			$pago['email'] = $this->email;								//(Correo electrónico del cliente)
			//$pago['optionalParameter'] = "";						//(Parámetro opcional)
			return $pago;
		}

		function code()
		{
		  date_default_timezone_set('America/Guayaquil');
		  $time = time();
		  $fecha = date("YmdHis", $time);  //FECHA Y HORA ACTUAL
		  return $fecha;
		}
}
?>