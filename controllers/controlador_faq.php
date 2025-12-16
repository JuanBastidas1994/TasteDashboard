<?php
require_once "../funciones.php";
require_once "../clases/cl_faq.php";
require_once "../clases/cl_empresas.php";

//Clases
$Clfaq = new cl_faq(NULL);
$Clempresas = new cl_empresas(NULL);
$session = getSession();

controller_create();

function lista(){
    global $Clfaq;
    global $Clempresas;
    global $session;

    $emp = $Clempresas->get($session['cod_empresa']);
    $tipo_emp = $emp['cod_tipo_empresa']; 
    //$tipo_emp = 2;
    //echo $tipo_emp;
    
    if(isset($_GET['filter']) && trim($_GET['filter']) <> "" ){
        $resp = $Clfaq->filterByTitulo($tipo_emp, $_GET['filter']);
    }else{
        $resp = $Clfaq->listaMostrar($tipo_emp);
    }
    
    $html = '';
    if($resp){
        foreach ($resp as $faq) {
            $id = $faq['cod_faq'];
            $titulo = $faq['titulo'];
            $desc = $faq['desc_corta'];
            
            $html .= '
            <div class="card">
                <div class="card-header" id="hd-statistics-1">
                  <div class="mb-0">
                    <div data-toggle="collapse" role="navigation" data-target="#collapse-faq-'.$id.'" aria-expanded="true" aria-controls="collapse-hd-statistics-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg>
                      '.$titulo.'
                    </div>
                  </div>
                </div>
    
                <div id="collapse-faq-'.$id.'" class="collapse show" aria-labelledby="hd-statistics-1" data-parent="#hd-statistics">
                  <div class="card-body">
                    <p>'.$desc.'</p>
                  </div>
                  <div class="col-12" style="text-align:right; margin-bottom: 20px;">
                    <a href="faq-detalle.php?id='.$id.'" class="btn btn-primary" target="_blank">Ver m&aacute;s</a>
                  </div>
                </div>
            </div>
            ';
        }
        $return['success'] = 1;
        $return['mensaje'] = "Items";
        $return['html'] = $html;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    
    return $return;
}

function crear(){
    global $Clfaq;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $Clfaq->titulo = $txt_titulo;
    $Clfaq->cod_tipo_empresa = $cmb_faq;
    $Clfaq->desc_corta = $txt_descripcion;
    $Clfaq->desc_larga = editor_encode($desc_larga);

    if(isset($_POST['chk_estado']))
        $Clfaq->estado = 'A';
    else
        $Clfaq->estado = 'I';
    
    if(!isset($_POST['cod_faq'])){
        $id=0;
        if($Clfaq->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "FAQ creada correctamente";
            $return['id'] = $id;

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la FAQ, por favor vuelva a intentarlo";
        }
    }else{
        $Clfaq->cod_faq = $cod_faq;
        if($Clfaq->editar($Clfaq->cod_faq)){
            $return['success'] = 1;
            $return['mensaje'] = "FAQ editada correctamente";
            $return['id'] = $Clfaq->cod_faq;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la FAQ";
        }
    }
    return $return;
}

function get(){
    global $Clfaq;

    if(!isset($_GET['cod_faq'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clfaq->getArray($cod_faq, $array)){
        $array['desc_larga'] = editor_decode($array['desc_larga']);
        $return['success'] = 1;
        $return['mensaje'] = "FAQ encontrada";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "FAQ no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function getDetalle(){
    global $Clempresas;
    global $Clfaq;

    $emp = $Clempresas->get(cod_empresa);
    $tipo_emp = $emp['cod_tipo_empresa'];   

    if(!isset($_GET['cod_faq'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clfaq->getArrayDetalle($cod_faq, $tipo_emp, $array)){
        $array['desc_larga'] = editor_decode($array['desc_larga']);
        $return['success'] = 1;
        $return['mensaje'] = "FAQ encontrada";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "FAQ no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clfaq;
	if(!isset($_GET['cod_faq']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clfaq->set_estado($cod_faq, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "FAQ editada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la FAQ";
    }
    return $return;
}


function actualizar(){
   global $Clfaq;

    if(!isset($_POST['helpdesk'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($helpdesk); $i++) { 
        $Clfaq->moverHelpdesk($helpdesk[$i], $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

?>