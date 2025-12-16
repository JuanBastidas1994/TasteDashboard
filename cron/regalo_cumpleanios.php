<?php
require_once "../funciones.php";

$usuarios = [];
$usuariosArr = [];
$currentDate = date_format(date_create(fecha_only()), "-m-d");
$empresasID = "121, 16"; //* 16,24,70,121
$query = "SELECT e.cod_empresa, e.nombre, efp.compra_minimo_regalo_cumple, efp.valor_regalo_cumple, efp.dias_regalo_cumple, efp.cant_dias_caducidad_dinero, en.token 
            FROM tb_empresas e, tb_empresa_fidelizacion_puntos efp, tb_empresa_notificaciones en
            WHERE e.cod_empresa = efp.cod_empresa
            AND e.cod_empresa = en.cod_empresa
            AND e.fidelizacion = 1 
            AND e.cod_empresa IN($empresasID)
            AND e.estado = 'A'
            AND en.aplicacion = 'USUARIOS'
            AND en.estado = 'A'";
$empresas = Conexion::buscarVariosRegistro($query);
if($empresas) {
    foreach ($empresas as $empresa) {
        $cod_empresa = $empresa["cod_empresa"];
        $nombreEmpresa = $empresa["nombre"];
        $compra_minimo = $empresa["compra_minimo_regalo_cumple"];
        $valor_regalo = $empresa["valor_regalo_cumple"];
        $diasRegaloCumple = $empresa["dias_regalo_cumple"];
        $tokenFirebase = $empresa["token"];
        if($diasRegaloCumple == 0) {
            $diasRegaloCumple = $empresa["cant_dias_caducidad_dinero"];
        }

        $query = "SELECT c.cod_cliente, u.cod_usuario, u.fecha_nacimiento, u.nombre
                    FROM tb_clientes c, tb_usuarios u
                    WHERE c.cod_usuario = u.cod_usuario
                    AND c.cod_empresa = $cod_empresa
                    AND u.estado = 'A'
                    AND u.cod_rol = 4
                    AND u.fecha_nacimiento LIKE '%$currentDate%'";
        $usuarios = Conexion::buscarVariosRegistro($query);
        if($usuarios) {
            foreach ($usuarios as &$usuario) {
                $dineroEnCompras = 0;
                $cod_usuario = $usuario["cod_usuario"];
                $cod_cliente = $usuario["cod_cliente"];
                $topicoA = "usuario" . $cod_usuario;
                $topicoB = "usuariousuario" . $cod_usuario;

                $usuario["topicoA"] = $topicoA;
                $usuario["topicoB"] = $topicoB;
                $usuario["notificar"] = null;
                $usuario["regalo"]["valor"] = $valor_regalo;    
                
                if($compra_minimo > 0) {
                    $compra = getCompras($cod_usuario);
                    if($compra) {
                        $dineroEnCompras = $compra["total"];
                    }
                }

                if($dineroEnCompras >= $compra_minimo) {
                    $usuario["regalo"]["restriccion"] = "Cumple con la restricción";
                    if(addDinero($cod_cliente, $valor_regalo, $diasRegaloCumple)) {
                        $usuario["notificar"] = notificar($topicoA, $topicoB, $nombreEmpresa, $tokenFirebase, $cod_empresa);
                        $usuario["regalo"]["estado"] = "Regalo añadido correctamente";
                    }
                    else{
                        $usuario["regalo"]["estado"] = "Error al añadir regalo";
                    }
                }
                else {
                    $usuario["regalo"]["restriccion"] = "No cumple con la restricción";
                }

            }
            $usuariosArr[] = $usuarios;
        }
    }

}

$return["usuarios"] = $usuariosArr;

function getCompras($cod_usuario) {
    $query = "SELECT SUM(total) as total
                FROM tb_orden_cabecera 
                WHERE cod_usuario = $cod_usuario
                AND estado = 'ENTREGADA'
                GROUP BY cod_usuario";
    return Conexion::buscarRegistro($query);
}

function addDinero($cod_cliente, $valor_regalo, $diasRegaloCumple) {
    $fecha = fecha_only();
    $query = "INSERT INTO tb_cliente_dinero
                SET cod_cliente = $cod_cliente, cod_tipo_pago = 2, dinero = $valor_regalo, saldo = $valor_regalo, fecha = '$fecha', fecha_caducidad = DATE_ADD(NOW(), INTERVAL $diasRegaloCumple DAY), estado = 'A'";
    return Conexion::ejecutar($query, null);
}

function notificar($topicA, $topicB, $nombreEmpresa, $token, $cod_empresa) {
    $mensajeAlt = ", revisa tu crédito.";
    if($cod_empresa == 16)
        $mensajeAlt = "";

    $titulo = $nombreEmpresa;
    $mensaje = "Hoy es un grandioso día ¡Feliz Cumpleaños! y para celebrarlo $nombreEmpresa te da un regalo" . $mensajeAlt;
    $codigo = 0;
    $tipo = "NOTIFICACION";

    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    $data = array('title' =>$titulo , 'body' => $mensaje, 'message' => $mensaje, 'valor' => $codigo, 'tipo' => $tipo);
    $arrayToSend = array('condition' => "'$topicA' in topics || '$topicB' in topics", 
                            'notification' => $data, //EN MOTORIZADO ESTO NO VA
                            'data' => $data, 
                            'priority'=>'high'
                        );
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
                    
   /*  $return["success"] = 1;
    $return["mensaje"] = ""; */
    $return["firebase"] = json_decode($response, true);
    $return["token"] = $token;
    return $return;
}

/* GUARDAR LOGS */
$folder = "logs";
if (!file_exists($folder)) {
    mkdir($folder, 0777);
}
$file = $folder."/regalo_cumpleanios.log";
$fecha = fecha();
$log = "[$fecha] Se ejecutó el cronjob de cumpleaños => " . json_encode($usuariosArr);
file_put_contents($file, PHP_EOL . $log, FILE_APPEND);

header("Content-Type: application/json");
echo json_encode($return);
?>