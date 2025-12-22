<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_fidelizacion.php";
require_once "../clases/cl_botonPagos.php";
require_once "../clases/cl_transporte.php";
require_once "../clases/cl_sucursales.php";
$Clempresas = new cl_empresas();
$Clusuarios = new cl_usuarios();
$Clfidelizacion = new cl_fidelizacion();
$ClPagos = new cl_botonpagos();
$Clsucursales = new cl_sucursales();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function update_Info(){
    global $session;
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($Clempresas->set_update_contact($direccion, $telefono, $correo,$codigo)){
    $return['success'] = 1;
        $return['mensaje'] = "Informacion actualizada correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar la informacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_Redes()
{
    global $session;
    global $Clempresas;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    if($Clempresas->set_update_redes($id,$text,$codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Informacion actualizada correctamente";
        $return['datos'] = "Informacion actualiza".$text;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar la informacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_formas_pago()
{
    global $session;
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($_GET['estado']=="true")
        $estado = 'A';
    else
        $estado = 'I';
   
    
  if($Clempresas->set_update_formas_pago($estado,$codigo)){
    $return['success'] = 1;
        $return['mensaje'] = "Informacion actualizada correctamente";
        $return['datos'] = "Informacion actualiza";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar la informacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_costo_envio(){
    global $session;
    global $Clfidelizacion;
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($Clfidelizacion->set_costo_envio($base_dinero, $base_km, $adicional_km,$codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Costo de envio actualizado correctamente";
        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($session['cod_empresa'], 'Costo envio agregado', 10);
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar el costo de envio, por favor vuelva a intentarlo";
    }
    return $return;
}

function insert_costo_envio(){
    global $session;
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($Clfidelizacion->insert_costo_envio($cod_empresa,$base_dinero, $base_km, $adicional_km,$id)){
        $return['success'] = 1;
        $return['id'] = $id;
        $return['mensaje'] = "Costo de envio insertado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar el costo de envio, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_fidelizacion(){
    global $session;
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $cod_empresa = $session['cod_empresa'];
    if($Clfidelizacion->set_fidelizacion_puntos($cod_empresa, $divisor, $puntos)){
        $return['success'] = 1;
        $return['mensaje'] = "Parametros  actualizados correctamente";
        //$return['id'] = $id;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar los parametros, por favor vuelva a intentarlo";
        $return['datas'] = $_GET;
    }
    return $return;
}

function updateFidelizacionCumple(){
    global $session;
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $cod_empresa = $session['cod_empresa'];
    if($Clfidelizacion->set_fidelizacion_cumple($monto, $dias, $restriccion, $cod_empresa)){
        $return['success'] = 1;
        $return['mensaje'] = "Parametros  actualizados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar los parametros, por favor vuelva a intentarlo";
    }
    return $return;
}

function UploadImageCumple(){
    global $session;
    global $Clempresas;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        $return['info'] = $_POST;
        return $return;
    }

    $nameImg = 'cumple.jpg';
    $base64 = $_POST['crop'];

    $cod_empresa = $session['cod_empresa'];
    if(!$Clempresas->getImgCumple($cod_empresa)){
        if($Clempresas->createImgCumple($nameImg, $cod_empresa)){
            base64ToImage($base64, $nameImg);
            $return['success'] = 1;
            $return['mensaje'] = "Imagen Subida Correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al subir la imagen, por favor intentalo nuevamente";
        }
    }else{
        if($Clempresas->updateImgCumple($nameImg, $cod_empresa)){
            base64ToImage($base64, $nameImg);
            $return['success'] = 1;
            $return['mensaje'] = "Imagen Actualizada Correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al subir la imagen, por favor intentalo nuevamente";
        }
    }
    return $return;
}

function setEstadoImgCumple(){
    global $session;
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $cod_empresa = $session['cod_empresa'];
    if($Clempresas->getImgCumple($cod_empresa)){
        if($Clempresas->setEstadoImgCumple($estado, $cod_empresa)){
            $return['success'] = 1;
            if($estado == "A")
                $return['mensaje'] = "Imagen activada correctamente";
            else    
                $return['mensaje'] = "Imagen inactivada correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al actualizar el estado de la imagen, por favor vuelva a intentarlo";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Debes primero subir una imagen para poder activarla"; 
    }

    
    return $return;
}

function insert_fidelizacion(){
    global $session;
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($Clfidelizacion->insert_fidelizacion($cod_empresa,$divisor, $puntos,$id)){
        $return['success'] = 1;
        $return['id'] = $id;
        $return['mensaje'] = "Parametros fidelizacion insertados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Eror al insertar los parametros de fidelizacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_niveles(){
    global $session;
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $cod_empresa = $session['cod_empresa'];
    if($Clfidelizacion->set_niveles($codigo, $nombre, $inicio, $fin, $monto)){
        $return['success'] = 1;
        $return['mensaje'] = "Nivel  actualizado correctamente";
        //$return['id'] = $id;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar el nivel, por favor vuelva a intentarlo";
    }
    return $return;
}

function insert_niveles(){
    global $Clfidelizacion;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    $cod_empresa=$_GET['cod_empresa'];
    $nombre=$_POST['nombre'];
    $inicio=$_POST['inicio'];
    $fin=$_POST['fin'];
    $monto=$_POST['monto'];
    $cantidad=count($inicio);
    
    for($x=0; $x<$cantidad; $x++){
        $nombre_final = $nombre[$x];
        $inicio_final =$inicio[$x];
        $fin_final =$fin[$x];
        $monto_final =$monto[$x];
      $Clfidelizacion->insert_niveles($cod_empresa, $nombre_final, $inicio_final,  $fin_final,$monto_final,$x);
    }
  
     $return['success'] = 1;
     $return['mensaje'] = "Niveles  insertados correctamente";
  
    return $return;
}

function edit_niveles(){
    global $Clfidelizacion;
   
    $cod_empresa=$_GET['cod_empresa'];
    $nombre=$_POST['nombre'];
    $inicio=$_POST['inicio'];
    $fin=$_POST['fin'];
    $monto=$_POST['monto'];
    $cantidad=count($inicio);
    
    $Clfidelizacion->delete_niveles($cod_empresa);
   
        
        for($x=0; $x<$cantidad; $x++){
            $nombre_final = $nombre[$x];
            $inicio_final =$inicio[$x];
            $fin_final =$fin[$x];
            $monto_final =$monto[$x];
          $Clfidelizacion->insert_niveles($cod_empresa, $nombre_final, $inicio_final,  $fin_final,$monto_final,$x);
        }
   
  
     $return['success'] = 1;
     $return['mensaje'] = "Niveles  editados correctamente";
   
    
    return $return;
}

function insert_notificacion(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
   
    if($Clfidelizacion->insert_notificacion($cod_empresa,$token, $topic,$tipo,$id)){
        $return['success'] = 1;
        $return['id'] = $id;
        $return['mensaje'] = "Parametros notificacion insertados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar los parametros de la notificacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function edit_notificacion(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->update_notificacion($token, $topic,$codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Parametros notificacion editados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar los parametros de la notificacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function crear_modulo(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->insert_modulo($nombre_modulo, $cod_empresa, $descripcion, $id)){
        $html.='<tr id="contMo'.$id.'">
                    <td><span>'.$id.'</span></td>
                    <td><input type="text" id="txt_modulo'.$id.'" class="form-control" value="'.$nombre_modulo.'" ></td>
                    <td><textarea id="txa_modulo'.$id.'" class="form-control" >'.$descripcion.'</textarea></td>
                    <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarModulo" data-codigo="'.$id.'">Editar</button></td>
                    <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEliminarModulo" data-codigo="'.$id.'">Eliminar</button></td>
                </tr>';
                
        $return['success'] = 1;
        $return['html'] = $html;
        $return['mensaje'] = "Modulo agregado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar el modulo, por favor vuelva a intentarlo";
    }
    return $return;
}

function editar_modulo(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->update_modulo($nombre_modulo, $descripcion, $codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Modulo editado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar el modulo, por favor vuelva a intentarlo";
    }
    return $return;
}

function eliminar_modulo(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->delete_modulo($codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Modulo eliminado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el modulo, por favor vuelva a intentarlo";
    }
    return $return;
}

function crear_anuncio(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->insert_anuncio($nombre_anuncio, $cod_empresa, $descripcion,$width,$height, $id)){
        $html.='<tr id="contAn'.$id.'">
                    <td><span>'.$id.'</span></td>
                    <td><input type="text" id="txt_anuncio'.$id.'" class="form-control" value="'.$nombre_anuncio.'" ></td>
                    <td><textarea id="txa_anuncio'.$id.'" class="form-control" >'.$descripcion.'</textarea></td>
                    <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarAnuncio" data-codigo="'.$id.'">Editar</button></td>
                    <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEliminarAnuncio" data-codigo="'.$id.'">Eliminar</button></td>
                </tr>';
                
        $return['success'] = 1;
        $return['html'] = $html;
        $return['mensaje'] = "Anuncio agregado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar el anuncio, por favor vuelva a intentarlo";
    }
    return $return;
}

function editar_anuncio(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->update_anuncio($nombre_anuncio, $descripcion,$width,$height, $codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Anuncio editado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar el anuncio, por favor vuelva a intentarlo";
    }
    return $return;
}

function eliminar_anuncio(){
    global $Clfidelizacion;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($Clfidelizacion->delete_anuncio($codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Anuncio eliminado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el anuncio, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_descripcion(){
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    // $desc_larga = editor_encode($desc_larga);
    if($Clempresas->updateDescripcionFormaPago($codigo,$desc_larga)){
    $return['success'] = 1;
        $return['mensaje'] = "Informacion actualizada correctamente..";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar la informacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function actualizarPosicion(){
    global $Clempresas;

    extract($_POST);
    if($tipo == "opciones")
    {
        for ($i=0; $i < count($datos); $i++) { 
        $Clempresas->actPosicionOpciones($datos[$i], $i+1);
        }
    }
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function establecerBoton(){
    global $ClPagos;
    global $session;

    $cod_empresa = $_GET['cod_empresa'];
    $cod_proveedor_botonpagos = $_GET['cod_proveedor_botonpagos'];

    if($ClPagos->establecerBoton($cod_empresa, $cod_proveedor_botonpagos)){
        $return['success'] = 1;
        $return['mensaje'] = "Botón configurado correctamente";
    }
    else{

        $return['success'] = 0;
        $return['mensaje'] = "Error al configurar botón";
    }
    return $return;
}

/* FUNCIONES PAYMENTEZ */
function insert_paymentez(){
    global $Clempresas;
    global $ClPagos;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
   
    if($ClPagos->insert_paymentez($cod_empresa,$servercode, $serverkey,$clientcode,$clientkey,$tipo,$id)){
        $return['success'] = 1;
        $return['id'] = $id;
        $return['mensaje'] = "Tokens insertados correctamente";

        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($cod_empresa, 'Boton de pagos agregado', 10);

        if(!$ClPagos->existeBotonConfigurado($cod_empresa, 2))
            $ClPagos->agregarComoConfigurado(2, $cod_empresa, 'I');
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar los Tokens, por favor vuelva a intentarlo";
    }
    return $return;
}

function edit_paymentez(){
    global $ClPagos;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if($ClPagos->update_paymentez($servercode, $serverkey,$clientcode,$clientkey,$tipo,$codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Tokens editados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar los Tokens , por favor vuelva a intentarlo";
    }
    return $return;
}

function verificar_paymentez(){
   
    extract($_GET);
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    require_once "../clases/cl_paymentez.php";
    $datos =verifyApi($cod_empresa);
    
    $return['success']=2;
    if($datos->error)
    {
        $return['success']=0;
    }
    else if($datos->banks)
    {
        $return['success']=1;
    }
    return $datos;
}

/* FUNCIONES DATAFAST */
function insert_datafast(){
    global $Clempresas;
    global $ClPagos;
    global $session;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
   
    if($ClPagos->insert_datafast($cod_empresa, $api, $entity, $mid, $tid, $ambiente, $fase, $id)){
        $return['success'] = 1;
        $return['id'] = $id;
        $return['mensaje'] = "Tokens insertados correctamente";
        
        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($cod_empresa, 'Boton de pagos agregado', 10);

        if(!$ClPagos->existeBotonConfigurado($cod_empresa, 1))
            $ClPagos->agregarComoConfigurado(1, $cod_empresa, 'I');
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar los Tokens, por favor vuelva a intentarlo";
    }
    return $return;
}

function permisoTienda(){
    global $Clempresas;
    global $session;

    $cod_empresa = $session['cod_empresa'];
    extract($_GET);

    if($Clempresas->actualizarPermisoTienda($cod_empresa, $encender)){
        $permiso = "encendido";
        if($encender == 0)
            $permiso = "apagado";

        $return['success'] = 1;
        $return['mensaje'] = "Permiso $permiso";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al cambiar el permiso";
    }
    return $return;
}

function configuracionTransporte(){
    $Cltransporte = new cl_transporte();
    extract($_POST);

    foreach($base_km as $clave => $valor){
        if($base_dinero[$clave] == null)
            $base_dinero[$clave] = 0;
        if($base_km[$clave] == null)
            $base_km[$clave] = 0;
        if($adicional_km[$clave] == null)    
            $adicional_km[$clave] = 0;
        if($peso_maximo[$clave] == null)
            $peso_maximo[$clave] = 0;
        
        $Cltransporte->cod_empresa_costo_envio = $cod_empresa_costo_envio[$clave];
        $Cltransporte->base_dinero = $base_dinero[$clave];
        $Cltransporte->base_km = $base_km[$clave];
        $Cltransporte->adicional_km = $adicional_km[$clave];
        $Cltransporte->peso_maximo = $peso_maximo[$clave];
        $Cltransporte->actualizar();
    }
    $return['success'] = 1;
    $return['mensaje'] = 'Actualizado con éxito';
    return $return;
}

function getSucursalesCourier(){
    global $Clsucursales;

    $sucursales = [];
    if(isset($_GET["cod_sucursal"])) {
       $sucursales[] = $Clsucursales->getInfo($_GET["cod_sucursal"]);
    }
    else {
        $sucursales = $Clsucursales->lista();
    }
    
    if($sucursales){
        foreach ($sucursales as &$sucursal) {
            $couriers = $Clsucursales->getCouriers($sucursal["cod_sucursal"]);
            if($couriers){
                $sucursal['couriers'] = $couriers;
            }
            else{
                $sucursal['couriers'] = [];
            }
        }

        $return['success'] = 1;
        $return['mensaje'] = "Lista de sucursales";
        $return['data'] = $sucursales;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay sucursales";
        
    }
    return $return;
}

function courierValidarCobertura(){
    global $Clsucursales;

    if(count($_GET) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Error, falta información";
        return $return;
    } 
    extract($_GET);

    if($Clsucursales->courierValidarCobertura($courier, $sucursal, $estado)){
        $return['success'] = 1;
        $return['mensaje'] = "Editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar";
    }
    return $return;
}

function gurdarPosicionesCouriers(){
    if(count($_POST) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Error, falta información";
        return $return;
    } 
    global $Clsucursales;
    extract($_POST);

    if(count($couriers) > 0){
        for ($i=0; $i<count($couriers); $i++) { 
            if($Clsucursales->gurdarPosicionesCouriers($couriers[$i], $cod_sucursal, $i)){
                $return['success'] = 1;
                $return['mensaje'] = "Editado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al editar";
            }
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al obtener el arreglo de couriers";
    }
    return $return;
}

function setMontoMaximo(){
    if(count($_POST) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Error, falta información";
        return $return;
    } 
    global $Clempresas;
    extract($_POST);

    if($Clempresas->setMontoMaximoFormaPago($formaPago, $monto)){
        $return['success'] = 1;
        $return['mensaje'] = "Monto máximo editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar el monto máximo";
    }

    return $return;
}

function setNombreFormaPago(){
    global $Clempresas;
    extract($_POST);

    if($Clempresas->setNombreFormaPago($nombre, $cod_empresa_forma_pago)){
        $return["success"] = 1;
        $return["mensaje"] = "Forma de pago editada correctamente";
    }
    else{
        $return["success"] = 0;
        $return["mensaje"] = "Error al cambiar el nombre de la forma de pago";
    }

    return $return;
}

function setPermisoTipoEnvio(){
    global $Clempresas;
    extract($_POST);
    
    if($Clempresas->setPermisoTipoEnvio($tipo_envio, $encendido, $cod_empresa_forma_pago)){
        $encendidoText = ($encendido == 1) ? "Activada" : "Desactivada"; 
        $formapagoText = ($tipo_envio == "D") ? "Delivery" : "Pickup";
        $return["success"] = 1;
        $return["mensaje"] = "$encendidoText esta forma de pago para pedidos $formapagoText";
    }
    else{
        $return["success"] = 0;
        $return["mensaje"] = "Error al cambiar el tipo envío de la forma de pago";
    }
    return $return;
}

function actualizarFechasCaducidad() {
    global $Clfidelizacion;

    if(!isset($_GET['fechaPuntos']) || !isset($_GET['fechaDinero']) || !isset($_GET['fechaSaldo']) || !isset($_GET['cod_empresa'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta información";
        return $return;
    }

    extract($_GET);

    $Clfidelizacion->cantDiasCaducidadPuntos = $fechaPuntos;
    $Clfidelizacion->cantDiasCaducidadDinero = $fechaDinero;
    $Clfidelizacion->cantDiasCaducidadSaldo = $fechaSaldo;
    if(!$Clfidelizacion->actualizarFechasCaducidad($cod_empresa)) {
        $return["success"] = 0;
        $return["mensaje"] = "Error al editar los tiempos de caducidad";
        return $return;
    }
    $return["success"] = 1;
    $return["mensaje"] = "Tiempos de caducidad editados con éxito";
    return $return;
}
?>