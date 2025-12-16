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
	
	$query = "SELECT ca.cod_orden, ca.fecha, ca.total, ca.is_envio, ca.referencia, ca.estado, ca.is_programado, ca.hora_retiro,
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
                return '<ul class="table-controls">
                    <li><a href="orden_detalle.php?id='.$row['cod_orden'].'" title="Ver orden"><i data-feather="eye"></i></a></li>
                </ul>';
            }
        ),
        // Campos adicionales que quieres acceder pero no mostrar (invisibles en la tabla)
        array( 'dt' => 11, 'db' => 'hora_retiro' ),
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
?>