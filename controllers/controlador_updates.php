<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_updates.php";
$Clempresas = new cl_empresas();
$Clupdates = new cl_updates();

$session = getSession();
error_reporting(E_ALL);

controller_create();

function tipos(){
    global $Clempresas;
    $cod_tipo_empresa = $_GET['cod_tipo_empresa'];
    $empresas = $Clempresas->getEmpresasPorTipo($cod_tipo_empresa);
    
    /*$return['success'] = 0;
        $return['mensaje'] = "no hay datos ".$cod_tipo_empresa;
        return $return;*/
    
    if($empresas){
        $html="";
        foreach($empresas as $emp){
            $html.='<option value="'.$emp['cod_empresa'].'">'.$emp['nombre'].'</option>';
        }
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "no hay datos";
    } 
    
    return $return;
}

function crear(){
    global $Clupdates;

    extract($_POST);
    
        $Clupdates->titulo = $txt_nombre;
        $Clupdates->detalle = editor_encode($desc_larga);
        $Clupdates->desc_corta = $txt_descripcion_corta;
        $Clupdates->tipo = $cmb_multimedia;
        if(isset($chk_estado))
            $Clupdates->estado = "A";
        else
            $Clupdates->estado = "I";

        if($txt_url <> ""){
            if($cmb_multimedia == 2){
                $txt_url = embedYt($txt_url);
                $Clupdates->url = editor_encode($txt_url);
            }
            else
                $Clupdates->url = editor_encode($txt_url);
        }
        else
            $Clupdates->url = "";

    if($id==""){
        if($Clupdates->crear($cod_update)){
            for ($i=0; $i < count($cmb_empresas); $i++) { 
                $Clupdates->crearDetalle($cod_update, $cmb_empresas[$i]);
            }
            $return['success'] = 1;
            $return['mensaje'] = "Actualización creada correctamente";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la actualización";
        }
    }
    else{
        if($Clupdates->editar($id)){
            if($Clupdates->eliminarDetalle($id)){
                for ($i=0; $i < count($cmb_empresas); $i++) { 
                    $Clupdates->crearDetalle($id, $cmb_empresas[$i]);
                }
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al editar el detalle de la actualización";
            }
            $return['success'] = 1;
            $return['mensaje'] = "Actualización editada correctamente";
        }   
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la actualización";
        } 
    }
    return $return;
}

function marcarLeido(){
    global $Clupdates;
    $cod_usuario = $_GET['cod_usuario'];
    $cod_update = $_GET['cod_update'];

    if($Clupdates->marcarLeido($cod_update, $cod_usuario)){
        $return['success'] = 1;
        $return['mensaje'] = "No se volverá a mostrar el diálogo";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al marcar leído";
    }
    return $return;
}
?>