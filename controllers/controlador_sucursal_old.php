<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_productos.php";

$Clsucursales = new cl_sucursales();
$Clproductos = new cl_productos();
$session = getSession();

controller_create();

function crear(){
    global $Clsucursales;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $nameImg = 'sucursal_'.datetime_format().'.jpg';
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $Clsucursales->nombre = $txt_nombre;
    $Clsucursales->direccion = $txt_direccion;
    $Clsucursales->hora_ini = $hora_ini;
    $Clsucursales->hora_fin = $hora_fin;
    $Clsucursales->emisor = $txt_emisor;
    $Clsucursales->latitud = $txt_latitud;
    $Clsucursales->longitud = $txt_longitud;
    $Clsucursales->distancia_km = $txt_cobertura;
    $Clsucursales->telefono = "";
    $Clsucursales->correo = "";
    
    $Clsucursales->imagen= $nameImg;
    
    if(isset($_POST['chkEstadoSuc']))
        $Clsucursales->estado = 'A';
    else
        $Clsucursales->estado = 'I';
        
    if(isset($_POST['cmbCiudades']))
        $Clsucursales->cod_ciudad = $_POST['cmbCiudades'];
    else
        $Clsucursales->cod_ciudad = 59; // Guayaquil    

    $pasar = false;
    if(!isset($_POST['cod_sucursal'])){
        $id=0;
        if($Clsucursales->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Sucursal guardada correctamente";
            $return['id'] = $id;
            $return['data'] = "";
            $cod_sucursal = $id;
            $Clsucursales->getArray($id, $return['data']);
            $return['imagen'] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$nameImg;
            
             /*SUBIR IMAGEN*/
            if($txt_crop != ""){
                base64ToImage($txt_crop, $nameImg);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                copy($img1, $img2);
            }
            
            /*--NUEVO--*/
            /*ASIGNAR PRODUCTOS*/
            global $Clproductos;
            $lista = $Clproductos->GetProductosbyEmpresa();
            foreach ($lista as $l) {
                $return['match']= $Clproductos->setProductSucursal($l['cod_producto'],$id);
			}
            
            $pasar = true;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }else{
        $Clsucursales->cod_sucursal = $cod_sucursal;
        if($Clsucursales->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Sucursal editada correctamente";
            $return['id'] = $Clsucursales->cod_sucursal;
            $return['data'] = "";
            if($Clsucursales->getArray($Clsucursales->cod_sucursal, $data))
            {
                //uploadFile($_FILES["img_product"], $data['image_min']);
                if($txt_crop != ""){
                    base64ToImage($txt_crop, $data['image']);
                }
                $return['imagen'] = "editada";
                 $return['nom'] = $data['image'];
            }
            $pasar = true;
            /*--NUEVO--*/
            
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }
    
    /*--NUEVO--*/
    if($pasar && isset($txtdia))
    {
        if($Clsucursales->eliminar_disponibilidadSucursal($cod_sucursal))
        for ($i=0; $i < count($txtdia); $i++) {
            if ($selectDia[$i]== 1)
            {
                $Clsucursales->crear_disponibilidadSucursal($cod_sucursal, $txtdia[$i], $hora_iniD[$i], $hora_finD[$i]);
            }
        }
    }
    return $return;
}

/*--NUEVO--*/
function editProductoSucursal(){
    global $Clproductos;
    if(!isset($_POST['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion".$_POST['cod_sucursal'];
        return $return;
    }

    extract($_POST);

    /*EDITAR PRODUCTOS*/
    for($x=0; $x<count($txt_producto); $x++){
        $precioReplace=$precioR[$x]; 
        $cod_producto = $txt_producto[$x];
        $precio = $txt_precio_sucursal[$x];
        $precio_anterior = $txt_precio_anterior_sucursal[$x];
        if($select[$x]==1)
            $estado = 'A';
        else
            $estado = 'I';
            
       $Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado,$precioReplace);
     //   $resultado.=($cod_producto."---".$cod_sucursal."---". $precio."---". $precio_anterior."---". $estado."---".$precioReplace."--".count($txt_producto));
    }
    //$return['data']=$resultado;
    $return['success'] = 1;
    $return['mensaje'] = "Productos editados correctamente";
    return $return;
}
/*--NUEVO--*/

function get(){
    global $Clsucursales;
    if(!isset($_GET['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clsucursales->getArray($cod_sucursal, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal encontrada";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Sucursal no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clsucursales;
	if(!isset($_GET['cod_sucursal']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clsucursales->set_estado($cod_sucursal, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Sucursal editada correctamente";
        if($estado == "D")
            $return['mensaje'] = "Sucursal eliminada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la sucursal";
    }
    return $return;
}

/*CREAR DISPONIBILIDAD*/
function crear_disponibilidad(){
    global $Clsucursales;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    for ($j=0; $j < count($cmb_sucursales); $j++) { 
        $cod_sucursal = $cmb_sucursales[$j];
        if($Clsucursales->crear_disponibilidad($cod_sucursal, $fecha_inicio, $hora_ini, $hora_fin)){
            $return['success'] = 1;
            $return['mensaje'] = "Promocion creada correctamente";
            //$return['id'] = $id;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la promocion, por favor vuelva a intentarlo";
        }

    }
    $return['success'] = 1;
    $return['mensaje'] = "Promociones creadas correctamente";
    return $return;
}

function eliminar_disponibilidad(){
    global $Clsucursales;
    if(!isset($_GET['cod_sucursal_disponibilidad'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $Clsucursales->eliminar_disponibilidad($cod_sucursal_disponibilidad);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Disponibilidad eliminada correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar la disponibilidad";
    }
    return $return;
}

function ciudades(){

global $Clsucursales;   
    if(count($_POST)==0){
		$return['success'] = 0;
    	$return['mensaje'] = "Falta informacion";
		return $return;
	}
	extract($_POST);
    $resp=$Clsucursales->getCiudades($provincia);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Correcto";
    	$ciudades ="";
    	foreach($resp as $ciud)
        {
            $ciudades .= '<option value='.$ciud['cod_ciudad'].'>'.$ciud['nombre'].'</option>';
        }
    	$return['ciudadesHtml'] = $ciudades;
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "No hay ciudades en esta provincia";
    }
    return $return;
    
}
?>