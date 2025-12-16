<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_sucursales.php";
$Clsucursales = new cl_sucursales();
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

   
    
    for($i=0; $i<count($txt_sku); $i++){
        
        $Clsucursales->cod_producto = $txt_cod_producto[$i];
        $Clsucursales->sku = $txt_sku[$i];
        $Clsucursales->cod_sucursal = $cmb_sucursales;
        $Clsucursales->cantidad = $txt_cantidad[$i];
        
        $cod_sucursal =  $cmb_sucursales;
        $cod_producto =  $txt_cod_producto[$i];
        
        if($Clsucursales->existeStock($cod_sucursal, $txt_sku[$i])){
            if($Clsucursales->editarStock()){
                $return['success'] = 1;
                $return['mensaje'] = "Stock editado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al editar el stock";
            }
        }else{
            if($Clsucursales->insertarStock()){
                $return['success'] = 1;
                $return['mensaje'] = "Stock guardado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al guardar el stock";
            }
        }
    }
    $return['llego'] = $cod_producto;
    return $return;
}

function get(){
    global $Clsucursales;
    if(!isset($_GET['cod_sucursal'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $resp = $Clsucursales->getStockBySucursal($cod_sucursal);
    
    foreach($resp as $r){
        if($r['cantidad'] == NULL)
            $r['cantidad'] = 0;
            $readonly = "";
            $btnIrProd = "";
            if($r['sku'] == null || $r['sku'] == ""){
                $readonly = "readonly";
                $btnIrProd = '<a href="crear_productos.php?id='.$r['alias'].'" target="_blank" class="btn btn-primary">Ir</a>';
            }
        $html.= '<tr>
                    <td>'.$r['nombre'].'</td>
                    <td>'.$r['sku'].'</td>
                    <td>
                        <input type="number" class="form-control" name="txt_cantidad[]" value="'.$r['cantidad'].'" '.$readonly.'/>
                        <input type="hidden" name="txt_cod_producto[]" value="'.$r['cod_producto'].'"/>
                        <input type="hidden" name="txt_sku[]" value="'.$r['sku'].'"/>
                    </td>
                    <td style="text-align: center;">'.$btnIrProd.'</td>
                 </tr>';
                 /*<td class="text-center">
                        <ul class="table-controls">
                            <li><a href="href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit"></i></a></li>
                        </ul>
                    </td>*/
    }
    
    $array = NULL;
    if($Clsucursales->getArray($cod_sucursal, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Sucursal encontrada";
        $return['html'] = $html;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Sucursal no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function getBusquedaOrdenes(){
    // global $Clordenes;
    global $session;
    $busqueda = $_GET['busqueda'];
    $tipo_busqueda = $_GET['tipo_busqueda'];
    $cod_sucursal = $_GET['cod_sucursal'];
    $cod_empresa = $session['cod_empresa'];

    $html = "";

    if($tipo_busqueda == 1){
        $datos = getOrdenesByNumOrden($busqueda, $cod_sucursal, $cod_empresa);
    }
    else if($tipo_busqueda == 2){
        $datos = getOrdenesByCedula($busqueda, $cod_sucursal, $cod_empresa);
    }
    else if($tipo_busqueda == 3){
        $busqueda = str_replace(' ', '%', $busqueda);
        $datos = getOrdenesByNombre($busqueda, $cod_sucursal, $cod_empresa);
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "error al buscar";
    }

    if($datos){
        $html = "";
        foreach ($datos as $dat) {
            $html.= '    <tr>
                                <td>
                                    '.$dat['cod_orden'].'
                                </td>
                                <td>
                                    '.$dat['nom_cliente'].'
                                </td>
                                <td>
                                    '.$dat['fecha'].'
                                </td>
                                <td>
                                    '.$dat['estado'].'
                                </td>
                                <td>
                                    <ul class="table-controls">
                                        <li><a href="javascript:void(0);" data-value="'.$dat['cod_orden'].'" class="bs-tooltip mail-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                                    </ul>
                                </td>
                            </tr>';
        }
        
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
    }
    else{
        $html = '   <tr>
                        <td colspan="5">
                            Sin resultados
                        </td>
                    </tr>';
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }

    $return['html'] = $html;
    return $return;
}

function getOrdenesByNumOrden($busqueda, $cod_sucursal, $cod_empresa){
    $filtro = "";
    if($cod_sucursal > 0)
        $filtro = " AND oc.cod_sucurcal = $cod_sucursal";

    $query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
                FROM tb_orden_cabecera oc, tb_usuarios u
                WHERE oc.cod_usuario = u.cod_usuario
                AND oc.cod_orden LIKE '%$busqueda%'
                AND oc.cod_empresa = $cod_empresa.$filtro";
    return Conexion::buscarVariosRegistro($query);
}
function getOrdenesByCedula($busqueda, $cod_sucursal, $cod_empresa){
    $filtro = "";
    if($cod_sucursal > 0)
        $filtro = " AND oc.cod_sucurcal = $cod_sucursal";
    $query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
                FROM tb_orden_cabecera oc, tb_usuarios u
                WHERE oc.cod_usuario = u.cod_usuario
                AND u.num_documento LIKE '%$busqueda%'
                AND oc.cod_empresa = $cod_empresa.$filtro";
    return Conexion::buscarVariosRegistro($query);
}
function getOrdenesByNombre($busqueda, $cod_sucursal, $cod_empresa){
    $filtro = "";
    if($cod_sucursal > 0)
        $filtro = " AND oc.cod_sucurcal = $cod_sucursal";
    $query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
                FROM tb_orden_cabecera oc, tb_usuarios u
                WHERE oc.cod_usuario = u.cod_usuario
                AND CONCAT(u.nombre, u.apellido) LIKE '%$busqueda%'
                AND oc.cod_empresa = $cod_empresa.$filtro";
    return Conexion::buscarVariosRegistro($query);
}


?>