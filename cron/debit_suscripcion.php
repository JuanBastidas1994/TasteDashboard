<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_paymentez.php";
require_once "../clases/cl_empresas.php";
$Clempresas = new cl_empresas();

$fecha = fecha_only();

$query = "SELECT * FROM tb_empresas WHERE fecha_caducidad = '$fecha' AND estado = 'A'";
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $empresa){
    $cod_empresa = $empresa['cod_empresa'];
    $nombre = $empresa['nombre'];
    $correo = $empresa['representante_correo'];
    $monto = $empresa['mensualidad'];
    $descripcion = "Pago de mensualidad de ".$nombre;
    
    $cardActive = $Clempresas->getCardActive($cod_empresa);
    if($cardActive){
        if($monto > 0){
            $id = 0;
            $token = $cardActive['token'];
            $monto = number_format($monto, 2);
            
            setDebitLog($cod_empresa, $monto, $cardActive, $id);
            
            $resp = debitByToken($cod_empresa, $correo, $monto, $descripcion, $token);
            echo '<br/>-----------RESPUESTA PAYMENTEZ VAR DUMP--------------<br/>';
            var_dump($resp);
            
            if(isset($resp['error'])){
                $error = $resp['error'];
                echo '<br/>'.$error['description'].'<br/>';
                putDebitLogError($id, $error);
            }
            
            if(isset($resp['transaction'])){
                putDebitLogSuccess($id, $resp['transaction']);
                echo $nombre.' pago correctamente';
            }
        }else
            echo $nombre.' no tiene definido un monto en mensualidad';
    }else{
        echo $nombre.' no tiene configurada una tarjeta de debito o credito';
    }
}
if(count($resp)==0){
    echo 'Nadie caduca hoy';
}




?>