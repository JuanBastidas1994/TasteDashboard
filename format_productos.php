<?php
require_once "funciones.php";
require_once "clases/cl_productos.php";
$Clproductos = new cl_productos();
global $Clproductos;

$cod_empresa = $_GET['cod_empresa'];
$sucursales = array();
$query = "SELECT * FROM tb_sucursales WHERE estado IN('A') AND cod_empresa = ".$cod_empresa;
$resp = Conexion::buscarVariosRegistro($query);
foreach ($resp as $l) {
    $sucursales[]= $l['cod_sucursal'];
}


for($x=0; $x<count($sucursales); $x++){
        $precioReplace=0; 
        $cod_sucursal = $sucursales[$x];
        $precio =0;
        $precio_anterior = 0;
        $estado = 'A';
         $lista = $Clproductos->GetProductosbyEmpresaFormat($cod_empresa);
         foreach ($lista as $l) {
                if($Clproductos->getdisponibilidad($l['cod_producto'], $cod_sucursal)){
                }else
                {
                   $Clproductos->setProductSucursal($l['cod_producto'],$cod_sucursal);
                }
			}
     //  $respuesta[$x]=$r;
    }
  //  $return = $respuesta;
 
 
header("Content-type:application/json");
echo json_encode($return);
    
?>