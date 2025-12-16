<?php 
    require_once "../funciones.php";
    require_once "../clases/cl_usuarios.php";

    $ClUsuarios = new cl_usuarios();

    $cod_usuario = 0;
    $pass = "";
    $temporal = 0;

    if(isset($_GET['cod_usuario']))
        $cod_usuario = $_GET['cod_usuario'];
    if(isset($_GET['clave']))
        $pass = $_GET['clave'];
    if(isset($_GET['temporal']))
        $temporal = $_GET['temporal'];

    $ClUsuarios->cod_usuario = $cod_usuario;
    if($ClUsuarios->GetDatos()){
        $ClUsuarios->password = $clave;
        $ClUsuarios->estado = 'A';
        $ClUsuarios->temporal = $temporal;
        if($ClUsuarios->crearKeystore()){
            $return['success'] = 1;
            $return['mensaje'] = "Se creó la keystore";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "No se creó la keystore";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos de este usuario";
    }

    header('Content-Type: application/json');
    echo json_encode($return);
?>