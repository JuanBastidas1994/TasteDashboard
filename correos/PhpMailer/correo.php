<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php' ;

error_reporting(E_ALL);

$BodyCorreo = 'Holaa';
								$MailBody = "<html>
									<body style='background-color: #eaeaef !important; min-height: 500px !important;'>
									<div align='center' style='width: 100%;'><img src='https://medicma.com/login/images/logo_plomo.png' style='width: 100%;max-width: 150px;'></div>
									<div style='margin-left: 7%;width: 80%;margin-top: 25px; background-color: white; padding: 25px; box-shadow: 1px 1px 3px 0px #807e7e;'>
										".$BodyCorreo."	
										<p><address style='font-size: 10px;'>Nota: No responder este mensaje ya que es generado automaticamente</address></p>
									</div>
									</body>
									</html>";
								
								$mail = new PHPMailer();
								try {
								    //Server settings
								    //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
								    //host gator4210.hostgator.com
								    $mail->isSMTP();                                      // Set mailer to use SMTP
								    $mail->Host = 'medicma.com';  			// Specify main and backup SMTP servers
								    $mail->SMTPAuth = true;                               // Enable SMTP authentication
								    $mail->Username = 'recordatorios@medicma.com';                 // SMTP username
								    $mail->Password = 'fcjuventus';                           // SMTP password
								    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
								    $mail->Port = 465;                                    // TCP port to connect to
								
								    //Recipients
								    $mail->setFrom('info@medicma.com', 'Medicma');
								    $mail->SMTPDebug = true;
								    //$mail->addAddress($paraCorreo, $paraNombre);     // Add a recipient
								    $mail->addAddress('juankbastidasjuve@gmail.com', 'Juan Carlos');     // Add a recipient
								    //$mail->addAddress('ellen@example.com');               // Name is optional
								    $mail->addReplyTo('info@medicma.com', 'Medicma Informacion');
								    //$mail->addCC('cc@example.com');
								    //$mail->addBCC('bcc@example.com');
								
								    //Attachments
								    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
								    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
								
								    //Content
								    $mail->isHTML(true);                                  // Set email format to HTML
								    $mail->Subject = "dakljsajlksad";
								    //$mail->Body    = $MailBody;
								    $mail->Body    = "HOla";
								    $mail->AltBody = 'adsdas';

								    if (!$mail->send()) {
							                 $response['success']=0;
									 $response["message"]="Error al enviar correo";
								    }
								    else
								    {
									 $response['success']=1;
									 $response["message"]="Correo enviado satisfactoriamente";
									 var_dump($mail->ErrorInfo);
								    }
								} catch (Exception $e) {
								    $response['success']=0;
							            $response["message"]="Error del servidor de correo: ".$mail->ErrorInfo;
								}

header('Content-Type: application/json');
echo json_encode($response);

?>