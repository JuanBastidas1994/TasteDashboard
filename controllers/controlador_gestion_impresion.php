<?php
require_once "../funciones.php";
//Claseso
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_ordenes.php";
$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales();
$session = getSession();

controller_create();

function getOrden(){
    global $Clordenes;
    if(!isset($_POST['id']) || !isset($_POST['impresoras']) ){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    if(count($_POST['impresoras']) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "No tienes una impresora configurada";
        return $return;
    }

    $id = $_POST['id'];
    $impresoras = $_POST['impresoras'];
    $orden = $Clordenes->get_orden_array($id);
    if($orden){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        //$impresoras[0]['detalle'] = getPrintCaja($orden, 80);
        foreach($impresoras as $key => $item){
            if($item['tipo']=="CAJA")
                $impresoras[$key]['detalle'] = getPrintCaja($orden, $item['size']);
            else
                $impresoras[$key]['detalle'] = getPrintCocina($orden, $item['size']);    
        }
        $return['impresoras'] = $impresoras;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Orden no existente";
    }
    return $return;
}

function getPrintCaja($orden, $size){
    $maxLenght = 50;
    if($size==58)
        $maxLenght = 32;

    extract($orden);
    $print = [];
    addLine($fecha, "CENTER", $print);
    addLine("Danilo Restaurante", "CENTER", $print);
    addLine("Pedido #".$cod_orden, "CENTER", $print);
    addLine($entrega, "CENTER", $print);

    addLine("Cliente: ".$nombre." ".$apellido, "LEFT", $print);
    addLine("N. Doc.: ".$num_documento, "LEFT", $print);
    addLine("Correo: ".$correo, "LEFT", $print);
    addLine("Telf.: ".$telefono, "LEFT", $print);
    addLine("Observación: ".$observacion, "MULTILINEA", $print);

    addLine("PRODUCTOS", "CENTER", $print);
    addLine("------------------------", "CENTER", $print);

    foreach($orden['detalle'] as $item){
        addLine($item['cantidad']." - ".$item['nombre'], "LEFT", $print);
        addLine($item['descripcion'], "MULTILINEA", $print);
        addLine("------------------------", "CENTER", $print);
    }

    addTextRight("SUBTOTAL:", "$".number_format($subtotal,2), $maxLenght, $print);
    addTextRight("DESCUENTO:", "$".number_format($descuento,2), $maxLenght, $print);
    addTextRight("ENVIO:", "$".number_format($envio,2), $maxLenght, $print);
    addTextRight("IVA:", "$".number_format($iva,2), $maxLenght, $print);
    addTextRight("TOTAL:", "$".number_format($total,2), $maxLenght, $print);

    addLine("PAGOS", "CENTER", $print);
    addLine("------------------------", "CENTER", $print);
    foreach($orden['pagos'] as $item){
        addLine($item['descripcion'].": $".$item['monto'], "LEFT", $print);
    }

    addLine("Gracias por su compra", "CENTER", $print);
    return $print;
}

function getPrintCocina($orden, $size){
    $maxLenght = 50;
    if($size==58)
        $maxLenght = 32;

    extract($orden);
    $print = [];
    addLine($fecha, "CENTER", $print);
    addLine("Danilo Restaurante", "CENTER", $print);
    addLine("Pedido #".$cod_orden, "CENTER", $print);
    addLine($entrega, "CENTER", $print);

    addLine("Cliente: ".$nombre." ".$apellido, "LEFT", $print);
    addLine("Observación: ".$observacion, "MULTILINEA", $print);

    addLine("PRODUCTOS", "CENTER", $print);
    addLine("------------------------", "CENTER", $print);

    foreach($orden['detalle'] as $item){
        addLine($item['cantidad']." - ".$item['nombre'], "LEFT", $print);
        addLine($item['descripcion'], "MULTILINEA", $print);
        addLine("------------------------", "CENTER", $print);
    }

    addLine("Gracias por su compra", "CENTER", $print);
    return $print;
}

function addLine($texto, $tipo, &$print){
    $aux['texto'] = $texto;
    $aux['tipo'] = $tipo;
    $print[] = $aux;
}

function addTextRight($texto, $value, $max, &$print){
    $value = str_pad($value, 10, " ", STR_PAD_LEFT);
    $texto = $texto . $value;
    $texto = str_pad($texto, $max, " ", STR_PAD_LEFT);
    $aux['texto'] = $texto;
    $aux['tipo'] = "LEFT";
    $print[] = $aux;
}