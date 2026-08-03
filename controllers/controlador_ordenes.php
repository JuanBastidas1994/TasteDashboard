<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_ordenes.php";
$Clordenes = new cl_ordenes(NULL);
$session = getSession();

controller_create();

function getOrdenesEntrantes(){
    global $Clordenes;
    if(!isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clordenes->lista_gestion($estado, $tipo, $cod_sucursal);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['data'] = $resp;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay items para esta categoria";
    }
    return $return;
}

function renotificarOrden(){
    global $Clordenes;
    global $session;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $alias = $session['alias'];
    $remove = removeOrdenFirebase($alias, $id);
    $add = addOrdenFirebase($alias, $id, $cod_sucursal);

    $return['success'] = 1;
    $return['mensaje'] = "Notificó orden";
    return $return;
}


function addOrdenFirebase($alias, $id, $sucursal){
	$ProyectId = "ptoventa-3b5ed";
    $data = '{"estado":"ENTRANTE","id":'.$id.',"sucursal":'.$sucursal.'}';
    try {
    	$ch = curl_init("https://".$ProyectId.".firebaseio.com/ordenes/".$alias."/".$id.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
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

function removeOrdenFirebase($alias, $id){
    $ProyectId = "ptoventa-3b5ed";
    try {
        $link = "https://".$ProyectId.".firebaseio.com/ordenes/".$alias."/".$id.".json";
        echo $link;
    	$ch = curl_init("https://".$ProyectId.".firebaseio.com/ordenes/".$alias."/".$id.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
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

function datatable(){
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $where = "";
    $fecha = fecha();
    
    $payment = $_GET['payment'] ?? '';
    $tipo = $_GET['tipo'] ?? '';
    $tiempo = $_GET['tiempo'] ?? '';
    $cod_sucursal = $_GET['sucursal'] ?? '';
    
    if($session['cod_rol']==3){
        $cod_sucursal = $session['cod_sucursal'];
    }
    
    if($cod_sucursal !== ''){
        $where .= " AND ca.cod_sucursal =".$cod_sucursal;
    }
    
    if($tipo !== ''){
        $where .= ' AND ca.is_envio = '.$tipo;
    }
    
    if($payment !== ''){
        $where .= " AND ca.pago = '$payment'";
    }
    
    if($tiempo !== ''){
        if($tiempo == 'programadas'){
            $where .= " AND ca.hora_retiro > '$fecha'";
        }
    }
	
	$query = "SELECT ca.cod_orden, ca.fecha, ca.total, ca.is_envio, ca.referencia, ca.estado, ca.is_programado, ca.hora_retiro, ca.medio_compra,
                    CONCAT(u.nombre, ' ', u.apellido) as cliente, u.correo as email,
                    u.telefono as phone, s.nombre as sucursal,
                    GROUP_CONCAT(fp.descripcion SEPARATOR ', ') AS formas_pago
            FROM tb_orden_cabecera ca
            JOIN tb_usuarios u ON ca.cod_usuario = u.cod_usuario
            JOIN tb_sucursales s ON s.cod_sucursal = ca.cod_sucursal
            JOIN tb_orden_pagos op ON op.cod_orden = ca.cod_orden
            JOIN tb_formas_pago fp ON fp.cod_forma_pago = op.forma_pago
            WHERE ca.estado NOT IN('CREADA')
            AND ca.cod_empresa = ".$cod_empresa." 
            $where
            GROUP BY ca.cod_orden";
    $table = "($query) temp";

	
	$primaryKey = 'cod_orden';
    $columns = array(
        array( 'dt' => 0, 'db' => 'cod_orden'),
        array( 'dt' => 1, 'db' => 'cliente'),
        array( 'dt' => 2, 'db' => 'sucursal'),
        // array( 'dt' => 3, 'db' => 'fecha'),
        array( 'dt' => 3, 'db' => 'fecha',
            'formatter' => function($d, $row){
                return fechaHoraLatinoShort($d);
            }
        ),
        array( 'dt' => 4, 'db' => 'total',
            'formatter' => function($d, $row){
                return '$' . number_format($d, 2, '.', ','); 
            }
        ),
        array( 'dt' => 5, 'db' => 'formas_pago'),
        array( 'dt' => 6, 'db' => 'is_envio',
            'formatter' => function($d, $row){
                if($d==0)
                    return "Pickup";
                else if($d==1)
                    return "Delivery";
                else if($d==2)
                    return "En mesa";
                else
                    return "Pickup";
            }
        ),
        array( 'dt' => 7, 'db' => 'is_programado',
            'formatter' => function($d, $row){
                $text = 'Lo más pronto posible';
                if($d == 1){
                    $text = fechaHoraLatinoShort($row['hora_retiro']);
                }
                return "$text"; 
            }
        ),
        array( 'dt' => 8, 'db' => 'phone'),
        array( 'dt' => 9, 'db' => 'estado',
            'formatter' => function($d, $row){
                $status = $row['estado'];
                $colors = [
                    'ENTREGADA' => 'success',
                    'ASIGNADA' => 'warning',
                    'CANCELADA' => 'danger',
                    'ANULADA' => 'danger'
                ];
                $badge = isset($colors[$status]) ? $colors[$status] : 'primary';
                return '<span class="shadow-none badge badge-'.$badge.'">'.$status.'</span>';
            }
        ),
        array( 'dt' => 10, 'db' => 'cod_orden',
            'formatter' => function($d, $row){
                $medioIcon = '';
                if($row['medio_compra'] == 'IOS'){
                    $medioIcon = '<li><span title="Compra desde App iOS">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" style="fill:#1d1d1f !important;width:24px !important;height:24px !important;"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zm3.632-3.278c.837-1.012 1.4-2.427 1.245-3.831-1.207.052-2.662.805-3.532 1.817-.775.896-1.454 2.338-1.273 3.714 1.338.104 2.71-.688 3.56-1.7z"/></svg>
                    </span></li>';
                }
                else if($row['medio_compra'] == 'ANDROID'){
                    $medioIcon = '<li><span title="Compra desde App Android">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" style="fill:#1a9c53 !important;width:24px !important;height:24px !important;"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993.0003.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1367 1.0989L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/></svg>
                    </span></li>';
                }
                return '<ul class="table-controls">
                    <li><a href="orden_detalle.php?id='.$row['cod_orden'].'" title="Ver orden"><i data-feather="eye"></i></a></li>
                    '.$medioIcon.'
                </ul>';
            }
        ),
        // Campos adicionales que quieres acceder pero no mostrar (invisibles en la tabla)
        array( 'dt' => 11, 'db' => 'hora_retiro' ),
        array( 'dt' => 12, 'db' => 'medio_compra' ),
    );

    $sql_details = array(
        'type'=> 'mysql',
        'user' => usuario,
        'pass' => contrasena,
        'db'   => db,
        'host' => servidor
    );
    require( '../plugins/table/datatable/ssp.class.php' );
    return SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);
    
    
}

function datatableEventos(){
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $where = "";
    $fecha = fecha();

    $payment = $_GET['payment'] ?? '';
    $tipo = $_GET['tipo'] ?? '';
    $tiempo = $_GET['tiempo'] ?? '';
    $cod_sucursal = $_GET['sucursal'] ?? '';

    if($session['cod_rol']==3){
        $cod_sucursal = $session['cod_sucursal'];
    }

    if($cod_sucursal !== ''){
        $where .= " AND ca.cod_sucursal =".$cod_sucursal;
    }

    if($tipo !== ''){
        $where .= ' AND ca.is_envio = '.$tipo;
    }

    if($payment !== ''){
        $where .= " AND ca.pago = '$payment'";
    }

    if($tiempo !== ''){
        if($tiempo == 'programadas'){
            $where .= " AND ca.hora_retiro > '$fecha'";
        }
    }

	$query = "SELECT ca.cod_orden, ca.fecha, ca.total, ca.is_envio, ca.referencia, ca.estado, ca.is_programado, ca.hora_retiro,
                    CONCAT(u.nombre, ' ', u.apellido) as cliente, u.correo as email,
                    u.telefono as phone, s.nombre as sucursal,
                    GROUP_CONCAT(fp.descripcion SEPARATOR ', ') AS formas_pago,
                    oe.dia as dia_evento,
                    oe.estado as estado_evento,
                    oe.cod_producto,
                    p.nombre as nombre_producto
            FROM tb_orden_cabecera ca
            JOIN tb_orden_evento oe ON oe.cod_orden = ca.cod_orden
            JOIN tb_usuarios u ON ca.cod_usuario = u.cod_usuario
            JOIN tb_sucursales s ON s.cod_sucursal = ca.cod_sucursal
            JOIN tb_orden_pagos op ON op.cod_orden = ca.cod_orden
            JOIN tb_formas_pago fp ON fp.cod_forma_pago = op.forma_pago
            LEFT JOIN tb_productos p ON p.cod_producto = oe.cod_producto
            WHERE ca.estado NOT IN('CREADA')
            AND ca.cod_empresa = ".$cod_empresa."
            $where
            GROUP BY ca.cod_orden, oe.dia, oe.estado, oe.cod_producto, p.nombre";
    $table = "($query) temp";


	$primaryKey = 'cod_orden';
    $columns = array(
        array( 'dt' => 0, 'db' => 'cod_orden'),
        array( 'dt' => 1, 'db' => 'cliente'),
        array( 'dt' => 2, 'db' => 'fecha',
            'formatter' => function($d, $row){
                return fechaHoraLatinoShort($d);
            }
        ),
        array( 'dt' => 3, 'db' => 'total',
            'formatter' => function($d, $row){
                return '$' . number_format($d, 2, '.', ',');
            }
        ),
        array( 'dt' => 4, 'db' => 'formas_pago'),
        array( 'dt' => 5, 'db' => 'is_envio',
            'formatter' => function($d, $row){
                if($d==0)
                    return "Pickup";
                else if($d==1)
                    return "Delivery";
                else if($d==2)
                    return "En mesa";
                else
                    return "Pickup";
            }
        ),
        array( 'dt' => 6, 'db' => 'dia_evento',
            'formatter' => function($d, $row){
                return fechaLatino($d);
            }
        ),
        array( 'dt' => 7, 'db' => 'phone'),
        array( 'dt' => 8, 'db' => 'nombre_producto'),
        array( 'dt' => 9, 'db' => 'estado_evento',
            'formatter' => function($d, $row){
                $status = $row['estado_evento'];
                $badge = $status === 'EJECUTADO' ? 'success' : 'primary';
                return '<span class="shadow-none badge badge-'.$badge.'">'.$status.'</span>';
            }
        ),
        array( 'dt' => 10, 'db' => 'cod_orden',
            'formatter' => function($d, $row){
                $checkBtn = '';
                if($row['estado_evento'] === 'EJECUTAR'){
                    $checkBtn = '<li><a href="javascript:void(0)" class="btnEjecutarEvento" data-cod_orden="'.$row['cod_orden'].'" title="Marcar como ejecutado"><i data-feather="check-circle"></i></a></li>';
                }
                return '<ul class="table-controls">
                    <li><a href="orden_detalle.php?id='.$row['cod_orden'].'" title="Ver orden"><i data-feather="eye"></i></a></li>
                    '.$checkBtn.'
                </ul>';
            }
        ),
        array( 'dt' => 11, 'db' => 'hora_retiro' ),
        array( 'dt' => 12, 'db' => 'estado_evento' ),
    );

    $sql_details = array(
        'type'=> 'mysql',
        'user' => usuario,
        'pass' => contrasena,
        'db'   => db,
        'host' => servidor
    );
    require( '../plugins/table/datatable/ssp.class.php' );
    return SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);


}

function ejecutarEvento(){
    $cod_orden = $_POST['cod_orden'] ?? '';
    if(empty($cod_orden)){
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }
    $query = "UPDATE tb_orden_evento SET estado = 'EJECUTADO' WHERE cod_orden = ?";
    Conexion::ejecutar($query, [intval($cod_orden)]);
    return ['success' => 1, 'mensaje' => 'Evento marcado como ejecutado'];
}

function getOrdersFlota(){
     global $Clordenes;

    extract($_GET);
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $resp = $Clordenes->getListOrdersFlota($comercios, $estados);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['data'] = $resp;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay items para mostrar";
    }
    return $return;
}

function asignarFlota(){
     global $Clordenes;

    extract($_GET);
    global $session;
    $resp = $Clordenes->asignarMotorizadoFlota($orden, $motorizado);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Asignado" . $resp;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo asignar" . $resp;
    }
    return $return;
}

function datatableChangeStatus() {
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $event_id = (isset($_GET['event_id'])) ? $_GET['event_id'] : 0;

    $status = "";

    $filterStatuses = "";
    if($status == "") {
        $filterStatuses = "WHERE oc.estado IN ('ENTRANTE', 'ACEPTADA', 'ENVIANDO', 'ENTREGADA', 'ASIGNADA', 'NO_ENTREGADA', 'CANCELADA', 'ANULADA')";
    }

    $table ="   (
                    SELECT oc.cod_orden,
                           e.nombre AS nombre_empresa,
                           u.nombre AS nombre_usuario,
                           CONCAT('$', oc.total) AS total,
                           oc.fecha, 
                           oc.estado
                    FROM tb_orden_cabecera oc
                    INNER JOIN tb_empresas e ON oc.cod_empresa = e.cod_empresa
                    INNER JOIN tb_usuarios u ON oc.cod_usuario = u.cod_usuario
                    {$filterStatuses}
                    ORDER BY oc.cod_orden DESC
                ) temp
    ";
    
    $primaryKey = 'cod_orden';
    $x = 0;
    $columns = array(
        array( 'dt' => $x++, 'db' => 'cod_orden'),
        array( 'dt' => $x++, 'db' => 'nombre_empresa'),
        array( 'dt' => $x++, 'db' => 'nombre_usuario'),
        array( 'dt' => $x++, 'db' => 'total'),
        array( 'dt' => $x++, 'db' => 'fecha'),
        array( 'dt' => $x++, 'db' => 'estado'),
        array( 'dt' => $x++, 'db' => 'cod_orden',
            'formatter' => function($d, $row) {
                $estado = 'ENTREGADA';
                $class = 'success';

                if($row['estado'] === "ENTREGADA") {
                    $estado = 'ANULADA';
                    $class = 'danger';
                }

                $html = "
                    <ul class='table-controls'>
                        <li>
                            <button class='btn btn-outline-{$class}' onclick='changeStatusConfirm({$row['cod_orden']}, \"{$estado}\")'>
                                Cambiar estado a {$estado}
                            </button>
                        </li>
                    </ul>
                ";
                return $html;
            }
        ),
    );

    $sql_details = array(
        'type'=> 'mysql',
        'user' => usuario,
        'pass' => contrasena,
        'db'   => db,
        'host' => servidor
    );
    require( '../plugins/table/datatable/ssp.class.php' );
    return SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);
}

function changeStatus() {
    global $Clordenes;

    $POST = file_get_contents("php://input");
    extract(json_decode($POST, true));

    $resp = $Clordenes->set_estado($orden, $estado);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Estado cambiado";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo cambiar el estado";
    }
    return $return;
}

function getCalificacionesReport()
{
    global $Clordenes;
    global $session;
    extract($_POST);
    $cod_empresa=$session['cod_empresa'];

    $detalle = $Clordenes->getCalificacionesDetalle($fecha_inicio, $fecha_fin, $cod_empresa, $cod_sucursal ?? 0);
    $resumen = $Clordenes->getCalificacionesResumen($fecha_inicio,$fecha_fin,$cod_empresa,$cod_sucursal ?? 0);

    if (!$resumen || $resumen['total'] == 0) {
        return [
            'negativas' => [],
            'positivas' => [],
            'resumen' => [
                'promedio' => 0,
                'negativas' => 0,
                'positivas' => 0,
                'total' => 0,
                'porcentaje_negativas' => 0,
                'porcentaje_positivas' => 0
            ]
        ];
        return;
    }


    $negativas = [];
    $positivas = [];
    foreach ($detalle as $row) {
        if ($row['calificacion'] < 4) {
            $negativas[] = $row;
        } else {
            $positivas[] = $row;
        }
    }

    return [
        'negativas' => $negativas,
        'positivas' => $positivas,
        'resumen' => [
            'promedio' => (float)$resumen['promedio'],
            'negativas' => (int)$resumen['negativas'],
            'positivas' => (int)$resumen['positivas'],
            'total' => (int)$resumen['total'],
            'porcentaje_negativas' => round(($resumen['negativas'] * 100) / $resumen['total'], 2),
            'porcentaje_positivas' => round(($resumen['positivas'] * 100) / $resumen['total'], 2)
        ]
    ];
}
?>