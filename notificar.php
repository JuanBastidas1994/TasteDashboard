<?php
//TOKEN DASHBOARD
$token = "AAAA0f-fHSY:APA91bHI9F7EQuN4_HivAhfzmgFDGcH43kfaf314A4Jihe5tYsvpODYy2yL8nPNohyLDnYVX5xZnP7yjMTE7aNzfd2htGD8i6NkN7eCuqeh6FBPRMyyQT8y-HI6PyOwiaoO9RI9ablOK";

//TOKEN MOTORIZADO
//$token = 'AAAAzaKBlI4:APA91bEa7AOn_g5S-wmF7Yme6wfKL11Wf729SB-pJ1oyKdo8gEbK-gEXScv8i7MUFtefUPvLdy6cnKnkUqDPgUvPvbNJVMrIX4X6VbTgraSmSf8WNeeNBKGRksciD0CCOFnFNRSImGH3';

//TOKEN OAHU
//$token = 'AAAAzyv6Q_M:APA91bGjq9cYJZ-wLVQpDu6pcurHcu0dlc79Co66byniIt5FOZ6pP2REdQ3DOWSlxSSoT1QEYq71GXyNLlPpJKYu8DuBU7h_GeieyXSLZyDffcNRd_Oeb6bwPNUZ_3xTSDqAyXVS3xkm';

//TOKEN DANILO RESTAURANTE
$token = "AAAAfKVBkmc:APA91bH1YupHy-dAJSyT8aVhDbVQAnlqdjkX3qIMSltxpzM1W8qSDNG2g6Noz4z-wN2rh6jcN2XTMqYTLB33MhjxdqQ-BnU3yjfkQ5F_SzL0H8--JMvjkvRd_vinW5L4bz6zfwz7gmj3";

function sendNotify($titulo, $topic, $mensaje, $codigo, $tipo)
{
	global $token;
    if($topic == "")
        $topic = "general";
    
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    $data = array('title' =>$titulo , 'text' => $mensaje, 'message' => $mensaje, 'sound' => 'default', 'valor' => $codigo, 'tipo' => $tipo);
    $arrayToSend = array('to' => "/topics/".$topic, 'data' => $data, 'priority'=>'high');
    $json = json_encode($arrayToSend);
  
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key= '.$token; // key here
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   

    //Send the request
    $response = curl_exec($ch);

    //Close request
    curl_close($ch);
    return $response;
}


error_reporting(E_ALL);
$x = sendNotify("Nuevo pedido asignado", "bolon-city", "BolonCity te ha asignado un nuevo pedido a las 13:18, por favor recogerlo", "", "NOTIFICACION");
echo $x;
var_dump($x);
?>