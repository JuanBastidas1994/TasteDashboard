<?php
require_once "funciones.php";
require_once "clases/cl_eventos.php";

$ClEventos = new cl_eventos(NULL);

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

$resp = $ClEventos->listaBetweenFechas($start, $end);

foreach($resp as $orden){
    $start = $orden['fecha']."T".$orden['hora_inicio'];
    $end = $orden['fecha']."T".$orden['hora_fin'];
    //$start = str_replace(" ","T", $orden['hora_retiro']);
    //$end = str_replace(" ","T",AddIntervalo($orden['hora_retiro'], '00:30:00'));
    $item['id'] = $orden['cod_agenda'];
    $item['title'] = $orden['titulo']; 
	$item['start'] = $start;
	$item['end'] = $end;
	$item['description'] = $orden['descripcion'];
	$item['editable'] = true;
	
	if($orden['color'] == 1){
	    $item['className'] = "bg-primary";
	}else{
	    $item['className'] = "bg-danger";
	}    
	$data[]=$item;
}



header("Content-Type:Application/json");
echo json_encode($data);
?>