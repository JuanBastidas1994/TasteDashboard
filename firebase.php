<?php
function set(){
	$ProyectId = "ptoventa-3b5ed";
        $data = '{"estado":"ENTRANTE","id":47,"sucursal":1}';
		$ch = curl_init("https://".$ProyectId.".firebaseio.com/ordenes/47.json");
      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        $response = curl_exec($ch);
        if(curl_errno($ch)){
        	return curl_errno($ch);
        }
        curl_close($ch);
        return $response;
}

	$resp = set();
	var_dump($resp)

?>