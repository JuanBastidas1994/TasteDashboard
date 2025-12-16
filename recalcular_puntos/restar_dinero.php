<?php

require_once "../funciones.php";

$cedula = "1803221082"; 
$monto = 20;
if(DecrementarDinero($cedula, $monto)){
    echo "RESTO DINERO";
}else
    echo "NO RESTO DINERO";

function DecrementarDinero($cedula, $monto){
    $cliente = getCliente($cedula);
    if(!$cliente){
        echo "NO EXISTE CLIENTE <br/>";
        return false;
    }
    
	//TRAE TODO SU DINERO DESGLOSADO
    $query = "SELECT * FROM tb_cliente_dinero cd 
    		WHERE cd.cod_cliente = ".$cliente['cod_cliente']." AND cd.estado = 'A' AND cd.saldo > 0 AND cd.fecha_caducidad > NOW() 
    		ORDER BY cd.fecha_caducidad ASC";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach ($resp as $row){
        $saldo = $row['saldo'];
		if($monto > $saldo){
		    $nuevoSaldo = 0;
		    $estadoSaldo = "I";
            $monto = $monto - $saldo;
        }else{
            $nuevoSaldo = $saldo - $monto;
            $estadoSaldo = "A";
            if($nuevoSaldo == 0)
                $estadoSaldo = "I";
            $monto = 0;
        }
        
        $query = "UPDATE tb_cliente_dinero SET saldo=$nuevoSaldo, estado='$estadoSaldo' WHERE cod_cliente_dinero = ".$row['cod_cliente_dinero'];
        $respSaldo = Conexion::ejecutar($query,NULL);
        if(!$respSaldo)
            return false;
        
        if($monto == 0)
            return true;
    }
    return true;  
}

function getCliente($cedula){
    $cod_empresa = 78;
	$query = "SELECT * FROM tb_clientes c WHERE c.num_documento = '$cedula' AND c.cod_empresa = ".$cod_empresa;
	$row = Conexion::buscarRegistro($query);
	if($row){
	    echo $row['nombre'].'<br/>';
	    return $row;
	}
    return false;
}
?>