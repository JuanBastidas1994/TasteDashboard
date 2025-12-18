<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_sucursales.php";
require_once '../clases/cl_laar.php';
require_once '../clases/cl_botonPagos.php';
require_once '../clases/cl_couriers.php';
require_once '../clases/cl_pedidosya.php';

$Clempresas = new cl_empresas();
$Clusuarios = new cl_usuarios();
$ClSucursales = new cl_sucursales(NULL);
$ClLaar = new cl_laar();
$ClBotonPagos = new cl_botonpagos();
//$ClBotonPagos = new cl_botonpagos();
$ClCouriers = new cl_couriers();

$session = getSession();
error_reporting(E_ALL);

controller_create();

function crear(){
    global $Clempresas;
    global $Clusuarios;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    $html="";
    extract($_POST);

    $aux = "";
    do{
        $alias = create_slug(sinTildes($txt_nombre.$aux));
        $aux = intval(rand(1,100)); 
    }while(!$Clempresas->aliasDisponible($alias));
    
    $api=getApikey($alias);

    $nameImg = 'logo.jpg';
    $Clempresas->nombre = $txt_nombre;
    $Clempresas->alias = $alias;
    $Clempresas->contacto = $txt_contacto;
    $Clempresas->correo = $txt_correo;
    $Clempresas->telefono = $txt_telefono;
    $Clempresas->tipoem = $cmbTipoE;
    $Clempresas->api = $api;
    $Clempresas->logo = $nameImg;
    $Clempresas->urlWeb = $txt_urlWeb;
    $Clempresas->color=$txtcolor;
    $Clempresas->description=$txt_description;
    $Clempresas->keywords=$txt_keywords;
    $Clempresas->pixel = $pixel;
    $Clempresas->pixel_verify = $pixel_verify;
    $Clempresas->tipoRecorte = $cmbTipoRecorte;
    

    if(isset($_POST['chk_estado']))
        $Clempresas->estado = 'A';
    else
        $Clempresas->estado = 'I';
    
    if(isset($_POST['ckStartOnMenu']))
        $Clempresas->ckStartOnMenu = 1;
    else
        $Clempresas->ckStartOnMenu = 0;

    if(!isset($_POST['cod_empresa'])){
        $id=0;
        if($Clempresas->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Empresa guardada correctamente";
            $return['id'] = $id;
            $return['alias'] = $alias;
            $return['api'] = $api;
            
            $Clempresas->set_costo_envio($id, 5, 2, 0.5);
            
            $html.='<div class="col-md-12 col-sm-12 col-xs-12" >
                      	<label>Alias</label>
                      	<p>'.$alias.'</p>
                      </div>
                      <div class="col-md-12 col-sm-12 col-xs-12">
                      	<label>Api Key</label>
                      	<p>'.$api.'</p>
                      </div>';
            $return['html'] = $html;

            $dir = url_upload.'/assets/empresas/'.$alias;
            if (!file_exists($dir)) {
                mkdir($dir, 0755);
            }

            if($txt_crop != ""){
                base64ToImageDir($txt_crop, $nameImg, $dir);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$alias.'/'.$nameImg;
                @copy($img1, $img2);
            }

            /*CREAR USUARIO*/
            $cod_usuario = 0;
            $nameImgUser = 'user_'.datetime_format().'.jpg';
            $Clusuarios->cod_empresa = $id;
            $Clusuarios->cod_rol = 2;
            $Clusuarios->nombre = $txt_nombre;
            $Clusuarios->telefono = $txt_telefono;
            $Clusuarios->imagen = $nameImgUser;
            $Clusuarios->correo = $txt_usuario;
            $Clusuarios->usuario = $txt_usuario;
            $Clusuarios->password = $txt_password;
            $Clusuarios->estado = 'A';
            $Clusuarios->crear($cod_usuario);
            $img1 = url_upload.'/assets/img/200x200.jpg';
            $img2 = url_upload.'/assets/empresas/'.$alias.'/'.$nameImgUser;
            copy($img1, $img2);
            if($Clusuarios->crearKeystore($cod_usuario, $txt_password, 1))
                $return['keystore'] = "Se creó";
            else
                $return['keystore'] = "No se creó";


            /*INSERT FORMAS DE PAGO*/
            $cmb_pago_permiso = $_POST['cmb_pago_permiso'];
            for($x=0; $x < count($cmb_pago_permiso); $x++){
                $combo = explode("-", $cmb_pago_permiso[$x]);
                $tipoPago = $combo[1];
                $tipoPagoEstado = $combo[0];
                
                if($tipoPagoEstado == "on")
                    $estado = "A";
                else if($tipoPagoEstado == "off")
                    $estado = "I";
                else if($tipoPagoEstado == "no")
                    $estado = "D";
                
                if($tipoPagoEstado <> "no")
                    $Clempresas->insertFormasPagoEmpresa($id, $tipoPago, $estado);
            }
            /*FIN INSERT FORMAS DE PAGO*/
            
            //Aumentar paginas
            $pages = $Clempresas->getPagesCopy();
            foreach($pages as $page){
                $Clempresas->addPagina($id, $page['cod_rol'], $page['cod_pagina'], $page['posicion']);
            }
            
            $Clempresas->updateFolder($id, $txt_folder);
            $Clempresas->updateHosting($id, $hosting);

            /* AUMENTAR PROGRESO DE LA EMPRESA*/
            $Clempresas->updateProgresoEmpresa($id, 'Empresa creada', 10);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }else{
        $Clempresas->cod_empresa = $_POST['cod_empresa'];
        $resp=$Clempresas->editarEmpresa($cod_empresa);
        if($resp){
            
            $u=$Clusuarios->set_userAdmin($txtiduser,$txt_usuario, $txt_password);
            
            $return['success'] = 1;
            $return['mensaje'] = "Empresa editada correctamente ";
            $return['id'] = $cod_empresa;
            $return['alias'] = $alias;
            
           $data = $Clempresas->get($cod_empresa);
            if($data){
                 $return['data'] = $data;

                $dir = url_upload.'/assets/empresas/'.$data['alias'];
                if (!file_exists($dir)) {
                    mkdir($dir, 0755);
                }

                if($txt_crop != ""){
                    base64ToImageDir($txt_crop, $data['logo'], $dir);
                }
                $return['imagen'] = "editada";
            }
            
            /*UPDATE FORMAS DE PAGO*/
            $combo = "";
            $cmb_pago_permiso = $_POST['cmb_pago_permiso'];
            for($x=0; $x < count($cmb_pago_permiso); $x++){
                
                $combo = explode("-", $cmb_pago_permiso[$x]);
                $tipoPago = $combo[1];
                $tipoPagoEstado = $combo[0];
                
                if($tipoPagoEstado == "on")
                    $estado = "A";
                else if($tipoPagoEstado == "off")
                    $estado = "I";
                else if($tipoPagoEstado == "no")
                    $estado = "D";
                    
                if($Clempresas->getUnaFormaPagoEmpresa($cod_empresa, $tipoPago))
                    $Clempresas->updateFormasPagoEmpresa($cod_empresa, $tipoPago, $estado);
                else
                    if($tipoPagoEstado <> "no")
                        $Clempresas->insertFormasPagoEmpresa($cod_empresa, $tipoPago, $estado);
            }
             /*FIN UPDATE FORMAS DE PAGO*/
             
             $Clempresas->updateFolder($cod_empresa, $txt_folder);
             $Clempresas->updateHosting($cod_empresa, $hosting);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la empresa";
        }
    }
    return $return;
}

function get(){
    global $Clempresas;
    if(!isset($_GET['cod_empresa'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clempresas->getArray($cod_sucursal, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal encontrada";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Sucursal no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function verificarTokens(){
    global $Clempresas;
    if(!isset($_GET['api'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

   require_once "../clases/cl_gacela.php";
    $ClGacela = new cl_gacela($api, $token, $ambiente);
    $UrlWebhooks = 'https://dashboard.mie-commerce.com/webhooks/order_status.php';
    $data = $ClGacela->order_status_update($UrlWebhooks);

    if($data->status=="Webhook agregado exitosamente.")
    {
        $return['success'] = 1;
        $return['mensaje'] = "Correcto";
        $mensaje = "";
        if($cod_gacela_sucursal != 0 )
        {
            $resp = $Clempresas->editarGacela($cod_gacela_sucursal, $token,$api,$ambiente);
            $mensaje = "editados";
        }
        else
        {
            $id = 0;
            $resp = $Clempresas->ingresarGacela($cod_empresa,$cod_sucursal,$token,$api,$ambiente,$id);
            $cod_gacela_sucursal = $id;
            $mensaje = "registrados";
        }
        if($resp){
        	$return['success'] = 1;
        	$return['mensaje'] = "Tokens Verificados y ".$mensaje." correctamente";
        	$return['idGacela'] = $cod_gacela_sucursal;
        	
        	$cod_courier = 1;//Gacela
        	$myCourier = $Clempresas->getEmpresaCourierId($cod_empresa,$cod_courier);
        	if(!$myCourier){
        	    $Clempresas->setEmpresaCourierId($cod_empresa,$cod_courier);
        	}

            $myCourier = $Clempresas->getSucursalCourierId($cod_sucursal, $cod_courier);
        	if(!$myCourier){
        	    $Clempresas->setSucursalCourierId($cod_sucursal, $cod_courier);
        	}
        	
        }else{
        	$return['success'] = 0;
        	$return['mensaje'] = "Error,intentelo mas tarde..";
        }
        
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error, Tokens invalidos";
        $return['gacelaResp'] = $data;
        $return['gacelaClass'] = $ClGacela;
        $return['urlWebhook'] = $UrlWebhooks;
    }
    return $return;
}

function verificarTokensPicker(){
    global $Clempresas;
    if(!isset($_GET['api'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

   require_once "../clases/cl_picker.php";
    $ClPicker = new cl_picker($cod_picker_sucursal);
    $UrlWebhooks = 'https://dashboard.mie-commerce.com/webhooks/order_status_picker.php';
    $data = $ClPicker->order_status_update_ambiente($api, $UrlWebhooks,"DRIVER_ASSIGNED",$ambiente);
    $return['DRIVER_ASSIGNED'] = $data;
    $data = $ClPicker->order_status_update_ambiente($api, $UrlWebhooks,"UPDATE_BOOKING_STATUS",$ambiente);
    $return['UPDATE_BOOKING_STATUS'] = $data;
    
    if($data->message=="Éxito")
    {
        $return['success'] = 1;
        $return['mensaje'] = "Correcto";
        $mensaje = "";
        if($cod_picker_sucursal != 0 ){
            $resp = $Clempresas->editarPicker($cod_picker_sucursal, $api, $ambiente);
            $mensaje = "editados";
        }
        else{
            $id = 0;
            $resp = $Clempresas->ingresarPicker($cod_empresa, $cod_sucursal, $api, $ambiente, $id);
            $cod_picker_sucursal = $id;
            $mensaje = "registrados";
        }
        if($resp){
        	$return['success'] = 1;
        	$return['mensaje'] = "Tokens Verificados y ".$mensaje." correctamente";
        	$return['idPicker'] = $cod_picker_sucursal;
        	
        	$cod_courier = 3;//PIcker
        	
            $myCourier = $Clempresas->getEmpresaCourierId($cod_empresa, $cod_courier);
        	if(!$myCourier){
        	    $Clempresas->setEmpresaCourierId($cod_empresa, $cod_courier);
        	}
            
            $myCourier = $Clempresas->getSucursalCourierId($cod_sucursal, $cod_courier);
        	if(!$myCourier){
        	    $Clempresas->setSucursalCourierId($cod_sucursal, $cod_courier);
        	}
        	
        }else{
        	$return['success'] = 0;
        	$return['mensaje'] = "Error,intentelo mas tarde..";
        }
        
    }else{
        $msg ="Tokens invalidos";
        if(isset($data->message))
            $msg = "Picker: ".$data->message;
        $return['success'] = 0;
        $return['mensaje'] = "Error, ".$msg;
        $return['respPicker'] = $data;
        $return['pickerObj'] = $ClPicker;
        $return['urlPicker'] = $ClPicker->URL;
        $return['getInfo'] = $_GET;
    }
    return $return;
}

function viewConfigPicker(){
    global $Clempresas;
    if(!isset($_GET['api'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

   require_once "../clases/cl_picker.php";
    $ClPicker = new cl_picker($token);
    $data = $ClPicker->webhooks_info();
    $return['success'] = 1;
    $return['mensaje'] = "Correcto";
    $return['info'] = $data;
    return $return;
}

function viewConfigGacela(){
    global $Clempresas;
    if(!isset($_GET['api'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

   require_once "../clases/cl_gacela.php";
    $ClGacela = new cl_gacela($api,$token,$ambiente);
    $data = $ClGacela->webhooks_info();
    $return['success'] = 1;
    $return['mensaje'] = "Correcto";
    $return['info'] = $data;
    return $return;
}

function AmbienteGacela() {
	global $Clempresas;
	if(!isset($_GET['cod_empresa']) || !isset($_GET['ambiente'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
	if($Clempresas->getgacelaEmpresa($cod_empresa,$ambiente))
	{
	    
	    if($Clempresas->AmbienteGacelaEmpresa($cod_empresa,$ambiente))
	    {
    	    $return['success'] = 1;
        	$return['mensaje'] = "Ambiente ".$ambiente." actualizado correctamente";
	    }
	}
	else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Primero debe registrar tokens en ambiente de ".$ambiente." para realizar el cambio";
    }
    return $return;
	
}

function SaveTokensLaar(){
	global $Clempresas;
	global $ClLaar;
	if(!isset($_GET['user']) || !isset($_GET['pass']) || !isset($_GET['cod_laar_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
	
	$ClLaar->username = $user;
    $ClLaar->password = $pass;
    
    if($ClLaar->getToken()){
        if($ClLaar->API <> ""){
            $mensaje="";
            if($cod_laar_sucursal!=0)
            {
                $resp = $Clempresas->editarLaar2($cod_laar_sucursal, $user, $pass);
                $mensaje = "editado";
            }
            else
            {
                $resp = $Clempresas->ingresarLaar2($cod_empresa, $cod_sucursal, $user, $pass, $id);
                $cod_laar_sucursal = $id;
                $mensaje = "registrado";
            }
            if($resp){
            	$return['success'] = 1;
            	$return['mensaje'] = "Token ".$mensaje." correctamente";
            	$return['idLaar'] = $cod_laar_sucursal;
            	
            	$cod_courier = 2;//Laar
                	$myCourier = $Clempresas->getEmpresaCourierId($cod_empresa,$cod_courier);
                	if(!$myCourier)
                	{
                	    $Clempresas->setEmpresaCourierId($cod_empresa,$cod_courier);
                	}

                    $myCourier = $Clempresas->getSucursalCourierId($cod_sucursal, $cod_courier);
                    if(!$myCourier){
                        $Clempresas->setSucursalCourierId($cod_sucursal, $cod_courier);
                    }

                    
            }else{
            	$return['success'] = 0;
            	$return['mensaje'] = "Error,intentelo mas tarde..";
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "API está vacía ".$ClLaar->msgError;
        }        
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = $ClLaar->msgError;
    }
    return $return;
}

function login_laar(){
    global $ClLaar;
    extract($_POST);
    
    $user = $txt_userL82;
    $pass = $_GET['pass'];
    
    $ClLaar->username = $user;
    $ClLaar->password = $pass;
    
    if($ClLaar->getToken()){
        if($ClLaar->API <> ""){
            $return['success'] = 1;
            $return['mensaje'] = "Sí hay token";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "API está vacía ".$ClLaar->msgError;
        }        
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = $ClLaar->msgError;
    }
    return $return;
}

function set_estado(){
	global $Clempresas;
	if(!isset($_GET['cod_empresa']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clempresas->set_estado($cod_sucursal, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Empresa editada correctamente";
        if($estado == "D")
            $return['mensaje'] = "Empresa eliminada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la empresa";
    }
    return $return;
}

function InfoDelivery(){
	global $Clempresas;
	global $ClSucursales;
	if(!isset($_GET['ambiente'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
    $respS = $ClSucursales->listaByEmpresa($id);
    $html ="";
    if($respS)    
    {
        foreach ($respS as $suc) {
        $nombre = $suc['nombre'];
        $cod_sucursal = $suc['cod_sucursal'];
        $infoG = $ClSucursales->getgacelaSucursalAmbiente($cod_sucursal,$ambiente);
        $api ="";
        $token ="";
        $cod_gacela_sucursal = 0;
        $ver = "";
        if($infoG)
        {
            $api =$infoG['api'];
            $token =$infoG['token'];
            $cod_gacela_sucursal=$infoG['cod_gacela_sucursal'];
            $ver = '<td  class="text-center"><button type="button" class="btn btn-outline-primary btnverconfig " data-api="'.$api.'" data-token="'.$token.'"> <i data-feather="eye"></i></button></td>';
        }
    $html .='
    <tr id="contAn'.$cod_sucursal.'" class="'.$ambiente.'" >
        <td><span>'.$cod_sucursal.'</span></td>
        <td><input type="text" id="txt_nombreG'.$cod_sucursal.'" class="form-control " value="'.$nombre.'" disabled></td>
        <td><input type="text" id="txt_empresaG'.$cod_sucursal.'" class="form-control " value="'.$api.'" disabled></td>
        <td><input type="text" id="txt_sucursalG'.$cod_sucursal.'" class="form-control " value="'.$token.'" disabled></td>
        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarGacela " id="editar_'.$cod_sucursal.'" data-codigo="'.$cod_sucursal.'" data-id="'.$cod_gacela_sucursal.'">Editar</button></td>
        '.$ver.'
    </tr>
    ';
    }
    }
    $return['info']=$html;
    return $return;
}

function InfoDeliveryPicker(){
	global $Clempresas;
	global $ClSucursales;
	if(!isset($_GET['ambiente'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
    $respS = $ClSucursales->listaByEmpresa($id);
    $html = "";
    if($respS){
        foreach ($respS as $suc) {
            $nombre = $suc['nombre'];
            $cod_sucursal = $suc['cod_sucursal'];
            $infoP = $ClSucursales->getpickerSucursalAmbiente($cod_sucursal, $ambiente);
            $api = "";
            $token = "";
            $cod_picker_sucursal = 0;
            $ver = "";
            if($infoP){
                $api = $infoP['api'];
                $token = $infoP['token'];
                $cod_picker_sucursal = $infoP['cod_picker_sucursal'];
                $ver = '<td  class="text-center"><button type="button" class="btn btn-outline-primary btnVerconfigPicker " data-api="'.$api.'" data-token="'.$cod_sucursal.'"> <i data-feather="eye"></i></button></td>';
            }
            $html .='
                    <tr id="contAn'.$cod_sucursal.'" class="'.$ambiente.'" >
                        <td><span>'.$cod_sucursal.'</span></td>
                        <td><input type="text" id="txt_nombreP'.$cod_sucursal.'" class="form-control " value="'.$nombre.'" disabled></td>
                        <td><input type="text" id="txt_empresaP'.$cod_sucursal.'" class="form-control " value="'.$api.'" disabled></td>
                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarPicker " id="editarP_'.$cod_sucursal.'" data-codigo="'.$cod_sucursal.'" data-id="'.$cod_picker_sucursal.'">Editar</button></td>
                        '.$ver.'
                    </tr>
                    ';
        }
    }
    $return['info']=$html;
    return $return;
}

/*MENU*/
function menuRol(){
    global $session;
    if(!isset($_GET['cod_rol']) || !isset($_GET['cod_empresa'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $htmlPaginas = listCheckMenuEmpresa(0, 1, $cod_empresa, $cod_rol);
    $htmlMenu = listDraggableMenuEmpresa(0, 1, $cod_empresa, $cod_rol);
    //echo $htmlAgotados;

    $return['success'] = 1;
    $return['mensaje'] = "Info";
    $return['paginas'] = $htmlPaginas;
    $return['menu'] = $htmlMenu;
    return $return;
}

function addPage(){
    global $Clempresas;
    global $session;
    if(!isset($_GET['cod_rol']) || !isset($_GET['cod_empresa']) || !isset($_GET['cod_pagina']) || !isset($_GET['activo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($activo == "true"){
        $resp = $Clempresas->addPagina($cod_empresa, $cod_rol, $cod_pagina);
        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($cod_empresa, 'Menu creado', 10);
    }else{
        $resp = $Clempresas->deletePagina($cod_empresa, $cod_rol, $cod_pagina);
    }

    
    if($resp){
       $return['success'] = 1;
        $return['mensaje'] = "Proceso realizado correctamente"; 
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo completar la transaccion";
    }
    return $return;
}

function actualizar(){
    global $Clempresas;
    global $session;
    if(!isset($_POST['cod_rol']) || !isset($_POST['cod_empresa']) || !isset($_POST['paginas'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    for ($i=0; $i < count($paginas); $i++) { 
        $Clempresas->updatePaginaPosicion($cod_empresa, $cod_rol, $paginas[$i], $i+1);
    }
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}


/*TARJETAS PARA PAGO A DIGITAL MIND*/
function addCard(){
    global $Clempresas;
    global $session;
    
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    //var_dump($_POST);
    $cod_empresa = $session['cod_empresa'];
    $resp = $Clempresas->setCard($cod_empresa,$token,$type,$status,$number,$transaction_reference,$expiry_month,$expiry_year);
    if($resp){
       $return['success'] = 1;
        $return['mensaje'] = "Tarjeta agregada correctamente"; 
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo agregar la tarjeta en nuestro sistema, por favor recarga la pagina.";
    }
    return $return;
}

function getAllCards(){
    global $Clempresas;
    global $session;
    require_once "../clases/cl_paymentez.php";
    
    $html = '';
    $tarjetas = listCardsById($session['cod_empresa']);
    $cod_tarjeta_activa = $Clempresas->getTarjetaActiva($session['cod_empresa']);
    if(isset($tarjetas['cards'])){
        $tarj = $tarjetas['cards'];
        foreach($tarj as $card){
            $checked = "";
            if($card['token'] == $cod_tarjeta_activa)
                $checked = "checked";
            $html .= '
            <div class="row">
                <div class="col-md-12 col-sm-12 col-12" style="padding: 15px 0px; border: 1px solid #dedede;  margin-bottom: 5px; margin-bottom: 5px;">
                    <div class="col-md-6 col-sm-6 col-9">
                        <img src="/assets/img/cards/'.$card['type'].'.svg" style="width: 35px;"/> •••• •••• •••• '.$card['number'].'
                    </div>
                    <div class="col-md-3 col-sm-3 col-3">
                       '.$card['expiry_month'].'/'.$card['expiry_year'].'
                    </div>
                    <div class="col-md-3 col-sm-3 col-12 btnActivarCard" style="text-align: right;">
                        <label class="new-control new-radio radio-primary label-radio">
                            <input type="radio" class="chkEstado new-control-input" name="chkEstado[]" id="chkEstado" value="'.$card['token'].'" '.$checked.'>
                            <span class="new-control-indicator"></span>&nbsp;
                        </label>
                        
                        <a href="javascript:void(0);" data-value="'.$card['token'].'" class="bs-tooltip btnEliminarCard" title="Eliminar"><i data-feather="trash"></i></a>
                    </div>
                </div>
            </div>';
        }
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de Tarjetas"; 
    $return['html'] = $html;
    return $return;
}

function getAllCardsByEmpresa(){
    global $session;
    require_once "../clases/cl_paymentez.php";
    
    $cod_empresa = $_POST['cod_empresa'];
    
    $html = '';
    $tarjetas = listCardsById($cod_empresa);
    if(isset($tarjetas['cards'])){
        $tarj = $tarjetas['cards'];
        foreach($tarj as $card){
            $html .= '
            <div class="row">
                <div class="col-md-12 col-sm-12 col-12" style="padding: 15px 0px; border: 1px solid #dedede;  margin-bottom: 5px; margin-bottom: 5px;">
                    <div class="col-md-6 col-sm-6 col-9">
                        <img src="/assets/img/cards/'.$card['type'].'.svg" style="width: 35px;"/> •••• •••• •••• '.$card['number'].'
                    </div>
                    <div class="col-md-3 col-sm-3 col-3">
                       '.$card['expiry_month'].'/'.$card['expiry_year'].'
                    </div>
                    <div class="col-md-3 col-sm-3 col-12" style="text-align: right;">
                        <a href="javascript:void(0);" data-value="'.$card['token'].'" class="bs-tooltip btnActivarCard"  title="Activar"> <i data-feather="check-square"></i></a>
                        <a href="javascript:void(0);" data-value="'.$card['token'].'" class="bs-tooltip btnEliminarCard" title="Eliminar"><i data-feather="trash"></i></a>
                    </div>
                </div>
            </div>';
        }
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de Tarjetas"; 
    $return['html'] = $html;
    return $return;
}

function deleteCard(){
    global $Clempresas;
    global $session;
    require_once "../clases/cl_paymentez.php";
    
    if(!isset($_GET['token'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_GET);
    $cod_empresa = $session['cod_empresa'];
    
    
    $cardActive = $Clempresas->getCardActive($cod_empresa);
    if($cardActive){
        if($cardActive['token'] == $token){
            $return['success'] = 0;
            $return['mensaje'] = "No se puede eliminar la tarjeta activa, por favor seleccione o ingrese otra tarjeta para poder borrarla";
            return $return;
        }
    }
    
    
    $resp = deleteCardUser($cod_empresa, $token);
    $return['success'] = 1;
    $return['mensaje'] = "Tarjeta eliminada"; 
    $return['respuesta'] = $resp;
    return $return;
}

function getMieLogs(){
    global $Clempresas;
    $cod_log = $_GET['cod_log'];
    if($_GET['tipo'] == "S"){
        if($Clempresas->getLogsSuccess($cod_log, $row)){
            $return['datos'] = $row;
            $return['success'] = 1;
            $return['mensaje'] = "Datos obtenidos"; 
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Datos no obtenidos"; 
        }
    }
    else{
        if($Clempresas->getLogsError($cod_log, $row)){
            $return['datos'] = $row;
            $return['success'] = 1;
            $return['mensaje'] = "Datos obtenidos"; 
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Datos no obtenidos"; 
        }
    }
    return $return;
}

function set_tarjeta_actual(){
    global $Clempresas;
    global $session;
    $token = $_GET['token'];
    if($Clempresas->actulizarTarjeta($token, $session['cod_empresa'])){
        $return['success'] = 1;
        $return['mensaje'] = "Tarjeta Actualizada";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo actualizar la tarjeta";
    }
    return $return;
}

function setProgramarPedido(){
    global $Clempresas;
    
    $cod_empresa = $_GET['cod_empresa'];
    $programa = $_GET['programa'];

    if($Clempresas->setProgramarPedido($cod_empresa, $programa)){
        $return['success'] = 1;
        $return['mensaje'] = "Permiso editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar permiso";
    }    
    return $return;
}

function setGravaIva(){
    global $Clempresas;
    
    $cod_empresa = $_GET['cod_empresa'];
    $grava = $_GET['grava'];

    if($Clempresas->setGravaIva($cod_empresa, $grava)){
        $return['success'] = 1;
        $return['mensaje'] = "Permiso editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar permiso";
    }    
    return $return;
}

function setPermisoFidelizacion(){
    global $Clempresas;
    
    $cod_empresa = $_GET['cod_empresa'];
    $permiso = $_GET['estado'];

    if($Clempresas->setPermisoFidelizacion($permiso, $cod_empresa)){
        $return['success'] = 1;
        $return['mensaje'] = "Permiso editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar permiso";
    }    
    return $return;
}

function setPermisos(){
    global $Clempresas;
    
    $cod_empresa = $_GET['cod_empresa'];
    $permiso = $_GET['permiso'];
    $status = $_GET['status'];

    if($Clempresas->setPermisionToBusiness($cod_empresa, $permiso, $status)){
        $return['success'] = 1;
        if($status == 0)
            $return['mensaje'] = "Permiso quitado correctamente";
        else    
            $return['mensaje'] = "Permiso agregado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar permiso";
    }    
    return $return;
}

function subirLogos(){
    global $Clempresas;
    
    extract($_POST);
    $cod_empresa = $hdIdLogo;

    if($cod_empresa > 0){
        $row = $Clempresas->get($cod_empresa);
        $rutafile = url_upload.'assets/empresas/'.$row['alias'].'/'.$nomImage;
        $rutaImagen = url_sistema.'assets/empresas/'.$row['alias'].'/'.$nomImage."?v=".date("s");
        if(move_uploaded_file($_FILES['inputFile']['tmp_name'], $rutafile)){
            $return['success'] = 1;
            $return['mensaje'] = "Gurdado correctamente ".$rutafile;
            $return['rutaImagen'] = $rutaImagen;
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al subir la imagen";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Primero guarde la empresa";
    }
    return $return;
}

function eliminarLogos(){
    global $Clempresas;
    
    extract($_GET);
    $cod_empresa = $business_id;

    if($cod_empresa > 0){
        $row = $Clempresas->get($cod_empresa);
        $rutafile = url_upload.'assets/empresas/'.$row['alias'].'/'.$image;
        
        if(!file_exists($rutafile)){
            return [ 'success' => 0, 'mensaje' => 'Archivo no existe' ];
        }
        
        if(!unlink($rutafile)){
            return [ 'success' => 0, 'mensaje' => 'No se pudo eliminar el archivo' ];
        }
        
        return [ 'success' => 1, 'mensaje' => 'Imagen eliminada correctamente' ];
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Primero guarde la empresa";
    }
    return $return;
}

function getFolder(){
    global $Clempresas;
    
    extract($_GET);

    $empresa = $Clempresas->get($cod_empresa);
    if($empresa){
        $return['folder'] = $empresa['folder'];
        $return['success'] = 1;
        $return['mensaje'] = "Carpeta obtenida";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al obtener la carpeta";
    }
    return $return;
}

function updateFolder(){
    global $Clempresas;
    
    extract($_GET);
    if(file_exists("/home1/digitalmind/".$folder)){
        if($Clempresas->updateFolder($cod_empresa, $folder)){
            $return['success'] = 1;
            $return['mensaje'] = "Carperta actualizada";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al actualizar la carpeta";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "El directorio no existe, carpeta no actualizada";
    }
    return $return;
}

function getConfigs(){
    global $Clempresas;
    global $ClSucursales;
    global $ClBotonPagos;
    global $ClCouriers;

    extract($_GET);

    $empresa = $Clempresas->getByAlias($alias);
    if($empresa){
        //BOTONES DE PAGO
        $htmlPaymentez = '<p class="pBot text-primary">Paymentez</p>';
        $htmlDatafast = '<p class="pBot text-primary">Datafast</p>';
        $cod_empresa = $empresa['cod_empresa'];
        $sucursales = $ClSucursales->listaByEmpresa($cod_empresa);

        // PAYMENTEZ
        $payEmpresa = $ClBotonPagos->datos_paymentez($cod_empresa);
        if($payEmpresa)
            $htmlEmpresaPay = '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Empresa ambiente: '.$payEmpresa['ambiente'].'</p>';
        else   
            $htmlEmpresaPay = '<p class="pEmp"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Empresa No configurado</p>';
        foreach ($sucursales as $sucursal) {
            $paySucursales = $ClBotonPagos->sucursalPaymentez($sucursal['cod_sucursal']);
            if($paySucursales)
                $htmlSucPay.= '<p class="pSuc"><i class="feather-16 text-success" data-feather="check-circle"></i> Sucursal: '.$sucursal['nombre'].' Configurado ambiente '.$paySucursales['ambiente'].'</p>';
            else
                $htmlSucPay.= '<p class="pSuc"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Sucursal: '.$sucursal['nombre'].' No configurado </p>';
        }
        $htmlPaymentez.= $htmlEmpresaPay.$htmlSucPay;
        
        //DATAFAST
        $dataEmpresa = $ClBotonPagos->datos_datafast($cod_empresa);
        if($dataEmpresa)
            $htmlEmpresaData = '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Empresa ambiente: '.$dataEmpresa['ambiente'].'</p>';
        else   
            $htmlEmpresaData = '<p class="pEmp"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Empresa No configurado</p>';
        foreach ($sucursales as $sucursal) {
            $dataSucursales = $ClBotonPagos->sucursalDatafast($sucursal['cod_sucursal']);
            if($dataSucursales)
                $htmlSucData.= '<p class="pSuc"><i class="feather-16 text-success" data-feather="check-circle"></i> Sucursal: '.$sucursal['nombre'].' Configurado ambiente '.$dataSucursales['ambiente'].'</p>';
            else
                $htmlSucData.= '<p class="pSuc"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Sucursal: '.$sucursal['nombre'].' No configurado </p>';
        }
        $htmlDatafast.= $htmlEmpresaData.$htmlSucData;

        //COURIERS
        $htmlGacela = '<p class="pBot text-primary">Gacela</p>';
        $htmlLaar = '<p class="pBot text-primary">Laar</p>';
        $htmlPicker = '<p class="pBot text-primary">Picker</p>';

        //GACELA
        foreach ($sucursales as $sucursal) {
            $gacSucursales = $ClCouriers->sucursalGacela($sucursal['cod_sucursal']);
            if($gacSucursales)
                $htmlSucGac.= '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Sucursal: '.$sucursal['nombre'].' Configurado ambiente '.$gacSucursales['ambiente'].'</p>';
            else
                $htmlSucGac.= '<p class="pEmp"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Sucursal: '.$sucursal['nombre'].' No configurado </p>';
        }
        $htmlGacela.= $htmlEmpresaGac.$htmlSucGac;

        //LAAR
        foreach ($sucursales as $sucursal) {
            $laarSucursales = $ClCouriers->sucursalLaar($sucursal['cod_sucursal']);
            if($laarSucursales)
                $htmlSucLaar.= '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Sucursal: '.$sucursal['nombre'].' Configurado ambiente '.$laarSucursales['ambiente'].'</p>';
            else
                $htmlSucLaar.= '<p class="pEmp"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Sucursal: '.$sucursal['nombre'].' No configurado </p>';
        }
        $htmlLaar.= $htmlSucLaar;

        //PICKER
        foreach ($sucursales as $sucursal) {
            $picSucursales = $ClCouriers->sucursalPicker($sucursal['cod_sucursal']);
            if($picSucursales){
                $picStatus = "(Activo)";
                if($picSucursales["estado"] == "I"){
                    $picStatus = "<span class='text-danger'>(Inactivo)</span>";
                }
                $htmlSucPic.= '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Sucursal: '.$sucursal['nombre'].' Configurado ambiente '.$picSucursales['ambiente'].' '.$picStatus.'</p>';
            }
            else
                $htmlSucPic.= '<p class="pEmp"><i class="feather-16 text-danger" data-feather="alert-triangle"></i> Sucursal: '.$sucursal['nombre'].' No configurado </p>';
        }
        $htmlPicker.= $htmlSucPic;

        //OTROS
        $cantProductos = $Clempresas->cantProductosByEmpresa($cod_empresa);
        $cantPermisosAdminEmpresa = $Clempresas->cantPermisosAdminEmpresa($cod_empresa);
        $cantPermisosAdminSucursal = $Clempresas->cantPermisosAdminSucursal($cod_empresa);

        $htmlOtros = '<p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Productos Subidos: '.$cantProductos.'</p>
                        <p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Permisos de Admin de empresas: '.$cantPermisosAdminEmpresa.'</p>
                        <p class="pEmp"><i class="feather-16 text-success" data-feather="check-circle"></i> Permisos de Admin de sucursales: '.$cantPermisosAdminSucursal.'</p>';

        $htmlBotonPago = $htmlPaymentez.$htmlDatafast;
        $htmlCouriers = $htmlGacela.$htmlLaar.$htmlPicker;
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['htmlBotonPago'] = $htmlBotonPago;
        $return['htmlCouriers'] = $htmlCouriers;
        $return['htmlOtros'] = $htmlOtros;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "La empresa no existe";
    }
    return $return;
}

function setAmbienteEmpresa(){
    global $Clempresas;
    extract($_GET);

    if($Clempresas->setAmbienteEmpresa($cod_empresa, $ambiente)){
        $return['success'] = 1;
        $return['mensaje'] = "Actualizado a ".$ambiente;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actulizar";
    }
    return $return;
}

function setPedidosYa() {
    global $Clempresas;
    
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);
    
    if(!isset($token)) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta información";
        return $return;
    }    

    $mensaje = "Error al guardar el token";
    $success = 0;

    $ClPedidosYa = new cl_pedidosya($token, $ambiente);
    $UrlWebhooks = 'https://dashboard.mie-commerce.com/webhooks/order_status_pedidosya.php';
    $data = $ClPedidosYa->shipping_status($UrlWebhooks);

    if(isset($data["status"])) {
        $return['success'] = 0;
        $return['mensaje'] = "Error al crear el webhook";
        $return['errorPedidosYa'] = $data;
        return $return;
    }

    $ClPedidosYa->cod_empresa = $cod_empresa;
    $ClPedidosYa->cod_sucursal = $cod_sucursal;
    
    $idPedidosYa = $cod_pedidosya_sucursal;
    $cod_courier = 5; // PedidosYa
    
    if($cod_pedidosya_sucursal == 0) {
        if($ClPedidosYa->setOfficeToken($idPedidosYa)) {
            $mensaje = "Token insertado y verificado correctamente";
            $success = 1;
        }
        else {
            $mensaje = "Token no insertado correctamente";
            $success = 0;
        }
    }
    else {
        $ClPedidosYa->estado = $estado;
        if($ClPedidosYa->updateOfficeToken($cod_pedidosya_sucursal)) {
            $mensaje = "Token editado y verificado correctamente";
            $success = 1;
        }
        else {
            $mensaje = "Token no editado correctamente";
            $success = 0;
        }
    }

    $respCourier = $Clempresas->getEmpresaCourierId($cod_empresa, $cod_courier);
    if(!$respCourier) {
        $Clempresas->setEmpresaCourierId($cod_empresa, $cod_courier);
    }

    $respCourier = $Clempresas->getSucursalCourierId($cod_sucursal, $cod_courier);
    if(!$respCourier){
        $Clempresas->setSucursalCourierId($cod_sucursal, $cod_courier);
    }

    $return['success'] = $success;
    $return['mensaje'] = $mensaje;
    $return['cod_pedidosya_sucursal'] = $idPedidosYa;
    return $return;
}

function verificarTokensPedidosYa() {

}

function InfoDeliveryPedidosYa() {
	global $ClSucursales;
	if(!isset($_GET['ambiente'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
    $sucursales = $ClSucursales->listaByEmpresa($id);
    if($sucursales) {
        foreach ($sucursales as &$suc) {
            $cod_sucursal = $suc['cod_sucursal'];
            $suc["token"] = "";
            $suc["tokenEstado"] = "Sin registro";
            $suc["cod_pedidosya_sucursal"] = 0;
            $infoP = $ClSucursales->getPedidosYaSucursalAmbiente($cod_sucursal, $ambiente);
            if($infoP){
                $suc["token"] = $infoP['token'];
                $suc["tokenEstado"] = $infoP['estado'];
                $suc["cod_pedidosya_sucursal"] = $infoP['cod_pedidosya_sucursal'];
            }
        }
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['data'] = $sucursales;
    }
    else {
        $return['success'] = 0;
        $return['mensaje'] = "No existen sucursales";
        return $return;
    }
    return $return;
}

function InfoDeliveryPedidosYaOLD() {
	global $Clempresas;
	global $ClSucursales;
	if(!isset($_GET['ambiente'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);
    $respS = $ClSucursales->listaByEmpresa($id);
    $html = "";
    if($respS){
        foreach ($respS as $suc) {
            $nombre = $suc['nombre'];
            $cod_sucursal = $suc['cod_sucursal'];
            $infoP = $ClSucursales->getPedidosYaSucursalAmbiente($cod_sucursal, $ambiente);
            $token = "";
            $cod_pedidosya_sucursal = 0;
            if($infoP){
                $token = $infoP['token'];
                $cod_pedidosya_sucursal = $infoP['cod_pedidosya_sucursal'];
            }
            $html .='
                    <tr id="contAn'.$cod_sucursal.'" class="'.$ambiente.'" >
                        <td><span>'.$cod_sucursal.'</span></td>
                        <td><input type="text" id="txt_nombrePYA'.$cod_sucursal.'" class="form-control " value="'.$nombre.'" disabled></td>
                        <td><input type="text" id="txt_empresaPYA'.$cod_sucursal.'" class="form-control " value="'.$token.'" disabled></td>
                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarPedidosYa " id="editar_ya'.$cod_sucursal.'" data-codigo="'.$cod_sucursal.'" data-id="'.$cod_pedidosya_sucursal.'">Editar</button></td>
                    </tr>
                    ';
        }
    }
    $return['info']=$html;
    return $return;
}

function setStatusEnvironmentPedidosYa() {
    
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);
    
    $ClPedidosYa = new cl_pedidosya();

}

function actualizarImpuesto(){
    global $Clempresas;
    
    $cod_empresa = $_GET['cod_empresa'];
    $impuesto = $_GET['impuesto'];
    $tipo = $_GET['tipo']; //mantener_pvp | mantener_precioNoTax

    if($Clempresas->updateImpuesto($cod_empresa, $impuesto, $tipo)){
        $return['success'] = 1;
        $return['mensaje'] = "Impuesto editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo editar los impuestos";
    }    
    return $return;
}

function getFaltaPagos() {
    global $Clempresas;

    extract($_GET);

    $data = $Clempresas->getMessagePayment($id);
    if($data) {
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['data'] = $data;

        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay datos";
    return $return;
}

function guardarFaltaPagos() {
    global $Clempresas;

    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    // var_dump($POST);
    // return;

    if($Clempresas->setMessagePayment($id, $title, $message)) {
        $return['success'] = 1;
        $return['mensaje'] = "Guardado correctamente";

        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al guardar";
    return $return;
}

function removeFaltaPagos() {
    global $Clempresas;

    extract($_GET);

    $data = $Clempresas->removeMessagePayment($id);
    if($data) {
        $return['success'] = 1;
        $return['mensaje'] = "Eliminado correctamente";

        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al eliminar";
    return $return;
}

function replicarWebHostingExterno(){
    global $Clempresas;

    global $session;
    $data = json_decode(file_get_contents('php://input'), true);
    if(!isset($data['zip']) || !isset($data['cod_empresa'])){
        return [ 'success' => 0, 'mensaje' => 'Falta informacion' ];
    }
    
     extract($data);
     
    $empresa = $Clempresas->get($cod_empresa);
    if(!$empresa){
        return [ 'success' => 0, 'mensaje' => 'Empresa no existe' ];
    }
    
    $url = $empresa['url_web'];
    if (!filter_var($url, FILTER_VALIDATE_URL))
        return [ 'success' => 0, 'mensaje' => 'Url web no válida o no configurada' ];
        
    
    $installerUrl = $url."/installer.php?token=clave-super-secreta&zip={$zip}";
    $ch = curl_init($installerUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200){
        return [ 'success' => 0, 'mensaje' => 'Archivo installer del hosting externo fallo', 'code' => $httpCode, 'installerUrl' => $installerUrl ];
    }
    
    
    return [ 
        'success' => 1, 
        'mensaje' => 'Replicado correctamente',
        'empresa' => $empresa,
        'installerUrl' => $installerUrl
    ];
    
}
?>