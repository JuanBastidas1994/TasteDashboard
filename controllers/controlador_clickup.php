<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_clickup.php";
require_once "../bot/cl_telegram.php";

$clScrum = new cl_clickup();
$Clempresas = new cl_empresas();
$session = getSession();

$token= "791140271:AAF62ruO7KGMEwrUUFq9q2PJAGcbJoYeLjQ";
$chat_id = -1001513069610;
$nombreUser = $session['nombre']." ".$session['apellido'];

controller_create();

function listaTareas(){
    global $session;
    global $clScrum;
    
    $info = $clScrum->getTaskByList();
    //var_dump($info);
    
    $html = '';
    if(isset($info['tasks'])){
        $tareas = $info['tasks'];
        
        $items = [];
        for($x=0;$x<=count($tareas)-1;$x++){
            $tarea = $tareas[$x];
            
            $id = $tarea['id'];
            $nombre = $tarea['name'];
            $descripcion = $tarea['description'];
            $status = $tarea['status'];
            $ColumnaIndex = $status['orderindex'];
            $fecha = TimeStampToDate($tarea['date_created']);
            
            $creador = $tarea['creator'];
            
            /*TAGS*/
            $tags = $tarea['tags'];
            $tagHtml = '';
            foreach($tags as $tg){
                $tagHtml .= '<span class="sc-tags" style="background-color: '.$tg['tag_bg'].'40;color: '.$tg['tag_bg'].';">'.$tg['name'].'</span>';
            }
            
            /*ASIGNACIONES*/
            $assignees = $tarea['assignees'];
            $asignadosHtml = '<div class="avatar--group">';
            foreach($assignees as $asi){
                $nameProfile = $asi['username'];
                $imgProfile = $asi['profilePicture'];
                if($imgProfile != null)
                    $asignadosHtml .= '<div class="avatar avatar-sm translateY-axis" title="'.$nameProfile.'">
                            <img alt="avatar" src="'.$imgProfile.'" class="rounded-circle" style="height: auto;"/>
                        </div>';
                else
                    $asignadosHtml .= '<div class="avatar avatar-sm translateY-axis" title="'.$nameProfile.'">
                            <span class="avatar-title rounded-circle">'.$asi['initials'].'</span>
                        </div>';
                
            }
            $asignadosHtml .= '</div>';
            
            /*PRIORITY*/
            $prioridadHtml = '';
            if(is_array($tarea['priority'])){
                $prioridad = $tarea['priority'];
                $prioridadHtml = '<span title="Prioridad: '.$prioridad['priority'].'"><i data-feather="flag" style="color:'.$prioridad['color'].';fill:'.$prioridad['color'].'40;"></i></span>';
            }
            
            $html = '
            <div data-draggable="true" class="card img-task taskDetail" style="" data-id="'.$id.'" data-title="'.$nombre.'">
                <div class="card-body">
                    <div class="task-content" style="display:none;">
                        <img src="assets/img/400x168.jpg" class="img-fluid" alt="scrumboard">
                    </div>

                    <div class="task-header" style="display:none;">
                        <div class="">
                            <h4 class="" data-taskTitle="'.$nombre.'">'.$nombre.'</h4>
                        </div>
                    </div>

                    <div class="task-body">
                        
                        <div class="task-content">
                            <p class="" data-taskText="'.$nombre.'">'.$nombre.'</p>
                            <p>'.$tagHtml.'</p>
                            <p>'.$asignadosHtml.'</p>
                        </div>

                        <div class="task-bottom">
                            <div class="tb-section-1">
                                <span data-taskDate="'.$fecha.'" style="font-size: 12px;"><i data-feather="calendar"></i> '.$fecha.' '.$prioridadHtml.'</span>
                            </div>
                            <div class="tb-section-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 s-task-edit"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 s-task-delete"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            ';
            
            $items[$x]['id'] = $id;
            $items[$x]['columna'] = $ColumnaIndex;
            $items[$x]['html'] = $html;
        }
        $return['success'] = 1;
        $return['mensaje'] = "Items";
        $return['items'] = $items;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    return $return;
}


function get(){
    global $session;
    global $clScrum;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $info = $clScrum->getTask($id);

    if(isset($info['id'])){
        $html = '';
        
        $tags = $info['tags'];
        $tagHtml = '';
        foreach($tags as $tg){
            $tagHtml .= '<span class="sc-tags" style="background-color: '.$tg['tag_bg'].'40;color: '.$tg['tag_bg'].';">'.$tg['name'].'</span>';
        }
        $html .= $tagHtml;
        
        $html .= '<p><input class="form-control" type="text" name="txt_titulo_tarea" id="txt_titulo_tarea" value="'.$info['name'].'" style="font-size: 22px; border: 0;padding-left: 0;"/></p>';
        $html .= '<p><textarea class="form-control" name="txt_desc_tarea" id="txt_desc_tarea" style="font-size: 14px; border: 0;padding-left: 0;">'.$info['description'].'</textarea></p>';
        
        
        
        /*SUBTAREAS*/
        if(isset($info['subtasks'])){
            $html .= '<p><b>SUBTAREAS</b></p>';
            $subtareas = $info['subtasks'];
            foreach($subtareas as $st){
                $stId = $st['id'];
                $stNombre = $st['name'];
                $stEstado = $st['status'];
                $stAsignados = $st['assignees'];
                
                $stHtmlAsignados='';
                if(is_array($stAsignados)){
                    foreach($stAsignados as $stA){
                        $stNameProfile = $stA['username'];
                        $stImgProfile = $stA['profilePicture'];
                        if($stImgProfile != null)
                            $stHtmlAsignados .= '<div class="avatar avatar-xs translateY-axis" title="'.$stNameProfile.'">
                                    <img alt="avatar" src="'.$stImgProfile.'" class="rounded-circle" />
                                </div>';
                        else
                            $stHtmlAsignados .= '<div class="avatar avatar-xs translateY-axis" title="'.$stNameProfile.'">
                                    <span class="avatar-title rounded-circle">'.$stA['initials'].'</span>
                                </div>';
                    }
                }
                
                
                $html .= '<div class="d-block d-xl-flex" style="background-color: #fff; border-radius: 6px; border: 1px solid #e0e6ed; padding: 14px 26px;">
                            <div class="avatar--group list-inline people-liked-img text-center text-sm-left">
                                '.$stHtmlAsignados.'
                            </div>
                            <div class="media-body">
                                <div class="d-sm-flex d-block justify-content-between text-sm-left text-center">
                                    <div class="">
                                        '.$stNombre.'
                                        <span style="color:'.$st['status']['color'].';background-color:'.$st['status']['color'].'40;padding: 3px 5px; text-transform: uppercase; font-size:10px;">'.$st['status']['status'].'</span>
                                        
                                    </div>
                                    <div>
                                        <button class="btn btn-dark btn-sm taskDetail" data-id="'.$stId.'" data-title="'.$stNombre.'">Ver</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
            }
        }    
        
        /*CHECKLIST*/
        $checklist = $info['checklists'];
        foreach($checklist as $ch){
            $html .= '<p><b>'.$ch['name'].'</b></p>';
            $items = $ch['items'];
            foreach($items as $it){
                $check = '';
                if($it['resolved'])
                    $check = 'checked="checked"';
                $html .= '<div class="n-chk">
                      <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                        <input type="checkbox" class="new-control-input chkAddPagina" value="" '.$check.' readonly="readonly" disabled="disabled">
                        <span class="new-control-indicator"></span>'.$it['name'].'
                      </label>
                  </div>';
            }
        }
        
        
        /*ADJUNTOS*/
        if(is_array($info['attachments'])){
            $html .= '<p><b>ADJUNTOS</b></p>
                    <div class="row">';
            $adjuntos = $info['attachments'];
            foreach($adjuntos as $adj){
                $thumbnail = getThumbnail($adj['extension'],$adj['thumbnail_large']);
                $html .= '<div class="col-xl-3 col-md-3 col-sm-4 col-xs-6 col-3">
                            <div class="card component-card_2" style="width: auto;">
                                <a class="fancybox" data-fancybox-group="gallery" href="'.$thumbnail.'" title="'.$adj['title'].'"><img src="'.$thumbnail.'" class="card-img-top" alt="widget-card-2"></a>
                                <div class="card-body">
                                    <p class="card-text" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">'.$adj['extension'].$adj['title'].'</p>
                                    <a href="'.$adj['url'].'" target="_blank" class=""><i data-feather="download"></i></a>
                                </div>
                            </div>
                        </div>';
            }
            $html .= '</div>';
        }
        
        /*INFORMACION EN EL HEADER*/
        $estadoHtml = '<span style="color:'.$info['status']['color'].';background-color:'.$info['status']['color'].'40;padding: 5px 10px; text-transform: uppercase; margin-right: 25px;">'.$info['status']['status'].'</span>'; 
        $header = $estadoHtml;
        $assignees = $info['assignees'];
        $header .= '<div class="avatar--group">';
        foreach($assignees as $asi){
            $nameProfile = $asi['username'];
            $imgProfile = $asi['profilePicture'];
            if($imgProfile != null)
                $header .= '<div class="avatar translateY-axis" title="'.$nameProfile.'">
                        <img alt="avatar" src="'.$imgProfile.'" class="rounded-circle" />
                    </div>';
            else
                $header .= '<div class="avatar translateY-axis" title="'.$nameProfile.'">
                        <span class="avatar-title rounded-circle">'.$asi['initials'].'</span>
                    </div>';
            
        }
        $header .= '</div>';
        
        
        
        
        $return['success'] = 1;
        $return['mensaje'] = "Informacion correcta";
        $return['html'] = $html;
        $return['header'] = $header;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Tarea no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function getCommentsByTask(){
    global $session;
    global $clScrum;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $html = '';
    $info = $clScrum->getCommentByTask($id);
    if(isset($info['comments'])){
        $comentarios = array_reverse($info['comments']);
        foreach($comentarios as $item){
            $permitOpcions = false;
            $id = $item['id'];
            $userCreate = $item['user']['username'];
            $userId = $item['user']['id'];
            if($userId == $clScrum->IdUser)
                $permitOpcions = true;
            
            $userImage = $item['user']['profilePicture'];
            if($userImage != null)
                $image = '<div class="avatar avatar-xs translateY-axis" title="'.$userCreate.'" style="margin-right: 15px;">
                        <img alt="avatar" src="'.$userImage.'" class="rounded-circle" />
                    </div>';
            else
                $image = '<div class="avatar avatar-xs translateY-axis" title="'.$userCreate.'" style="margin-right: 15px;">
                        <span class="avatar-title rounded-circle">'.$item['user']['initials'].'</span>
                    </div>';
            
            $adicional = '';
            foreach($item['comment'] as $element){
                if(isset($element['type'])){
                    if($element['type'] == 'attachment'){
                        $adjunto = $element['attachment'];
                        $thumbnail = getThumbnail($adjunto['extension'],$adjunto['thumbnail_large']);
                        $adicional .= '<div class="card component-card_2" style="width: auto;">
                                        <a class="fancybox" data-fancybox-group="comments" href="'.$thumbnail.'" title="'.$adjunto['title'].'"><img src="'.$thumbnail.'" class="card-img-top" alt="widget-card-2"></a>
                                        <div class="card-body">
                                            <p class="card-text" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">'.$adjunto['title'].'</p>
                                            <a href="'.$adjunto['url'].'" target="_blank" class=""><i data-feather="download"></i></a>
                                        </div>
                                    </div>';
                        $permitOpcions = false;
                    }
                    if($element['type'] == 'frame'){
                        $frame = $element['frame'];
                        $adicional .= '<video width="320" height="240" controls>
                                          <source src="'.$frame['url'].'">
                                            Your browser does not support the video tag.
                                        </video>';
                        $permitOpcions = false;                
                    }
                    if($element['type'] == 'doc_embed'){
                        $adicional .= '*Documento creado en clickup, no se puede abrir desde el dashboard*';
                        $permitOpcions = false;
                    }
                    if($element['type'] == 'emoticon'){
                        $adicional .= $element['text'];
                    } 
                    if($element['type'] == 'tag'){
                        $adicional .= '<b>'.$element['text'].'</b>';
                    }
                }else{
                    $adicional .= '<p>'.$element['text'].'</p>';
                }
            }
            
            $htmlOpciones = '';
            if($permitOpcions){
                $htmlOpciones = '
                    <a href="javascript:void(0);" class="btnEditComment" data-id="'.$id.'"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> Edit </a>
                    <a href="javascript:void(0);" class="btnDeleteComment" data-id="'.$id.'"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete </a>
                    <a href="javascript:void(0);" class="btnUpdateComment" data-id="'.$id.'"><i data-feather="save"></i> Update </a>
                ';
            }
                                
            $html .= '<div class="media" style="margin-bottom: 25px;">
                    '.$image.'
                    <div class="media-body">
                        <h4 class="media-heading" style="font-size:12px;"><b>'.$userCreate.'</b> coment&oacute;: </h4>
                        <div class="media-text" id="showComment'.$id.'">'.$adicional.'</div>
                        <div id="zoneEditComment'.$id.'" style="display:none;">
                            <textarea class="form-control" id="editComment'.$id.'">'.$item['comment_text'].'</textarea>
                        </div>
                        <div class="media-notation" style="font-size:12px;">
                            '.$htmlOpciones.'
                        </div>
                    </div>
                </div>';
        }
        
            
        $return['success'] = 1;
        $return['mensaje'] = "Informacion correcta";
        $return['html'] = $html;
    }else{
        $html = 'Aun no hay comentarios';
        $return['success'] = 0;
        $return['mensaje'] = "Aun no hay comentarios";
        $return['html'] = $html;
    }
    return $return;
}

function crear(){
    global $token;
    global $chat_id;
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->setTask($txt_titulo, $txt_descripcion, $cmbTags, $cmbPrioridad, $txt_estado);
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "Tarea creada correctamente";
    	$return['id'] = $resp['id'];
    	
    	$texto = $session['cod_usuario']."- ".$nombreUser." de la empresa ".$session['empresa']." creo un ticket: ".$txt_titulo;
        mylog($session['alias'], "scrumboard-tickets", $texto, "Ticket creado");
    	
    	$html = '<b>'.strtoupper($session['empresa']).'</b>'.PHP_EOL;
    	$html.= '<b>Observaciones:</b>'.PHP_EOL;
    	$html.= '<b>'.$txt_titulo.'</b>'.PHP_EOL;
    	$html.= $txt_descripcion;
    	$telegram = new cl_telegram($token);
    	$return['respTelegram'] = $telegram->sendMessage($chat_id, $html);
    	
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al crear el ticket, por favor vuelva a intentarlo";
    }
    return $return;
}

function update_task_info(){
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->updateTask($id, $titulo, $descripcion);
    
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "Tarea editada correctamente";
    	$return['id'] = $resp['id'];
    	$return['name'] = $titulo;
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la tarea, por favor vuelva a intentarlo";
    }
    return $return;
}

function set_estado_tarea(){
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->updateStatusTask($id, $name);
    
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "Tarea editada correctamente";
    	$return['id'] = $resp['id'];
    	$return['name'] = $name;
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la tarea, por favor vuelva a intentarlo";
    }
    return $return;
}

function addComment(){
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->setCommentByTask($id, $comment);
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "comentario creado correctamente";
    	$return['id'] = $resp['id'];
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al crear el comentario, por favor vuelva a intentarlo";
    	$return['resp'] = $resp;
    }
    return $return;
}

function removeComment(){
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $clScrum->deleteComment($id);
    $return['success'] = 1;
    $return['mensaje'] = "comentario eliminado correctamente";
    /*
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "comentario eliminado correctamente";
    	$return['id'] = $resp['id'];
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al eliminar el comentario, por favor vuelva a intentarlo";
    	$return['resp'] = $resp;
    }*/
    return $return;
}

function updateComment(){
	global $session;
    global $clScrum;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->updateComment($id, $comment);
    $return['success'] = 1;
    $return['mensaje'] = "comentario editado correctamente";
    /*
    if(isset($resp['id'])){
    	$return['success'] = 1;
    	$return['mensaje'] = "comentario editado correctamente";
    	$return['id'] = $resp['id'];
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el comentario, por favor vuelva a intentarlo";
    	$return['resp'] = $resp;
    }*/
    return $return;
}

function uploadAdjunto(){
    global $token;
    global $chat_id;
    global $session;
    global $clScrum;
    global $nombreUser;
    
    $formatos = array('image/jpeg', 'image/png'); 
    
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    if(!in_array($_FILES["attachment"]['type'], $formatos)){
        $return['success'] = 0;
    	$return['mensaje'] = "Formato no compatible";
    	return $return;
    }
    
    $nombre = $_FILES["attachment"]['name'];
    $extension = explode(".",$nombre);
    $extension = $extension[count($extension) - 1];
    
    $nameFile = "attachment_".datetime_format().".".$extension;
    
    if(uploadFile($_FILES["attachment"], $nameFile)){
        $dir = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameFile;
        $dir2 = url_sistema.'/assets/empresas/'.$session['alias'].'/'.$nameFile;
        
        $getMime = mime_content_type($dir);
        $formatosProhibidos = array('text/x-php');
        $formatosNoProhibidos = array('image/jpeg', 'image/png'); 
        if(!in_array($getMime, $formatosNoProhibidos)){
            unlink($dir);
            $texto = $nombreUser." intento subir un archivo no compatible, mime: ".$getMime;
            mylog($session['alias'], "scrumboard-attachment-error", $texto, "Formato no compatible");
            
            if(in_array($getMime, $formatosProhibidos)){    //SIGNIFICA QUE ES UN FORMATO PROHIBIDO
                $html = '<b>-------ALERTA-------</b>'.PHP_EOL;
                $html .= 'Se intento subir un archivo prohibido desde Scrumboard'.PHP_EOL;
                $html .= '<b>Empresa: </b>'.$session['empresa'].PHP_EOL;
                $html .= '<b>Usuario: </b>'.$nombreUser.PHP_EOL;
                $html .= '<b>MIME: </b>'.$getMime;
            	$telegram = new cl_telegram($token);
            	$return['respTelegram'] = $telegram->sendMessage($chat_id, $html);
            }
            
            $return['success'] = 0;
	        $return['mensaje'] = "Formato no compatible, debe ser una imagen";
	        return $return;
        }
        
        $resp = $clScrum->setAttachment($idTask, $dir, $nameFile);
        if(isset($resp['id'])){
            $return['success'] = 1;
            $return['fileInfo'] = $_FILES["attachment"];
            $return['mimeType'] = mime_content_type($dir);
    	    $return['mensaje'] = "Adjunto subido correctamente";
    	    $return['nameFile'] = $nameFile;
    	    $return['respuesta'] = $resp;
    	    
    	    $texto = $nombreUser." subio un adjunto con mime: ".$getMime;
            mylog($session['alias'], "scrumboard-attachment-success", $texto, "Adjunto subido correctamente");
    	    
    	    $telegram = new cl_telegram($token);
    	    $return['respTelegram'] = $telegram->sendImage($chat_id, $dir2,strtoupper($session['empresa']));
        }else{
            $return['success'] = 0;
    	    $return['mensaje'] = "No se pudo subir el adjunto, por favor vuelva a intentarlo.";
        }
    }else{
        $return['success'] = 0;
    	$return['mensaje'] = "Error al subir el adjunto, por favor verificar el archivo a subirse";
    }
    return $return;
}

function crearLista(){
	global $session;
    global $clScrum;
    global $Clempresas;
	if(!isset($_POST)){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $resp = $clScrum->createList($nombre, 'Soporte para '.$nombre);
    if(isset($resp['id'])){
        $id = $resp['id'];
    	$return['success'] = 1;
    	$return['mensaje'] = "Empresa integrada correctamente con ClickUp";
    	$return['id'] = $id;
    	
    	$query = "INSERT INTO tb_empresa_clickup(cod_empresa, id_lista, estado) VALUES($cod_empresa, '$id', 'A')";
    	Conexion::ejecutar($query,NULL);
        /* AUMENTAR PROGRESO DE LA EMPRESA*/
        $Clempresas->updateProgresoEmpresa($cod_empresa, 'Clickup integrado', 10);
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al crear la lista, por favor vuelva a intentarlo";
    	$return['resp'] = $resp;
    }
    return $return;
}

function crearWebhook(){
	global $session;
    global $clScrum;

    $resp = $clScrum->createWebHook();
    $return['success'] = 1;
    $return['mensaje'] = "Servicio de creacion de WebHooks ejecutado";
    $return['resp'] = $resp;
    return $return;
}

function getWebhook(){
	global $session;
    global $clScrum;


    $resp = $clScrum->getWebHooks();
    $return['success'] = 1;
    $return['mensaje'] = "Lista de Webhooks";
    $return['resp'] = $resp;
    return $return;
}

function getThumbnail($extension, $urlImage){
    $extension = strtolower($extension);
    $extImagenes = array("jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "apng", "avif", "gif", "webp", "bmp", "ico", "tiff", "svg");
    $extDoc = array("doc", "docx", "rtf");
    $extXls = array("xls", "xlsx", "csv");
    $extZip = array("zip", "rar");
    
    if(in_array($extension, $extImagenes)){ //ES UNA IMAGEN
        return $urlImage;
    }
    if(in_array($extension, $extDoc)){ //ES UNA DOC
        return 'https://dashboard.mie-commerce.com/assets/img/icons/doc.png';
    }
    if(in_array($extension, $extXls)){ //ES UN EXCEL
        return 'https://dashboard.mie-commerce.com/assets/img/icons/xls.png';
    }
    if(in_array($extension, $extZip)){ //ES UN ZIP o RAR
        return 'https://dashboard.mie-commerce.com/assets/img/icons/rar.png';
    }
    return 'https://dashboard.mie-commerce.com/assets/img/icons/desconocido.png';
}
?>