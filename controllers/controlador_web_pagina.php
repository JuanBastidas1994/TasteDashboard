<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_web_pagina.php";
require_once "../clases/cl_empresas.php";
$ClWebPagina = new cl_web_pagina();
$Clempresas = new cl_empresas();
$session = getSession();

controller_create();

function crearPage(){
    global $ClWebPagina;
    global $Clempresas;
    extract($_POST);

    $ClWebPagina->titulo = $txt_titulo;
    $aux = "";
        do{
            $alias = create_slug(sinTildes($txt_titulo.$aux));
            $aux = intval(rand(1,100)); 
        }while($ClWebPagina->getByAlias($alias, $cod_empresa));
        $ClWebPagina->alias = $alias;

    if($ClWebPagina->crearPage($cod_empresa)){
        $return['success'] = 1;
        $return['mensaje'] = "Guardado correctamente";
        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($cod_empresa, 'Esquema creado', 10);
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al guardar";
    }
    return $return;
}

function guardar(){
    global $ClWebPagina;
    extract($_POST);

    $tipo = $cmbTipo;
    $ClWebPagina->cod_front_pagina = $cod_pagina;
    $ClWebPagina->titulo = $txt_titulo;
    $ClWebPagina->tipo = $tipo;
    $ClWebPagina->classname = $txt_classname;
    $ClWebPagina->showTitle = 1;

    $columnas = 0;
    $md = 0;
    $sm = 0;
    $cod_detalle = 0;
    $forma = "slide_4";
    $html = "";
    if($tipo=="ordenar" || $tipo=="anuncios" || $tipo=="blog" || $tipo=="categorias"){
        $forma = $cmbForma;
        $columnas = $cmbNumColumnas;
        $md = $cmbMD;
        $sm = $cmbSM;
        switch($tipo){
            case "ordenar": $cod_detalle = $cmbModulos; break;
            case "anuncios": $cod_detalle = $cmbAnuncios; break;
            case "blog": $cod_detalle = $cmbBlog; break;
        }
    }
    if($tipo=="youtube"){
        $detalle = $txt_detalle;
    }
    if($tipo=="script"){
        $html = $txt_detalle;
    }
    if($tipo=="html"){
        $html = editor_encode($desc_larga);
    }

    $ClWebPagina->forma = $forma;
    $ClWebPagina->numColumnas = $columnas;
    $ClWebPagina->md = $md;
    $ClWebPagina->sm = $sm;
    $ClWebPagina->detalle = $detalle;
    $ClWebPagina->detalle2 = "";
    $ClWebPagina->cod_detalle = $cod_detalle;
    $ClWebPagina->html = $html;

    if($ClWebPagina->crear($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Guardado Correctamente";
        $return['id'] = $id;
    }
    else{
        $return['success'] = 1;
        $return['mensaje'] = "Error al guardar";
    }
    return $return;
}

function editar(){
    global $ClWebPagina;
    extract($_POST);

    $tipo = $cmbTipo;
    $ClWebPagina->cod_front_pagina = $cod_pagina;
    $ClWebPagina->titulo = $txt_titulo;
    $ClWebPagina->tipo = $tipo;
    $ClWebPagina->classname = $txt_classname;
    $ClWebPagina->showTitle = 1;

    $columnas = 0;
    $md = 0;
    $sm = 0;
    $cod_detalle = 0;
    $forma = "slide_4";
    $html = "";
    if($tipo=="ordenar" || $tipo=="anuncios" || $tipo=="blog" || $tipo=="categorias"){
        $forma = $cmbForma;
        $columnas = $cmbNumColumnas;
        $md = $cmbMD;
        $sm = $cmbSM;
        switch($tipo){
            case "ordenar": $cod_detalle = $cmbModulos; break;
            case "anuncios": $cod_detalle = $cmbAnuncios; break;
            case "blog": $cod_detalle = $cmbBlog; break;
        }
    }
    if($tipo=="youtube"){
        $detalle = $txt_detalle;
    }
    if($tipo=="script"){
        $html = $txt_detalle;
    }
    if($tipo=="html"){
        $html = editor_encode($desc_larga);
    }

    $ClWebPagina->forma = $forma;
    $ClWebPagina->numColumnas = $columnas;
    $ClWebPagina->md = $md;
    $ClWebPagina->sm = $sm;
    $ClWebPagina->detalle = $detalle;
    $ClWebPagina->detalle2 = "";
    $ClWebPagina->cod_detalle = $cod_detalle;
    $ClWebPagina->html = $html;

    if($ClWebPagina->editar($idEsquema)){
        $return['success'] = 1;
        $return['mensaje'] = "Editado Correctamente";
        $return['id'] = $id;
    }
    else{
        $return['success'] = 1;
        $return['mensaje'] = "Error al editar";
    }
    return $return;
}

function eliminar(){
    global $ClWebPagina;

    $id = $_GET['id'];
    if($ClWebPagina->eliminarDetalle($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Bloque eliminado correctamente";
        $return['id'] = $id;
    }
    else{
        $return['success'] = 1;
        $return['mensaje'] = "Error al editar";
    }
    return $return;
}

function changeHome(){
    global $ClWebPagina;

    $id = $_GET['id'];
    if($ClWebPagina->setHome($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Página aplicada al inicio correctamente";
        $return['id'] = $id;
    }
    else{
        $return['success'] = 1;
        $return['mensaje'] = "Error, por favor vuelva a intentarlo";
    }
    return $return;
}

function cargarDatosSeccion(){
    global $ClWebPagina;
    extract($_GET);

    $resp = $ClWebPagina->cargarDatosSeccion($seccion);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Obtenido";
        $return['datos'] = $resp;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer";
    }
    return $return;
}

function get(){
    global $ClWebPagina;
    extract($_GET);

    $resp = $ClWebPagina->get($id);
    if($resp){

        if($resp['cod_tipo']=="html")
            $resp['html'] = editor_decode($resp['html']);
        $return['success'] = 1;
        $return['mensaje'] = "Obtenido";
        $return['datos'] = $resp;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer";
    }
    return $return;
}


function actualizarPosicion(){
    global $ClWebPagina;
 
     if(!isset($_POST['codigos'])){
         $return['success'] = 0;
         $return['mensaje'] = "Falta informacion";
         return $return;
     }
 
     extract($_POST);
 
    
     for ($i=0; $i < count($codigos); $i++) { 
         $ClWebPagina->setPosition($codigos[$i], $i+1);
     }
   
     $return['success'] = 1;
     $return['mensaje'] = "Actualizado correctamente";
     return $return;
 }
?>