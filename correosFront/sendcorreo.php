<?php
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PhpMailer/src/PHPMailer.php';
require 'PhpMailer/src/SMTP.php';
require 'PhpMailer/src/Exception.php';

$correo = "juankbastidasjuve@gmail.com";
$correo = "jbastidas@saro-ec.com";
$asunto = "Danilo Restaurante";
$nombre = "Juan Carlos";

$_GET['id'] = 62;
include("orden_complete.php");

$MailBody = $html;
//echo $MailBody;


	$mail = new PHPMailer();
	try {
	    //Server settings
	    $mail->SMTPDebug = 2; 
	    // Enable verbose debug output
		
  		//GMAIL
  		$mail->Host = 'smtp.gmail.com';
  		$mail->SMTPAuth = true;
  		$mail->Username = 'juankbastidasjuve@gmail.com';
  		$mail->Password = 'fcjuventus'; 
  		$mail->SMTPSecure = 'ssl';
  		$mail->Port = 465;
  			
  		//Recipients
  		$mail->setFrom('juankbastidasjuve@gmail.com', 'Juan Bastidas');
  		$mail->addAddress($correo, $nombre);
  		$mail->addReplyTo('juankbastidasjuve@gmail.com', 'Juan Bastidas');
  			  
  		//Content
  		$mail->isHTML(true);
      $mail->CharSet = 'UTF-8';
  		$mail->Subject = $asunto;
  		$mail->Body    = $MailBody;
  		$mail->AltBody = 'DigitalMind';

  		if (!$mail->send())
  		{
  		    $return['success']= 0;
              $return['mensaje']= "Error al enviar el correo";
  		}
  		else
  		{
  			$return['success']= 1;
              $return['mensaje']= "Correo enviado correctamente";
  		}
    } catch (Exception $e) {
		  $return['success']= 0;
        $return['mensaje']= "Error al enviar el correo";
	}

header('Content-Type: application/json');
echo json_encode($return);

?>