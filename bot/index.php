<?php
require_once "../funciones.php";
//Clases
require_once "cl_telegram.php";

error_reporting(E_ALL);

if(isset($_SERVER['PATH_INFO'])){
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    if(count($request)>0){
        $alias = $request[0];
        $query = "SELECT * FROM tb_empresas WHERE alias = '$alias'";
        $empresa = Conexion::buscarRegistro($query);
        if($empresa){
            define('cod_empresa',$empresa['cod_empresa']);
            
            $query = "SELECT * FROM tb_telegram WHERE cod_empresa = ".$empresa['cod_empresa'];
            $bot = Conexion::buscarRegistro($query);
            if(!$bot){
                $return['success'] = 0;
                $return['mensaje'] = "Empresa no tiene asignado un bot";
                file_put_contents("registro_de_respuestas.log", PHP_EOL . $return['mensaje'], FILE_APPEND);
                header("Content-type:application/json; charset=utf-8");
        	    echo json_encode($return);
        	    exit();
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Empresa no existe";
            file_put_contents("registro_de_respuestas.log", PHP_EOL . $return['mensaje'], FILE_APPEND);
            header("Content-type:application/json; charset=utf-8");
    	    echo json_encode($return);
    	    exit();
        }
    }    
}else{
    $return['success'] = 0;
    $return['mensaje'] = "Faltan parametros";
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}

$telegram = new cl_telegram($bot['token']);
$request = file_get_contents("php://input");
$log_request = $request;

$fecha = date('Y-m-d H:i:s');
$mensaje = $fecha.' - '.$log_request;
file_put_contents("registro_de_actualizaciones.log", PHP_EOL . $mensaje, FILE_APPEND);

$input = json_decode($request, true);
if (JSON_ERROR_NONE !== json_last_error()){
	$return['success']= -1;
	$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
	var_dump($return);
}
else{
    $mensaje = "";
    if(analizar($input, $mensaje)){
        echo "Pudo enviar el mensaje";
        file_put_contents("registro_de_respuestas.log", PHP_EOL . $mensaje, FILE_APPEND);
    }else{
        echo "No se pudo analizar el mensaje<br/>";
        echo $mensaje;
        file_put_contents("registro_de_respuestas.log", PHP_EOL . $mensaje, FILE_APPEND);
    }
}

function analizar($input, &$respuesta){
    global $telegram;
    if(isset($input['message'])){
        $mensaje = $input['message'];
        //USUARIO
        $usuario = $mensaje['from'];
        $nombre = $usuario['first_name'];
        $apellido = $usuario['last_name'];
        //CHAT
        $chat = $mensaje['chat'];
        $nom_chat = $chat['title'];
        $cod_chat = $chat['id'];
        
        if(!isset($chat['type'])){
            $respuesta = "No tengo permisos para responder mensajes, lo siento";
            $telegram->sendMessage($cod_chat,$respuesta);
            return false;
        }else{
            if($chat['type'] == "private"){
                $respuesta = "No puedo ayudarte en chats privados";
                $telegram->sendMessage($cod_chat,$respuesta);
                return false;
            }
            else if($chat['type'] != "supergroup"){
                $respuesta = "No puedo responder en grupos si no soy el administrador";
                $telegram->sendMessage($cod_chat,$respuesta);
                return false;
            }
        }
        
        $grupo = $telegram->grupo_existe($cod_chat);
        if($grupo){
            if($grupo['estado']!='A'){
                $respuesta = "Este grupo no tiene permisos para obtener informacion, por favor solicitar al administrador conceder los permisos. Error: Inactivo";
                $telegram->sendMessage($cod_chat,$respuesta);
                return false;
            }
        }else{
            $telegram->grupo_crear($cod_chat, $nom_chat);
            $respuesta = "Este grupo no tiene permisos para obtener informacion, por favor solicitar al administrador conceder los permisos. Error: Inactivo";
            $telegram->sendMessage($cod_chat,$respuesta);
            return false;
        }
        
        $cod_usuario = $usuario['id'];
        $infoUsuario = $telegram->usuario_existe($cod_usuario);
        if(!$infoUsuario){
            $code = "es";
            $telegram->usuario_crear($cod_usuario, $nombre, $apellido, $code);
            $mensaje = "Bienvenid@ $nombre $apellido al grupo, este Chat te servirá para poder brindarle al cliente de Danilo Restaurante un mejor servicio.";
            $telegram->sendMessage($cod_chat, $mensaje);
            return false;
        }
        
        $latitud = "";
        $longitud="";
        if(isset($mensaje['location'])){
            $latitud = $mensaje['location']['latitude'];
            $longitud = $mensaje['location']['longitude'];
            $long = $mensaje['date'];
            if($telegram->update_location($cod_chat, $cod_usuario, $latitud, $longitud, $infoUsuario['cod_usuario']))
                $telegram->sendMessage($cod_chat, "Gracias $nombre $apellido, estamos receptando tu ubicacion, recuerda siempre enviar la ubicacion en tiempo real");
            else
                $telegram->sendMessage($cod_chat, "$nombre $apellido no tienes asignado un usuario en el sistema, por favor solicita a un administrador crearte un usuario.");
        }else if(isset($mensaje['new_chat_participant'])){
            $nuevo_usuario = $mensaje['new_chat_participant'];
            $infoUsuario = $telegram->usuario_existe($nuevo_usuario['id']);
            if(!$infoUsuario){
                $cod2 = $nuevo_usuario['id'];
                $nombre2 = $nuevo_usuario['first_name'];
                $apellido2 = $nuevo_usuario['last_name'];
                $code = "es";
                $telegram->usuario_crear($cod2, $nombre2, $apellido2, $code);
                $respuesta = "Bienvenid@ $nombre2 $apellido2 al grupo, este Chat te servirá para poder brindarle al cliente de Danilo Restaurante un mejor servicio.";
                $telegram->sendMessage($cod_chat, $respuesta);
            }
        }else{
            $respuesta = "Lo siento $nombre $apellido, solo admito ubicaciones en tiempo real para poder brindarle al cliente un tracking estable";
            file_put_contents("registro_de_respuestas.log", PHP_EOL . $respuesta, FILE_APPEND);
            $resp = $telegram->sendMessage($cod_chat, $respuesta);
            var_dump($resp);
        }
    }
    else if(isset($input['edited_message'])){
        $mensaje = $input['edited_message'];
        
        $long = $mensaje['edit_date']; 
        $fecha = date('Y-m-d H:i:s', $long);
        
        $usuario = $mensaje['from'];
        $chat = $mensaje['chat'];
        $cod_chat = $chat['id'];
        $cod_usuario = $usuario['id'];
        $infoUsuario = $telegram->usuario_existe($cod_usuario);
        if($infoUsuario){
            $latitud = "";
            $longitud;
            if(isset($mensaje['location'])){
                $latitud = $mensaje['location']['latitude'];
                $longitud = $mensaje['location']['longitude'];
                $long = $mensaje['date']; 
                $telegram->update_location($cod_chat, $cod_usuario, $latitud, $longitud, $infoUsuario['cod_usuario']);
            }
        }    
    }else{
        $respuesta = "la informacion de entrada no es de telegram";
        return false;
    }
}
?>