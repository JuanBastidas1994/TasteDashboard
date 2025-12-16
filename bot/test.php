<?php
require_once "../funciones.php";
//Clases
require_once "cl_telegram.php";

error_reporting(E_ALL);

$token= "791140271:AAF62ruO7KGMEwrUUFq9q2PJAGcbJoYeLjQ";
$chat_id = -1001513069610;
$chat_id = "947549392";

$telegram = new cl_telegram($token);


$resp = $telegram->sendImage($chat_id, 'https://dashboard.mie-commerce.com/assets/empresas/danilo/product_2020_12_08_19_32_11.jpg','Cazuela de Pescado');
var_dump($resp);

/*
$resp = $telegram->sendContact($chat_id, '0979393146','Juan Bastidas');
var_dump($resp);*/

/*
$option[0] = "Muy Malo";
$option[1] = "Malo";
$option[2] = "Regular";
$option[3] = "Bueno";
$option[4] = "Muy Bueno";
$resp = $telegram->sendPoll($chat_id, 'Califica nuestro servicio',json_encode($option));
var_dump($resp);*/


/*
$media[0]['type'] = "photo";
$media[0]['media'] = "https://dashboard.mie-commerce.com/assets/empresas/watches-premiun/galery-3281-2021_09_22_12_22_59.jpg";
$media[1]['type'] = "photo";
$media[1]['media'] = "https://dashboard.mie-commerce.com/assets/empresas/watches-premiun/galery-3281-2021_09_22_12_22_49.jpg";
$media[2]['type'] = "video";
$media[2]['media'] = "https://dashboard.mie-commerce.com/assets/empresas/watches-premiun/galery-3281-2021_09_22_12_23_45.mp4";
$media[3]['type'] = "video";
$media[3]['media'] = "https://dashboard.mie-commerce.com/assets/empresas/watches-premiun/galery-3281-2021_09_22_16_02_30.mp4";
echo json_encode($media).'<br/>';
$resp = $telegram->sendMediaGroup($chat_id, json_encode($media));
var_dump($resp);*/
?>