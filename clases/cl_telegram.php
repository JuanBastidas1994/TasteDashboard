<?php

class cl_telegram
{
		var $TOKEN = "791140271:AAF62ruO7KGMEwrUUFq9q2PJAGcbJoYeLjQ";
        var $URL = "https://api.telegram.org/bot791140271:AAF62ruO7KGMEwrUUFq9q2PJAGcbJoYeLjQ";
        var $session;
		var $cod_empresa;

		public function __construct()
		{
            $this->session = getSession();
            $this->cod_empresa = $this->session['cod_empresa'];
		}
		
		/*ENVIAR MENSAJES*/
		public function sendMessage($chat_id, $text)
        {
            $json = ['chat_id'       => $chat_id,
                     'text'          => $text,
                     'parse_mode'    => 'HTML'];
                     
            $ch = curl_init($this->URL.'/sendMessage');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        
        public function sendImage($chat_id, $url, $subtitle="")
        {
            $json = ['chat_id'      => $chat_id,
                     'photo'        => $url,
                     'caption'      => $subtitle];
                     
            $ch = curl_init($this->URL.'/sendPhoto');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        
        public function sendContact($chat_id, $phone_number, $name)
        {
            $json = ['chat_id'          => $chat_id,
                     'phone_number'     => $phone_number,
                     'first_name'       => $name];
                     
            $ch = curl_init($this->URL.'/sendContact');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        
        public function sendPoll($chat_id, $question, $options) //ENCUESTA
        {
            $json = ['chat_id'          => $chat_id,
                     'question'     => $question,
                     'options'       => $options,
                     'is_anonymous'     => false];
                     
            $ch = curl_init($this->URL.'/sendPoll');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        
        public function sendMediaGroup($chat_id, $galery)
        {
            $json = ['chat_id'          => $chat_id,
                     'media'     => $galery];
                     
            $ch = curl_init($this->URL.'/sendMediaGroup');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }


        public function sendLocation($chat_id, $latitud, $longitud)
        {
            global $URL;
            $json = ['chat_id'       => $chat_id,
                     'latitude'     => $latitud,
                     'longitude'    => $longitud];
                     
            $ch = curl_init($this->URL.'/sendLocation');
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }

        
        public function addURLtoBot($token, $url){
            $link = "https://api.telegram.org/bot".$token."/setWebhook?url=".$url;
            $ch = curl_init($link);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }

        public function getURLtoBot($token){
            $link = "https://api.telegram.org/bot".$token."/getWebhookInfo";
            $ch = curl_init($link);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        }

        /*TOKEN*/
        public function get(){
            $query = "SELECT * FROM tb_telegram WHERE cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }

        public function getByEmpresa($cod_empresa){
            $query = "SELECT * FROM tb_telegram WHERE cod_empresa = $cod_empresa";
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }

        public function crear($empresa, $nombre, $token){
            $query = "INSERT INTO tb_telegram(cod_empresa, token, botname) 
                    VALUES($empresa, '$token', '$nombre')";
            return Conexion::ejecutar($query,NULL);
        }

        public function editar($empresa, $nombre, $token){
            $query = "UPDATE tb_telegram SET token='$token', botname='$nombre' WHERE cod_empresa=$empresa";
            return Conexion::ejecutar($query,NULL);
        }
        
        /*GRUPOS*/
        public function lista_grupos(){
            $query = "SELECT * FROM tb_telegram_grupos WHERE cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }

        public function grupo_existe($cod_chat){
            $query = "SELECT * FROM tb_telegram_grupos WHERE cod_chat = '$cod_chat'";
            $resp = Conexion::buscarRegistro($query);
			return $resp;
        }
        
        public function grupo_crear($cod_chat, $nombre){
            $query = "INSERT INTO tb_telegram_grupos(cod_chat,nombre,estado) VALUES('$cod_chat', '$nombre', 'I')";
            return Conexion::ejecutar($query,NULL);
        }

        public function grupo_set_estado($cod_chat, $estado){
            $query = "UPDATE tb_telegram_grupos SET estado='$estado' WHERE cod_chat = $cod_chat";
            return Conexion::ejecutar($query,NULL);
        }
        
        /*LISTA Y CREAR USUARIOS*/
        public function lista_usuarios(){
            $query = "SELECT * FROM tb_telegram_usuarios WHERE cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }

        public function usuario_existe($cod_usuario){
            $query = "SELECT * FROM tb_telegram_usuarios WHERE cod_telegram_usuario = $cod_usuario";
            $resp = Conexion::buscarRegistro($query);
			return $resp;
        }
        
        public function chatVerificado($chatId){
            $query = "SELECT * FROM tb_telegram_usuarios WHERE chat_id = '$chatId'";
            $resp = Conexion::buscarRegistro($query);
			return $resp;
        }
        
        /*GUARDAR UBICACION*/
        public function update_location($cod_chat, $cod_telegram_usuario, $latitud, $longitud, $cod_usuario){
            $fecha = fecha();
            $query = "INSERT INTO tb_telegram_usuarios_ubicacion(cod_telegram_usuario, cod_chat, latitud, longitud, fecha) ";
            $query.= "VALUES('$cod_telegram_usuario','$cod_chat','$latitud','$longitud', '$fecha')";
            if(Conexion::ejecutar($query,NULL)){
                $query = "UPDATE tb_usuarios SET latitud='$latitud', longitud='$longitud', fecha_ubicacion='$fecha' WHERE cod_usuario=$cod_usuario";
                return Conexion::ejecutar($query,NULL);
            }
            return false;
        }
        
        
        public function listaTelegramUsuarios($cod_usuario){
			$query = "SELECT u.nombre, u.apellido,tu.* FROM tb_telegram_usuarios tu
			INNER JOIN tb_usuarios u ON u.cod_usuario = tu.cod_usuario
			WHERE tu.cod_usuario = $cod_usuario";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function crearTelegramUsuarios($cod_usuario){
		    $code = $this->generarCodigoAleatorio().$cod_usuario;
            $query = "INSERT INTO tb_telegram_usuarios(cod_usuario, code, estado) 
                    VALUES($cod_usuario, '$code', 'P')";
            return Conexion::ejecutar($query,NULL);
        }
        
        function generarCodigoAleatorio($longitud = 3) {
            $codigo = '';
            for ($i = 0; $i < $longitud; $i++) {
                $letra = chr(random_int(65, 90)); // Letras A-Z en ASCII
                $codigo .= $letra;
            }
            return $codigo;
        }
        
		public function deleteTelegramUsuarios($cod_usuario){
            $query = "DELETE FROM tb_telegram_usuarios WHERE cod_usuario = $cod_usuario";
            echo $query;
            return Conexion::ejecutar($query,NULL);
        }

        /*ASINGAR USUARIO*/
        public function getAsignacion($cod_usuario_telegram, $cod_usuario){
            $query = "SELECT * FROM tb_telegram_usuarios WHERE cod_usuario = $cod_usuario";
            return Conexion::buscarRegistro($query);
        }   

        public function asignarUsuario($cod_usuario_telegram, $cod_usuario){
            $query = "UPDATE tb_telegram_usuarios SET cod_usuario = $cod_usuario WHERE cod_telegram_usuario = $cod_usuario_telegram";
            return Conexion::ejecutar($query,NULL);
        }

        public function removerUsuario($cod_usuario_telegram, $cod_usuario){
            $query = "UPDATE tb_telegram_usuarios SET cod_usuario = '' WHERE cod_telegram_usuario = $cod_usuario_telegram AND cod_usuario = $cod_usuario";
            return Conexion::ejecutar($query,NULL);
        }
}
?>