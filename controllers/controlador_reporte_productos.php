<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";


$Clempresas = new cl_empresas();

$session = getSession();
error_reporting(E_ALL);

controller_create();

function getProductosMasVendidos(){
    global $session;
    
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $tabla = "";
    
    require_once "../clases/cl_reporte_productos.php";
    $clreportProductos = new cl_reporte_productos();
    $productos = $clreportProductos->getReporteProductosMasVendido($cod_empresa, $cod_sucursal, $fechaInicio, $fechaFin);

    if($productos){
        foreach($productos as $producto){
            $tabla.='<tr>
                        <td>'.$producto['nombre'].'</td>
                        <td class="text-right">'.$producto['cantidad'].'</td>
                    </tr>';
        }
        
        return [
            "success" => 1,
            "mensaje" => 'Información encontrada',
            "query"=> $productos,
            "tabla" => $tabla,
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