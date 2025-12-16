<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_telegram_sucursales.php";
$ClSucursales = new cl_sucursales();
$ClTelegramSucursales = new cl_telegram_sucursales();
$session = getSession();

controller_create();

function getList(){
    global $ClSucursales;
    global $ClTelegramSucursales;

    extract($_GET);

    $sucursales = $ClSucursales->listaActivas();
    if($sucursales){
        foreach ($sucursales as &$sucursal) {
            $cod_sucursal = $sucursal["cod_sucursal"];
            $config_telegram = $ClTelegramSucursales->getConfig($cod_sucursal);
            if($config_telegram)
                $sucursal["config_telegram"] = $config_telegram;
            else
                $sucursal["config_telegram"] = null;
        }
        $return['success'] = 1;
        $return['mensaje'] = "Lista obtenida";
        $return['data'] = $sucursales;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer la lista de sucursales";
    }
    return $return;
}

function crear(){
    global $ClTelegramSucursales;
    extract($_GET);
    
    if($ClTelegramSucursales->create($cod_sucursal, $hash)){
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal configurada correctamente";
        $return['code'] = $hash;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al configurar la sucursal";
    }
    return $return;
}

?>