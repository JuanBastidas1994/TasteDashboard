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

   // ini_set('display_errors', 1);
  //error_reporting(E_ALL);

require_once "conexion.php";
require_once "design.php";
require_once "clases/cl_usuarios.php";
$regenerarSesion = isLogin();

function controller_create(){
   if(isset($_GET['metodo'])){
        $func = $_GET['metodo'];
        if(function_exists($func)){
            $return = $func();
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Metodo no existe";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Falta metodo a ejecutar";
    }

    if(is_array($return)){
        header("Content-Type: application/json");
        echo json_encode($return);
    }else{
        echo $return;
    }
} 

function fecha()
{
	date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("Y-m-d H:i:s", $time);	//FECHA Y HORA ACTUAL
	return $fecha;
}

function getHourToDateTime($datetime){
    $date = strtotime($datetime);
    return date('H:i', $date);
}

 function fecha_only()
{
	date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("Y-m-d", $time);	//FECHA Y HORA ACTUAL
	return $fecha;
}

function hour_only(){
  date_default_timezone_set('America/Guayaquil');
	$time = time();
	$fecha = date("H:i", $time);	//HORA ACTUAL
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

function hoursAgo($fecha, $min=0){
  $date = new DateTime($fecha);
  $now = new DateTime();
  $diff = $now->diff($date);
  $dia = $diff->format('%d');
  if($dia>$min){
      return fechaLatinoShort($fecha);
  }
  $hora = $diff->format('%h');
  if($hora == 0)
      return $diff->format('Hace %i minutos');
  else
      return $diff->format('Hace %h horas %i minutos');
}

function format_time_remaining($minutes) {
    // Calcular días, horas y minutos
    $days = floor($minutes / 1440); // 1 día = 1440 minutos
    $hours = floor(($minutes % 1440) / 60); // 1 hora = 60 minutos
    $remaining_minutes = $minutes % 60; // Minutos restantes

    $time_str = '';

    if ($days > 0) {
        $time_str .= $days . ' día' . ($days > 1 ? 's' : '') . ' ';
    }

    if ($hours > 0) {
        $time_str .= $hours . ' hora' . ($hours > 1 ? 's' : '') . ' ';
    }

    if ($remaining_minutes > 0) {
        $time_str .= $remaining_minutes . ' minuto' . ($remaining_minutes > 1 ? 's' : '') . ' ';
    }

    // Si el tiempo restante es 0, agregar "Ya disponible"
    if ($time_str === '') {
        $time_str = 'Ya disponible';
    }

    return trim($time_str);
}

function diffTime($datetime1, $datetime2) {
  $datetime1 = new DateTime($datetime1);
  $datetime2 = new DateTime($datetime2);

  $diff = $datetime1->diff($datetime2);
  $horas = $diff->format("%h");
  $minutos = $diff->format("%i");

  $r = [];
  $r["horas"] = $horas;
  $r["minutos"] = $minutos;
  return $r;
}

function TimeStampToDate($text){
    $text = substr($text, 0, 10);
    $dt = new DateTime("@$text");
    return  $dt->format('Y-m-d');
}

function diasdelMes() {
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
function isLogin(){
  if(isset($_SESSION[name_session]))
  {
    if($_SESSION[name_session]!= NULL){
        /*VERIFICAR SI PUEDE TENER ABIERTA LA SESION*/
        if(isset($_COOKIE['token'])){
            $clUsuario = new Cl_usuarios();
            $info = $clUsuario->getRememberLogin($_COOKIE['token']);
            if(!$info){
              destroySession();
              return false;
            }
        }
        return true;
    }
  }
  
  if(isset($_COOKIE['token'])){
    $clUsuario = new Cl_usuarios();
    $info = $clUsuario->getRememberLogin($_COOKIE['token']);
    if($info){
      setSession($info);
      return true;
    }else{
      return false;
    }
  }
  else
    return false;
}

function infoLogin(){
  if(isset($_SESSION[name_session]))
  {
    if($_SESSION[name_session]!= NULL){
      return true;
    }
  }
  return false;
}

function destroySession(){
    $cod_usuario = $_SESSION[name_session]['cod_usuario'];
    $token = $_COOKIE['token'];
    setSession(NULL);
    session_destroy();
    setcookie("token", "", time() - 3600, '/');
    
    $clUsuario = new Cl_usuarios();
    //$clUsuario->DisabledAllAuthTokens($cod_usuario);
    $clUsuario->DisabledOneTokenAuthTokens($cod_usuario, $token);
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

function fechaHoraLatinoShort($datetime){
  $dt = explode(" ", $datetime);
  $fecha = $dt[0];
  $hora = substr($dt[1], 0, 5);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));

  $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return "$numeroDia $nombreMes $anio a las $hora";
}

 function fechaCastellano2($fecha) {
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
    case 'P': return "Pendiente";
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


function getApikey($alias){
	$an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
	$su = strlen($an) - 1;
	return  "API-".$alias."-".
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

function uploadFileWithAlias($alias, $file, $name){
  $files = url_upload.'/assets/empresas/'.$alias.'/';
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

function base64ToImage($base64, $name){
  $session = getSession();
  $files = url_upload.'/assets/empresas/'.$session['alias'].'/';
  $dir = $files.$name;
  try {
    $img = explode(',',$base64,2);
    $data = base64_decode($img[1]);
    file_put_contents($dir, $data);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

function base64ToImageDir($base64, $name, $url){
  $session = getSession();
  $dir = $url."/".$name;
  try {
    $img = explode(',',$base64,2);
    $data = base64_decode($img[1]);
    file_put_contents($dir, $data);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

function getNameImagejpg($name, &$cambio){
    $parts = explode(".png", $name);
    if (count($parts)>1) {
        $cambio = true;
        return $parts[0].".jpg";
    }
    $cambio = false;
    return $name;
}


function getdirfile($name=""){
    $session = getSession();
    $files = url_sistema.'/assets/empresas/'.$session['alias'].'/';
     return $files.$name;
}

function deleteFile($name){
  $session = getSession();
  $files = url_upload.'/assets/empresas/'.$session['alias'].'/';
  $dir = $files.$name;
  try {
      unlink($dir);
      return true;
  } catch (Exception $e) {
      return false;
  } 
}

function deleteFileWithAlias($alias, $name){
  $files = url_upload.'/assets/empresas/'.$alias.'/';
  $dir = $files.$name;
  try {
      unlink($dir);
      return true;
  } catch (Exception $e) {
      return false;
  } 
}

function getFormatoFile($string){
  $f = explode(".", $string);
  return ".".$f[count($f) -1];
}	 

function editor_encode($texto){
  return htmlentities(htmlspecialchars($texto));
}

function editor_decode($texto){
  return html_entity_decode(htmlspecialchars_decode($texto));
}

function listDraggableMenuEmpresa($id, $nivel, $empresa, $rol)
{
    $pref = "";
    if($nivel>1)
      $pref="&emsp; - ";
    $html = "";
    $query = "SELECT p.* 
            FROM tb_paginas p, tb_pagina_rol r
            WHERE p.cod_pagina = r.cod_pagina
            AND p.estado IN ('A', 'I') 
            AND p.cod_padre = $id
            AND r.cod_empresa = $empresa 
            AND  r.cod_rol = $rol ORDER BY r.posicion ASC";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach ($resp as $row) {
      $html .=  '<tr id="'.$row['cod_pagina'].'" data-id="'.$row['cod_pagina'].'" data-parent="'.$row['cod_padre'].'" data-level="'.$nivel.'">
            <td>'.$pref.$row['titulo'].'</td>
            <td>'.$row['nombre'].'</td>
        </tr>';

        
        $x=0;
        $query2 = "SELECT * FROM tb_paginas WHERE estado IN ('A', 'I') AND cod_pagina = ".$row['cod_pagina'];
        $resp2 = Conexion::buscarVariosRegistro($query2);
        if(count($resp2)>0)
        {
            $html .= listDraggableMenuEmpresa($resp2[$x]['cod_pagina'], $nivel+1, $empresa, $rol);
            $x++;
        }
    }
    return $html;
}

function listCheckMenuEmpresa($id, $nivel, $empresa, $rol)
{
    $pref = "";
    if($nivel>1) 
      $pref="&emsp; - ";
    $html = "";
    $query = "SELECT p.* FROM tb_paginas p WHERE p.estado IN ('A', 'I') AND p.cod_padre = $id ORDER BY p.posicion";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach ($resp as $row) {
        $check = "";
        $queryCheck = "SELECT * FROM tb_pagina_rol
                    WHERE cod_pagina = ".$row['cod_pagina']." 
                    AND cod_rol = ".$rol."
                    AND cod_empresa = ".$empresa;
        if(Conexion::buscarRegistro($queryCheck))
          $check = 'checked="checked"';
        


      $html .=  '<tr id="'.$row['cod_pagina'].'" data-id="'.$row['cod_pagina'].'" data-parent="'.$row['cod_padre'].'" data-level="'.$nivel.'">
            <td data-column="name">
              <div class="n-chk">
                  <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                    <input type="checkbox" class="new-control-input chkAddPagina" value="'.$row['cod_pagina'].'" '.$check.'>
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
            $html .= listCheckMenuEmpresa($resp2[$x]['cod_pagina'], $nivel+1, $empresa, $rol);
            $x++;
        }
    }
    return $html;
}

function userGrant(){
    $session = getSession();
    $cod_rol = $session['cod_rol'];
    $cod_empresa = $session['cod_empresa'];
    $name_page = fileActual();
    $query = "SELECT * 
                FROM tb_paginas p, tb_pagina_rol pr
                WHERE p.cod_pagina = pr.cod_pagina
                AND p.nombre = '$name_page' 
                AND pr.cod_rol = $cod_rol 
                AND pr.cod_empresa = $cod_empresa";
    if(Conexion::buscarRegistro($query))
        return true;
    else
        return false;
}

function embedYt($url){
	$link = explode("v=", $url);
	$link = explode("&", $link[1]);
	$url = "https://www.youtube.com/embed/".$link[0];
	return $url;
}

function getTipoNotificaciones(){
  $tipos = array("General"=>"general", "Cumpleaños"=>"cumple", "Recordatorio"=>"recordatorio");
  return $tipos;
}

function ExecuteRemoteQuery($link){
  $ch = curl_init($link);
  $headers = array();
  $headers[] = 'Content-Type: application/json';
  //$headers[] = 'Api-Key: '.api_key;

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);   
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
  $response = curl_exec($ch);
  curl_close($ch);
  return json_decode($response);
}

function mylog($aliasEmpresa, $nameFile, $texto, $title=""){
  //SERÍA GENIAL HACER FOLDERS POR MES
  $folder = url_upload."logs/".$aliasEmpresa;
  
  if (!file_exists($folder)){
      mkdir($folder, 0777);
  }
  $file = $folder."/".$nameFile.".log";
  $log = "[".fecha()."] ".$title." ".$texto;
file_put_contents($file, PHP_EOL . $log, FILE_APPEND);
}

function agregaComillasEnArray($array){
  $texto = "'".implode("','", $array)."'";
  return $texto;
}

function sumarTiempo($cantidad, $tiempo){
//   // EJEMPLOS
	  // $cantidad +5, -5, -1
	  //$tiempo => hours, minute, second
    date_default_timezone_set('America/Guayaquil');
	  $mifecha = new DateTime(); 
	  $mifecha->modify($cantidad.' '.$tiempo); 
	  return $mifecha->format('Y-m-d H:i:s');
}

function replaceUnicode($string){
  $search  = array('u00c1', 'u00e1', 'u00c9', 'u00e9', 'u00cd', 'u00ed', 'u00d3', 'u00f3', 'u00da', 'u00fa', 'u00d1', 'u00f1', 'u00bf');
  $replace = array('A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n', '');
  return str_replace($search, $replace, $string);
}

ob_end_flush();

 
