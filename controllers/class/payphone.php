<?php
//LAB2DESIGN
//$GLOBALS['API'] = "Bearer 4hiVTyw9oyoG3Yede0lzp87U7yhkaUWb19h_Oy4LZfZ0GjGX0o1NOibYk0eQurArBSyCm62wQ1DTQ-hm8m5m1PM4t2otqUC6RpPMKtTptOXo3WaMKz2o8o_RgyHSoK1ei6qU6HSxvpmfXpfTLK0VoJ2UUEOOiYDmIOuucOQCTaSeCFsmFts_4wA-yDqy7mSKPYXr749UU8ZUX8uI802sIY32hbRI9ro13ehrV26rzVVqxwImgHUtnhFIuxi8kN0BiYyWb2gZvfpkNNv9-lp3vMyPC3wkPS5rcbVOXIET1_7dMLxGeOZl_fKJemxjpZVI_urn23J92x4LKradRy37pw";

//MACROGRAM
//$GLOBALS['API'] = "Bearer FPu6Wy9r1wlNaSJOPJYkmiXvk643hZUe-vPywThK8B-94WJloOxqKYONLXUMu52mPMiV6mn1faRw08qjUvg7Uvj4XGEyeYPYRazsvKmny0T9K3df9n4V1xV0r27lniizq0Plf851ahhJ4fr2WaaAApDsUxgeZw-lkhZgC6VIZ0j2A6HIPuRyks75YRFXdOj8jehA9j6t3mJ-YgqSjlakN7CzN84cL4ION3p0cFpagoRGXCYgMFRwuUvCH4R6H-6mmQEbN7Qx7ueH37CCGkvkrt56H29epHFKFr9Gxj5jt9Bjtyg_YzT-WaH1KfGijjuM6EcoUrO4TaUkHA9FhldRDw";

//POPUP
//$GLOBALS['API'] = "Bearer wIa3qZL3JvpNR9mm_tPTC2fDszBjes7DnVOZh0Sbcu5GlxN43NA6dV0YN279hchcOhe-UOCyG-JHesBvaITH65JVfvXdh4kp5RGF_nWJfq0JSHV5dQIAd3YOptznfD4d21gMmm71sOP5rmzfwfsk0pTHvmyvOKHMQOzLPS8C-C5W8SeQyqJ9Rn5kSeCKpFX2tF2uOH2GXvOFWfZi0t94zWSepR1AiQVli0xu5jbwRxhHDU7sdZIuDkzvP5K5r2VNkCn3_MhNRAVt7MUNGPl7s7dyqnwaOUchoFkhLpbIDZ9Bc_DSsISIWgvQwZYwvOYrWIbgMtBsQc254UsufM6Q8w";

//PENTAX CENTER
$GLOBALS['API'] = "Bearer 6Dpjz6sfIcQFRfQI-t93LAdqAMAeFyOSvidjT23Tl1w3O4l-eToIZAIdrBgdL1MUhZfFzZj7wHP1CtTNC8FMtobc6rHpsVelAtKe6EycY5qVXDQu4ujrWjgg9oV2a0-o3AsWWhiNjPj_bAQj6DzFXQDJM0_nQgDzN6t7BHagVdly452ePAxTutsOTf4PzhSf01oWT2Iw50IHnh5mUz2NjR-yAvXkKrhgfa27QwA_NzCan8mZ-XuMpdVwJqcd7bSc5o-ZUTAfo9t4Se_3xSSq6NfxFaozZjA6eGMc70Ua3Cb9grQkMIiv1SEX5-eIZMEkXgjJ4A";

function LstRegiones()
{
	$API = $GLOBALS['API'];
    $ch = curl_init("https://pay.payphonetodoesposible.com/api/Regions");
    $json = NULL;
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Authorization: '.$API; // key here
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function PayWithCart($cardNumber, $mes, $year, $CVV, $impuestos, $subtotal, $total, $phone, $email, $Id)
{
    $impuestos = $impuestos*100;
    $subtotal = $subtotal*100;
    $total =$total*100;

	$API = $GLOBALS['API'];
	$pago['number'] = base64_encode($cardNumber);			//(Número de tarjeta codificado en base64)º
	$pago['expirationMonth'] = base64_encode($mes);		     //(Mes de expiración de la tarjeta codificado en base64) 
	$pago['expirationYear'] = base64_encode($year);		     //(Año de expiración de la tarjeta codificado en base64) 
	$pago['verificationCode'] = base64_encode ($CVV);		//(Código de seguridad de la tarjeta codificado en base64) 
	$pago['amount'] = $total;								//(Valor con impuestos y todo.)
	$pago['amountWithTax'] = $subtotal;      				//(productos que si cobran impuesto pero con el valor sin el impuesto) 
	//$pago['amountWithoutTax'] = 0;						//(productos que no cobran impuestos) 
	$pago['tax'] = $impuestos;     							//(Valor total de los impuestos)
	//$pago['service'] = 0;									//(Valor total del servicio) 
	//$pago['tip'] = 0;										//(Valor total de la propina)
	$pago['clientTransactionId'] = $Id;			        //(Identificador de la transacción en la aplicación del cliente)	Lo generamos de nuestro lado para darle seguimiento.
	//$pago['storeId'] = $StoreId;							//(Id del Store que va a cobrar) No es Obligatorio
    //$pago['storeId'] = 'fd8bd908-3325-455a-842c-7632de2565a8';
	//$pago['terminalId'] = "";								//(Id del terminal que va a cobrar) No es obligatorio
	$pago['currency'] = "USD";								//(Moneda con la que se cobra, ejemplo “USD”)
	$pago['phoneNumber'] = $phone;							//(Número de teléfono del cliente en formato internacional) 
	$pago['email'] = $email;								//(Correo electrónico del cliente)
	//$pago['optionalParameter'] = "";						//(Parámetro opcional)

	$ch = curl_init("https://pay.payphonetodoesposible.com/api/transaction/Create");
    $json = json_encode($pago);
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$API; // key here
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function Reverse($idTransaccion)
{
	$API = $GLOBALS['API'];
	$reverse['id'] = $idTransaccion;			//id de la transaccion

	$ch = curl_init("https://pay.payphonetodoesposible.com/api/Reverse");
    $json = json_encode($reverse);
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$API; // key here
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function ReverseClient($idCliente)
{
	$API = $GLOBALS['API'];
	$reverse['clientId'] = $idCliente;			//id de la transaccion

	$ch = curl_init("https://pay.payphonetodoesposible.com/api/Reverse");
    $json = json_encode($reverse);
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$API; // key here
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

//DATOS TARJETA.
//4349620013560005
//04 18
//CVV 670 

//{"cardToken":"bc465456-3e37-4621-86d6-45c542396cbc","authorizationCode":"046361","messageCode":0,"status":"Approved","statusCode":3,"transactionId":223885,"clientTransactionId":"8912"}"
//string(184) "{"cardToken":"bc465456-3e37-4621-86d6-45c542396cbc","authorizationCode":"046961","messageCode":0,"status":"Approved","statusCode":3,"transactionId":223915,"clientTransactionId":"8913"}"


//PayWithCart($cardNumber, $mes, $year, $CVV, $impuestos, $subtotal, $total, $phone, $email)
//reverse solo responde true o false

//var_dump(LstRegiones());
//var_dump(PayWithCart("5144400023309006", "03", "20", "512", 0.12, 0.88, 1, "0979393146", "jbastidas@saro-ec.com", "fd8bd908-3325-455a-842c-7632de2565a8"));
//var_dump(Reverse("237758"));

//fd8bd908-3325-455a-842c-7632de2565a8 ->  StoreId Urdesa.
//339875ad-e762-416a-bcbb-16f4dc827530 ->  StoreId Samborondon.






?>