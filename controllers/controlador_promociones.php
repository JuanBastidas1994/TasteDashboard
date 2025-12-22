<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_promociones.php";
$Clpromociones = new cl_promociones(NULL);
$session = getSession();

controller_create();

function crear(){
    global $Clpromociones;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    if (!fechaValida(!$fecha_inicio)) {
        return [ 'success' => 0, 'mensaje' => 'Fecha de inicio es incorrecta' ];
    }
    if (!fechaValida(!$fecha_fin)) {
        return [ 'success' => 0, 'mensaje' => 'Fecha Fin es incorrecta' ];
    }

    for ($i=0; $i < count($cmb_productos); $i++) { 
        $cod_producto = $cmb_productos[$i];
        for ($j=0; $j < count($cmb_sucursales); $j++) { 
            $cod_sucursal = $cmb_sucursales[$j];

            $Clpromociones->cod_producto = $cod_producto;
            $Clpromociones->cod_sucursal = $cod_sucursal;
            $Clpromociones->fecha_inicio = $fecha_inicio;
            $Clpromociones->fecha_fin = $fecha_fin;
            $Clpromociones->is_porcentaje = $rb_tipo_descuento;
            $Clpromociones->valor = $txt_valor;
            if($rb_tipo_descuento == 1){
                $Clpromociones->cantidad = 0;
                $Clpromociones->texto = $txt_valor."%";
            }else{
                $Clpromociones->cantidad = $txt_cantidad;
                $Clpromociones->texto = $txt_cantidad."x".(intval($txt_cantidad)-1);
            }
                
            $id = 0;
            if($Clpromociones->crear($id)){
                $return['success'] = 1;
                $return['mensaje'] = "Promocion creado correctamente";
                $return['id'] = $id;
            }else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al crear la promocion, por favor vuelva a intentarlo";
            }

        }
    }
    $return['success'] = 1;
    $return['mensaje'] = "Promociones creadas correctamente";
    return $return;
}

function get(){
    global $session;
    global $Clpromociones;
    if(!isset($_GET['cod_promocion'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clpromociones->getArray($cod_promocion, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Promocion encontrada";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Promocion no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clpromociones;
	if(!isset($_GET['cod_promocion']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clpromociones->set_estado($cod_promocion, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Promocion editada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la promocion";
    }
    return $return;
}


?>