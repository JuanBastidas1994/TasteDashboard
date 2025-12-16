<?php

class cl_telegram
{
        var $URL = "https://api.telegram.org/bot";
        var $token;
		var $cod_empresa;
		
		public function __construct($ptoken = null)
		{
            $this->cod_empresa = cod_empresa;
            $this->token = $ptoken;
            $this->URL = $this->URL.$this->token;
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
        
        /*GRUPOS*/
        public function grupo_existe($cod_chat){
            $query = "SELECT * FROM tb_telegram_grupos WHERE cod_chat = '$cod_chat'";
            $resp = Conexion::buscarRegistro($query);
			return $resp;
        }
        
        public function grupo_crear($cod_chat, $nombre){
            $query = "INSERT INTO tb_telegram_grupos(cod_chat,cod_empresa, nombre,estado) VALUES('$cod_chat', $this->cod_empresa, '$nombre', 'I')";
            return Conexion::ejecutar($query,NULL);
        }
        
        /*LISTA Y CREAR USUARIOS*/
        public function usuario_existe($cod_usuario){
            $query = "SELECT * FROM tb_telegram_usuarios WHERE cod_telegram_usuario = $cod_usuario AND cod_empresa = $this->cod_empresa";
            $resp = Conexion::buscarRegistro($query);
			return $resp;
        }
        
        public function usuario_crear($cod_usuario, $nombre, $apellido, $code){
            $query = "INSERT INTO tb_telegram_usuarios (cod_telegram_usuario,cod_empresa,nombre,apellido,idioma_codigo) ";
            $query.= "VALUES('$cod_usuario',$this->cod_empresa,'$nombre','$apellido','$code')";
            return Conexion::ejecutar($query,NULL);
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
}
?>