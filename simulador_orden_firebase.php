<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
$Clordenes = new cl_ordenes(NULL);

function addOrdenFirebase($id, $sucursal){
    $alias = "bolon-city";
	$ProyectId = "ptoventa-3b5ed";
    $data = '{"estado":"ENTRANTE","id":'.$id.',"sucursal":'.$sucursal.'}';
    try {
    	$ch = curl_init("https://".$ProyectId.".firebaseio.com/ordenes/".$alias."/".$id.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        $response = curl_exec($ch);
        if(curl_errno($ch)){
        	return curl_errno($ch);
        }
        curl_close($ch);
        return $response;
    } catch (Exception $e) {
    	return false;
    }
}

$id = 763;
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $orden = $Clordenes->get_orden_array($id);
    if($orden){
        $cod_sucursal = $orden['cod_sucursal'];
        $resp = addOrdenFirebase($id, $cod_sucursal);
        var_dump($resp);
        echo '<br/>Orden Existente: Id:'.$id.' | Sucursal:'.$cod_sucursal;
    }else{
        echo 'Orden no existe';
    }
}else{
    echo 'No hay cod orden';
}
/*
$sucursal = 52;
$resp = addOrdenFirebase($id, $sucursal);
var_dump($resp);*/
?>