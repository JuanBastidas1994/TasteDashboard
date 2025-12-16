<?php
    include "./funciones.php";
    $query = "SELECT * FROM tb_empresa_clickup
                GROUP BY cod_empresa";
    $resp = Conexion::buscarVariosRegistro($query);
    echo count($resp)."<br><br>";
    foreach ($resp as $r) {
        echo $r['cod_empresa']."<br>";
        $cod_empresa = $r['cod_empresa'];
        $fecha_create = $r['fecha_registro'];
        if(null == $fecha_create)
            $fecha_create = fecha();
        $query = "INSERT INTO tb_empresa_progresos(cod_empresa, titulo, porcentaje, fecha_create)
                    VALUES( $cod_empresa, 'Clickup integrado', 10, '$fecha_create')";
        //Conexion::ejecutar($query, null);
    }
?>