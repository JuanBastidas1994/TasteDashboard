<?php
require_once "../funciones.php";
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_clientes.php";

require_once "SimpleXLSXGen.php";
use Shuchkin\SimpleXLSXGen;


$Clusuarios = new cl_usuarios(NULL);
$Clclientes = new cl_clientes(NULL);

$session = getSession();

// Traer clientes
$query = "SELECT * FROM tb_usuarios WHERE estado IN('A','I') AND cod_rol = 4 AND cod_empresa = ".$session['cod_empresa'];
$clientes = Conexion::buscarVariosRegistro($query);

// Construir matriz para XLSX
$data = [];
$data[] = ["ID", "Nombre", "DNI", "Correo", "Teléfono", "Fecha Creación", "Estado"]; // encabezados

foreach ($clientes as $c) {
    $estado = ($c['estado'] == 'A') ? 'Activo' : 'Inactivo';
    $fecha_create = fechaLatino($c['fecha_create']);

    $data[] = [
        $c['cod_usuario'],
        $c['nombre'],
        $c['num_documento'],
        $c['correo'],
        $c['telefono'],
        $fecha_create,
        $estado
    ];
}

// Crear xlsx
$xlsx = SimpleXLSXGen::fromArray($data);

// Descargar
$xlsx->downloadAs('clientes_'.date('Ymd_His').'.xlsx');