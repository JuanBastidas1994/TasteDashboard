<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_ordenes.php";
require_once "../clases/cl_gacela.php";
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_clientes.php";
$Clusuario = new cl_usuarios();
$Clsucursales = new cl_sucursales();
$Clordenes = new cl_ordenes();
$Clgacela = new cl_gacela(NULL);
$Clempresas = new cl_empresas();
$Clclientes = new cl_clientes();
$session = getSession();

controller_create();

function crear(){
    global $session;
    global $Clusuario;
    global $Clsucursales;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $nameImg = 'profile_'.datetime_format().'.jpg';
    $Clusuario->cod_rol = $cmbRol;
    $Clusuario->nombre = $txt_nombre;
    $Clusuario->apellido = $txt_apellido;
    $Clusuario->telefono = $txt_telefono;
    $Clusuario->imagen = $nameImg;
    $Clusuario->correo = $txt_correo;
    $Clusuario->usuario = $txt_correo;
    $Clusuario->password = $txt_password;
    $Clusuario->fecha_nacimiento = $fecha_nacimiento;
    $Clusuario->cod_sucursal = $cmbSucursal;
    $Clusuario->estado = 'A';
    if(isset($txt_placa))
        $Clusuario->placa = $txt_placa;
    
    $sucursal = $Clsucursales->getInfo($cmbSucursal);

    if(!isset($_POST['cod_usuario'])){
        $id=0;
        if($Clusuario->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Usuario creado correctamente";
            $return['id'] = $id;
            $return['usuario'] = $Clusuario->get($id);

            /*SUBIR IMAGEN*/
            if(!uploadFile($_FILES["img_profile"], $nameImg)){
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                @copy($img1, $img2);  
            }
            if($Clusuario->crearKeystore($id, $txt_password, 0))
                $return['keystore'] = "Se creó";
            else
                $return['keystore'] = "No se creó";
            
            if($cmbRol == 17){ //Es un motorizado
                if($sucursal)
                    $Clusuario->setUserLocation($id, $sucursal['latitud'], $sucursal['longitud']);
                else
                    $Clusuario->setUserLocation($id, -2.1724405, -79.8946697);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el usuario, por favor vuelva a intentarlo";
        }
    }else{
        $usuario = $Clusuario->get($cod_usuario);
        if(!$usuario){
            $return['success'] = 0;
            $return['mensaje'] = "No existe el usuario";
        }
        
        $Clusuario->cod_usuario = $cod_usuario;
        if($Clusuario->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Usuario editado correctamente";
            $return['id'] = $Clusuario->cod_usuario;
            $return['usuario'] = $Clusuario->get($cod_usuario);

            if("" <> $txt_password){
                if($Clusuario->crearKeystore($cod_usuario, $txt_password, 0))
                    $return['keystore'] = "Se creó";
                else
                    $return['keystore'] = "No se creó";
            }
            
            if($cmbRol == 17){ //Es un motorizado
                if($usuario['latitud'] == ""){
                    if($sucursal)
                        $Clusuario->setUserLocation($cod_usuario, $sucursal['latitud'], $sucursal['longitud']);
                    else
                        $Clusuario->setUserLocation($cod_usuario, "-2.1724405", "-79.8946697");
                }
            }

            $usuario = $Clusuario->get($cod_usuario);
            if($usuario)
                uploadFile($_FILES["img_profile"], $usuario['imagen']);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el usuario";
        }
    }
    return $return;
}

/**
 * 
*/
function crearFlota(){
    global $session;
    global $Clusuario;
 //   global $Clsucursales;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $nameImg = 'profile_'.datetime_format().'.jpg';
    $Clusuario->cod_rol = $cmbRol;
    $Clusuario->nombre = $txt_nombre;
    $Clusuario->apellido = $txt_apellido;
    $Clusuario->telefono = $txt_telefono;
    $Clusuario->imagen = $nameImg;
    $Clusuario->correo = $txt_correo;
    $Clusuario->usuario = $txt_correo;
    $Clusuario->password = $txt_password;
    $Clusuario->placa = $txt_placa;
    $Clusuario->estado = 'A';
    
    //$sucursal = $Clsucursales->getInfo($cmbSucursal);

    if(!isset($_POST['cod_usuario'])){
        $id=0;
        if($Clusuario->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Usuario creado correctamente";
            $return['id'] = $id;
            $return['usuario'] = $Clusuario->get($id);

            /*SUBIR IMAGEN*/
            if(!uploadFile($_FILES["img_profile"], $nameImg)){
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                @copy($img1, $img2);  
            }
            if($Clusuario->crearKeystore($id, $txt_password, 0))
                $return['keystore'] = "Se creó";
            else
                $return['keystore'] = "No se creó";
            
            if($cmbRol == 17){ //Es un motorizado
                $Clusuario->setUserLocation($id, -2.1724405, -79.8946697);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el usuario, por favor vuelva a intentarlo";
        }
    }else{
        $usuario = $Clusuario->get($cod_usuario);
        if(!$usuario){
            $return['success'] = 0;
            $return['mensaje'] = "No existe el usuario";
        }
        
        $Clusuario->cod_usuario = $cod_usuario;
        if($Clusuario->editarFlota()){
            $return['success'] = 1;
            $return['mensaje'] = "Usuario editado correctamente";
            $return['id'] = $Clusuario->cod_usuario;
            $return['usuario'] = $usuario;

            if("" <> $txt_password){
                if($Clusuario->crearKeystore($cod_usuario, $txt_password, 0))
                    $return['keystore'] = "Se creó";
                else
                    $return['keystore'] = "No se creó";
            }
            
            if($cmbRol == 17){ //Es un motorizado
                if($usuario['latitud'] == ""){
                    $Clusuario->setUserLocation($cod_usuario, "-2.1724405", "-79.8946697");
                }
            }

            $usuario = $Clusuario->get($cod_usuario);
            if($usuario)
                uploadFile($_FILES["img_profile"], $usuario['imagen']);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el usuario";
        }
    }
    return $return;
}

function restablecer(){
    global $session;
    global $Clusuario;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $Clusuario->usuario = $txt_usuario;
    
    if($txt_password_new <> ""){
        if($Clusuario->set_password($cod_usuario, $txt_password_new)){
            $return['mensajedos'] = "Contraseña editada correctamente";
            if($Clusuario->crearKeystore($cod_usuario, $txt_password_new, 1))
                $return['keystore'] = "Se creó";
            else
                $return['keystore'] = "No se creó";
        }
        else{
            $return['mensaje2'] = "Error al editar la contraseña";
        }
    }
    
    if($Clusuario->restablecerUsuario($cod_usuario)){
        $return['success'] = 1;
        $return['mensaje'] = "Usuario editado correctamente ";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar el usuario";
    }
    return $return;
}

function get(){
    global $Clusuario;
    global $session;
    if(!isset($_GET['cod_usuario'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clusuario->getArray($cod_usuario, $array)){
        $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
        $array['imagen'] = $files.$array['imagen'];

        $return['success'] = 1;
        $return['mensaje'] = "Usuario encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Usuario no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function get_ubicacion(){
    global $Clusuarios;
    if(!isset($_GET['cod_usuario'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clusuarios->get($cod_usuario);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Encontrado";
        $return['data'] = $resp;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay motorizados registrados";
    }
    return $return;
}

function get_ubicacionGacela(){
    global $Clgacela;
    if(!isset($_GET['cod_orden'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clgacela->trackingOrder($cod_token);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Encontrado";
      $driver = $resp->results->driver;
      $lat = "";
      $lng = "";
      if($driver != null)
      {
          $lat=$resp->results->driver->lat;
          $lng=$resp->results->driver->lng;
      }
        $return['data']['latitud'] = $lat;
        $return['data']['longitud'] = $lng;
    }else{
        $return['success'] = 0;
        $return['mensaje'] =$resp->status;
    }
    return $return;
}

function get_ubicacionAleatorio(){
    global $Clgacela;
    if(!isset($_GET['cod_orden'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

        $return['success'] = 1;
        $return['mensaje'] = "Encontrado";
      
      $ubicacion = array("-2.202105,-79.914176", "-2.202405, -79.912738", "-2.202904, -79.911365", "-2.203772, -79.911231", "-2.204941, -79.911467", "-2.204362, -79.913505");
      $r = $ubicacion[array_rand($ubicacion)];
      $c=explode(" ", $r);
      $lat=$c[0];
      $lng=$c[1];
        $return['data']['latitud'] = $lat;
        $return['data']['longitud'] = $lng;
    return $return;
}

function set_estado(){
	global $Clusuario;
	if(!isset($_GET['cod_usuario']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clusuario->set_estado($cod_usuario, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Usuario editado correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el usuario";
    }
    return $return;
}

function set_password(){
    global $session;
	global $Clusuario;
	if(!isset($_GET['password'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clusuario->set_password($session['cod_usuario'], $password);
    if($resp){
        if($Clusuario->crearKeystore($session['cod_usuario'], $password, 0))
            $return['keystore'] = "Se creó";
        else
            $return['keystore'] = "No se creó";
    	$return['success'] = 1;
    	$return['mensaje'] = "Password editada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la password";
    }
    return $return;
}

function recuperar_password(){
    global $Clusuario;
    if(!isset($_GET['usuario'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $usuario = $Clusuario->getByUsuario($usuario);
    if($usuario){
        if($usuario['estado']=="A"){
            $password = passRandom();
            if($Clusuario->set_password($usuario['cod_usuario'], $password)){
                $return['success'] = 1;
                $return['mensaje'] = "Clave reestablecida, hemos enviado un correo electronico con tu nueva clave para que puedas acceder al sistema, recuerda cambiarla para mayor seguridad";
                $return['password'] = $password;
                if($Clusuario->crearKeystore($usuario['cod_usuario'], $password, 1))
                    $return['keystore'] = "Se creó";
                else
                    $return['keystore'] = "No se creó";
            }else{
                $return['success'] = 0;
                $return['mensaje'] = "No hemos podido reestablecerte una clave, intentalo nuevamente. Si el problema persiste comunicate con soporte";
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Tu usuario ha sido deshabilitado, comunicate con el administrador o con Soporte.";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "El usuario no existe, por favor verifica si la informacion ingresada es correcta";
    }

    return $return;
}

function lista_motorizados(){
    global $Clusuario;
    $lista = $Clusuario->lista_motorizados();
    if($lista){
        $return['success'] = 1;
        $return['mensaje'] = "Lista de motorizados";
        $return['data'] = $lista;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay motorizados registrados";
    }
    return $return;
}

function info_motorizado(){
    global $Clusuario;
    global $Clordenes;
    extract($_POST);
    $info = $Clusuario->info_motorizado($codigo);
    if($info){
        
        $html.='<div>
                	<h3 style="border-bottom: 1px solid #dee2e6;">Datos del Motorizado</h3>
                	<p><span><b>Nombre: </b>'.$info['nombre'].''.$info['apellido'].'</span> </p>
                	<p><span><b>Cedula/Ruc: </b>'.$info['num_documento'].'</span></p>
                	<p><span><b>Correo: </b>'.$info['correo'].'</span></p>
                	<p><span><b>Telefóno: </b>'.$info['telefono'].'</span></p>
                	<p><span><b>Dirección: </b>'.$info['direccion'].'</span></p>
                </div>';
    
        
        $info = $Clordenes->OrdenesMotorizado($codigo);
        foreach ($info as $orden) {
            $badge='primary';
            if($orden['estado'] == 'I')
                $badge='danger';
            else if($orden['estado'] == "ENTREGADA")
                $badge='success';
            else if($orden['estado'] == "ASIGNADA")
                $badge='warning';
        $tabla.='<tr>
                    <td>'.$orden['cod_orden'].'</td>
                    <td>'.$orden['nombre'].' '.$orden['apellido'].'</td>
                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($orden['estado']).'</span></td>
                    <td class="text-center">
                        <ul class="table-controls">
                            <li><a href="orden_detalle.php?id='.$orden['cod_orden'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                        </ul>
                    </td>
                </tr>';
        }
    
        $return['success'] = 1;
        $return['mensaje'] = "Lista de motorizados";
        $return['html'] = $html;
        $return['tabla'] = $tabla;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay motorizados registrados";
    }
    return $return;
}

function login(){
    global $Clusuario;
    if(!isset($_POST['username']) || !isset($_POST['password'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_POST);

    if($username == "" || $password == ""){
        $return['success'] = 0;
        $return['mensaje'] = "El usuario y el password son campos obligatorios";
        return $return;
    }

    $navigator = (isset($_POST['navigator'])) ? $_POST['navigator'] : "";
    $operative_system = (isset($_POST['operative_system'])) ? $_POST['operative_system'] : "";
    $is_mobile = (isset($_POST['is_mobile'])) ? $_POST['is_mobile'] : 0;

    $username = htmlentities($username);
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "0.0.0.0";
    $resp = $Clusuario->LoginV2($username,$password);
    if($resp){
        if($resp['cod_rol'] == "5" || $resp['cod_rol'] == "3"){ //Si es un cajero o un administrador sucursal borrarle todos los inicios de sesion anteriores
            $oldTokens = $Clusuario->getAllAuthTokens($resp['cod_usuario']);
            foreach ($oldTokens as $key => $value) {
                removeTokenFirebase($value['token']);
            }
            $Clusuario->DisabledAllAuthTokens($resp['cod_usuario']);
        }

        $token = md5(session_id()."-".$resp['cod_empresa']."-".$resp['cod_usuario']);
        $fecha_expira = time() + 365 * 24 * 60 * 60;
        if($Clusuario->setRememberLogin($resp['cod_usuario'], $token, $fecha_expira, $navigator, $operative_system, $is_mobile)){
            setSession($resp);
            $prefijo = $Clusuario->getLang($resp['cod_idioma']);
            setcookie("token",$token, $fecha_expira, '/');
            setcookie("alias",$resp['alias'], $fecha_expira, '/');
            setcookie("lang", $prefijo, $fecha_expira, '/'); //Setear o Crear una cookie
            
            $return['success'] = 1;
            $return['mensaje'] = "Login Correcto";
            $return['id'] = $resp['cod_usuario'];
            $return['alias'] = $resp['alias'];
            $return['token'] = $token;
            $password = md5($password);
            
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo crear un token de autenticacion";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Credenciales incorrectas";
    }

    //SET LOG AUTH INTENT LOGIN
    $idUser = isset($resp['cod_usuario']) ? $resp['cod_usuario'] : 0;
    $Clusuario->setIntentLogin($username, $password, session_id(), $ip, $return['success'], $idUser);
    return $return;
}

function loginAutomatico(){
    global $Clusuario;
    if(!isset($_POST['username']) || !isset($_POST['password'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $Clusuario->LoginAutomatico($username,$password);
    if($resp){
        $token = md5(session_id()."-".$resp['cod_empresa']);
        $fecha_expira = time() + 365 * 24 * 60 * 60;
        if($Clusuario->setRememberLogin($resp['cod_usuario'], $token, $fecha_expira)){
            setSession($resp);
            $prefijo = $Clusuario->getLang($resp['cod_idioma']);
            setcookie("token",$token, $fecha_expira, '/');
            setcookie("alias",$resp['alias'], $fecha_expira, '/');
            setcookie("lang", $prefijo, $fecha_expira, '/'); //Setear o Crear una cookie
            $return['success'] = 1;
            $return['mensaje'] = "Login Correcto";
            $return['id'] = $resp['cod_usuario'];
            $return['alias'] = $resp['alias'];
            $return['token'] = $token;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo crear un token de autenticacion";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Credenciales incorrectas";
    }
    return $return;
}

function removeTokenFirebase($token){
    $ProyectId = "ptoventa-3b5ed";
    try {
        $ch = curl_init("https://".$ProyectId.".firebaseio.com/auth/".$token.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            return curl_errno($ch);
        }
        curl_close($ch);
        return $response;
    } catch (Exception $e) {
        return false;
    }
}

function logout(){
    global $session;
    try {
        $return['alias'] = $session['alias'];
        $return['id'] = $session['cod_usuario'];

        destroySession();
        $return['success'] = 1;
        $return['mensaje'] = "Sesion cerrada exitosamente";
    } catch (Exception $e) {
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo cerrar la sesion";
    }
    return $return;
}


function subscribeTokenToTopic(){
    if(!isset($_POST['token']) || !isset($_POST['topic'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $ch = curl_init("https://iid.googleapis.com/iid/v1/".$token."/rel/topics/".$topic);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='.firebaseMessagingToken;
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
}

function unSubscribeTokenToTopic(){
    if(!isset($_POST['token']) || !isset($_POST['topic'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $ch = curl_init("https://iid.googleapis.com/iid/v1/".$token."/rel/topics/".$topic);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='.firebaseMessagingToken;
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);

    echo "Desuscrito al topic ".$topic." - code: ".$info['http_code'];
}

function editarCedula(){
    global $Clusuario;

    extract($_GET);
    if($Clusuario->editarCedula($cod_usuario, $num_documento)){
        $return['success'] = 1;
        $return['mensaje'] = "Número de documento editado";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar";
    }
    return $return;
}

    function set_estado_cliente(){
        global $Clusuario;
        
        extract($_GET);
        
        $estado = "D";
        $Clusuario->motivoBloqueo = $motivo;
        $resp = $Clusuario->set_estado($cod_usuario, $estado);
        if($resp){
            $Clusuario->set_estado_cliente($cod_usuario, $estado);
            $return['success'] = 1;
            $return['mensaje'] = "Cliente editado correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el Cliente";
        }
        return $return;
    }

    function editarCliente(){
        global $Clusuario;
        global $session;
        $cod_empresa = $session['cod_empresa'];

        extract($_POST);

        $Clusuario->nombre = $txt_nombres;
        $Clusuario->apellido = $txt_apellidos;
        $Clusuario->fecha_nacimiento = $fecha_nacimiento;
        $Clusuario->num_documento = $txt_cedula;
        $Clusuario->telefono = $txt_telefono;
        $Clusuario->direccion = $txt_direccion;

        if($Clusuario->cedulaRepetida($txt_cedula, $cod_usuario, $cod_empresa)){
            $return['success'] = 0;
            $return['mensaje'] = "El número de documento ya está registrado";
            return $return;
        }

        if($Clusuario->editarCliente($cod_usuario)){
            $return['success'] = 1;
            $return['mensaje'] = "Cliente editado correctamente";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el cliente";
        }
        return $return;
    }

    function getUserAdmin(){
        global $Clempresas;
        global $Clusuario;
        extract($_GET);

        $empresa = $Clempresas->getByAlias($alias);
        $usuario = $Clusuario->getUserByEmpresa($empresa['cod_empresa'], $user);
        if($usuario){
            $return['usuario'] = $usuario['usuario'];
            $return['pass'] = $usuario['password'];
            $return['success'] = 1;
            $return['mensaje'] = "Usuario obtenido";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al obtener el usuario";
        }
        return $return;
    }

    function setCredito(){
        if(!isset($_POST['cod_usuario']) || !isset($_POST['credito'])){
            $return['success'] = 0;
            $return['mensaje'] = "Falta informacion";
            return $return;
        }
    
        global $Clclientes;
        global $session;
        extract($_POST);
    
        $cliente = $Clclientes->getByCodUsuario($cod_usuario);
        if ($cliente) {
            if($Clclientes->AddDinero($credito, $cliente["cod_cliente"], 6, 12, $session['cod_usuario'])){
                $return['success'] = 1;
                $return['mensaje'] = "Crédito agregado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al dar el crédito";
            }
        } 
        else {
            $return['success'] = 0;
            $return['mensaje'] = "Cliente no existe";
        }
        return $return;
    }
    
function getUserByToken(){
    global $Clusuario;
    if(!isset($_POST['token'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_POST);
    
    $user = $Clusuario->getRememberLogin($_COOKIE['token']);
    if(!$user){
        return [ 'success' => 0, 'mensaje' => 'Token no valido' ];
    }
    
    $roles_order_sound = [2,3,5];
    if(!in_array($user['cod_rol'], $roles_order_sound)){
        return [ 'success' => 0, 'mensaje' => 'No debe sonar ordenes entrantes' ];
    }
    
    // if($user['cod_rol'] !== 2){ //Debo buscar la sucursal a la que pertenece
        
    // }
    return [ 'success' => 1, 'mensaje' => 'valido', 'data' => $user ];
}
?>