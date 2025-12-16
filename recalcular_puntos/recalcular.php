<?php
require_once "../funciones.php";

$page = 13;
$numRows = 100;
$init = ($page-1) * $numRows;
$query = "SELECT DISTINCT u.num_documento
        FROM tb_orden_puntos op, tb_orden_cabecera oc, tb_usuarios u
        WHERE op.cod_orden = oc.cod_orden
        AND oc.fecha > '2023-02-01'
        AND oc.cod_usuario = u.cod_usuario
        AND op.estado = 0
        AND oc.cod_empresa = 78
        ORDER BY num_documento DESC
        limit 0,".$numRows;
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $clientes){
    echo '<br/><b>'.$clientes['num_documento'].'</b><br/>';
    $link = "https://api.mie-commerce.com/v3/puntos/calcular/".$clientes['num_documento'];
    $json = ExecuteRemoteQuery33($link, 'API-maga-studio-test-XVF9E96-BUWN0R9');
    echo $json.'<br/>--------------------------------------------<br/>';
}

function ExecuteRemoteQuery33($link, $apiKey){
    $ch = curl_init($link);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Api-Key: '.$apiKey;
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }
?>