<?php
require_once "funciones.php";
//METODOS
$funciones = array(
    "usuarios"   => "controllers/Usuarios.php",
    "ordenes"   => 	"controllers/Ordenes.php",
    "puntos" => "controllers/Fidelizacion.php",
    "configuracion" => "controllers/Configuracion.php"
);

//DANILO Api-DA3445561Ad128
//OAHU Api-OAHU2007270820
//OAHU LOCAL Api-OAHU154662154

$empresa = NULL;
if(verificateWs($empresa))
{
    $cod_empresa = $empresa['cod_empresa'];
	$alias = $empresa['alias'];
	$files = url_sistema.'assets/empresas/'.$alias.'/';
	define('cod_empresa',$cod_empresa);
	define('alias',$alias);
	define('url',$files);

	$method = $_SERVER['REQUEST_METHOD'];
	$request = explode('/', trim($_SERVER['ORIG_PATH_INFO'],'/'));
	if(count($request)>0){
		if (array_key_exists($request[0], $funciones)) {
			if($method == "POST"){
			    $json = file_get_contents('php://input');
			    file_put_contents("json_entrante.log", PHP_EOL . $json, FILE_APPEND);
			    $input = json_decode($json,true);
				if (JSON_ERROR_NONE !== json_last_error()){
					$return['success']= -1;
					$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
					showResponse($return);
				}
				if(count($input)==0){
					$return['success']= -1;
					$return['mensaje']= "No hay valor de entrada";
					showResponse($return);
				}
			}

		    require_once $funciones[$request[0]];
		}else{
			$return['success']= -1;
			$return['mensaje']= "Evento ".$request[0]." no existente, por favor verificar la URL.";
		}
	}
}
else
{
	$return['success']= -1;
	$return['mensaje']= "No autorizado";
	showResponse($return);
}

$return['success']= 0;
$return['mensaje']= "No hay respuesta";
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: *");
header("Content-type:application/json");
echo json_encode($return);

function showResponse($return){
	/* MANEJO DE ERRORES */
	switch ($return['success']) {
		case -1:	//NO AUTORIZADO
			http_response_code(401);
			break;
		case 1:
			http_response_code(200);
			break;
		case 0:
			http_response_code(200);
			break;
		default:
			http_response_code(401);
			break;
	}
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: *");
	header("Content-type:application/json; charset=utf-8");
	echo json_encode($return);
	exit();
}

?>