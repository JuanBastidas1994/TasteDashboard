<?php
require_once "../funciones.php";
error_reporting(E_ALL);

$cod_empresa = 121; //oahu

$queryEmp = "SELECT * 
            FROM tb_empresas
            WHERE cod_empresa = $cod_empresa";
$rowEmp = Conexion::buscarRegistro($queryEmp);

if($rowEmp) {
    $nombreEmpresa = $rowEmp['nombre'];
    $aliasEmpresa = $rowEmp['alias'];
    
    $return["empresa"] = $nombreEmpresa;
    $return['success'] = 1;
    $return['mensaje'] = "Ok";

    $query = "SELECT COUNT(*) as cantidad, cod_usuario 
                FROM tb_orden_cabecera 
                WHERE cod_empresa = $cod_empresa 
                GROUP BY cod_usuario 
                HAVING cantidad > 1";
    $usuarios = Conexion::buscarVariosRegistro($query);

    foreach ($usuarios as &$usuario) {
        $cod_usuario = $usuario["cod_usuario"];
        $query3 = "SELECT fecha
                    FROM tb_orden_cabecera
                    WHERE cod_usuario = $cod_usuario
                    ORDER BY cod_orden
                    LIMIT 1";
        $orden = Conexion::buscarRegistro($query3);
        $fecha = $orden["fecha"];
        $usuario["fecha_creacion_nueva"] = $orden["fecha"];

        $usuario["actualizado"] = false;
        $queryUp = "UPDATE tb_usuarios 
                    SET fecha_create = '$fecha'
                    WHERE cod_usuario = $cod_usuario";
        /* if(Conexion::ejecutar($queryUp, null)) {
            $usuario["actualizado"] = true;
        } */
    }
    $return["usuarios"] = $usuarios;
}
else {
    $return['success'] = 0;
    $return['mensaje'] = "Empresa no existe";
}

header("Content-Type: application/json");
echo json_encode($return);
?>