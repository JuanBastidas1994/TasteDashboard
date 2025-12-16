<?php
require_once "../funciones.php";
error_reporting(E_ALL);
$eliminar = $_GET['eliminar'];

$cod_empresa = $_GET['cod_empresa'];
$queryEmp = "SELECT * 
            FROM tb_empresas
            WHERE cod_empresa = $cod_empresa";
$rowEmp = Conexion::buscarRegistro($queryEmp);

$nombreEmpresa = $rowEmp['nombre'];
$aliasEmpresa = $rowEmp['alias'];

echo "Empresa: $nombreEmpresa<br>";

$files = "assets/empresas/$aliasEmpresa/";
eliminarPuntosClientes();


/*FIDELIZACION*/
function eliminarPuntosClientes(){
    global $cod_empresa;
    
    $queryProd = "SELECT *
                    FROM tb_clientes 
                    WHERE cod_empresa = $cod_empresa";
    $respProd = Conexion::buscarVariosRegistro($queryProd);
    if($respProd){
        foreach($respProd as $prod){
            $cod_cliente = $prod['cod_cliente'];
            $nom_prod = $prod['nombre'];
            echo "<br><br>CLIENTE FIDELIZACION: $cod_producto<br>";
            eliminarFidelizacionNoClientes($cod_cliente);
        }
    }
    else{
        echo "No hay clientes a eliminar";
    }
}

function eliminarFidelizacionNoClientes($cod_cliente){
    $query = "DELETE FROM tb_clientes_puntos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClientePuntos $cod_cliente eliminada <br>";
    else
        echo "ClientePuntos $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteSaldo $cod_cliente eliminada <br>";
    else
        echo "ClienteSaldo $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_cliente_dinero WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteDinero $cod_cliente eliminada <br>";
    else
        echo "ClienteDinero $cod_cliente no eliminado <br>";
}

?>