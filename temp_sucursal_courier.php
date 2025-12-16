<?php
    require_once "funciones.php";
    //echo fecha_only()."<br>";

    $gacela = "SELECT DISTINCT cod_sucursal 
                FROM tb_laar_sucursal 
                ORDER BY cod_sucursal ASC";
    $resp = Conexion::buscarVariosRegistro($gacela);
    foreach ($resp as $gc) {
        //echo $gc['cod_sucursal'].'<br>';
        $cod_sucursal = $gc['cod_sucursal'];
        $query2 = "INSERT INTO tb_sucursal_courier
                    SET cod_sucursal = $cod_sucursal, cod_courier = 2, estado = 'A', prioridad = 1";
        echo $query2."<br>";
        // if(Conexion::ejecutar($query2, null))
        //     echo "Agregado $cod_sucursal<br>";
        // else
        //     echo "no Agregado $cod_sucursal <br>";
    }
?>