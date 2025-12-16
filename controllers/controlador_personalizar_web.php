<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_categorias.php";
require_once "../clases/cl_personalizar_web.php";

$Clcategorias = new cl_categorias();
$Clpersonalizarweb = new cl_personalizar_web();
$session = getSession();
error_reporting(0);

controller_create();

function get_adicionales(){
    global $Clcategorias;
    global $Clpersonalizarweb;
    
    $cod_categoria = $_GET['cod_categoria'];
    $html="";
    if($Clpersonalizarweb->listaByCategoria($cod_categoria, $adicionales)){
        foreach($adicionales as $adi){
            $html.='<p>&nbsp;&nbsp;&nbsp;'.$adi['categoria'].' "></p>';
            $html.='
                    <tr data-codigo="'.$adi['cod_categoria_items'].'">
                        <td>'.$adi['categoria'].'<input type="hidden" name="txt_cod_item[]" value="'.$adi['cod_categoria_items'].'"</td>
                        <td><input type="text" class="form-control" name="txt_titulo[]" value="'.$adi['titulo'].'"></td>
                        <td style="text-align: center;"><button class="btn btn-danger btn-sm btnEliminarItem">x</button></td>
                    </tr>
            ';
        }
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Hay datos";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    return $return;
}

function set_adicionales(){
    global $Clcategorias;
    global $Clpersonalizarweb;
    
    extract($_POST);
    
    $cod_categoria = $id;
    if($Clpersonalizarweb->eliminar_adicionales($cod_categoria)){    
        for($i=0; $i<count($txt_cod_item); $i++){
            $cod_item = $txt_cod_item[$i];
            $Clpersonalizarweb->posicion = $i+1;
            $Clpersonalizarweb->titulo = $txt_titulo[$i];
            if($Clpersonalizarweb->insert_adicionales($cod_categoria, $cod_item)){
                $return['success'] = 1;
                $return['mensaje'] = "Adicionales agregados";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al insertar adicionales ";
            }   
        }
    }
    return $return;
}

function ordenar(){
    global $Clpersonalizarweb;
    
    extract($_POST);
    
    $cod_categoria = $id;
    
    for($i=0; $i<count($txt_cod_item); $i++){
        $cod_item = $txt_cod_item[$i];
        
        $Clpersonalizarweb->posicion = $i+1;
        
        if($Clpersonalizarweb->update_position($cod_categoria, $cod_item)){
            $return['success'] = 1;
            $return['mensaje'] = "Posicion actualizada ".$cod_item;
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al actualizar posicion ";
        }  
    }
    $return['cant'] = count($txt_cod_item);
    return $return;
}

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
        $return['mensaje'] = "Informacion actualizada correctamente".$estado.$_GET['estado'];
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
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($Clfidelizacion->set_costo_envio($base_dinero, $base_km, $adicional_km,$codigo)){
        $return['success'] = 1;
        $return['mensaje'] = "Costo de envio actualizado correctamente";
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
    if($Clfidelizacion->set_fidelizacion_puntos($codigo, $divisor, $puntos)){
        $return['success'] = 1;
        $return['mensaje'] = "Parametros  actualizados correctamente";
        //$return['id'] = $id;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar los parametros, por favor vuelva a intentarlo";
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

function insert_paymentez(){
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
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al insertar los Tokens, por favor vuelva a intentarlo";
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

function update_descripcion()
{
    global $Clempresas;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $desc_larga = editor_encode($desc_larga);
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
?>