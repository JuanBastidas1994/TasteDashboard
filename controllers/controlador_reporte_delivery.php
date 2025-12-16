<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_reporte_delivery.php";
$Clempresas = new cl_empresas();
$clreporteDelivery = new cl_reporte_delivery();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getEnvios(){
    global $session;
    global $clreporteDelivery;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $tabla = "";
    $envios = $clreporteDelivery->total_delivery($sucursal, $f_inicio, $f_fin);
    if($envios){
        $totalDelivery = 0;
        foreach($envios as $envio){
            $total = number_format($envio['total'],2);
            $totalDelivery += $total;
            $tabla.='<tr>
                        <td>'.$envio['cod_usuario'].'</td>
                        <td>'.$envio['nombre'].' '.$envio['apellido'].'</td>
                        <td>'.$envio['correo'].'</td>
                        <td>'.$envio['telefono'].'</td>
                        <td>$ '.$total.'</td>
                        <td class="text-right">'.$envio['num_items'].'</td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li>
                                    <a href="javascript:void(0)" class="btnDetalle" title="Ver Detalles" 
                                            data-id="'.$envio['cod_usuario'].'"
                                            data-name="'.$envio['nombre'].'"
                                            data-total="'.$total.'"
                                            data-numitems="'.$envio['num_items'].'"
                                            data-office="'.$sucursal.'"
                                            data-start="'.$f_inicio.'"
                                            data-end="'.$f_fin.'"
                                    >
                                        <i data-feather="eye"></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>';
        }
        
        return [
            "success" => 1,
            "mensaje" => 'Información encontrada',
            "tabla" => $tabla,
            "total" => number_format($totalDelivery,2)
        ];
    }else{
        return [
            "success" => 0,
            "mensaje" => 'No hay información en este lapso de tiempo',
            "tabla" => ''
        ];
    }

}

function getDetalle(){
    global $session;
    global $clreporteDelivery;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $tabla = "";
    $envios = $clreporteDelivery->orders_motorizado($cod_motorizado, $sucursal, $f_inicio, $f_fin);
    if($envios){
        
        foreach($envios as $envio){
            $amount = number_format($envio['envio'],2);
            $total = number_format($envio['total'],2);
            $tabla.='<tr>
                        <td>'.$envio['cod_orden'].'</td>
                        <td>'.$envio['fecha'].'</td>
                        <td>$ '.$amount.'</td>
                        <td>$ '.$total.'</td>
                        <td>'.$envio['estado'].'</td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li>
                                    <a href="orden_detalle.php?id='.$envio['cod_orden'].'" target="_blank" title="Ver Detalles" 
                                    >
                                        <i data-feather="eye"></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>';
        }
        
        return [
            "success" => 1,
            "mensaje" => 'Información encontrada',
            'resp' => $envios,
            "tabla" => $tabla
        ];
    }else{
        return [
            "success" => 0,
            "mensaje" => 'No hay información en este lapso de tiempo',
            "tabla" => ''
        ];
    }

}

?>