<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";


$Clempresas = new cl_empresas();

$session = getSession();
error_reporting(E_ALL);

controller_create();

function getEnvios(){
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $tabla = "";
    
    require_once "../clases/cl_reporte_couriers.php";
    $clreporteCouriers = new cl_reporte_couriers();
    $envios = $clreporteCouriers->orderCouriers($cod_sucursal, $cod_courier, $fechaInicio, $fechaFin);
    if($envios){
        $reporte = $clreporteCouriers->getReport($cod_sucursal, $fechaInicio." 00:00:00", $fechaFin." 23:59:59");
        $totalCouriers = 0;
        foreach($envios as $envio){
            $total = number_format($envio['envio'],2);
            $totalCouriers += $total;
            $tabla.='<tr>
                        <td>'.$envio['cod_orden'].'</td>
                        <td>'.$envio['fecha'].'</td>
                        <td>'.$envio['envio'].'</td>
                        <td>'.$envio['estado'].'</td>
                        <td>'.$envio['courier'].'</td>
                        <td class="text-center">
                            <ul class="table-controls">
                             <li><a href="orden_detalle.php?id='.$envio['cod_orden'].'" title="Ver orden"><i data-feather="eye"></i></a></li>
                            </ul>
                        </td>
                    </tr>';
        }
        
        return [
            "success" => 1,
            "mensaje" => 'Información encontrada',
            "query"=> $envios,
            "tabla" => $tabla,
            "total" => number_format($totalCouriers,2),
            "compendio" => $reporte,
        ];
    }else{
        return [
            "success" => 0,
            "mensaje" => 'No hay información en este lapso de tiempo',
            "tabla" => ''
        ];
    }

}

function getOrdenesFlota(){
    global $session;
    
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $tabla = "";
    
    $resumen = [];
    require_once "../clases/cl_reporte_flotas.php";
    $clreportFlotas = new cl_reporte_flotas();
    $envios = $clreportFlotas->orderFlotas($cod_sucursal, $cod_flota, $fechaInicio, $fechaFin);
    if($envios){
        $totalCouriers = 0;
        foreach($envios as $envio){
            $total = number_format($envio['envio'],2);
            $totalCouriers += $total;
            $tabla.='<tr>
                        <td>'.$envio['cod_orden'].'</td>
                        <td>'.$envio['sucursal'].'</td>
                        <td>'.$envio['fecha'].'</td>
                        <td>'.$envio['distancia'].'km</td>
                        <td class="text-right">$'.$envio['envio'].'</td>
                        <td class="text-right">$'.$envio['total'].'</td>
                        <td class="text-right">'.$envio['pagos'].'</td>
                        <td>'.$envio['estado'].'</td>
                        <td class="text-center">
                            <ul class="table-controls">
                             <li><a href="orden_detalle.php?id='.$envio['cod_orden'].'" target="_blank" title="Ver orden"><i data-feather="eye"></i></a></li>
                            </ul>
                        </td>
                    </tr>';
        }
        $resumen = $clreportFlotas->resumenPagosFlota($cod_sucursal, $cod_flota, $fechaInicio, $fechaFin);
        
        return [
            "success" => 1,
            "mensaje" => 'Información encontrada',
            "query"=> $envios,
            "tabla" => $tabla,
            "total" => number_format($totalCouriers,2),
            "resumen" => $resumen
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