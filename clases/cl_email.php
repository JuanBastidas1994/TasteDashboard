<?php
//$hostname = '{mail.danilorestaurante.com:993/imap/ssl}INBOX.Sent';
class cl_email
{       
        var $server, $username, $password, $port, $hostname, $folder="";
        var $inbox;
		
		public function __construct($pHost, $pPort, $pUser, $pPass){
		    $this->server = $pHost;
		    $this->username = $pUser;
		    $this->password = $pPass;
		    $this->port = $pPort;
		    
		    $this->hostname = "{".$pHost.":".$pPort."/imap/ssl}";
		}
		
		//CONEXION AL IMAP
		public function open($folder){  //EJ. INBOX - INBOX.Sent
		    $this->folder = $folder;
		    $hostname = $this->hostname.$folder;
            $this->inbox = imap_open($hostname,$this->username,$this->password) or die('Cannot connect to Email: ' . imap_last_error());
		}
		
		public function getFolders(){
		    $folders = imap_list($this->inbox, $this->hostname, "*");
            if ($folders === false) {
                return false;
            } else {
                foreach($folders as $key => $folder){
                    $part = explode($this->hostname, $folder);
                    $folders[$key] = $part[1];
                }
                return $folders;
            }
		}
		
		public function getEmails(){
		    $data = [];
		    $emails = imap_search($this->inbox,'SINCE "1 October 2021"');
		    if(!$emails){
		        return false;
		    }
		    
		    rsort($emails); //Ordenar el ultimo correo primero.
		    foreach($emails as $email_number) {
		        $overview = imap_fetch_overview($this->inbox,$email_number,0);
                $structure = imap_fetchstructure($this->inbox, $email_number);
                $attachment = [];
                
                $body = "";
                $num_part = 1;
                $parts = $structure->parts;
                foreach($parts as $part){
                    $message = imap_fetchbody($this->inbox,$email_number,$num_part);
                    if($part->encoding == 3) {
                        //$message = imap_base64($message);
                        $message = "";
                        $adjunto['tipo'] = $part->subtype;
                        $adjunto['nombre'] = $part->parameters[0]->value;
                        $attachment[] = $adjunto;
                    } else if($part->encoding == 4) {
                        $message = imap_qprint($message);
                    }
                    $body .= $message;
                    $num_part++;
                }
                $overview[0]->subject = iconv_mime_decode($overview[0]->subject,0,"UTF-8");
                $overview[0]->body = strip_tags($body);
                $overview[0]->attachment = $attachment;
                $overview[0]->folder = $this->folder;
                
                $data[] = $overview[0];
		    }
		    return $data;
		}
		
		public function getEmailDetail($email_number){
		    $overview = imap_fetch_overview($this->inbox,$email_number,0);
            $structure = imap_fetchstructure($this->inbox, $email_number);
            $attachment = [];
            
            $body = "";
            $num_part = 1;
            $parts = $structure->parts;
            foreach($parts as $part){
                $message = imap_fetchbody($this->inbox,$email_number,$num_part);
                if($part->encoding == 3){
                    $adjunto['tipo'] = $part->subtype;
                    $adjunto['nombre'] = $part->parameters[0]->value;
                    $adjunto['peso'] = $part->bytes/1000;
                    //$adjunto['aditional'] = $part;
                    $adjunto['data'] = $message;
                    $attachment[] = $adjunto;
                    
                    /*
                    //CONVERTIR BASE64 EN FILE
                    $decoded_data = base64_decode($message);
                    file_put_contents('image002.jpg',$decoded_data);*/
                    
                    $message = "";
                } else if($part->encoding == 4) {
                    $message = imap_qprint($message);
                }
                $body .= $message;
                $num_part++;
            }
            //$overview[0]->parts = $parts;
            $overview[0]->subject = iconv_mime_decode($overview[0]->subject,0,"UTF-8");
            $overview[0]->body = $body;
            $overview[0]->attachment = $attachment;
            
            return $overview[0];
		}
		
		public function close(){
		    imap_close($this->inbox);
		}
}
?>