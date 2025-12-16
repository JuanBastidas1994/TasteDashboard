<?php

class cl_clickup
{
		var $TOKEN = "pk_3088635_M4GGH5TWC2K4UOHN7LA5B1XUA4PCLG4L";
        var $URL = "https://api.clickup.com/api/v2";
        var $IdUser = "3088635";
        var $IdTeam = "3048482"; //TEAM: DIGITAL MIND
        var $IdSpace = "3098857"; //SPACE: SOPORTE Y MANTENIMIENTO
        var $IdLista = "";
        var $session;
		var $cod_empresa;

		public function __construct()
		{
            $this->session = getSession();
            $this->cod_empresa = $this->session['cod_empresa'];
            $this->getIdLista();
		}
		
		public function getIdLista(){
            $query = "SELECT * FROM tb_empresa_clickup WHERE cod_empresa = ".$this->session['cod_empresa']." AND estado = 'A'";
            $row = Conexion::buscarRegistro($query, NULL);
            if($row){
                $this->IdLista = $row['id_lista'];
            }
		}
		
		public function getList(){
            $link = $this->URL.'/list/'.$this->IdLista;
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function getTaskByList(){
            $link = $this->URL.'/list/'.$this->IdLista."/task?archived=false";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function getTask($idTarea){
            $link = $this->URL.'/task/'.$idTarea.'?include_subtasks=true';
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        
        public function setTask($titulo, $descripcion, $tags, $prioridad, $estado){
            $tarea['name'] = $titulo;
            $tarea['description'] = $descripcion;
            $tarea['tags'] = $tags;
            $tarea['status'] = $estado;
            $tarea['priority'] = $prioridad;
            $json = json_encode($tarea);
            
            $link = $this->URL.'/list/'.$this->IdLista."/task";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function updateTask($id, $titulo, $descripcion){
            $tarea['name'] = $titulo;
            $tarea['description'] = $descripcion;
            $tarea['archived'] = false;
            $json = json_encode($tarea);
            
            $link = $this->URL.'/task/'.$id;
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function updateStatusTask($id, $status){
            $tarea['status'] = $status;
            $tarea['archived'] = false;
            /*
            $tarea['name'] = $titulo;
            $tarea['description'] = $descripcion;
            $tarea['tags'] = $tags;
            $tarea['priority'] = $prioridad;*/
            $json = json_encode($tarea);
            
            $link = $this->URL.'/task/'.$id;
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function setAttachment($taskId, $urlFile, $filename){
            if (function_exists('curl_file_create')) { // php 5.5+
              $cFile = curl_file_create($urlFile);
            } else { // 
              $cFile = '@' . realpath($urlFile);
            }
            $post = array('filename' => $filename,'attachment'=> $cFile);
            $link = $this->URL.'/task/'.$taskId."/attachment";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: multipart/form-data';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        
        public function createList($name, $content){
            $lista['name'] = $name;
            $lista['content'] = $content;
            $lista['status'] = "red";
            $lista['priority'] = 1;
            $json = json_encode($lista);
            
            $link = $this->URL.'/space/'.$this->IdSpace."/list";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        /*COMENTARIOS*/
        public function getCommentByTask($idTarea){
            $link = $this->URL.'/task/'.$idTarea.'/comment';
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function setCommentByTask($idTarea, $coment){
            $comentario['comment_text'] = $coment;
            $comentario['notify_all'] = true;
            $json = json_encode($comentario);
            
            $link = $this->URL.'/task/'.$idTarea."/comment";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response,true);
        }
        
        public function updateComment($idComent, $coment){
            $comentario['comment_text'] = $coment;
            $comentario['resolved'] = true;
            $json = json_encode($comentario);
            
            $link = $this->URL.'/comment/'.$idComent;
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response,true);
        }
        
        public function deleteComment($idComent){
            $link = $this->URL.'/comment/'.$idComent;
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response,true);
        }
        
        
        
        
        /*WEBHOOKS*/
        public function createWebHook(){
            $events = array("taskCreated", "taskUpdated", "taskDeleted", "taskMoved", "taskCommentPosted", "taskCommentUpdated");
            
            $data['endpoint'] = "https://dashboard.mie-commerce.com/webhooks/wh_clickup.php";
            $data['events'] = $events;
            $json = json_encode($data);
            
            $link = $this->URL.'/team/'.$this->IdTeam."/webhook";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        public function getWebHooks(){
            $link = $this->URL.'/team/'.$this->IdTeam."/webhook";
            $ch = curl_init($link);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: '.$this->TOKEN; // key here
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }
        
        
}
?>