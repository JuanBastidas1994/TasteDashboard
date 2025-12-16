<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_demo.php";
$Cldemos = new cl_demo();
$Clusuarios = new cl_usuarios();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function crear(){
    global $Cldemos;
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
    }while(!$Cldemos->aliasDisponible($alias));

    $nameImg = 'logo-'.$alias.'.jpg';
    $Cldemos->nombre = $txt_nombre;
    $Cldemos->cod_empresa = $cmb_empresas;
    $Cldemos->alias = $alias;
    $Cldemos->correo = $txt_correo;
    $Cldemos->telefono = $txt_telefono;
    $Cldemos->direccion = $txt_direccion;
    $Cldemos->logo = $nameImg;
    $Cldemos->color=$txtcolor;

    if(isset($_POST['chk_estado']))
        $Cldemos->estado = 'A';
    else
        $Cldemos->estado = 'I';

    if(!isset($_POST['cod_empresa'])){
        $id=0;
        if($Cldemos->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Demo guardado correctamente";
            $return['id'] = $id;
            $return['alias'] = $alias;
            $return['api'] = $api;
            
            $html.='<div class="col-md-12 col-sm-12 col-xs-12" >
                      	<label>Alias</label>
                      	<p>'.$alias.'</p>
                      </div>
                      <div class="col-md-12 col-sm-12 col-xs-12">
                      	<label>Api Key</label>
                      	<p>'.$api.'</p>
                      </div>';
            $return['html'] = $html;

            $dir = url_upload.'/assets/demos/';
            /*if (!file_exists($dir)) {
                mkdir($dir, 0755);
            }*/

            if($txt_crop != ""){
                base64ToImageDir($txt_crop, $nameImg, $dir);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/demos/'.$nameImg;
                copy($img1, $img2);
            }

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }else{
        $cod_demo = $_POST['cod_empresa'];
        $resp=$Cldemos->editarDemo($cod_demo);
        if($resp){
            
            $return['success'] = 1;
            $return['mensaje'] = "Demo editado correctamente ";
            $return['id'] = $cod_demo;
            $return['alias'] = $alias;
            
           $data = $Cldemos->get($cod_demo);
            if($data){
                 $return['data'] = $data;

                $dir = url_upload.'/assets/demos/';
                /*if (!file_exists($dir)) {
                    mkdir($dir, 0755);
                }*/

                if($txt_crop != ""){
                    base64ToImageDir($txt_crop, $data['logo'], $dir);
                }
                $return['imagen'] = "editada ".$dir;
            }
         
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la empresa";
        }
    }
    return $return;
}

function delete_demo(){
    global $Cldemos;
    
    $cod_demo = $_GET['cod_demo'];
    $estado = $_GET['estado'];
    
    if($Cldemos->set_estado($cod_demo, $estado)){
        $return['success'] = 1;
        $return['mensaje'] = "Demo eliminado";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo eliminar";
    }
    return $return;
} 

function get(){
    global $Cldemos;
    if(!isset($_GET['cod_empresa'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Cldemos->getArray($cod_sucursal, $array)){
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
	global $Cldemos;
	if(!isset($_GET['cod_empresa']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Cldemos->set_estado($cod_sucursal, $estado);
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
    global $Cldemos;
    global $session;
    if(!isset($_GET['cod_rol']) || !isset($_GET['cod_empresa']) || !isset($_GET['cod_pagina']) || !isset($_GET['activo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    if($activo == "true"){
        $resp = $Cldemos->addPagina($cod_empresa, $cod_rol, $cod_pagina);
    }else{
        $resp = $Cldemos->deletePagina($cod_empresa, $cod_rol, $cod_pagina);
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
    global $Cldemos;
    global $session;
    if(!isset($_POST['cod_rol']) || !isset($_POST['cod_empresa']) || !isset($_POST['paginas'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    for ($i=0; $i < count($paginas); $i++) { 
        $Cldemos->updatePaginaPosicion($cod_empresa, $cod_rol, $paginas[$i], $i+1);
    }
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}



?>