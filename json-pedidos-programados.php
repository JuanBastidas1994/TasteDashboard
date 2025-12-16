<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";

$Clordenes = new cl_ordenes(NULL);

$data = [];

if(!isLogin()){
    header("Content-Type:Application/json");
    echo json_encode($data);
}

if(isset($_GET['start'])){
    $start = $_GET['start'];
}else
    $start = fecha();

if(isset($_GET['end'])){
    $end = $_GET['end'];
}else
    $end = fecha();

$resp = $Clordenes->listaProgramados($start, $end);

foreach($resp as $orden){
    $start = str_replace(" ","T", $orden['hora_retiro']);
    $end = str_replace(" ","T",AddIntervalo($orden['hora_retiro'], '00:30:00'));
    $item['id'] = $orden['cod_orden'];
    $item['title'] = $orden['nombre']." ".$orden['apellido']." - $".$orden['total']; 
	$item['start'] = $start;
	$item['end'] = $end;
	$item['editable'] = false;
	
	if($orden['is_envio'] == 1){
	    $item['className'] = "bg-primary";
	    $item['description'] = $orden['referencia'];
	}else{
	    $item['className'] = "bg-danger";
	    $item['description'] = "Retirar en local";
	}    
	$data[]=$item;
}



header("Content-Type:Application/json");
echo json_encode($data);
?>