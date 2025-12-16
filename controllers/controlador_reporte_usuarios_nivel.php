<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_clientes.php";
require_once "../clases/cl_reporte_usuarios_nivel.php";
$Clclientes = new cl_clientes();
$clreporte = new cl_reporte_usuarios_nivel();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getDatos(){
    global $session;
    global $clreporte;
    global $Clclientes;

    $cod_empresa = $session['cod_empresa'];
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    // $return['success'] = 1;
    // $return['mensaje'] = $cod_empresa;
    // return $return;

    $tabla="";
    $reportes = $clreporte->getClientesByNivel($cod_empresa, $cod_nivel);
    if($reportes){        
        foreach ($reportes as $reporte){
            $Clclientes->cod_cliente = $reporte['cod_cliente'];
            $dinero = $Clclientes->GetDinero();
            $puntos = $Clclientes->GetPuntos();
            $saldo = $Clclientes->GetSaldo();
            $tabla.='<tr>                        
                        <td>'.$reporte['nombre'].' '.$reporte['apellido'].'</td>
                        <td>'.$reporte['num_documento'].'</td>
                        <td>'.$reporte['correo'].'</td>
                        <td>$ '.number_format($dinero, 2).'</td>
                        <td> '.$puntos.'</td>
                        <td>$ '.number_format($saldo, 2).'</td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li><a href="cliente_detalle.php?id='.$reporte['cod_usuario'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                            </ul>
                        </td>
                    </tr>';
        }
        $return['success'] = 1;
        $return['mensaje'] = "Niveles obtenidos";
        $return['html'] = $tabla;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se encontraron resultados, vuelva a intentarlo";
        
    }
    return $return;
}

function getDatosDatatablesPRE() {
    global $session;
    $cod_empresa = $session['cod_empresa'];

    extract($_GET);

    $filtro = "";
    if($cod_nivel <> "")
        $filtro = "AND c.cod_nivel = $cod_nivel";
        
    $table ="(SELECT c.*, n.nombre as nom_nivel
                FROM tb_clientes c, tb_niveles n
                WHERE c.cod_nivel = n.posicion
                AND c.cod_empresa = $cod_empresa
                AND n.cod_empresa = $cod_empresa
                AND c.estado = 'A'
                AND c.nombre <> ''
                $filtro
                ORDER BY n.posicion ASC) temp";

    $primaryKey = 'cod_cliente';

    $columns = array(
        array( 'dt' => 0, 'db' => 'nombre'),
        array( 'dt' => 1, 'db' => 'num_documento'),
        array( 'dt' =>2, 'db' => 'cod_cliente',
            'formatter' => function($d, $row) {
                global $Clclientes;
                $Clclientes->cod_cliente = $row['cod_cliente'];
                return "$" .number_format($Clclientes->GetDinero(), 2);
            }),
        array( 'dt' => 3, 'db' => 'cod_cliente',
            'formatter' => function($d, $row) {
                global $Clclientes;
                $Clclientes->cod_cliente = $row['cod_cliente'];
                return $Clclientes->GetPuntos();
            }),
        array( 'dt' => 4, 'db' => 'cod_cliente',
            'formatter' => function($d, $row) {
                global $Clclientes;
                $Clclientes->cod_cliente = $row['cod_cliente'];
                return "$". number_format($Clclientes->GetSaldo(), 2);
            }),
        array( 'dt' => 5, 'db' => 'cod_usuario',
            'formatter' => function($d, $row){
                return '<ul class="table-controls">
                            <li>
                                <a href="cliente_detalle.php?id='.$row['cod_usuario'].'" title="Ver m&aacute;s informaci&oacute;n">
                                    <i data-feather="eye"></i>
                                </a>
                            </li>
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
    $data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);
    //var_dump($data);
    return $data;
}

function getDatosDatatables() {
    global $session;
    $cod_empresa = $session['cod_empresa'];

    extract($_GET);

    $filtro = "";
    if($cod_nivel <> "")
        $filtro = "AND c.cod_nivel = $cod_nivel";
        
    $table ="(SELECT c.cod_cliente, c.cod_usuario, c.num_documento, c.nombre, n.nombre as nivel, COALESCE(cd.dinero, '0') as dinero, COALESCE(cp.puntos, '0') as puntos, COALESCE(cs.saldo, '0') as saldo
    FROM tb_clientes c
    INNER JOIN (SELECT cod_nivel, nombre, posicion
                FROM tb_niveles
                WHERE cod_empresa = $cod_empresa)
                n ON c.cod_nivel = n.posicion
    LEFT JOIN (SELECT cod_cliente, SUM(saldo) as dinero 
                FROM tb_cliente_dinero
                WHERE estado = 'A'
                AND fecha_caducidad > CURDATE()
                GROUP BY cod_cliente) 
                cd ON c.cod_cliente = cd.cod_cliente
    LEFT JOIN (SELECT cod_cliente, SUM(puntos) as puntos
                FROM tb_clientes_puntos
                WHERE estado = 'A'
                AND fecha_caducidad > CURDATE()
                GROUP BY cod_cliente) 
                cp ON c.cod_cliente = cp.cod_cliente
    LEFT JOIN (SELECT cod_cliente, SUM(dinero) as saldo
                FROM tb_clientes_saldos
                WHERE estado = 'A'
                AND fecha_caducidad > CURDATE()
                GROUP BY cod_cliente) 
                cs ON c.cod_cliente = cs.cod_cliente
    WHERE c.cod_empresa = $cod_empresa
    AND c.nombre <> ''
    $filtro
    GROUP BY c.cod_cliente
    ORDER BY dinero DESC) temp";

    $primaryKey = 'cod_cliente';

    $columns = array(
        array( 'dt' => 0, 'db' => 'nombre'),
        array( 'dt' => 1, 'db' => 'num_documento'),
        array( 'dt' => 2, 'db' => 'nivel'),
        array( 'dt' =>3, 'db' => 'dinero',
            'formatter' => function($d, $row) {
                return "$" . number_format($row['dinero'], 2);
            }),
        array( 'dt' => 4, 'db' => 'puntos'),
        array( 'dt' => 5, 'db' => 'saldo',
            'formatter' => function($d, $row) {
                return "$". number_format($row['saldo'], 2);
            }),
        array( 'dt' => 6, 'db' => 'cod_usuario',
            'formatter' => function($d, $row){
                return '<ul class="table-controls">
                            <li>
                                <a href="cliente_detalle.php?id='.$row['cod_usuario'].'" title="Ver m&aacute;s informaci&oacute;n">
                                    <i data-feather="eye"></i>
                                </a>
                            </li>
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
    $data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns);
    //var_dump($data);
    return $data;
}
?>