<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_notificaciones.php";
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_empresas.php";

$ClNotificaciones = new cl_notificaciones();
$ClUsuarios = new cl_usuarios();
$ClEmpresas = new cl_empresas();
$session = getSession();

controller_create();

function notificar() {
    // ? SE UTILZA EN MÓDULOS -> NOTIFICACIONES

    global $ClNotificaciones;
    global $session;

    if (!isset($_POST['titulo']) || !isset($_POST['descripcion']) || !isset($_POST['aplicacion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $empresa = $ClNotificaciones->get($aplicacion);
    if ($empresa) {
        $token = $empresa['token'];
        $topic = $empresa['topic'];
        if ($token == "") {
            $return['success'] = 0;
            $return['mensaje'] = "La empresa no esta configurada para enviar notificaciones, por favor ponerse en contacto con Soporte";
            return $return;
        }

        if ($ClNotificaciones->crear($aplicacion, 0, "ORDEN", htmlentities($titulo), htmlentities($descripcion))) {
            $resp = sendNotify_v1($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
            $return['success'] = 1;
            $return['mensaje'] = "Notificacion enviada";
            $return['resp'] = $resp;
            return $return;
        } else {
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo crear la notificacion";
            return $return;
        }
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "Error al encontrar la empresa";
        return $return;
    }
}

function notificar_admins()
{
    global $ClNotificaciones;
    global $session;

    if (!isset($_POST['txt_titulo']) || !isset($_POST['descripcion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $ClNotificaciones->cod_usuario = $cod_usuario;
    $ClNotificaciones->icono = $cmb_tipo_noti;
    $ClNotificaciones->titulo = $txt_titulo;
    $ClNotificaciones->detalle = $descripcion;
    $ClNotificaciones->url = "javascript:void(0);";
    $ClNotificaciones->fecha = fecha();

    if ($ClNotificaciones->insertarNotiDash()) {
        $return['success'] = 1;
        $return['mensaje'] = "Notificacion enviada";
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo crear la notificacion";
    }
    return $return;
}

function notificar_variosAdmins()
{
    global $ClNotificaciones;
    global $ClEmpresas;
    global $ClUsuarios;
    global $session;

    if (!isset($_POST['titulo']) || !isset($_POST['descripcion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    /*$val_rol = explode("rol-", $cmb_usuarios[0]);
    $cod_rol = $val_rol[1];
    $return['success'] = 1;
    $return['emp'] = $cmb_empresas[0];
    $return['us'] = $cmb_usuarios[0];
    $return['cod_rol'] = $cod_rol;
    return $return;*/

    $ClNotificaciones->icono = $cmb_tipo_noti;
    $ClNotificaciones->titulo = $titulo;
    $ClNotificaciones->detalle = htmlentities($descripcion);
    $ClNotificaciones->fecha = fecha();

    $url = "javascript:void(0);";
    if ($txt_url <> "")
        $url = $txt_url;
    $ClNotificaciones->url = $url;

    if ($cmb_empresas[0] == "all" && $cmb_usuarios[0] == "all") {
        $empresas = $ClEmpresas->lista();
        foreach ($empresas as $emp) {
            $usuarios = $ClUsuarios->getAdmins($emp['cod_empresa']);
            foreach ($usuarios as $usu) {
                $ClNotificaciones->cod_usuario = $usu['cod_usuario'];

                if ($ClNotificaciones->insertarNotiDash()) {
                    $return['success'] = 1;
                    $return['mensaje'] = "Notificacion enviada ";
                } else {
                    $return['success'] = 0;
                    $return['mensaje'] = "No se pudo crear la notificacion";
                }
            }
        }
        return $return;
    }
    if ($cmb_empresas[0] == "all" && !is_numeric($cmb_usuarios[0])) {
        $empresas = $ClEmpresas->lista();
        foreach ($empresas as $emp) {
            for ($i = 0; $i < count($cmb_usuarios); $i++) {
                $val_rol = explode("rol-", $cmb_usuarios[$i]);
                $cod_rol = $val_rol[1];
                $usuarios = $ClUsuarios->getAdminsByRol($emp['cod_empresa'], $cod_rol);
                foreach ($usuarios as $usu) {
                    $ClNotificaciones->cod_usuario = $usu['cod_usuario'];

                    if ($ClNotificaciones->insertarNotiDash()) {
                        $return['success'] = 1;
                        $return['mensaje'] = "Notificacion enviada ";
                    } else {
                        $return['success'] = 0;
                        $return['mensaje'] = "No se pudo crear la notificacion";
                    }
                }
            }
        }
        return $return;
    } else if ($cmb_empresas[0] <> "all" && $cmb_usuarios[0] == "all") {
        for ($i = 0; $i < count($cmb_empresas); $i++) {
            $usuarios = $ClUsuarios->getAdmins($cmb_empresas[$i]);
            foreach ($usuarios as $usu) {
                $ClNotificaciones->cod_usuario = $usu['cod_usuario'];

                if ($ClNotificaciones->insertarNotiDash()) {
                    $return['success'] = 1;
                    $return['mensaje'] = "Notificacion enviada ";
                } else {
                    $return['success'] = 0;
                    $return['mensaje'] = "No se pudo crear la notificacion";
                }
            }
        }
        return $return;
    } else if ($cmb_empresas[0] <> "all" && !is_numeric($cmb_usuarios[0])) {
        $val_rol = explode("rol-", $cmb_usuarios[0]);
        $cod_rol = $val_rol[1];
        for ($i = 0; $i < count($cmb_empresas); $i++) {
            $usuarios = $ClUsuarios->getAdminsByRol($cmb_empresas[$i], $cod_rol);
            foreach ($usuarios as $usu) {
                $ClNotificaciones->cod_usuario = $usu['cod_usuario'];

                if ($ClNotificaciones->insertarNotiDash()) {
                    $return['success'] = 1;
                    $return['mensaje'] = "Notificacion enviada ";
                } else {
                    $return['success'] = 0;
                    $return['mensaje'] = "No se pudo crear la notificacion";
                }
            }
        }
        return $return;
    } else if ($cmb_empresas[0] <> "all" && $cmb_usuarios[0] <> "all") {
        for ($i = 0; $i < count($cmb_usuarios); $i++) {
            $ClNotificaciones->cod_usuario = $cmb_usuarios[$i];

            if ($ClNotificaciones->insertarNotiDash()) {
                $return['success'] = 1;
                $return['mensaje'] = "Notificacion enviada ";
            } else {
                $return['success'] = 0;
                $return['mensaje'] = "No se pudo crear la notificacion";
            }
        }
        return $return;
    }
}

function notificarOrden()
{
    global $ClNotificaciones;
    global $session;

    if (!isset($_GET['orden'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);

    $query = "SELECT e.cod_empresa, e.nombre as empresa, s.nombre as sucursal, c.estado, c.cod_usuario
            FROM tb_orden_cabecera c, tb_empresas e, tb_sucursales s
            WHERE c.cod_empresa = e.cod_empresa
            AND c.cod_sucursal = s.cod_sucursal
            AND c.cod_orden = $orden";
    $resp = Conexion::buscarRegistro($query);
    if (!$resp) {
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer la informacion";
        return $return;
    }
    extract($resp);


    /*NOTIFICAR AL MOTORIZADO*/
    if ($resp['estado'] == "ASIGNADA") {  //Solo debe notificar al motorizado en estado asignado
        $config = $ClNotificaciones->getByTipo($resp['cod_empresa'], "MOTORIZADOS");
        if ($config) {
            $query = "SELECT m.cod_motorizado, m.fecha_asignacion, m.fecha_salida, m.fecha_llegada
                    FROM tb_orden_cabecera c, tb_motorizado_asignacion m
                    WHERE c.cod_orden = m.cod_orden
                    AND c.cod_orden = $orden";
            $resp2 = Conexion::buscarRegistro($query);
            if ($resp2) {
                extract($resp2);
            }


            $configId = $config['cod_empresa_notificacion'];
            $token = $config['token'];
            $topic = $config['topic'];
            $topic = "motorizado" . $cod_motorizado;
            $titulo = "Se te ha asignado una orden";
            $descripcion = "$empresa - $sucursal te ha asignado una orden a las $fecha_asignacion";
            if ($ClNotificaciones->crear($configId, $cod_motorizado, "ORDEN", htmlentities($titulo), htmlentities($descripcion))) {
                sendNotify($token, $titulo, $topic, $descripcion, 0, "PEDIDOS");
            }
        }
    }

    /*NOTIFICAR AL USUARIO FINAL*/
    $config = $ClNotificaciones->getByTipo($resp['cod_empresa'], "USUARIOS");
    if ($config) {
        $configId = $config['cod_empresa_notificacion'];
        $token = $config['token'];
        $topic = $config['topic'];
        $topic = "usuario" . $cod_usuario;

        switch ($resp['estado']) {
            case 'ASIGNADA':
                $titulo = "Tu pedido fue asignado a un motorizado";
                $descripcion = "$empresa - $sucursal te ha asignado la orden a las $fecha_asignacion";
                break;
            case 'ENVIANDO':
                $titulo = "Tu pedido está en camino";
                $descripcion = "La persona encargada ha salido con tu pedido hacia la direccion que nos indicaste, pronto esta persona te llamara para proceder a realizar la entrega.";
                break;
            case 'ENTREGADA':
                $titulo = "Tu pedido ha sido entregado";
                $descripcion = "Disfruta de tu pedido!!, si tienes algún comentario, mejora sobre nuestro servicio puedes proceder a calificarnos.";
                break;
            case 'PREPARANDO':
                $titulo = "Tu pedido se está preparando";
                $descripcion = "Ya estamos preparando tu pedido, no olvides que debes venir a recogerlo a las en la sucursal $sucursal";
                break;
            case 'CANCELADA';
            case 'ANULADA':
                $titulo = "Tu orden ha sido " . strtolower($resp['estado']);
                $descripcion = $titulo;
                break;
        }
        if ($ClNotificaciones->crear($configId, $cod_usuario, "ORDEN", htmlentities($titulo), htmlentities($descripcion))) {
            sendNotify($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
        }
    }

    $return['success'] = 1;
    $return['mensaje'] = "Notificacion enviada";
    return $return;
}

function notificarPedidoListo()
{
    global $ClNotificaciones;
    global $session;

    if (!isset($_GET['orden'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);

    $query = "SELECT e.cod_empresa, e.nombre as empresa, s.nombre as sucursal, c.estado, c.cod_usuario, m.cod_motorizado, m.fecha_asignacion, m.fecha_salida, m.fecha_llegada
            FROM tb_orden_cabecera c, tb_empresas e, tb_sucursales s, tb_motorizado_asignacion m
            WHERE c.cod_empresa = e.cod_empresa
            AND c.cod_sucursal = s.cod_sucursal
            AND c.cod_orden = m.cod_orden
            AND c.cod_orden = $orden";
    $resp = Conexion::buscarRegistro($query);
    if (!$resp) {
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer la informacion";
        return $return;
    }
    extract($resp);

    /*NOTIFICAR AL MOTORIZADO*/
    $config = $ClNotificaciones->getByTipo($resp['cod_empresa'], "MOTORIZADOS");
    if ($config) {
        $token = $config['token'];
        $topic = $config['topic'];
        $topic = "motorizado" . $resp['cod_motorizado'];
        $titulo = "Orden Lista";
        $descripcion = "Ven a recoger el pedido a $sucursal";
        sendNotify($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
        $return['success'] = 1;
        $return['mensaje'] = "Notificacion enviada";
        $return['orden'] = $resp;
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "La empresa no esta configurada para enviar notificaciones, por favor ponerse en contacto con Soporte";
    }

    return $return;
}

function notificarMotorizadoCustom()
{
    global $ClNotificaciones;
    global $session;

    if (!isset($_GET['cod_motorizado']) || !isset($_GET['mensaje'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);

    /*NOTIFICAR AL MOTORIZADO*/
    $config = $ClNotificaciones->getByTipo($session['cod_empresa'], "MOTORIZADOS");
    if ($config) {
        $token = $config['token'];
        $topic = $config['topic'];
        $topic = "motorizado" . $cod_motorizado;
        $titulo = "Orden Lista";
        $descripcion = $mensaje;
        sendNotify($token, $titulo, $topic, $descripcion, 0, "GENERAL");
        $return['success'] = 1;
        $return['mensaje'] = "Notificacion enviada";
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "La empresa no esta configurada para enviar notificaciones, por favor ponerse en contacto con Soporte";
    }

    return $return;
}

function notificarUsuario() {
    // ? SE UTILZA EN CLIENTE DETALLE

    global $ClNotificaciones;
    global $session;

    if (!isset($_GET['usuario']) || !isset($_GET['texto'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $config = $ClNotificaciones->getByTipo($session['cod_empresa'], "USUARIOS");
    if ($config) {
        $configId = $config['cod_empresa_notificacion'];
        $token = $config['token'];

        $titulo = "Recordatorio";
        $titulo = $tituloTipo;
        $descripcion = $texto;
        $topic = "usuario{$usuario},usuariousuario{$usuario}";
        $tipo = $tipo;

        // sendNotify($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
        $return = sendNotify_v1($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
        if($return["success"] == 1)
            $ClNotificaciones->crear($configId, $usuario, $tipo, htmlentities($titulo), $descripcion);
        $return['mensaje'] = $return["message"];
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "La empresa no esta configurada para enviar notificaciones, por favor ponerse en contacto con Soporte";
    }
    return $return;
}

function notificarClientes() { 
    // ? SE UTILZA EN GESTIÓN DE ÓRDENES
    global $ClNotificaciones;
    global $session;

    $POST = file_get_contents("php://input");
    extract(json_decode($POST, true));

    if (!isset($cod_usuario) || !isset($descripcion) || !isset($titulo) || !isset($tipo)) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    $config = $ClNotificaciones->getByTipo($session['cod_empresa'], "USUARIOS");
    if ($config) {
        $configId = $config['cod_empresa_notificacion'];
        $token = $config['token'];

        /* $topicA = "usuario" . $cod_usuario;
        $topicB = "usuariousuario" . $cod_usuario;
       
        $respNotify = sendNotify2($token, $titulo, $topicA, $topicB, $descripcion, 0, "NOTIFICACION");
        $return['success'] = 1;
        $return['notify'] = $respNotify;
        $return['mensaje'] = "Notificacion enviada"; 
        $ClNotificaciones->crear($configId, $cod_usuario, $tipo, htmlentities($titulo), $descripcion); */

        $topic = "usuario{$cod_usuario},usuariousuario{$cod_usuario}";

        $return = sendNotify_v1($token, $titulo, $topic, $descripcion, 0, "NOTIFICACION");
        if($resp["success"] == 1)
            $ClNotificaciones->crear($configId, $cod_usuario, $tipo, htmlentities($titulo), $descripcion);
        $return['mensaje'] = $return["message"];

    } else {
        $return['success'] = 0;
        $return['mensaje'] = "La empresa no esta configurada para enviar notificaciones, por favor ponerse en contacto con Soporte";
    }
    return $return;
}

function get()
{
    global $Clempresas;
    if (!isset($_GET['cod_empresa'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if ($Clempresas->getArray($cod_sucursal, $array)) {
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal encontrada";
        $return['data'] = $array;
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "Sucursal no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function get_admins()
{
    global $ClUsuarios;
    global $ClEmpresas;

    extract($_POST);

    $html = "";

    $empresas = $ClEmpresas->lista();
    for ($i = 0; $i < count($cmb_empresas); $i++) {
        if ($cmb_empresas[$i] == "all") {
            foreach ($empresas as $emp) {
                $usuarios = $ClUsuarios->getAdmins($emp['cod_empresa']);
                foreach ($usuarios as $usu) {
                    $html .= '<option value="' . $usu['cod_usuario'] . '">' . $usu['nombre'] . ' ' . $usu['apellido'] . ' - ' . $usu['nom_empresa'] . '</option>';
                }
            }
        } else {
            $usuarios = $ClUsuarios->getAdmins($cmb_empresas[$i]);
            foreach ($usuarios as $usu) {
                $html .= '<option value="' . $usu['cod_usuario'] . '">' . $usu['nombre'] . ' ' . $usu['apellido'] . ' - ' . $usu['nom_empresa'] . '</option>';
            }
        }
    }
    if ($html <> "") {
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "Datos no obtenidos " . count($cmb_empresas);
    }
    $return['html'] = $html;
    return $return;
}
/*
function sendNotify($token, $titulo, $topic, $mensaje, $codigo, $tipo)
{
    if($topic == "")
        $topic = "general";
    
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    $data = array('title' =>$titulo , 'body' => $mensaje, 'message' => $mensaje, 'sound' => 'default', 'valor' => $codigo, 'tipo' => $tipo);
    $arrayToSend = array('to' => "/topics/".$topic, 'notification' => $data, 'data' => $data, 'priority'=>'high');
    $json = json_encode($arrayToSend);
  
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key= '.$token; // key here
    
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   

    //Send the request
    $response = curl_exec($ch);

    //Close request
    curl_close($ch);
    return $response;
}*/



function sendNotify($token, $titulo, $topic, $mensaje, $codigo, $tipo)
{
    if ($topic == "")
        $topic = "general";

    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    $data = array('title' => $titulo, 'body' => $mensaje, 'message' => $mensaje, 'valor' => $codigo, 'tipo' => $tipo);
    $arrayToSend = array(
        'to' => "/topics/" . $topic,
        'notification' => $data, //EN MOTORIZADO ESTO NO VA
        'data' => $data,
        'priority' => 'high'
    );
    $json = json_encode($arrayToSend);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key= ' . $token; // key here

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    $response = curl_exec($ch);

    //Close request
    curl_close($ch);
    return $response;
}

function sendNotify2($token, $titulo, $topicA, $topicB, $mensaje, $codigo, $tipo)
{
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    $data = array('title' => $titulo, 'body' => $mensaje, 'message' => $mensaje, 'valor' => $codigo, 'tipo' => $tipo);
    $arrayToSend = array(
        'condition' => "'$topicA' in topics || '$topicB' in topics",
        'notification' => $data, //EN MOTORIZADO ESTO NO VA
        'data' => $data,
        'priority' => 'high'
    );
    $json = json_encode($arrayToSend);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key= ' . $token; // key here

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    $response = curl_exec($ch);

    //Close request
    curl_close($ch);

    /*  $return["success"] = 1;
    $return["mensaje"] = ""; */
    $return["firebase"] = json_decode($response, true);
    $return["token"] = $token;
    return $return;
}

// * FIREBASE NOTIFICATION V1 
function sendNotify_v1($token, $titulo, $topic, $mensaje, $codigo, $tipo) {

    if ($topic == "")
        $topic = "general";

    $ch = curl_init("https://notifications.mie-commerce.com/api/pushNotification");
    $data = array('topic' => $topic, 'title' => $titulo, 'body' => $mensaje, 'message' => $mensaje, 'valor' => $codigo, 'tipo' => $tipo);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: application/json';

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    $response = curl_exec($ch);

    //Close request
    curl_close($ch);
    
    return json_decode($response, true);
}
