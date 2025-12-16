<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_usuarios.php";
require_once "../clases/cl_clientes.php";
$Clusuarios = new cl_usuarios(NULL);
$Clclientes = new cl_clientes(NULL);
$session = getSession();

error_reporting(E_ALL);

controller_create();

function datatable(){
    global $session;
    $cod_empresa=$session['cod_empresa'];
    $event_id = (isset($_GET['event_id'])) ? $_GET['event_id'] : 0;

    $table ="(SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol = 4 AND u.cod_empresa = $cod_empresa ORDER BY fecha_create DESC) temp";
    
    $primaryKey = 'cod_usuario';
    $columns = array(
        array( 'dt' => 0, 'db' => 'nombre'),
        array( 'dt' => 1, 'db' => 'num_documento'),
        array( 'dt' => 2, 'db' => 'correo'),
        array( 'dt' => 3, 'db' => 'telefono'),
        array( 'dt' => 4, 'db' => 'fecha_nacimiento'),
        array( 'dt' => 5, 'db' => 'fecha_create'),
        array( 'dt' => 6, 'db' => 'estado'),
        array( 'dt' => 7, 'db' => 'cod_usuario',
            'formatter' => function($d, $row){
                return '<ul class="table-controls">
                    <li><a href="cliente_detalle.php?id='.$row['cod_usuario'].'" title="Ver m&aacute;s informaci&oacute;n"><i data-feather="eye"></i></a></li>
                    <li><a href="javascript:void(0);" data-value="'.$row['cod_usuario'].'" title="Eliminar"><i data-feather="trash"></i></a></li>
                </ul>';
            }
        ),
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

function fidelizacion() {
    global $Clclientes;
    $return = [];
    
    if(!isset($_GET["cod_usuario"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Falta el ID de cliente";
        return $return;
    }

    extract($_GET);

    $cliente = $Clclientes->getByCodUsuario($cod_usuario);
    if(!$cliente) {
        $return["success"] = 0;
        $return["mensaje"] = "Cliente no existe";
        return $return;
    }

    $Clclientes->cod_cliente = $cliente["cod_cliente"];
    $Clclientes->cod_nivel = $cliente["cod_nivel"];
    
    // OBTENER NIVEL ACTUAL
    $cliente["nivel"] = $Clclientes->getDataNivelByPosicion();
    
    $cliente["puntos"] = $Clclientes->historicoPuntos($cliente["cod_cliente"]);
    $cliente["dinero"] = $Clclientes->historicoDinero($cliente["cod_cliente"]);
    $cliente["puntos_actuales"] = $Clclientes->GetPuntos();
    $cliente["dinero_actual"] = $Clclientes->GetDinero();
    $cliente["total_ordenes"] = $Clclientes->historicoOrdenesDinero($cliente["cod_usuario"]);
    $cliente["total_credito_utilizado"] = $Clclientes->historicoCreditoUtilizado($cliente["cod_usuario"]);
    $cliente["total_dinero_caducado"] = $Clclientes->historicoDineroCaducado($cliente["cod_cliente"]);
    $cliente["total_puntos_caducados"] = $Clclientes->historicoPuntosCaducados($cliente["cod_cliente"]);
    
    $creditoActivo = $Clclientes->creditoActivo($cliente["cod_usuario"]);
    $creditoCaducado = $Clclientes->creditoCaducado($cliente["cod_usuario"]);
    $dineroCaducado = $Clclientes->dineroCaducado($cliente["cod_usuario"]);

    $data["cliente"] = $cliente;
    $data["puntos_activos"] = $creditoActivo;
    $data["puntos_caducados"] = $creditoCaducado;
    $data["dinero_caducado"] = $dineroCaducado;

    $return["success"] = 1;
    $return["mensaje"] = "Reporte";
    $return["data"] = $data;
    return $return;
}

function fidelizacionMaga() {
    global $Clclientes;
    $return = [];
    
    if(!isset($_GET["cod_usuario"])) {
        $return["success"] = 0;
        $return["mensaje"] = "Falta el ID de cliente";
        return $return;
    }

    extract($_GET);

    $cliente = $Clclientes->getByCodUsuario($cod_usuario);
    if(!$cliente) {
        $return["success"] = 0;
        $return["mensaje"] = "Cliente no existe";
        return $return;
    }

    $Clclientes->cod_cliente = $cliente["cod_cliente"];
    $Clclientes->cod_nivel = $cliente["cod_nivel"];
    
    // OBTENER NIVEL ACTUAL
    $cliente["nivel"] = $Clclientes->getDataNivelByPosicion();
    
    $cliente["puntos"] = $Clclientes->historicoPuntos($cliente["cod_cliente"]);
    $cliente["dinero"] = $Clclientes->historicoDinero($cliente["cod_cliente"]);
    $cliente["puntos_actuales"] = $Clclientes->GetPuntos();
    $cliente["dinero_actual"] = $Clclientes->GetDinero();
    $cliente["total_ordenes"] = $Clclientes->historicoOrdenesDinero($cliente["cod_usuario"]);
    $cliente["total_credito_utilizado"] = $Clclientes->historicoCreditoUtilizado($cliente["cod_usuario"]);
    $cliente["total_dinero_caducado"] = $Clclientes->historicoDineroCaducado($cliente["cod_cliente"]);
    $cliente["total_puntos_caducados"] = $Clclientes->historicoPuntosCaducados($cliente["cod_cliente"]);
    
    $creditoActivo = $Clclientes->creditoActivoMaga($cliente["cod_usuario"]);
    $creditoCaducado = $Clclientes->creditoCaducado($cliente["cod_usuario"]);
    $dineroCaducado = $Clclientes->dineroCaducado($cliente["cod_usuario"]);

    /*DESGLOSE MAGA*/
    foreach ($creditoActivo as &$credito) {
        $getDineroRegistrado = $Clclientes->getDineroRegistrado($credito["cod_orden"]);
        $credito["dinero_ganado"] = $getDineroRegistrado["ganado"];
        $credito["saldo"] = $getDineroRegistrado["saldo"];
        $credito["dinero_ganado_status"] = "ENTREGADA";
        if($getDineroRegistrado["saldo"] > 0 && $credito["dinero_status"] == "EXPIRADO") {
            $credito["dinero_ganado_status"] = "EXPIRADO";
        }
        else if($getDineroRegistrado["saldo"] == 0) {
            $credito["dinero_ganado_status"] = "UTILIZADO";
            $credito["dinero_status"] = 'UTILIZADO';
        }

        $credito["dinero_utilizado"] = $Clclientes->getCreditoUsadoEnOrden($credito["cod_orden"]);

        if(!$credito["puntos"])
            $credito["puntos"] = "--";
        if(!$credito["dinero_ganado"])
            $credito["dinero_ganado"] = "--";
        if(!$credito["saldo"])
            $credito["saldo"] = "--";
    }

    $data["cliente"] = $cliente;
    $data["puntos_activos"] = $creditoActivo;
    $data["puntos_caducados"] = $creditoCaducado;
    $data["dinero_caducado"] = $dineroCaducado;

    $return["success"] = 1;
    $return["mensaje"] = "Reporte";
    $return["data"] = $data;
    return $return;
}

function otherCredits() {
    global $Clclientes;
    
    if(!isset($_GET["cod_usuario"])) {
        return [
            "success" => 0,
            "mensaje" => "Falta el ID de cliente"
        ];
    }

    extract($_GET);

    $credits = $Clclientes->getOtherCredits($cod_usuario);
    if(!$credits) {
        return [
            "success" => 0,
            "mensaje" => "No hay registros"
        ];
    }

    return [
        "success" => 1,
        "mensaje" => "Créditos obtenidos",
        "data" => $credits
    ];
}
?>