<?php
require_once "config.php";
ob_start();
session_start();

if(ENVIRONMENT == "production"){
  ini_set('display_errors', 0);
  error_reporting(0);
}else{
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}

require_once "conexion.php";

function verificateWs(&$codigo)
{
  $Allheaders = getallheaders();
  if (array_key_exists("Api-Key",$Allheaders))
  {
    $query = "SELECT * FROM tb_empresas WHERE api_key = '".$Allheaders['Api-Key']."' AND estado = 'A'";
    $codigo = Conexion::buscarRegistro($query);
    if($codigo){
      return true;
    }
    else
      return false;
  }
  else
    return false;
}

function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-128-CBC";
    $secret_key = '1234567890123456';
    $secret_iv = '1234567890123456';
  
    if (strlen($secret_key) == 16){
      $encrypt_method = "AES-128-CBC";
    }else{
      $encrypt_method = "AES-256-CBC";
    }

    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $secret_key, 0,$secret_iv);
        //$output is base64 encoded automatically!
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt($string, $encrypt_method, $secret_key, 0,$secret_iv);
        //$string must be base64 encoded!
    }
    return $output;
}

function fecha()
{
	date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("Y-m-d H:i:s", $time);	//FECHA Y HORA ACTUAL
	return $fecha;
}

 function fecha_only()
{
	date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("Y-m-d", $time);	//FECHA Y HORA ACTUAL
	return $fecha;
}

function AddIntervalo($datetime, $intervalo){
    list($h, $m, $s) = explode(':', $intervalo);
    $intervalo_minutos = ($h*60) + $m;

    list($dia, $hora) = explode(' ', $datetime);
    $nuevaHora = strtotime ( $intervalo_minutos.' minute' , strtotime ( $hora ) ) ;
    $nuevaHora = date ( 'H:i:s' , $nuevaHora);

    return $dia." ".$nuevaHora;
}

function diasdelMes()
{
  date_default_timezone_set('America/Guayaquil');
  $time = time();
  $mes = date("m", $time);
  $year = date("Y", $time);
  return cal_days_in_month(CAL_GREGORIAN, $mes, $year);
}

function diasdelMesRol($mes)
{
 
  $mes_anio= explode("-",$mes);
  return cal_days_in_month(CAL_GREGORIAN, $mes_anio[1], $mes_anio[0]);
}

function mesTextOnly($mes = null)
{
  if($mes == null){
    date_default_timezone_set('America/Guayaquil');
    $time = time();
    $mes = date("n", $time);
  }
    
  switch ($mes) {
    case 1: return "Enero";
    case 2: return "Febrero";
    case 3: return "Marzo";
    case 4: return "Abril";
    case 5: return "Mayo";
    case 6: return "Junio";
    case 7: return "Julio";
    case 8: return "Agosto";
    case 9: return "Septiembre";
    case 10: return "Octubre";
    case 11: return "Noviembre";
    case 12: return "Diciembre";
  }
  return $mes;
}

function getYear()
{
  date_default_timezone_set('America/Guayaquil');
  $time = time();
  $mes = date("Y", $time);
  return $mes;
}

 function fileActual()
 {
     $data = explode('/', $_SERVER['PHP_SELF']);
     return $data[count($data)-1];
 }
 
/*START FUNCIONES DE SESSION*/
function isLogin()
{
  if(isset($_SESSION[name_session]))
  {
    if($_SESSION[name_session]!= NULL)
      return true;
    else
      return false;
  }
  else
    return false; 
}

function getSession(){
  if(!isset($_SESSION[name_session]))
    return null;
  return $_SESSION[name_session];
}

function setSession($data){
  $_SESSION[name_session] = $data;
}
/*END FUNCIONES DE SESSION*/
 
 function create_slug($string){
    $slug = preg_replace('/[^A-Za-z0-9-]+/','-',$string);
    $slug = strtolower($slug);
    return $slug;
}
 
function fechaLatinoShort($fecha){
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));

  $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return "$nombreMes $numeroDia, $anio";
}

 function fechaLatino($fecha) {
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
  $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}

function name_page()
{
    $name = fileActual();
    $data = Conexion::buscarRegistro("SELECT * FROM tb_paginas WHERE nombre = '$name'");
    if($data){
    	echo $data['titulo'];
    }else{
    	echo 'Tienda';
    }
}

function getEstado($estado){
	switch ($estado) {
    case 'A': return "Activo";
    case 'I': return "Inactivo";
    case 'D': return "Eliminado";
  }
  return $estado;
}

function datetime_format()
{
	date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("Y_m_d_H_i_s", $time);	//FECHA Y HORA ACTUAL
	return $fecha;
}

function getApikey(){
	$an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
	$su = strlen($an) - 1;
	return  "WEEK-".
	        substr($an, rand(0, $su-1), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1) .
			substr($an, rand(0, $su), 1);
}

function getNivel($con, $cod_noticia)
{
    $nivel = 1;
    if($cod_noticia != 0)
    {
        do
        {
            $nivel = $nivel + 1;
            $query = "SELECT * FROM tb_noticias WHERE cod_noticia = ".$cod_noticia." AND estado IN ('A','I')";
            $resp = mysqli_query($con,$query);
            $data = mysqli_fetch_array($resp);
            $padre = $data['cod_noticia_padre']; 
            $cod_noticia = $padre;
        }while($padre != 0);
    }
    return $nivel;
}

function validar_correo($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    else
        return false;
}

function fechaSignos()
{
  date_default_timezone_set('America/Guayaquil');
  $time = time();
  $fecha = date("YmdHis", $time);  //FECHA Y HORA ACTUAL
  return $fecha;
}

function get_client_ip_server() {
      $ipaddress = '';
      if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
      return $ipaddress;
  }

function SendEmail($cod_plantilla, $envio, $correos, $datos, $con){
    $query = "SELECT * FROM tb_plantilla_correo WHERE cod_plantilla_correo = ".$cod_plantilla;
    $resp = mysqli_query($con, $query);
    if(mysqli_num_rows($resp)>0)
    {
        $plantilla = mysqli_fetch_assoc($resp);
        //$html = html_entity_decode(htmlspecialchars_decode($plantilla['html']));
        $html = $plantilla['html'];
        //$datos = array_map('utf8_encode', $datos); 
        $json['body'] = str_replace(array_keys($datos), $datos, $html);
        $json['asunto'] = $plantilla['asunto'];
        if($envio == 1){
            $json['emisor'] = implode(";", $correos);
            $json['receptor'] = $plantilla['correos'];
        }
        else{
            $json['receptor'] = implode(";", $correos);
            $json['emisor'] = $plantilla['correos'];
        }

        //return json_encode($json);
        
        $api = "digital2018";
        $ch = curl_init("http://www.digitalmindtec.com/pentax/administrator/sistema/sendCorreo2.php");
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Api-Key: '.$api;
      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
        
    }
    return "";
}  

function passRandom(){
  $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  $su = strlen($an) - 1;
  return  substr($an, rand(0, $su-1), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1) .
      substr($an, rand(0, $su), 1);
}

function calculaedad($fechanacimiento){
  list($ano,$mes,$dia) = explode("-",$fechanacimiento);
  $ano_diferencia  = date("Y") - $ano;
  $mes_diferencia = date("m") - $mes;
  $dia_diferencia   = date("d") - $dia;
  if ($dia_diferencia < 0 || $mes_diferencia < 0)
    $ano_diferencia--;
  return $ano_diferencia;
}

function sinTildes($cadena){
      $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
      $modificadas ='aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
      $cadena = utf8_decode($cadena);
      $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
      $cadena = strtolower($cadena);
      return utf8_encode($cadena);
}

function uploadFile($file, $name){
  $session = getSession();
  $files = url_upload.'/assets/empresas/'.$session['alias'].'/';
  $dir = $files.$name;
  if(basename($file["name"])!=""){
        if (move_uploaded_file($file["tmp_name"], $dir)) {
            return true;
        }
        else
        {
            return false;
        }
  }
}

function editor_encode($texto){
  return htmlentities(htmlspecialchars($texto));
}

function editor_decode($texto){
  return html_entity_decode(htmlspecialchars_decode($texto));
}

function listDraggableMenuEmpresa($id, $nivel, $rol)
{
    $pref = "";
    if($nivel>1) 
      $pref="&emsp; - ";
    $html = "";
    $query = "SELECT p.* 
            FROM tb_paginas p
            WHERE p.cod_pagina
            AND p.estado IN ('A', 'I') 
            AND p.cod_padre = ".$id." ORDER BY p.posicion";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach ($resp as $row) {
      $html .=  '<tr id="'.$row['cod_pagina'].'" data-id="'.$row['cod_pagina'].'" data-parent="'.$row['cod_padre'].'" data-level="'.$nivel.'">
            <td data-column="name">
              <div class="n-chk">
                  <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                    <input type="checkbox" class="new-control-input" checked>
                    <span class="new-control-indicator"></span>'.$pref.$row['titulo'].'
                  </label>
              </div>
            </td>
            <td>'.$row['nombre'].'</td>
        </tr>';

        
        $x=0;
        $query2 = "SELECT * FROM tb_paginas WHERE estado IN ('A', 'I') AND cod_pagina = ".$row['cod_pagina'];
        $resp2 = Conexion::buscarVariosRegistro($query2);
        if(count($resp2)>0)
        {
            $html .= listDraggableMenuEmpresa($resp2[$x]['cod_pagina'], $nivel+1, $rol);
            $x++;
        }
    }
    return $html;
}

ob_end_flush();

 
