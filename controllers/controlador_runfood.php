<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_runfood.php";

$session = getSession();
$cod_empresa = $session['cod_empresa'];
$Clrunfood = new cl_runfood(NULL);
define('cod_sistema_facturacion',1);

controller_create();


function setOffices(){
    global $Clrunfood;
    global $cod_empresa;
    
   $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);
    
    if($Clrunfood->setSucursal($sucursal, $dominio, $usuario)){
        $id_ruc = Conexion::lastId();
        $return['success'] = 1;
        $return['mensaje'] = "Configuración realizada correctamente";
        $return['ruc_id'] = $id_ruc;
        $return['productos'] = $productosRunfood;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se guardar la configuración, por favor intentelo nuevamente";
    }
    return $return;
}

function getAllProducts(){
    global $Clrunfood;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $productos = $Clrunfood->getAllProductsByOffices($id);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de productos";
    $return['officeId'] = $id;
    $return['productos'] = $productos;
    $return['ruc'] = $ruc;
    return $return;
}

function getAllIngredientes(){
    global $Clrunfood;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $ingredientes = $Clrunfood->getAllIngredientes($id);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de ingredientes";
    $return['ingredientes'] = $ingredientes;
    $return['ruc'] = $ruc;
    return $return;
}

function getAllRecipientes(){
    global $Clrunfood;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $recipientes = $Clrunfood->getAllRecipientes($id);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de recipientes";
    $return['recipientes'] = $recipientes;
    $return['ruc'] = $ruc;
    return $return;
}

function getAllFormasPago(){
    global $Clrunfood;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $formaspago = $Clrunfood->getAllFormasPago($id);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de formas de pago";
    $return['formaspago'] = $formaspago;
    $return['ruc'] = $ruc;
    return $return;
}


function setProduct(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    if($Clrunfood->setProduct($office_id, $product_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Producto ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el producto";
    }
    return $return;
}

function setIngrediente(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    
    if($Clrunfood->setIngrediente($office_id, $ingrediente_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el ingrediente";
    }
    return $return;
}

function setRecipiente(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    
    if($Clrunfood->setRecipiente($office_id, $recipiente_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Recipiente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el recipiente";
    }
    return $return;
}

function setFormaPago(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    
    if($Clrunfood->setFormaPago($office_id, $formapago_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Forma de Pago ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar la forma de pago";
    }
    return $return;
}

function importIngrediente(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    if($Clrunfood->saveIngrediente($office_id, $cod_empresa, $unidad, $precio, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el ingrediente";
    }
    return $return;
}

function importRecipientes(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    if($Clrunfood->saveRecipiente($office_id, $cod_empresa, $precio, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Recipiente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el recipiente";
    }
    return $return;
}

function setDomicilioAdicionales(){
    global $Clrunfood;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $alias = "ADICIONALES";
    $tipoText = "Productos Adicionales";
    if($tipo == "DOMICILIO"){
        $alias = "ENVIO_DOMICILIO";  
        $tipoText = "Servicio a Domicilio";
    }
    
    if($Clrunfood->setDomicilioAdicional($office_id, $alias, $cod_empresa, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = $tipoText." ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar ".$tipoText;
    }
    return $return;
}

function lstProducts(){
     global $Clrunfood;

    if(!isset($_GET['id'])){
        return [ 'success' => 0, 'mensaje' => 'Falta informacion'];
    }
    extract($_GET);

    $isConfig =$Clrunfood->getSucursal($id);
    if(!$isConfig){
        return [ 'success' => 0, 'mensaje' => 'Sucursal no configurada'];
    }

     $productos = $Clrunfood->LstProductos($id);
     if(!$productos){
        return [ 'success' => 0, 'mensaje' => 'Ha ocurrido un problema al llamar los productos de Runfood'];
     }
     return [ 
        'success' => 1, 
        'mensaje' => 'Lista de productos desde runfood',
        'productos' => $productos
    ];
}

function lstFormasPago(){
     global $Clrunfood;

    if(!isset($_GET['id'])){
        return [ 'success' => 0, 'mensaje' => 'Falta informacion'];
    }
    extract($_GET);

    $isConfig =$Clrunfood->getSucursal($id);
    if(!$isConfig){
        return [ 'success' => 0, 'mensaje' => 'Sucursal no configurada'];
    }

     $formaspago = $Clrunfood->lstFormasPago($id);
     if(!$formaspago){
        return [ 'success' => 0, 'mensaje' => 'Ha ocurrido un problema al llamar las formas de pago de Runfood'];
     }
     return [ 
        'success' => 1, 
        'mensaje' => 'Lista de formas de pago desde runfood',
        'formaspago' => $formaspago
    ];
}
?>