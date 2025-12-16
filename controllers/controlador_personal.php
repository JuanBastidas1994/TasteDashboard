<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_personal.php";
require_once "../clases/cl_usuarios.php";

$Clpersonal = new cl_personal();
$Clusuario = new cl_usuarios();
$session = getSession();

controller_create();

function crear(){
    global $Clpersonal;
    global $Clusuario;
    global $session;
    $session = getSession();
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $nameImg = 'personal_'.datetime_format().'.png';
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    if(isset($_POST['chkEstadoSuc'])){
        $Clusuario->estado = 'A';
        $Clpersonal->estado = 'A';
    }        
    else{
        $Clusuario->estado = 'I'; 
        $Clpersonal->estado = 'I'; 
    }

    $Clusuario->nombre = $txt_nombre;
    $Clusuario->apellido = $txt_apellido;
    $Clusuario->cod_rol = 5;
    $Clusuario->correo = $txt_correo;
    $Clusuario->usuario = $txt_correo;
    $Clusuario->password = $txt_password;
    $Clusuario->fecha_nacimiento = $txt_fecha_nac;
    $Clusuario->telefono = $txt_telefono;    
    $Clusuario->imagen= $nameImg;
    $Clusuario->cod_empresa= $session['cod_empresa'];    
    
    $Clpersonal->nombre = $txt_nombre;
    $Clpersonal->apellido = $txt_apellido;
    $Clpersonal->correo = $txt_correo;
    $Clpersonal->fecha_nac = $txt_fecha_nac;
    $Clpersonal->telefono = $txt_telefono;    
    $Clpersonal->imagen= $nameImg;
    $Clpersonal->cod_empresa= $session['cod_empresa'];   
    

    
    if(!isset($_POST['cod_usuario'])){
        if(!$Clusuario->getByUsuario($txt_correo)){
            if($Clusuario->crear($cod_usuario)){
                if($Clpersonal->crear($cod_usuario, $cod_personal)){
                    $return['success'] = 1;
                    $return['mensaje'] = "Personal creado correctamente";
                    $return['id'] = $cod_usuario;

                    /*SUBIR IMAGEN*/
                    if(!uploadFile($_FILES["img_product"], $nameImg)){
                        $img1 = url_upload.'/assets/img/200x200.jpg';
                        $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                        copy($img1, $img2);
                    }
                }
                else{
                    $return['success'] = 0;
                    $return['mensaje'] = "Error al crear personal";
                }
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al crear usuario";
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "El usuario ya existe, intente con otro correo";
        }
    }
    else{
        $Clusuario->cod_usuario = $cod_usuario;
        if(!$Clusuario->getExistenteByUsuario($txt_correo, $cod_usuario)){
            if($Clusuario->editar()){
                $return['id'] = $Clusuario->cod_usuario;

                if($Clpersonal->editar($cod_usuario)){
                    $return['success'] = 1;
                    $return['mensaje'] = "Personal editado correctamente";

                    $usuario = $Clusuario->get($cod_usuario);
                    if($usuario)
                        uploadFile($_FILES["img_product"], $usuario['imagen']);
                }
                else{
                    $return['success'] = 0;
                    $return['mensaje'] = "Error al editar el personal";
                }                
            }else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al editar el usuario";
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "El usuario ya existe, intente con otro correo";
        }
    }
    return $return;
}

function set_estado(){
    global $Clpersonal;
    global $Clusuario;

    $cod_usuario = $_GET['cod_usuario'];
    $estado = $_GET['estado'];
    if($Clusuario->set_estado($cod_usuario, $estado)){
        if($Clpersonal->set_estado($cod_usuario, $estado)){
            $return['success'] = 1;
            $return['mensaje'] = "Eliminado corectamente";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al eliminar personal";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar usuario";
    }
    return $return;
}
?>