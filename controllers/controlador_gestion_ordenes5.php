<?php
require_once "../funciones.php";
//Claseso
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_ordenes5.php";
$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales();
$session = getSession();

controller_create();

function crear(){
    global $Clcategorias;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    //codigo momentaneo
    $alias = create_slug(sinTildes($txt_nombre));
    if(!$Clcategorias->aliasDisponible($alias)){
        $alias = "no-disponible";
    }

    $desc_larga = editor_encode($desc_larga);
    $nameImg = 'categoria_'.datetime_format().'.jpg';
    $Clcategorias->alias = $alias;
    $Clcategorias->nombre = $txt_nombre;
    $Clcategorias->desc_corta = $txt_descripcion_corta;
    $Clcategorias->desc_larga = $desc_larga;
    $Clcategorias->image_min = $nameImg;
    $Clcategorias->image_max = $nameImg;
    $Clcategorias->cod_categoria_padre = $cmb_categoria;

    if(isset($_POST['chk_estado']))
        $Clcategorias->estado = 'A';
    else
        $Clcategorias->estado = 'I';
      
    if(!isset($_POST['cod_producto'])){
        $id=0;
        if($Clcategorias->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Categoria creada correctamente";
            $return['id'] = $id;

            /*SUBIR IMAGEN*/
            if(!uploadFile($_FILES["img_product"], $nameImg)){
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                copy($img1, $img2);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la categoria, por favor vuelva a intentarlo";
        }
    }else{
        $Clcategorias->cod_categoria = $cod_producto;
        if($Clcategorias->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Categoria editada correctamente";
            $return['id'] = $Clcategorias->cod_categoria;

            $data = NULL;
            if($Clcategorias->getArray($cod_producto, $data)){
                uploadFile($_FILES["img_product"], $data['image_min']);
                $return['imagen'] = "editada";
            }

            /*SUBIR IMAGEN*/
            //uploadFile($_FILES["img_product"], $nameImg);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la categoria";
        }
    }
    return $return;
}

function asignar(){
    global $Clordenes;
    if(!isset($_POST['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    if($Clordenes->asignarMotorizado($id, $motorizado, $hora)){
        $return['success'] = 1;
        $return['mensaje'] = "Orden asignada correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al asignar la orden, por favor vuelva a intentarlo";
    }
    return $return;
}

function asignarGacela()
{
    global $Clordenes;
    global $Clsucursales;
    if(!isset($_POST['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $orden = $Clordenes->get_orden_array($id);
    $gacela = $Clsucursales->getgacelaSucursal($orden['cod_sucursal']);
    if($gacela)
    {
        require_once "../clases/cl_gacela.php";
        $ClGacela = new cl_gacela($gacela['api'],$gacela['token'],$gacela['ambiente']);
        $data = $ClGacela->crearOrder($orden);
        if(isset($data->results)){
            $datagacela=$data->results;
            if(isset($datagacela->order_token)){
                $return['datagacela']=$datagacela->order_token;
                $isGacela=$Clordenes->order_updgacela($id,$datagacela->order_token,1);
                if($isGacela)
                {
                     $return['success'] = 1;
                    $return['mensaje'] =$data->status;
                }
                else{
                    $return['success'] = 0;
                    $return['mensaje'] = "Error al definir la orden, por favor vuelva a intentarlo";
                }
            }else{
                $return['success'] = 0;
                $return['mensaje'] = "Error enviado por Gacela: Error - ".$data->status;
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al asignar la orden $id, por favor vuelva a intentarlo";
            if(isset($data->status)){
                $return['mensaje'] = "Error generado por Gacela: ".$data->status." - Orden: $id";
            } 
        }    
    }
    
    return $return;
}

//NUEVA VERSION: ya no es por campo "is_gacela" sera por "cod_courier"
function asignarGacelaV2()
{
    global $Clordenes;
    global $Clsucursales;
    if(!isset($_POST['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $orden = $Clordenes->get_orden_array($id);
    $gacela = $Clsucursales->getgacelaSucursal($orden['cod_sucursal']);
    if($gacela)
    {
        require_once "../clases/cl_gacela.php";
        $ClGacela = new cl_gacela($gacela['api'],$gacela['token'],$gacela['ambiente']);
        $data = $ClGacela->crearOrder($orden);
        if(isset($data->results)){
            $datagacela=$data->results;
            $return['datagacela']=$datagacela->order_token;
            $cod_courier = 1; // TIPO GACELA
            $isGacela=$Clordenes->order_updCourier($id,$datagacela->order_token,$cod_courier);
            if($isGacela)
            {
                 $return['success'] = 1;
                $return['mensaje'] =$data->status;
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al definir la orden, por favor vuelva a intentarlo";
            } 
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la orden, por favor vuelva a intentarlo";
        }    
    }
    
    return $return;
}

function asignarLaar()
{
    global $Clordenes;
    global $Clsucursales;
    if(!isset($_POST['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $orden = $Clordenes->get_orden_array($id);
    $sucursal = $Clsucursales->getInfo($orden['cod_sucursal']);
    $Laar = $Clsucursales->getLaarSucursal($orden['cod_sucursal']);
    if($Laar)
    {
        require_once "../clases/cl_laar.php";
        $Cl_laar = new cl_laar($Laar['cod_empresa'], $Laar['cod_sucursal']);  
        $data = $Cl_laar->crearGuia($orden,$sucursal);
        if(isset($data->guia)){
            $guia=$data->guia;
            $cod_courier = 2; // TIPO LAAR
            $isLaar=$Clordenes->order_updCourier($id,$guia,$cod_courier);
            if($isLaar)
            {
                 $return['success'] = 1;
                $return['mensaje'] ="Guia creada correctamente.";
                $return['guia'] =$guia;
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al definir la orden, por favor vuelva a intentarlo";
            } 
        }else{
            $adi = "por favor vuelva a intentarlo";
            if(isset($data->Message))
                $adi = $data->Message;
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la guia, ".$adi.$Cl_laar->msgError;
            $return['data'] =$data;
        }
    }
    
    return $return;
}

function trackingGuia()
{
    global $Clordenes;
    if(!isset($_GET['guia'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    require_once "../clases/cl_laar.php";
    $Cl_laar = new cl_laar();  
    $data = $Cl_laar->DataGuia($guia);
    $return['info']=($data);
    
    return $return;
    
}

function cancelarGacela()
{
    global $Clordenes;
    global $Clsucursales;
     if(!isset($_POST['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_POST);
    $orden = $Clordenes->get_orden_array($id);
    $gacela = $Clsucursales->getgacelaSucursal($orden['cod_sucursal']);
    if($gacela)
    {
        require_once "../clases/cl_gacela.php";
        $ClGacela = new cl_gacela($gacela['api'],$gacela['token']);
        $data = $ClGacela->cancelarOrder($orden['order_token']);
        if(isset($data->status)){
            $return['success'] = 1;
            $return['mensaje'] =$data->status;
            $Clordenes->order_updgacela($id,"",0);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al cancelar la orden en gacela, por favor vuelva a intentarlo";
        }    
    }
    
    return $return;
}

function lista(){
    global $Clordenes;
    if(!isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clordenes->lista_gestion($estado, $tipo, $cod_sucursal);
    if($resp){
        $html = "";
        foreach ($resp as $orden) {
            $html .= itemOrden($orden);
        } 
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['html'] = $html;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay items para esta categoria";
    }
    return $return;
}

function item(){
    global $Clordenes;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $orden = $Clordenes->get_orden_array($id);
    if($orden){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['html'] = itemOrden($orden, true);
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay items para esta categoria";
    }
    return $return;
}

function get_orden(){
    require_once "../clases/cl_usuarios.php";
    global $Clordenes;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $orden = $Clordenes->get_orden_array($id);
    if($orden){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['html'] = itemOrdenExpandido($orden);
        //$return['htmlV2'] = itemOrdenExpandidoV2($orden);
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay items para esta categoria";
    }
    return $return;
}

function get(){
    global $Clordenes;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $orden = $Clordenes->get_orden_array($id);
    if($orden){
        $return['success'] = 1;
        $return['mensaje'] = "Lista";
        $return['data'] = $orden;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Orden no existente";
    }
    return $return;
}


function set_estado(){
  global $Clordenes;
  if(!isset($_GET['cod_orden']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    if($estado == "CANCELADA" || $estado == "ANULADA"){
        $resp = $Clordenes->anularFactura($cod_orden, $comentario);
    }else{
        $resp = $Clordenes->set_estado($cod_orden, $estado);
    }

    $estado = strtolower($estado);


    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Orden $estado correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al $estado la orden";
    }
    return $return;
}

function revertir_pago(){
    global $Clordenes;
    
    
    
  if(!isset($_GET['cod_orden'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $query = "SELECT p.monto, p.observacion, p.cod_proveedor_botonpagos as proveedor
                    FROM tb_orden_pagos p
                    WHERE p.forma_pago = 'T'
                    AND p.cod_orden = ".$cod_orden;
    $pagoTarjeta = Conexion::buscarRegistro($query, NULL);
    if($pagoTarjeta){
        
        if($pagoTarjeta['proveedor'] == 1){ //DATAFAST
            $session = getSession();
            $cod_empresa = $session['cod_empresa'];
            $status = "failure";
            $id = $pagoTarjeta['observacion'];
            $total = number_format($pagoTarjeta['monto'],2);
            $data = [];
            require_once "../clases/cl_datafast.php";
            $Cldatafast = new cl_datafast($cod_empresa);
            $refund = $Cldatafast->refund($id, $total, $data);
            if(isset($refund['result']['code'])){
                if($refund['result']['code'] == $Cldatafast->getDebitCodeSuccess()){
                    $status = "success";
                    $return['success'] = 1;
                    $return['mensaje'] = 'Transaccion anulada correctamente';
                }else{
                    $error = "No se pudo anular la transacción";
                    if(isset($refund['resultDetails']['ExtendedDescription'])){
                        $error = $refund['resultDetails']['ExtendedDescription'];
                    }else if(isset($refund['result']['description'])){
                        $error = $refund['result']['description'];
                    }
                    $return['success'] = 0;
                    $return['mensaje'] = "Error al revertir. Detalles: ".$error;
                }
            }else{
                $return['success'] = 0;
                $return['mensaje'] = "Respuesta desconocida, Verificar en la plataforma de Datafast";
            }
            
            $json = json_encode($refund);
            $query = "INSERT INTO tb_orden_devolucion(id,fecha,estado,respuesta) VALUES('$id',NOW(),'$status','$json')";
    	    Conexion::ejecutar($query,NULL);
            
        }else{  //PAYMENTEZ
            require_once "../clases/cl_paymentez.php";
            $mensaje = "";
            $codigo = $pagoTarjeta['observacion'];
            if(refund($codigo, $cod_orden, $mensaje)){
                $return['success'] = 1;
                $return['mensaje'] = $mensaje;
            }else{
                if($mensaje == "Invalid Status"){
                    $mensaje = "No se pudo revertir el pago, puede ser porque ya se anulo anteriormente o porque se excedio del tiempo de anulacion. Revisar la plataforma de Paymentez";
                }    
                $return['success'] = 0;
                $return['mensaje'] = $mensaje;
            }
        }
    }else{
        $return['success'] = 0;
          $return['mensaje'] = "No hay pagos con tarjeta";
    }
   
    return $return;
}

function revertir_pago2(){
    global $Clordenes;
    
    require_once "../clases/cl_paymentez.php";
    
  if(!isset($_GET['cod_orden'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $query = "SELECT p.monto, p.observacion, p.cod_proveedor_botonpagos
                    FROM tb_orden_pagos p
                    WHERE p.forma_pago = 'T'
                    AND p.cod_orden = ".$cod_orden;
    $pagoTarjeta = Conexion::buscarRegistro($query, NULL);
    if($pagoTarjeta){
         $mensaje = "";
         $codigo = $pagoTarjeta['observacion'];
         if(refund($codigo, $cod_orden, $mensaje)){
          $return['success'] = 1;
          $return['mensaje'] = $mensaje;
        }else{
            
            if($mensaje == "Invalid Status"){
                $mensaje = "No se pudo revertir el pago, puede ser porque ya se anulo anteriormente o porque se excedio del tiempo de anulacion. Revisar la plataforma de Paymentez";
            }    
          $return['success'] = 0;
          $return['mensaje'] = $mensaje;
        }
    }else{
        $return['success'] = 0;
         $return['mensaje'] = "No hay pagos con tarjeta";
    }
    
   
    return $return;
}

function getBusquedaOrdenes(){
    global $Clordenes;
    global $session;
    $busqueda = $_GET['busqueda'];
    $tipo_busqueda = $_GET['tipo_busqueda'];
    $cod_sucursal = $_GET['cod_sucursal'];
    $cod_empresa = $session['cod_empresa'];

    $html = "";

    if($tipo_busqueda == 1){
        $datos = $Clordenes->getOrdenesByNumOrden($busqueda, $cod_sucursal, $cod_empresa);
    }
    else if($tipo_busqueda == 2){
        $datos = $Clordenes->getOrdenesByCedula($busqueda, $cod_sucursal, $cod_empresa);
    }
    else if($tipo_busqueda == 3){
        $busqueda = str_replace(' ', '%', $busqueda);
        $datos = $Clordenes->getOrdenesByNombre($busqueda, $cod_sucursal, $cod_empresa);
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "error al buscar";
    }

    if($datos){
        $html = "";
        foreach ($datos as $dat) {
            $html.= '    <tr>
                                <td>
                                    '.$dat['cod_orden'].'
                                </td>
                                <td>
                                    '.$dat['nom_cliente'].'
                                </td>
                                <td>
                                    '.$dat['fecha'].'
                                </td>
                                <td>
                                    '.$dat['estado'].'
                                </td>
                                <td>
                                    <ul class="table-controls">
                                        <li><a href="javascript:void(0);" data-value="'.$dat['cod_orden'].'" class="bs-tooltip mail-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                                    </ul>
                                </td>
                            </tr>';
        }
        
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
    }
    else{
        $html = '   <tr>
                        <td colspan="5">
                            Sin resultados
                        </td>
                    </tr>';
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }

    $return['html'] = $html;
    return $return;
}
?>