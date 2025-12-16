<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_helpdesk.php";
$ClHelp = new cl_helpdesk();
$session = getSession();

controller_create();

function lista(){
    global $session;
    global $ClHelp;
    
    if(isset($_GET['filter'])){
        $resp = $ClHelp->filterByTags($_GET['filter']);
    }else{
        $resp = $ClHelp->lista();
    }
    
    $html = '';
    if($resp){
        foreach ($resp as $helpdesk) {
            $id = $helpdesk['cod_helpdesk'];
            $alias = $helpdesk['alias'];
            $titulo = $helpdesk['titulo'];
            $desc = $helpdesk['desc_corta'];
            
            $button='';
            $video = $helpdesk['video'];
            if($video != ""){
                $button = '<button class="btn btn-primary openVideo" data-src="'.$video.'">Ver Video</button>';
            }
            $html .= '
            <div class="card">
                <div class="card-header" id="hd-statistics-1">
                  <div class="mb-0">
                    <div class="" data-toggle="collapse" role="navigation" data-target="#collapse-helpdesk-'.$id.'" aria-expanded="false" aria-controls="collapse-hd-statistics-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg>
                      '.$titulo.'
                    </div>
                  </div>
                </div>
    
                <div id="collapse-helpdesk-'.$id.'" class="collapse" aria-labelledby="hd-statistics-1" data-parent="#hd-statistics">
                  <div class="card-body">
                    <p>'.$desc.'</p>
                    '.$button.'
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
    global $ClHelp;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $ClHelp->titulo = $txt_titulo;
    $ClHelp->url = embedYt($txt_url);
    $ClHelp->desc_corta = $txt_descripcion;
    $ClHelp->desc_larga = htmlspecialchars($desc_larga);
    $ClHelp->user_create = $session['cod_usuario'];
    $ClHelp->fecha_create = fecha();
    $ClHelp->tags = implode(",", $cmb_tag);
    
    
    if(isset($_POST['chk_estado']))
        $ClHelp->estado = 'A';
    else
        $ClHelp->estado = 'I';
    
    $aux = "";
        do{
            $alias = create_slug(sinTildes($txt_titulo.$aux));
            $aux = intval(rand(1,100)); 
        }while(!$ClHelp->aliasDisponible($alias));
        $ClHelp->alias = $alias;
    
    if(!isset($_POST['cod_helpdesk'])){
        $id=0;
        if($ClHelp->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Helpdesk creado correctamente";
            $return['id'] = $id;

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el Helpdesk, por favor vuelva a intentarlo";
        }
    }else{
        $ClHelp->cod_helpdesk = $cod_helpdesk;
        if($ClHelp->editar($ClHelp->cod_helpdesk)){
            $return['success'] = 1;
            $return['mensaje'] = "Helpdesk editado correctamente";
            $return['id'] = $ClHelp->cod_helpdesk;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el Helpdesk";
        }
    }
    return $return;
}

function get(){
    global $session;
    global $ClHelp;
    if(!isset($_GET['cod_helpdesk'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($ClHelp->getArray($cod_helpdesk, $array)){
        $array['desc_larga'] = editor_decode($array['desc_larga']);
        $html="";
        if($array['tags'] <> ""){
            $tags = explode(",", $array['tags']);
            for($i=0; $i<count($tags); $i++){
                $html.='<option selected value="'.$tags[$i].'">'.$tags[$i].'</option>';
            }
        }
        
        $return['html'] = $html;
        $return['success'] = 1;
        $return['mensaje'] = "Helpdesk encontrado";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Helpdesk no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $ClHelp;
	if(!isset($_GET['cod_helpdesk']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $ClHelp->set_estado($cod_helpdesk, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Helpdesk editado correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el Helpdesk";
    }
    return $return;
}


function actualizar(){
   global $ClHelp;

    if(!isset($_POST['helpdesk'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($helpdesk); $i++) { 
        $ClHelp->moverHelpdesk($helpdesk[$i], $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

?>