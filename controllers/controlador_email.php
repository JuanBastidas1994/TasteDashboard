<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_email.php";

$session = getSession();

$Clusuarios = new cl_usuarios();
$usuario = $session['cod_usuario'];

$mail = $Clusuarios->getEmailConfig($usuario);

controller_create();

function getFolders(){
    global $mail;
    if(!$mail){
        $return['success'] = 0;
        $return['mensaje'] = "No está configurado";
        return $return;
    }
    
    
    $ClEmail = new cl_email($mail['imap_server'], $mail['imap_port'], $mail['correo_user'], $mail['correo_pass']);
    $ClEmail->open("INBOX");
    $folders = $ClEmail->getFolders();
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de carpetas";
    $return['folders'] = $folders;
    return $return;
}

function getEmails(){
    global $mail;
    if(!$mail){
        $return['success'] = 0;
        $return['mensaje'] = "No está configurado";
        return $return;
    }
    
    $folder = isset($_GET['folder']) ? $_GET['folder'] : "INBOX";
    
    
    $ClEmail = new cl_email($mail['imap_server'], $mail['imap_port'], $mail['correo_user'], $mail['correo_pass']);
    $ClEmail->open($folder);
    $folders = $ClEmail->getEmails();
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de emails ".$folder;
    $return['emails'] = $folders;
    return $return;
}

function getEmailDetail(){
    global $mail;
    if(!$mail){
        $return['success'] = 0;
        $return['mensaje'] = "No está configurado";
        return $return;
    }
    
    $folder = isset($_GET['folder']) ? $_GET['folder'] : "INBOX";
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Debe enviar el id del correo";
        return $return;
    }
    $id = $_GET['id'];
    
    
    $ClEmail = new cl_email($mail['imap_server'], $mail['imap_port'], $mail['correo_user'], $mail['correo_pass']);
    $ClEmail->open($folder);
    $folders = $ClEmail->getEmailDetail($id);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de emails ".$folder;
    $return['email'] = $folders;
    return $return;
}


?>