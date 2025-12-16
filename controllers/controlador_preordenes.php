<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_usuarios.php";
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();

error_reporting(E_ALL);

controller_create();

function datatable(){
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $event_id = (isset($_GET['event_id'])) ? $_GET['event_id'] : 0;

    $table ="(SELECT pj.*, u.nombre
                FROM tb_preorden_json pj, tb_usuarios u
                WHERE pj.cod_usuario = u.cod_usuario
                AND u.cod_empresa = $cod_empresa
                AND pj.estado IN('CREANDO_ORDEN', 'VALIDADA', 'CERRADA')
                ORDER BY pj.fecha_update DESC) temp";
    
    $primaryKey = 'cod_usuario';
    $x=0;
    $columns = array(
        array( 'dt' => $x++, 'db' => 'cod_preorden'),
        array( 'dt' => $x++, 'db' => 'nombre'),
        array( 'dt' => $x++, 'db' => 'cod_orden'),
        array( 'dt' => $x++, 'db' => 'amount'),
        array( 'dt' => $x++, 'db' => 'motivo_fallo'),
        array( 'dt' => $x++, 'db' => 'paymentId'),
        array( 'dt' => $x++, 'db' => 'fecha_create'),
        array( 'dt' => $x++, 'db' => 'fecha_update'),
        array( 'dt' => $x++, 'db' => 'estado'),
        array( 'dt' => $x++, 'db' => 'json', 
            'formatter' => function($d, $row){
                $json = $row["json"];
                return '
                    <ul class="table-controls">
                        <li>
                            <a class="text-success bs-tooltip btnOpenPreorden" href="javascript:void(0);" data-preorden=\''.$json.'\' data-toggle="tooltip" data-placement="top" data-original-title="Copiar">
                                <i data-feather="eye"></i>
                            </a>
                        </li>
                        <li>
                            <a class="copy text-success bs-tooltip" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-text=\''.$json.'\' data-toggle="tooltip" data-placement="top" data-original-title="Copiar">
                                <i data-feather="copy"></i>
                            </a>
                        </li>
                        <li>
                            <a class="btnCrearOrden text-success bs-tooltip" href="javascript:void(0);" data-orden=\''.$json.'\' data-toggle="tooltip" data-placement="top" data-original-title="Crear orden" data-preorden="'.$row["cod_preorden"].'">
                                <i data-feather="check"></i>
                            </a>
                        </li>
                    </ul>
                ';
            }
        )
    );

    $sql_details = array(
        'type'=> 'mysql',
        'user' => usuario,
        'pass' => contrasena,
        'db'   => db,
        'host' => servidor
    );
    require( '../plugins/table/datatable/ssp.class.php' );
    return SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);
}


function actualizarEstadoPreorden() {
    $POST = json_decode(file_get_contents("php://input"), true);

    if(!isset($POST["cod_preorden"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Error al actualizar la preorden - falta ID de preorden";
        return $return;
    }
    
    if(!isset($POST["cod_orden"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Error al actualizar la preorden - falta ID de orden";
        return $return;
    }
    
    if(!isset($POST["estado"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Error al actualizar la preorden - falta estado de preorden";
        return $return;
    }

    $cod_preorden = $POST["cod_preorden"];
    $cod_orden = $POST["cod_orden"];
    $estado = $POST["estado"];

    $query = "UPDATE tb_preorden_json SET cod_orden = '$cod_orden', estado = '$estado'  WHERE cod_preorden = '$cod_preorden'";
    $resp = Conexion::ejecutar($query, null);
    if($resp) {
        $return["success"] = 1;
        $return["mensaje"] = "Preorden actualizada";
        return $return;
    }

    $return["success"] = 0;
    $return["mensaje"] = "Error al actualizar la preorden";
    return $return;
}

?>