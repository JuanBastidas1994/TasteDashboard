<?php
	/*	Variables Heredadas del Index
		$method - POST, GET, PUT, DELETE, etc.
		$request - Url y variables GET
		$input - Solo metodo POST, PUT */

require_once "clases/cl_usuarios.php";
$Clusuarios = new cl_usuarios();

	if($method == "GET"){
		$num_variables = count($request);
		if($num_variables == 2){
			$return = get();
			showResponse($return);
		}
		if($num_variables == 3){
			$first = $request[1];
			if($first=="ubicaciones"){
				$return = lista_ubicaciones($request[2]);
				showResponse($return);
			}
		}

		$return['success']= 0;
		$return['mensaje']= "Evento no existente";
		showResponse($return);
	}
	else if($method == "POST"){
		$num_variables = count($request);
		if($num_variables == 2){
			$first = $request[1];
			if($first=="login"){
				$return = login();
				showResponse($return);
			}else if($first=="registro"){
				$return = registro();
				showResponse($return);
			}
		}
		if($num_variables == 3){
			$first = $request[1];
			if($first=="ubicaciones"){
				$return = save_direccion($request[2]);
				showResponse($return);
			}else if($first=="password"){
				$return = change_password($request[2]);
				showResponse($return);
			}
		}
		
		$return['success']= 0;
		$return['mensaje']= "Evento no existente";
		showResponse($return);
	}else{
		$return['success']= 0;
		$return['mensaje']= "El metodo ".$method." para Login aun no esta disponible.";
		showResponse($return);
	}


/*FUNCIONES*/
function login(){
	global $Clusuarios;
	global $input;
	extract($input);

	if(!isset($email) || !isset($password)){
		$return['success'] = 0;
    	$return['mensaje'] = "Falta informacion";
		return $return;
	}

	extract($_POST);

	$resp = $Clusuarios->Login($email,$password);
    if($resp){
        $return['success'] = 1;
	    $return['mensaje'] = "Login correcto";
	    $return['data'] = $resp;
    }else{
        $return['success'] = 0;
	    $return['mensaje'] = "Login Incorrecto";
    }
    return $return;
}

function registro(){
	global $Clusuarios;
	global $input;
	extract($input);
	
	$datosObligatorios = array("nombre","apellido","correo","password","fecha_nacimiento","cedula");
	foreach ($datosObligatorios as $key => $value) {
		if (!array_key_exists($value, $input)) {
			$return['success'] = 0;
    		$return['mensaje'] = "Falta informacion, Error: Campo $value es obligatorio";
			return $return;
		}	
	}

    if($Clusuarios->usuarioDisponible($correo)){
    	$Clusuarios->cod_empresa = cod_empresa;
	    $Clusuarios->cod_rol = 4;
	    $Clusuarios->nombre = $nombre;
	    $Clusuarios->apellido = $apellido;
	    $Clusuarios->correo = $correo;
	    $Clusuarios->usuario = $correo;
	    $Clusuarios->password = $password;
	    $Clusuarios->fecha_nacimiento = $fecha_nacimiento;
	    $Clusuarios->num_documento = $cedula;

	    if($Clusuarios->registro()){
	    	$return['success'] = 1;
	    	$return['mensaje'] = "Registro completado con exito";
	    	/*LOGIN EN EL REGISTRO*/
	    		$resp = $Clusuarios->Login($correo,$password);
			    if($resp){
			        $return['login'] = 1;
				    $return['data'] = $resp;
			    }else{
			        $return['login'] = 0;
			    }
			/*LOGIN EN EL REGISTRO*/    
	    }else{
	    	$return['success'] = 0;
	    	$return['mensaje'] = "Error al registrar";
	    }
    }else{
    	$return['success'] = 0;
	    $return['mensaje'] = "Correo ya utilizado por otro usuario";
    }
    return $return;
}

function change_password($cod_usuario){
	global $Clusuarios;
	global $input;
	extract($input);
	
	$datosObligatorios = array("actual","nueva");
	foreach ($datosObligatorios as $key => $value) {
		if (!array_key_exists($value, $input)) {
			$return['success'] = 0;
    		$return['mensaje'] = "Falta informacion, Error: Campo $value es obligatorio";
			return $return;
		}	
	}

	$usuario = $Clusuarios->get($cod_usuario);
	if($usuario){
		$passwordOld = $usuario['password'];
		if($passwordOld == md5($actual)){
			if($Clusuarios->set_password($cod_usuario, $nueva)){
				$return['success'] = 0;
				$return['mensaje'] = html_entity_decode("Contrase&ntilde;a actualizada correctamente");
			}else{
				$return['success'] = 0;
				$return['mensaje'] = html_entity_decode("Error al actualizar la contrase&ntilde;a, por favor intentalo nuevamente");
			}
		}else{
			$return['success'] = 0;
			$return['mensaje'] = html_entity_decode("Tu contrase&ntilde;a actual es incorrecta, por favor intentalo nuevamente");
		}
	}else{
		$return['success'] = 0;
		$return['mensaje'] = "Usuario no existe";
	}
	return $return;
}

function get(){
	global $Clusuarios;
	global $request;
	$cod_usuario = $request[1];
	$array = $Clusuarios->get($cod_usuario);
	if($array)
	{
		$resp = array_map("html_entity_decode",$array);
		$return['success'] = 1;
		$return['mensaje'] = "Correcto";
		$return['data'] = $resp;
	}else{
		$return['success'] = 0;
		$return['mensaje'] = "No hay datos";
	}
	return $return;
}


/*-------------UBICACIONES------------------*/
function lista_ubicaciones($cod_usuario){
	global $Clusuarios;
	$direcciones = $Clusuarios->direcciones($cod_usuario);
	if($direcciones){
		$return['success'] = 1;
		$return['mensaje'] = "Correcto";
		$return['data'] = $direcciones;
	}else{
		$return['success'] = 0;
		$return['mensaje'] = "No hay datos";
	}
	return $return;
}

function save_direccion($cod_usuario){
	global $Clusuarios;
	global $input;
	extract($input);
	
	$datosObligatorios = array("nombre","direccion","latitud","longitud");
	foreach ($datosObligatorios as $key => $value) {
		if (!array_key_exists($value, $input)) {
			$return['success'] = 0;
    		$return['mensaje'] = "Falta informacion, Error: Campo $value es obligatorio";
			return $return;
		}	
	}

	$resp = $Clusuarios->save_direcciones($cod_usuario, $nombre, $direccion, $latitud, $longitud);
    if($resp){
        $return['success'] = 1;
	    $return['mensaje'] = "Direccion ingresada correctamente";
    }else{
        $return['success'] = 0;
	    $return['mensaje'] = "Error al ingresar la informacion";
    }
    return $return;
}
?>