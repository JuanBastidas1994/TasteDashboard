<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_web_modulos.php";
$ClWebModulos = new cl_web_modulos();
$session = getSession();
$variaciones = null;

controller_create();

function lista(){
    global $ClWebModulos;
    global $session;
    if(!isset($_GET['cod_modulo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $htmlAgotados = "";
    $respAgotados = $ClWebModulos->listaByModulo($cod_modulo);
    if(!$respAgotados)
        $htmlAgotados = '<tr><td colspan="4">No hay registros</td></tr>';
    foreach ($respAgotados as $productos) {
        $imagen = $files.$productos['image_min'];
        $badge='primary';
        if($productos['estado'] == 'I')
            $badge='danger';
        $htmlAgotados .= '<tr data-id="'.$productos['cod_producto'].'">
            <td class="text-center">
                <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
            </td>
            <td>'.$productos['nombre'].'</td>
            <td>$'.number_format($productos['precio'],2).'</td>
            <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($productos['estado']).'</span></td>
        </tr>';
    }

    $return['success'] = 1;
    $return['mensaje'] = "Info";
    $return['agotados'] = $htmlAgotados;
    return $return;
}

function actualizar(){
    global $ClWebModulos;
    global $session;
    if(!isset($_POST['cod_modulo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $delete = $ClWebModulos->deleteByModulo($cod_modulo);
    if($delete){
        for ($i=0; $i < count($productos); $i++) { 
            $ClWebModulos->addDetalleInModulo($cod_modulo, $productos[$i], $i+1);
        }
    }
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    $return['delete'] = $delete;
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
      $return['success'] = 1;
      $return['mensaje'] = "Producto fuera de venta durante $minutos minutos";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al pausar la venta del producto, por favor vuelta a intentarlo";
    }
    return $return;
}
?>