<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_laar.php";
$ClLaar = new cl_laar();
$session = getSession();

controller_create();

function login_laar(){
    global $ClLaar;
    
    $user = $_GET['user'];
    $pass = $_GET['pass'];
    
    $ClLaar->username = $user;
    $ClLaar->password = $pass;
    
    if($ClLaar->getToken()){
        if($ClLaar->API <> ""){
            $return['success'] = 1;
            $return['mensaje'] = "Sí hay token";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "API está vacía ".$ClLaar->msgError;
        }        
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = $ClLaar->msgError;
    }
    return $return;
}

?>