<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_productos.php";
require_once "../clases/cl_empresas.php";

$Clsucursales = new cl_sucursales();
$Clproductos = new cl_productos();
$Clempresas = new cl_empresas();
$session = getSession();

controller_create();

function crear(){
    global $Clempresas;
    global $Clsucursales;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    if($txt_intervalo == 0){
        return [ 'success' => 0, 'mensaje' => 'El intervalo no puede ser 0' ];
    }
    else if ($txt_intervalo % 5 != 0) {
        return [ 'success' => 0, 'mensaje' => 'El intervalo debe ser múltiplo de 5' ];
    }
    
    $nameImg = 'sucursal_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $Clsucursales->nombre = $txt_nombre;
    $Clsucursales->direccion = $txt_direccion;
    $Clsucursales->hora_ini = $hora_ini;
    $Clsucursales->hora_fin = $hora_fin;
    $Clsucursales->emisor = $txt_emisor;
    $Clsucursales->intervalo = $txt_intervalo;
    $Clsucursales->latitud = $txt_latitud;
    $Clsucursales->longitud = $txt_longitud;
    $Clsucursales->distancia_km = $txt_cobertura;
    $Clsucursales->telefono = $txt_telefono;
    $Clsucursales->correo = $txt_correo;
    $Clsucursales->delivery = 0;
    $Clsucursales->pickup = 0;
    $Clsucursales->envio_grava_iva = 0;
    $Clsucursales->insite = 0;

    
    $Clsucursales->imagen= $nameImg;
    $Clsucursales->image_min= $nameImgMin;
    
    if(isset($_POST['chkEstadoSuc']))
        $Clsucursales->estado = 'A';
    else
        $Clsucursales->estado = 'I';
        
    if(isset($_POST['cmbCiudades']))
        $Clsucursales->cod_ciudad = $_POST['cmbCiudades'];
    else
        $Clsucursales->cod_ciudad = 59; // Guayaquil 
        
    if(isset($_POST['chk_delivery']))
        $Clsucursales->delivery = 1;

    if(isset($_POST['chk_pickup']))
        $Clsucursales->pickup = 1;

    if(isset($_POST['chk_envio_grava_iva']))
        $Clsucursales->envio_grava_iva = 1;

    if(isset($_POST['chk_insite']))
        $Clsucursales->insite = 1;

    $pasar = false;
    if(!isset($_POST['cod_sucursal'])){
        $id=0;
        if($Clsucursales->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Sucursal guardada correctamente";
            $return['id'] = $id;
            $return['data'] = "";
            $cod_sucursal = $id;
            $Clsucursales->getArray($id, $return['data']);
            $return['imagen'] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$nameImg;
            
             /*SUBIR IMAGEN*/
            if($txt_crop != "" && $txt_crop_min != ""){
                base64ToImage($txt_crop, $nameImg);
                base64ToImage($txt_crop_min, $nameImgMin);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                $img3 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImgMin;
                copy($img1, $img2);
                copy($img1, $img3);
            }
 
            // ASIGNAR MIS MOTORIZADOS se apago por solicitud de Sebas
            // $empresa = $Clempresas->get($session["cod_empresa"]);
            // if($empresa){
            //     if($empresa["cod_tipo_empresa"] == 1){
            //         $Clsucursales->setCourierOffice($id, 99, 'A'); 
            //     }
            // }

            /*--NUEVO--*/
            /*ASIGNAR PRODUCTOS*/
            global $Clproductos;
            $lista = $Clproductos->GetProductosbyEmpresa();
            foreach ($lista as $l) {
                $return['match']= $Clproductos->setProductSucursal($l['cod_producto'],$id);
			}
            
            /* AUMENTAR PROGRESO DE LA EMPRESA*/
            $Clempresas->updateProgresoEmpresa($session['cod_empresa'], 'Sucursal creada', 10);

            $pasar = true;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }else{
        $Clsucursales->cod_sucursal = $cod_sucursal;
        if($Clsucursales->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Sucursal editada correctamente";
            $return['id'] = $Clsucursales->cod_sucursal;
            $idP = $Clsucursales->cod_sucursal;
            $return['data'] = "";
            if($Clsucursales->getArray($Clsucursales->cod_sucursal, $data))
            {
                //uploadFile($_FILES["img_product"], $data['image_min']);
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['image'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clsucursales->setImage($nameImgMax, 'max', $idP);
                        if($cambio){
                            deleteFile($data['image']);
                        }
                    }
                }
                if($txt_crop_min != ""){
                    $nameImgMin = ($data['image_min']!=$data['image']) ? $data['image_min'] : 'min_'.$data['image_min'];
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMin = getNameImagejpg($nameImgMin, $cambio);
                    if(base64ToImage($txt_crop_min, $nameImgMin)){
                        $Clsucursales->setImage($nameImgMin, 'min', $idP);
                        if($cambio){
                            deleteFile($data['image_min']);
                        }
                    }
                }
                $return['imagen'] = "editada";
                 $return['nom'] = $data['image'];
            }
            $pasar = true;
            /*--NUEVO--*/
            
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }
    
    /*--NUEVO--*/
    if($pasar && isset($txtdia))
    {
        if($Clsucursales->eliminar_disponibilidadSucursal($cod_sucursal))
        for ($i=0; $i < count($txtdia); $i++) {
            if ($selectDia[$i]== 1)
            {
                $Clsucursales->crear_disponibilidadSucursal($cod_sucursal, $txtdia[$i], $hora_iniD[$i], $hora_finD[$i]);
            }
        }
    }
    return $return;
}

/*--NUEVO--*/
function editProductoSucursal(){
    global $Clproductos;
    if(!isset($_POST['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion".$_POST['cod_sucursal'];
        return $return;
    }

    extract($_POST);

    /*EDITAR PRODUCTOS*/
    for($x=0; $x<count($txt_producto); $x++){
        $precioReplace=$precioR[$x]; 
        $cod_producto = $txt_producto[$x];
        $precio = $txt_precio_sucursal[$x];
        $precio_anterior = $txt_precio_anterior_sucursal[$x];
        if($select[$x]==1)
            $estado = 'A';
        else
            $estado = 'I';
            
       $Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado,$precioReplace);
     //   $resultado.=($cod_producto."---".$cod_sucursal."---". $precio."---". $precio_anterior."---". $estado."---".$precioReplace."--".count($txt_producto));
    }
    //$return['data']=$resultado;
    $return['success'] = 1;
    $return['mensaje'] = "Productos editados correctamente";
    return $return;
}
/*--NUEVO--*/

function get(){
    global $Clsucursales;
    if(!isset($_GET['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clsucursales->getArray($cod_sucursal, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal encontrada";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Sucursal no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clsucursales;
	if(!isset($_GET['cod_sucursal']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clsucursales->set_estado($cod_sucursal, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Sucursal editada correctamente";
        if($estado == "D")
            $return['mensaje'] = "Sucursal eliminada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la sucursal";
    }
    return $return;
}

/*CREAR DISPONIBILIDAD*/
function crear_disponibilidad(){
    global $Clsucursales;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    for ($j=0; $j < count($cmb_sucursales); $j++) { 
        $cod_sucursal = $cmb_sucursales[$j];
        if($Clsucursales->crear_disponibilidad($cod_sucursal, $fecha_inicio, $hora_ini, $hora_fin)){
            $return['success'] = 1;
            $return['mensaje'] = "Creado correctamente";
            //$return['id'] = $id;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear, por favor vuelva a intentarlo";
        }

    }
    $return['success'] = 1;
    $return['mensaje'] = "Creados correctamente";
    return $return;
}

function eliminar_disponibilidad(){
    global $Clsucursales;
    if(!isset($_GET['cod_sucursal_disponibilidad'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clsucursales->eliminar_disponibilidad($cod_sucursal_disponibilidad);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Disponibilidad eliminada correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar la disponibilidad";
    }
    return $return;
}

function ciudades(){

global $Clsucursales;   
    if(count($_POST)==0){
		$return['success'] = 0;
    	$return['mensaje'] = "Falta informacion";
		return $return;
	}
	extract($_POST);
    $resp=$Clsucursales->getCiudades($provincia);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Correcto";
    	$ciudades ="";
    	foreach($resp as $ciud)
        {
            $ciudades .= '<option value='.$ciud['cod_ciudad'].'>'.$ciud['nombre'].'</option>';
        }
    	$return['ciudadesHtml'] = $ciudades;
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "No hay ciudades en esta provincia";
    }
    return $return;
    
}

function setProgramarPedido(){
    global $Clsucursales;
    global $session;
    $cod_empresa = $session['cod_empresa'];

    extract($_GET);
    // $return['success'] = 0;
    // $return['mensaje'] = $cod_sucursal." ".$programa." ".$cod_empresa." ".$diasProgramar;
    // return $return;

    if($Clsucursales->setProgramarPedido($cod_sucursal, $programa, $cod_empresa, $diasProgramar)){
        $return['success'] = 1;
        $return['mensaje'] = "Programar pedido editado";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar Programar pedido";
    }    
    return $return;
}

function getFestivos(){
    global $Clsucursales;
    extract($_GET);
    $html = "";
    $festivo = $Clsucursales->getFestivosHoy($cod_sucursal);
    if($festivo){
        $html.='<label class="text-warning">Sucursal cerrada hasta '.$festivo['hora_fin'].' ¿Desea abrirla?</label>
                <div class="row" style="margin-top: 20px; text-align: right;">
                    <div class="col-md-12">
                        <button class="btn btn-primary btnQuitarFestivo" data-value="'.$festivo['cod_sucursal_festivos'].'">Abrir Sucursal</button>
                    </div>
                </div>';
                
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Local abierto";
    }
    else{
        $html.='<label for="">Tiempo de cierre de la sucursal</label>
                <select class="form-control" name="cmbHoras" id="cmbHoras">
                    <option value="0.25">15 minutos</option>
                    <option value="0.5">30 minutos</option>
                    <option value="0.75">45 minutos</option>
                    <option value="1">1 Hora</option>
                    <option value="2">2 Horas</option>
                    <option value="3">3 Horas</option>
                </select>
                <div class="row" style="margin-top: 20px; text-align: right;">
                    <div class="col-md-12">
                        <button class="btn btn-primary btnGuardarFestivo">Cerrar sucursal</button>
                    </div>
                </div>';
        $return['html'] = $html;
        $return['success'] = 0;
        $return['mensaje'] = "No hay cierre en la sucursal";
    }
    
    return $return;
}

function guardarCierreFestivo(){
    global  $Clsucursales;
    extract($_GET);
    $html = "";
    if($Clsucursales->guardarCierreFestivo($cod_sucursal, $tiempo, $id, $hora_fin)){
        $html= '<label class="text-warning">Sucursal cerrrada hasta '.$hora_fin.' ¿Desea abrirla?</label>
                <div class="row" style="margin-top: 20px; text-align: right;">
                    <div class="col-md-12">
                        <button class="btn btn-primary btnQuitarFestivo" data-value="'.$id.'">Abrir Sucursal</button>
                    </div>
                </div>';
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal cerrada";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al cerrar la sucursal";
    }
    
    return $return;
}

function quitarCierreFestivos(){
    global  $Clsucursales;
    extract($_GET);

    $html = "";
    if($Clsucursales->quitarCierreFestivos($cod_sucursal_festivos)){
        $html = '   <label for="">Tiempo de cierre de la sucursal</label>
                    <select class="form-control" name="cmbHoras" id="cmbHoras">
                        <option value="0.25">15 minutos</option>
                        <option value="0.5">30 minutos</option>
                        <option value="0.75">45 minutos</option>
                        <option value="1">1 Hora</option>
                        <option value="2">2 Horas</option>
                        <option value="3">3 Horas</option>
                    </select>
                    <div class="row" style="margin-top: 20px; text-align: right;">
                        <div class="col-md-12">
                            <button class="btn btn-primary btnGuardarFestivo">Cerrar sucursal</button>
                        </div>
                    </div>';
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal abierta";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "error al abrir la sucursal";
    }
    return $return;
}

function setCourierOffice(){
    global $Clsucursales;
    extract($_POST);

    $data = $Clsucursales->getCourierOffice($office, $courier);

    if($data){
        //UPDATE
        if($Clsucursales->setCourierOffice($office, $courier, $status, true)){
            $return['success'] = 1;
            $return['mensaje'] = "Actualizado correctamente";
            return $return;
        }
    }

    //INSERT
    if($Clsucursales->setCourierOffice($office, $courier, $status)){
        $return['success'] = 1;
        $return['mensaje'] = "Actualizado correctamente";
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "Error al actualizar";
    return $return;
}

function setFlotaOffice(){
    global $Clsucursales;
    extract($_POST);

    $isCorrect = false;
    $data = $Clsucursales->getFlotaOffice($office, $courier);
    if($data)
        $isCorrect = $Clsucursales->deleteFlotaOffice($office, $courier);
    else
        $isCorrect = $Clsucursales->setFlotaOffice($office, $courier);

    //INSERT
    if($isCorrect){
        $return['success'] = 1;
        $return['mensaje'] = "Actualizado correctamente";
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "Error al actualizar";
    return $return;
}

function lista() {
    global $Clsucursales;

    $sucursales = $Clsucursales->lista();
    if($sucursales) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de sucursales";
        $return['data'] = $sucursales;
        return $return;
    } 

    $return['success'] = 0;
    $return['mensaje'] = "No hay sucursales";
    return $return;
}

function getPolygons(){
    global $Clsucursales;
    $sucursales = $Clsucursales->lista();
    if($sucursales) {
        
        foreach($sucursales as $key => $office){
            $sucursales[$key]['color'] = colorPosition($key);
            $sucursales[$key]['vertices'] = $Clsucursales->getCoberturaByOffices($office['cod_sucursal']);
            $sucursales[$key]['vertices_count'] = count($sucursales[$key]['vertices']);
        }
        
        $return['success'] = 1;
        $return['mensaje'] = "Lista de sucursales";
        $return['data'] = $sucursales;
        return $return;
    } 

    $return['success'] = 0;
    $return['mensaje'] = "No hay sucursales";
    return $return;
}

function rand_color() {
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

function colorPosition($position){
    $colors = ["red", "green", "purple", "blue", "grey", "brown", "orange", "black"];
    return $colors[$position];
}

function savePolygons(){
    //Utilizar commit y rollback
    global $Clsucursales;
    
    if(!isset($_POST['poligonos'])){
        $return['success'] = 0;
        $return['mensaje'] = "No puedes guardar si no has creado un polígono";
        return $return;
    }

    extract($_POST);
    
    //Ellimninar poligonos anteriores
    $Clsucursales->deletePolygonsByBusiness();
    $querys = [];
    foreach($poligonos as $polygon){
        $querys[] = $Clsucursales->saveCoberturaInOffice($polygon['office_id'], $polygon['vertices']);
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Polígonos guardados correctamente";
    return $return;
}

/* SUCURSALES COSTO DE ENVÍO */
function getCostosEnvio() {
    global $Clsucursales;

    $sucursales = $Clsucursales->getCostosEnvio();
    if($sucursales) {
        
        $return["success"] = 1;
        $return["mensaje"] = "Costos de envío sucursales";
        $return["data"] = $sucursales;
    }
    else {
        $return["success"] = 0;
        $return["mensaje"] = "No hay costos de envío sucursales";
    }
    return $return;
}

function saveCostosEnvio() {
    global $Clsucursales;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    foreach ($costos as $key => $costo) {
        $Clsucursales->cod_sucursal = $costo["cod_sucursal"];
        $Clsucursales->cod_sucursal_costo_envio = $costo["cod_sucursal_costo_envio"];
        $Clsucursales->base_dinero = $costo["base_dinero"];
        $Clsucursales->base_km = $costo["base_km"];
        $Clsucursales->adicional_km = $costo["adicional_km"];

        if($Clsucursales->cod_sucursal_costo_envio == 0)
            $Clsucursales->saveCostosEnvio();
        else
            $Clsucursales->editCostosEnvio();

    }

    $return["success"] = 1;
    $return["mensaje"] = "Costos de envío sucursales actualizados";

    return $return;
}


// RANGOS

function getCostosEnvioRango() {
    global $Clsucursales;

    if(!isset($_GET["cod_sucursal"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Falta ID de la sucursal";
        return $return;
    }

    $Clsucursales->cod_sucursal = $_GET["cod_sucursal"];
    $rangos = $Clsucursales->getCostosEnvioRango();

    $return["success"] = 1;
    $return["mensaje"] = "Lista de rangos";
    $return["data"] = $rangos;
    return $return;
}

function saveCostosEnvioRango() {
    global $Clsucursales;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $Clsucursales->cod_sucursal = $cod_sucursal;
    foreach ($rangos as $key => $rango) {
        $Clsucursales->id = $rango["id"];
        $Clsucursales->distancia_ini = $rango["distancia_ini"];
        $Clsucursales->distancia_fin = $rango["distancia_fin"];
        $Clsucursales->precio = $rango["precio"];

        if($Clsucursales->id == 0)
            $Clsucursales->saveCostosEnvioRango();
        else
            $Clsucursales->editCostosEnvioRango();

    }

    $return["success"] = 1;
    $return["mensaje"] = "Costos de envío sucursales por rango actualizados";
    $return["data"] = $data;

    return $return;
}

function removeCostosEnvioRango() {
    global $Clsucursales;

    if(!isset($_GET["id"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Falta ID del rango a eliminar";
        return $return;
    }

    $resp = $Clsucursales->removeCostosEnvioRango($_GET["id"]);
    if(!$resp) {
        $return["success"] = 0;
        $return["success"] = "Error al eliminar el rango";
        return $return;
    }

    $return["success"] = 1;
    $return["mensaje"] = "Rango de costo de envío eliminado correctamente " . $_GET["id"];
    return $return;
}


function subirImagenesAdicionales(){
    global $Clsucursales;
    global $Clempresas;
    global $session;
    
    extract($_POST);
    
    $sucursal = $Clsucursales->getInfo($cod_sucursal);
    if(!$sucursal){
        return [ 'success'=> 0, 'mensaje' => 'Sucursal no encontrada '.$cod_sucursal ];
    }
    
    if($type == 'transferencia_img'){
        $prefix = "transfer_";
        $oldImgName = $sucursal['transferencia_img'];
    }else{
        $prefix = 'banner_';
        $oldImgName = $sucursal['banner_xl'];
    }
    $nameImg = $prefix.$sucursal['cod_sucursal'].'_'.datetime_format().'.jpg';
    
    $empresa = $Clempresas->get($session["cod_empresa"]);
    if($empresa){
        $rutaImg = 'assets/empresas/'.$empresa['alias'].'/'.$nameImg;
        if(move_uploaded_file($_FILES['inputFile']['tmp_name'], url_upload.$rutaImg)){
            
            if($type == 'transferencia_img')
                $save = $Clsucursales->setTransferImage($nameImg, $cod_sucursal);
            else
                $save = $Clsucursales->setBannerImage($nameImg, $cod_sucursal);
            
            if(!$save){
                return [ 'success'=> 0, 'mensaje' => 'No se pudo guardar la imagen' ];
            }
            
            $return['success'] = 1;
            $return['mensaje'] = "Guardado correctamente ".$rutaImg;
            $return['rutaImagen'] = url_sistema.$rutaImg;
            
            if($oldImgName !== ""){
                $oldfile = url_upload.'assets/empresas/'.$empresa['alias'].'/'.$oldImgName;
                if (file_exists($oldfile)) { // Verifica si el archivo existe
                    unlink($oldfile);
                }
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al subir la imagen";
            $return['upload'] = url_upload.$rutaImg;
            $return['img'] = url_sistema.$rutaImg;
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se encontro la empresa, recargue la pagina";
    }
    return $return;
}

?>