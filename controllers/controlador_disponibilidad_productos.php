<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos.php";
$Clproductos = new cl_productos();
$session = getSession();
$cod_usuario = $session['cod_usuario'];
$variaciones = null;

controller_create();

function lista(){
    global $Clproductos;
    global $session;
    if(!isset($_GET['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $htmlDisponibles = "";
    $resp = $Clproductos->listaBySucursal($cod_sucursal);
    if(!$resp)
        $htmlDisponibles = '<tr><td colspan="5">No hay registros</td></tr>';
    foreach ($resp as $productos) {
        $imagen = $files.$productos['image_min'];
        $badge='primary';
        if($productos['estado'] == 'I')
            $badge='danger';
        $htmlDisponibles .= '<tr>
            <td class="text-center">
                <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
            </td>
            <td>'.$productos['nombre'].'</td>
            <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($productos['estado']).'</span></td>
            <td class="text-center">
                <div class="dropdown  custom-dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="more-horizontal"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink-2">
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="30" data-producto="'.$productos['cod_producto'].'">Desactivar 30 minutos</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="60" data-producto="'.$productos['cod_producto'].'">Desactivar 1 hora</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="90" data-producto="'.$productos['cod_producto'].'">Desactivar 1 hora  y media</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="120" data-producto="'.$productos['cod_producto'].'">Desactivar 2 horas</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="150" data-producto="'.$productos['cod_producto'].'">Desactivar 2 horas y media</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="1440" data-producto="'.$productos['cod_producto'].'">Desactivar 1 día</a>
                        <a class="dropdown-item productoAgotar" href="javascript:void(0);" data-minutes="525600" data-producto="'.$productos['cod_producto'].'">Desactivar indefinidamente</a>
                    </div>
                </div>
            </td>
        </tr>';
    }

    $htmlAgotados = "";
    $fecha = fecha();
    $respAgotados = $Clproductos->lista_agotados($cod_sucursal, $fecha);
    if(!$respAgotados)
        $htmlAgotados = '<tr><td colspan="4">No hay registros</td></tr>';
    foreach ($respAgotados as $productos) {
        
        $imagen = $files.$productos['image_min'];
        $badge='primary';
        $htmlAgotados .= '<tr>
            <td class="text-center">
                <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
            </td>
            <td>'.$productos['nombre'].'</td>
            <td class="text-center">Faltan '.format_time_remaining($productos['tiempo_restante']).'</td>
            <td class="text-center">
                <a class="deleteProductoAgotar" href="#" role="button" data-producto="'.$productos['cod_producto'].'">
                    <i data-feather="x"></i>
                </a>
            </td>
        </tr>';
    }

    $return['success'] = 1;
    $return['mensaje'] = "Info";
    $return['disponibles'] = $htmlDisponibles;
    $return['agotados'] = $htmlAgotados;
    return $return;
}

//DISPONIBILIDAD
function setDisponibilidad(){
    global $Clproductos;
    
    extract($_POST);

    for($x=0; $x<count($id); $x++){
        $cod_sucursal = $id[$x];
        $precio = $txt_precio_sucursal[$x];
        $precio_anterior = $txt_precio_anterior_sucursal[$x];
        if($select[$x]==1)
            $estado = 'A';
        else
            $estado = 'I';
        $Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado);
    }
    $return['success'] = 1;
    $return['mensaje'] = "Disponibilidad actualizada";
    return $return;
}

function setAgotado(){
    global $Clproductos;
    global $cod_usuario;
    
    
    extract($_POST);

    if(!$Clproductos->getdisponibilidad($cod_producto, $cod_sucursal)){
        $val=null;
        $pro = $Clproductos->getArray($cod_producto, $val);
        if($pro){
            if(!$Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $pro['precio'], $pro['precio_anterior'], 'A')){
                $return['success'] = 0;
                $return['mensaje'] = "Este producto no esta asigando para esta sucursal, por favor consultar con el Super administrador";
                return $return;
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Este producto no esta asigando para esta sucursal, por favor consultar con el Super administrador";
            return $return;
        }
        
    }

    $fecha = fecha();
    $fecha_fin = strtotime('+'.$minutos.' minute',strtotime($fecha));
    $fecha_fin = date("Y-m-d H:i:s", $fecha_fin);

    $resp = $Clproductos->setAgotado($cod_producto, $cod_sucursal, $fecha, $fecha_fin);
    if($resp){
        $Clproductos->addAgotadoHistorial($cod_producto, $cod_sucursal, $cod_usuario, 'AGOTAR', $minutos, $fecha, $fecha_fin);
        $return['success'] = 1;
        $return['mensaje'] = "Producto fuera de venta durante $minutos minutos";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al pausar la venta del producto, por favor vuelta a intentarlo";
    }
    return $return;
}


function deleteAgotado(){
    global $Clproductos;
    global $cod_usuario;
    
    extract($_POST);
    $resp = $Clproductos->setAgotado($cod_producto, $cod_sucursal, "", "");
    if($resp){
        $Clproductos->addAgotadoHistorial($cod_producto, $cod_sucursal, $cod_usuario, 'DEVOLVER', 0, fecha());
      $return['success'] = 1;
      $return['mensaje'] = "Producto de nuevo en venta";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al regresar a la venta el producto, por favor vuelta a intentarlo";
    }
    return $return;
}


?>