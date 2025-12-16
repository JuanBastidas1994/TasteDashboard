<?php
require_once "../funciones.php";
error_reporting(E_ALL);

$cod_empresa = $_GET['cod_empresa'];
$page = $_GET['page'];

$queryEmp = "SELECT * 
            FROM tb_empresas
            WHERE cod_empresa = $cod_empresa";
$rowEmp = Conexion::buscarRegistro($queryEmp);

if($rowEmp) {
    $nombreEmpresa = $rowEmp['nombre'];
    $aliasEmpresa = $rowEmp['alias'];
    
    $return["empresa"] = $nombreEmpresa;
    
    $files = "assets/empresas/$aliasEmpresa/";

    $return["data"]["usuarios"] = getUsuariosVacios();

    $return['success'] = 1;
    $return['mensaje'] = "Ok";
}
else {
    $return['success'] = 0;
    $return['mensaje'] = "Empresa no existe";
}

function getUsuariosVacios() {
    global $cod_empresa;
    global $page;
    
    $query = "SELECT *
                FROM tb_usuarios 
                WHERE cod_empresa = $cod_empresa
                AND nombre <> ''
                LIMIT $page, 200";
    $usuarios = Conexion::buscarVariosRegistro($query);
    if($usuarios) {
        foreach ($usuarios as $usuario) {
            $return["clientes"][] = getCliente($usuario["cod_usuario"]);
        }
    }
    else {
        $return["mensaje"] = "No hay usuarios con nombre vacío";
    }
    return $return;
}

function getCliente($cod_usuario) {
    $query = "SELECT cod_cliente
                FROM tb_clientes
                WHERE cod_usuario = $cod_usuario";
    $cliente = Conexion::buscarRegistro($query);
    if($cliente) {
        $cod_cliente = $cliente["cod_cliente"];
        $return["id"] = $cod_cliente;
        $return["cliente_dinero"] = "Eliminado";
        $return["cliente_puntos"] = "Eliminado";
        $return["cliente_saldos"] = "Eliminado";
        $return["cliente"] = "Eliminado";


        /* $query = "DELETE FROM tb_cliente_dinero WHERE cod_cliente = $cod_cliente";
        if(Conexion::ejecutar($query, null)) {
            $return["cliente_dinero"] = "Eliminado";
        }

        $query = "DELETE FROM tb_clientes_puntos WHERE cod_cliente = $cod_cliente";
        if(Conexion::ejecutar($query, null)) {
            $return["cliente_puntos"] = "Eliminado";
        }
        $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $cod_cliente";
        if(Conexion::ejecutar($query, null)) {
            $return["cliente_saldos"] = "Eliminado";
        }
        $query = "DELETE FROM tb_clientes WHERE cod_cliente = $cod_cliente";
        if(Conexion::ejecutar($query, null)) {
            $return["cliente"] = "Eliminado";
        } */
    }
    else {
        $return["mensaje"] = "No hay clientes";
    }

    return $return;
}

header("Content-Type: application/json");
echo json_encode($return);
?>