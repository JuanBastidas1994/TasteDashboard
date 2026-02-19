<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
$Clempresas = new cl_empresas();
$session = getSession();
error_reporting(E_ALL);


controller_create();

function getLocationsOrders(){
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true);

    $officeId = $input['office_id'] ?? 0;
    $dateStart = $input['dateStart'] ?? 0;
    $dateEnd = $input['dateEnd'] ?? 0;;

    global $session;
    $empresa = $session['cod_empresa'];

    $sql = "
        SELECT latitud, longitud
        FROM tb_orden_cabecera
        WHERE cod_empresa = :empresa
        AND estado = 'ENTREGADA'
        AND is_envio = 1
        AND fecha BETWEEN :start AND :end
    ";

    $params = [
        ':empresa' => $empresa,
        ':start' => $dateStart,
        ':end' => $dateEnd
    ];

    if ($officeId > 0) {
        $sql .= " AND cod_sucursal = :officeId ";
        $params[':officeId'] = $officeId;
    }

    $data = Conexion::buscarVariosRegistro($sql, $params);
    return $data;
}


?>