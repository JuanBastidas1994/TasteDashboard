<?php
/*
App Code Client : TPP3-EC-CLIENT
App Key Client :  ZfapAKOk4QFXheRNvndVib9XU3szzg

App Code Server : TPP3-EC-SERVER
App Key Server : JdXTDl2d0o0B8ANZ1heJOq7tf62PC6 
*/

function getAuth($cod_orden, $cod_empresa=0){
    $URL = "";
    $SERVER_APP_CODE = "";
    $SERVER_APP_KEY = "";
    
    if($cod_empresa == null || $cod_empresa == 0)
    {
        $session = getSession();
        $cod_empresa = $session['cod_empresa'];
    }
    $query = "SELECT * FROM tb_empresa_paymentez WHERE cod_empresa = ".$cod_empresa;
    $resp = Conexion::buscarRegistro($query);
    if($resp){
        $ambiente = $resp['ambiente'];
        file_put_contents("url.log", PHP_EOL . $ambiente, FILE_APPEND);
        if($ambiente == "production")
            $URL = 'https://ccapi.paymentez.com/v2/';
        else
            $URL = 'https://ccapi-stg.paymentez.com/v2/';
        
        $SERVER_APP_CODE = $resp['server_code'];
        $SERVER_APP_KEY = $resp['server_key'];
    }
    
    $query = "SELECT s.* FROM tb_orden_cabecera c, tb_empresa_sucursal_paymentez s WHERE c.cod_sucursal = s.cod_sucursal AND c.cod_orden = ".$cod_orden;
    $resp = Conexion::buscarRegistro($query);
    if($resp){
        $ambiente = $resp['ambiente'];
        if($ambiente == "production")
            $URL = 'https://ccapi.paymentez.com/v2/';
        else
            $URL = 'https://ccapi-stg.paymentez.com/v2/';
            
        $SERVER_APP_CODE = $resp['server_code'];
        $SERVER_APP_KEY = $resp['server_key'];
    }
    
    define('URL',$URL);
    define('SERVER_APPLICATION_CODE', $SERVER_APP_CODE);
    define('SERVER_APP_KEY', $SERVER_APP_KEY);
    
    file_put_contents("url.log", PHP_EOL . URL, FILE_APPEND);
    file_put_contents("url.log", PHP_EOL . SERVER_APPLICATION_CODE, FILE_APPEND);
    file_put_contents("url.log", PHP_EOL . SERVER_APP_KEY, FILE_APPEND);
	$server_application_code = SERVER_APPLICATION_CODE;
	$server_app_key = SERVER_APP_KEY;
	$date = new DateTime();
	$unix_timestamp = $date->getTimestamp();
	// $unix_timestamp = "1546543146";
	$uniq_token_string = $server_app_key.$unix_timestamp;
	$uniq_token_hash = hash('sha256', $uniq_token_string);
	$auth_token = base64_encode($server_application_code.";".$unix_timestamp.";".$uniq_token_hash);
	return $auth_token;
}

function refund($id, $cod_orden, &$mensaje){
	$user['id'] = $id;
	$data['transaction'] = $user;
	$data = json_encode($data);

	$auth = getAuth($cod_orden);
	file_put_contents("url.log", PHP_EOL . URL, FILE_APPEND);
	file_put_contents("url.log", PHP_EOL . SERVER_APPLICATION_CODE, FILE_APPEND);
    file_put_contents("url.log", PHP_EOL . SERVER_APP_KEY, FILE_APPEND);
	$ch = curl_init(URL.'transaction/refund/');
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
	    $error_msg = curl_error($ch);
	    echo $error_msg;
	}
    curl_close($ch);

    file_put_contents("url.log", PHP_EOL . $data, FILE_APPEND);
    file_put_contents("url.log", PHP_EOL . $response, FILE_APPEND);
    $anula = json_decode($response, true);
    
    
    if(isset($anula['status'])){
        $status = $anula['status'];
        $query = "INSERT INTO tb_orden_devolucion(id,fecha,estado,respuesta) VALUES('$id',NOW(),'$status','$response')";
    	Conexion::ejecutar($query,NULL);
        
        if($status == "success"){
            $mensaje = "Devolucion de Dinero exitosa";
        	return true;
        }else{
        	$mensaje = $anula['detail'];
        	return false;
        }
    }else{
        if(isset($anula['error'])){
            $mensaje = $anula['error']['type']." - ".$anula['error']['description'];
        	return false;
        }else{
            $mensaje = "Error desconocido";
        	return false;
        }
    }
    
    
}


function getTransaction($id, $cod_orden, &$mensaje){
    $auth = getAuth($cod_orden);

    $ch = curl_init(URL.'transaction/'.$id);
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
	    $error_msg = curl_error($ch);
	    echo $error_msg;
	}
    curl_close($ch);
    
    $mensaje ="Mensaje por defecto";
    $transaction = json_decode($response, true);
    if(isset($transaction['transaction']['status'])){
        if($transaction['transaction']['status'] == "success"){
            $mensaje = "Informacion de la orden correcta";
        	return $transaction;
        }else{
        	$mensaje = $transaction['detail'];
        	return false;
        }
    }else{
        if(isset($transaction['error'])){
            $mensaje = $transaction['error']['type']." - ".$transaction['error']['description'];
        	return false;
        }else{
            $mensaje = "Error desconocido";
        	return false;
        }
    }
}

function verifyApi($cod_empresa){
	  
	$auth = getAuth(0,$cod_empresa);
	$ch = curl_init("https://noccapi-stg.paymentez.com/banks/PSE/");
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
   // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
  // return $auth;
}


/*FUNCIONES DEL DASHBOARD NO POR EMPRESA*/
function listCardsById($id){
    $auth = getAuth(0,1);
	$ch = curl_init(URL.'card/list?uid='.$id);
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
   // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    
    $response = curl_exec($ch);
    if(curl_exec($ch) === false)
    {
        echo 'Curl error: ' . curl_error($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

function deleteCardUser($id, $token){
   $data['card']['token'] = $token;
    $data['user']['id'] = $id;
	$json = json_encode($data);
	
    $auth = getAuth(0,1);
	$ch = curl_init(URL.'card/delete');
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    
    $response = curl_exec($ch);
    if(curl_exec($ch) === false)
    {
        echo 'Curl error: ' . curl_error($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

function debitByToken($id, $correo, $monto, $descripcion, $token){
    $data['user']['id'] = $id;
    $data['user']['email'] = $correo;
    
    $iva = $monto*0.12;
    $total = $monto + $iva;
    
    $data['order']['description'] = $descripcion;
    $data['order']['dev_reference'] = $descripcion;
    $data['order']['amount'] = (float)number_format($total,2);
    $data['order']['vat'] = (float)number_format($iva,2);
    $data['order']['taxable_amount'] = (float)$monto;
    $data['order']['tax_percentage'] = 12;
    
    $data['card']['token'] = $token;
	$json = json_encode($data);
	echo 'JSON: '.$json;
	
    $auth = getAuth(0,1);
	$ch = curl_init(URL.'transaction/debit/');
    $headers = array();
    $headers[] = 'Auth-Token: '.$auth;
    $headers[] = 'Content-Type: application/json';
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    
    $response = curl_exec($ch);
    if(curl_exec($ch) === false)
    {
        echo 'Curl error: ' . curl_error($ch);
        return false;
    }
    curl_close($ch);
    echo '<br/>-----------RESPUESTA PAYMENTEZ JSON--------------<br/>';
    echo $response;
    return json_decode($response, true);
}

function setDebitLog($cod_empresa, $monto, $card, &$id){
    $fecha = fecha();
    $type = $card['type'];
    $number = $card['number'];
    $token = $card['token'];
    $query = "INSERT INTO mie_log_pago(cod_empresa, fecha, monto, card_type, card_number, card_token, estado) 
            VALUES($cod_empresa,'$fecha','$monto', '$type', '$number', '$token', 'INTENTO_PAGO')";
	if(Conexion::ejecutar($query,NULL)){
	    $id = Conexion::lastId();
	    return true;
	}else
	    return false;
}

function putDebitLogSuccess($id, $transaction){
    $tid = $transaction['id'];
    $status = $transaction['status'];
    $reference = $transaction['dev_reference'];
    $autorizacion = $transaction['authorization_code'];
    $query = "INSERT INTO mie_log_pago_success(cod_mie_log_pago, transaction_id, transaction_status, transaction_reference, transaction_autorizacion) 
            VALUES($id,'$tid','$status', '$reference', '$autorizacion')";
	Conexion::ejecutar($query,NULL);
	
	$query = "UPDATE mie_log_pago SET estado='SUCCESS' WHERE cod_mie_log_pago = $id";
	Conexion::ejecutar($query,NULL);
	
	return true;
}

function putDebitLogError($id, $errores){
    $desc = $errores['description'];
    $json = json_encode($errores);
    $query = "INSERT INTO mie_log_pago_error(cod_mie_log_pago, descripcion, json) 
            VALUES($id,'$desc','$json')";
	Conexion::ejecutar($query,NULL);
	
	$query = "UPDATE mie_log_pago SET estado='FAILURE' WHERE cod_mie_log_pago = $id";
	Conexion::ejecutar($query,NULL);
	
	return true;
}

