<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_codigos_promocionales.php";
$Clcodigo = new cl_codigos_promocionales();
$session = getSession();

controller_create();

function crear(){
    global $Clcodigo;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    if(trim($txt_codigo) == "")
        $codigo = passRandom();
    else
        $codigo = trim($txt_codigo);
    
    $Clcodigo->codigo = $codigo;
    $Clcodigo->tipo = "descuento";
    $Clcodigo->por_o_din = $cmbTipo;
    $Clcodigo->monto = $txt_monto;
    $Clcodigo->cantidad = $txt_cantidad;
    $Clcodigo->usos_restantes = $txt_cantidad;
    $Clcodigo->restriccion = $txt_restriccion;
    $Clcodigo->fecha_expiracion = $fecha_expiracion;
    $Clcodigo->estado = 'A';
    $Clcodigo->usoIlimitado = $usoIlimitado;

    if(!isset($_POST['cod_codigo_promocional'])){
        $id=0;
        if($Clcodigo->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Codigo promocional guardado correctamente";
            $return['id'] = $id;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el codigo promocional";
        }
    }else{
        $Clcodigo->cod_codigo_promocional = $cod_codigo_promocional;
        if($Clcodigo->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Codigo promocional editado correctamente";
            $return['id'] = $Clcodigo->cod_codigo_promocional;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el codigo promocional";
        }
    }
    return $return;
}

function get(){
    global $Clcodigo;
    if(!isset($_GET['cod_codigo_promocional'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clcodigo->getArray($cod_codigo_promocional, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Codigo promocional encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Codigo promocional no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clcodigo;
	if(!isset($_GET['cod_codigo_promocional']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clcodigo->set_estado($cod_codigo_promocional, $estado);
    if(count($resp)>0){
    	$return['success'] = 1;
    	$return['mensaje'] = "Codigo promocional editado correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el codigo promocional";
    }
    return $return;
}

// CUPONES A CLIENTES
function getCoupons(){
    global $Clcodigo;
    global $session;

    $cupones = $Clcodigo->getCoupons();
    if($cupones){
        foreach ($cupones as &$cupon) {
            $cupon["imagen"] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$cupon["imagen"];
            $cupon["estado"] = "Activo";
            if($cupon["estado"] == "I")
                $cupon["estado"] = "Inactivo";
        }
        $return['success'] = 1;
        $return['mensaje'] = "Lista de cupones";
        $return['data'] = $cupones;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay cupones";
    return $return;
}

function setCoupon(){
    global $Clcodigo;

    extract($_POST);

    $Clcodigo->titulo = $txt_codigo; 
    $Clcodigo->cantidad_dias_disponibles = $txt_cantidad; 
    $Clcodigo->estado = $cmbEstado;
    $Clcodigo->tipo = $cmbTipo;
    $Clcodigo->cod_cupon = $cod_cupon;
    $Clcodigo->descripcion = $txtDescripcion;
    $Clcodigo->imagen = 'cupon_'.datetime_format().'.jpg';
    
    if($cod_cupon == 0)
        return createCoupon($Clcodigo);
    
    $cupon = $Clcodigo->getCoupon();
    if($cupon)
        $Clcodigo->imagen = $cupon["imagen"];
    return editCoupon($Clcodigo);
}

function createCoupon($cl){
    global $session;

    if($cl->createCoupon()){
        /*SUBIR IMAGEN*/
        if(!uploadFile($_FILES["img_profile"], $cl->imagen)){
            $img1 = url_upload.'/assets/img/200x200.jpg';
            $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$cl->imagen;
            copy($img1, $img2);  
        }
        $return['success'] = 1;
        $return['mensaje'] = "Cupón creado correctamente";
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "Error al crear el cupón";
    return $return;
}

function editCoupon($cl){
   if($cl->editCoupon()){
        uploadFile($_FILES["img_profile"], $cl->imagen);
        $return['success'] = 1;
        $return['mensaje'] = "Cupón editado correctamente";
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "Error al editar el cupón";
    return $return;
}

function getCoupon(){
    global $Clcodigo;
    global $session;
    extract($_GET);

    $Clcodigo->cod_cupon = $cod_cupon;
    $cupon = $Clcodigo->getCoupon();
    if($cupon){
        $cupon["imagen"] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$cupon["imagen"];
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['data'] = $cupon;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "El cupón no existe";
    return $return;
}

function getCantUse(){
    global $Clcodigo;
	if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
	
    $cupon = $Clcodigo->get($id);
    if(!$cupon){
        $return['success'] = 0;
        $return['mensaje'] = "El cupón no existe";
        return $return;
    }
	
	require_once "../clases/cl_ordenes.php";
	$Clordenes = new cl_ordenes();
	$ordenes = $Clordenes->listaByCupon($cupon['codigo']);
	$return['success'] = 1;
	$return['mensaje'] = "Cupon existente";
	$return['ordenes'] = $ordenes;
	$return['num_ordenes'] = count($ordenes);
	
    return $return;
	
}

function datatable(){
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $where = "";
    $fecha = fecha();
    
    $cod_sucursal = $_GET['sucursal'] ?? '';
    $codigo = $_GET['codigo'] ?? '';
    
    if($session['cod_rol']==3){
        $cod_sucursal = $session['cod_sucursal'];
    }
    
    if($cod_sucursal !== ''){
        $where .= " AND ca.cod_sucursal =".$cod_sucursal;
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
            AND ca.cod_descuento = '$codigo'
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
                    <li><a target="_blank" href="orden_detalle.php?id='.$row['cod_orden'].'" title="Ver orden"><i data-feather="eye"></i></a></li>
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
?>