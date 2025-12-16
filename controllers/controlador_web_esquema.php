<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_web_esquema.php";
$ClWebEsquema = new cl_web_esquema();
$session = getSession();

controller_create();

function lista(){
    global $ClWebEsquema;
    global $session;

    extract($_GET);
   
    $html = "";
    $resp = $ClWebEsquema->lista($idEmpresa, $plataforma);
    if(!$resp)
        $html = '<tr><td colspan="4">No hay registros</td></tr>';
    foreach ($resp as $es) {
        $badge='primary';
        $forma="Slide 4";
        if($es['forma'] == "lista_4")
        $forma="Lista de 4 Columnas";
        $html .= '<tr data-id="'.$es['cod_web_esquema'].'" id="tr'.$es['cod_web_esquema'].'">
                                    <td>'.$es['titulo'].'</td>
                                    <td>'.$forma.'</td>
                                    <td>'.$es['detalle'].'</td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li><a href="javascript:void(0);" data-value="'.$es['cod_web_esquema'].'"  class="bs-tooltip btnEditarEsquema" title="Editar"><i data-feather="edit-2"></i></a></li>
                                            <li><a href="javascript:void(0);" data-value="'.$es['cod_web_esquema'].'"  class="bs-tooltip btnEliminarEsquema" title="Eliminar"><i data-feather="trash"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>';
    }

    $return['success'] = 1;
    $return['mensaje'] = "Info";
    $return['esquema'] = $html;
    return $return;
}

function get(){
    global $ClWebEsquema;
    global $session;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $esquema = $ClWebEsquema->get($cod_esquema);
    if($esquema){
        $return['success'] = 1;
        $return['mensaje'] = "Esquema";
        $return['data'] = $esquema;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No existe este esquema";
    }
    return $return;
}

function crear(){
    global $ClWebEsquema;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $ClWebEsquema->titulo = $txt_titulo;
    $ClWebEsquema->forma = $cmbForma;
    $ClWebEsquema->tipo = $cmbTipo;
    $ClWebEsquema->plataforma = $plataforma;
    $ClWebEsquema->numColumnas = $cmbNumColumnas;
    
    if($cmbTipo == "ordenar"){
        $iddetalle = $cmbModulos;
        $detalle = $textModulos;
    }
    else{
        $iddetalle = $cmbAnuncios;
        $detalle = $textAnuncios;
    }
    $ClWebEsquema->iddetalle = $iddetalle;
    $ClWebEsquema->detalle = $detalle;
    
    $htmlNewtr = "";
        if(true){
            $id=0;
            
            if($ClWebEsquema->crear($id,$idEmpresa)){
           //if(true){
               // $return['query']=$ClWebEsquema->crear($id);
                $return['id'] = $id;

                $htmlNewtr .= '<tr data-id="'.$id.'" id="tr'.$id.'">
                                    <td>'.$txt_titulo.'</td>
                                    <td>'.$textForma.'</td>
                                    <td>'.$detalle.'</td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li><a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEliminarEsquema" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>';
                
                $return['success'] = 1;
                $return['mensaje'] = "Esquema creado correctamente";
                $return['html'] = $htmlNewtr;
                
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al crear la promocion, por favor vuelva a intentarlo";
            }
        }
    
   
    return $return;
}

function eliminar(){
    global $ClWebEsquema;
    global $session;
    
    extract($_GET);
    
    if($ClWebEsquema->eliminar($cod_esquema)){
        $return['success'] = 1;
        $return['mensaje'] = "Esquema eliminado";
        $return['id'] = $cod_esquema;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar";
    }
    return $return;
}


function actualizar(){
    global $ClWebEsquema;
    global $session;

    extract($_POST);
    
    for ($i=0; $i < count($esquemas); $i++) { 
        $ClWebEsquema->actPosicion($esquemas[$i], $i+1);
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function editar(){
    global $ClWebEsquema;
    global $session;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $esquema = $ClWebEsquema->update($cod_esquema, $titulo, $forma, $columnas);
    if($esquema){
        $return['success'] = 1;
        $return['mensaje'] = "Esquema actualizado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo actualizar el esquema";
    }
    return $return;
}
?>