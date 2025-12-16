<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_clientes.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();

$cod_empresa = $session['cod_empresa'];
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$imagen = url_sistema . '/assets/img/200x200.jpg';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario = $Clusuarios->get2($id);
    if ($usuario) {

        $estado = $usuario['estado'];

        $nombre = $usuario['nombre'] . ' ' . $usuario['apellido'];
        $correo = $usuario['correo'];
        $num_documento = $usuario['num_documento'];
        $fecha = fechaLatinoShort($usuario['fecha_nacimiento']);
        $fecha_only = fecha_only();

    

        // PUNTOS EXPIRADO
        $query2 = "SELECT cp.puntos, cp.dinero, cp.fecha_create, cp.fecha_caducidad, DATEDIFF(cp.fecha_caducidad, cp.fecha_create) as diferencia
                FROM tb_clientes c
                INNER JOIN tb_usuarios u
                    ON c.cod_usuario = u.cod_usuario
                INNER JOIN tb_clientes_puntos cp
                    ON c.cod_cliente = cp.cod_cliente
                    AND cp.fecha_caducidad <= '$fecha_only'
                WHERE c.cod_usuario = $id";

        $reporte2 = Conexion::buscarVariosRegistro($query2);
    } else {
        header("location: ./index.php");
    }
} else {
    header("location: ./index.php");
}

function datetimeShort($fecha)
{
    $separate = explode(" ", $fecha);
    $fecha = $separate[0];
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('y', strtotime($fecha));

    $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return "$nombreMes $numeroDia/$anio " . substr($separate[1], 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .respGalery>div {
            margin-top: 15px;
        }

        .itemSucursal {
            border-radius: 6px;
            border: 1px solid #e0e6ed;
            padding: 14px 26px;
            margin-bottom: 10px;
        }

        .itemSucursal .title {
            font-size: 16px;
            font-weight: bold;
        }

        .switch.s-icons {
            height: auto;
        }

        .feather-16 {
            width: 16px;
            height: 16px;
        }

        .popover {
            top: auto;
            left: auto;
            display: inline !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
            box-shadow: none !important;
            background-color: transparent !important;
            border: 0;
        }
        .popover-body {
            background-color: #fff !important;
        }
    </style>
</head>

<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <?php echo sidebar(); ?>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div><a id="btnBack" data-module-back="cliente_detalle.php?id=<?php echo $id; ?>" style="cursor: pointer;">
                                <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Informaci&oacute;n del Cliente</span></a>
                        </div>
                        <h3 id="titulo"><?php echo $nombre; ?></h3>
                    </div>
                </div>

                <div class="layout-top-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div class="row my-4">
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="dollar-sign"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <div class="mt-3">
                                                <span class="mr-1">
                                                    Total en consumos 
                                                </span>
                                                <span class="popover" data-container="body" data-toggle="popover" data-placement="top" data-content="Histórico total">
                                                    <i data-feather="info" class="feather-16"></i>
                                                </span>
                                            </div>
                                            <h3 class="mt-2" id="historic-total-orders">$0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="heart"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <div class="mt-3">
                                                <span class="mr-1">
                                                    Total puntos ganados 
                                                </span>
                                                <span class="popover" data-container="body" data-toggle="popover" data-placement="top" data-content="Histórico total">
                                                    <i data-feather="info" class="feather-16"></i>
                                                </span>
                                            </div>
                                            <h3 class="mt-2" id="historic-points">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="dollar-sign"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <div class="mt-3">
                                                <span class="mr-1">
                                                    Total crédito consumido 
                                                </span>
                                                <span class="popover" data-container="body" data-toggle="popover" data-placement="top" data-content="Histórico total">
                                                    <i data-feather="info" class="feather-16"></i>
                                                </span>
                                            </div>
                                            <h3 class="mt-2" id="historic-used-credit">$0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-4">
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-success">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="star"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <p class="mt-3">Nivel Actual</p>
                                            <h3 class="mt-2" id="current-level">--</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-success">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="heart"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <p class="mt-3">Total puntos actuales</p>
                                            <h3 class="mt-2" id="current-points">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="alert alert-success">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <i data-feather="dollar-sign"></i>
                                        </div>
                                        <div class="col-12 text-center">
                                            <p class="mt-3">Total dinero actual</p>
                                            <h3 class="mt-2" id="current-money">$0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12  layout-spacing">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Crédito vigente</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Crédito expirado</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="table-responsive" id="table-current-credit">
                                            <!-- TEMPLATE ID: current-credit-template -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                        <div class="row my-3">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-12 col-lg-4">
                                                        <label>Tipo</label>
                                                        <select id="cmbTipo" class="form-control">
                                                            <option value="puntos">Puntos caducados</option>
                                                            <option value="dinero">Dinero caducado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive d-none" id="table-expired-points">
                                            
                                        </div>
                                        <div class="table-responsive d-none" id="table-expired-money">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAHa67r_2hPqR_URtU8zsibmJx9Ahq7yGQ"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/orden_detalle.js" type="text/javascript"></script>
    <script src="assets/js/pages/cliente_puntos_maga.js?v=0" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <script id="current-credit-template" type="text/x-handlebars-template">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>N. orden</th>
                    <th>Fecha orden</th>
                    <th>Sucursal</th>
                    <th>Total</th>
                    <th>Tipo/origen</th>
                    <th>Estado</th>
                    <th>Nivel</th>
                    <th>Puntos ganados</th>
                    <th>Crédito ganado</th>
                    <th>Crédito usado</th>
                    <th>Crédito restante</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                {{#each this}}
                    <tr>
                        <td>{{ cod_orden }}</td>
                        <td>{{ fecha }}</td>
                        <td>{{ sucursal }}</td>
                        <td>$ {{ total }} </td>
                        <td>{{ tipo }}/{{strToUpperCase medio_compra}}</td>
                        <td>
                            <span class='badge badge-success'>{{ estado }}</span>
                        </td>
                        <td class="text-center">{{ nivel }}</td>
                        <td class="text-center">
                            {{#mayor puntos 0 }}
                                {{ puntos }}
                                <i data-feather="heart"></i>
                            {{else}}
                                {{ puntos }}               
                            {{/mayor}}
                        </td>
                        <td class="text-center">
                            {{#mayor dinero_ganado 0 }}
                                <span class="badge outline-badge-{{colorStatus dinero_ganado_status}}">
                                    + ${{ dinero_ganado }}
                                    <i data-feather="arrow-up"></i>
                                </span>
                            {{else}}
                                {{ dinero_ganado }}               
                            {{/mayor}}
                        </td>
                        <td class="text-center">
                            {{#mayor dinero_utilizado 0 }}
                                <span class='badge outline-badge-danger'>
                                    - ${{ dinero_utilizado }}
                                    <i data-feather="arrow-down"></i>
                                </span>
                            {{else}}
                                {{ dinero_utilizado }}               
                            {{/mayor}}
                        </td>
                        <td class="text-center">
                            {{#mayor saldo 0 }}
                                <span class="badge outline-badge-{{colorStatus dinero_status}}">
                                    ~ ${{ saldo }} {{ dinero_status }}
                                </span>
                            {{else}}
                                {{#eq saldo '--'}}
                                    {{saldo}}
                                {{else}}
                                    <span class="badge outline-badge-{{colorStatus dinero_status}}">
                                        {{ dinero_status }}
                                    </span>
                                {{/eq}}
                            {{/mayor}}
                                <!-- <i data-feather="arrow-right"></i> -->
                        </td>
                        <td>
                            <ul class='table-controls list-unstyled'>
                                <li>
                                    <a href='./orden_detalle.php?id={{cod_orden}}' target="_blank">
                                        <i data-feather='eye'></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                {{/each}}
            </tbody>
        </table>
    </script>

    <script id="expired-points-template" type="text/x-handlebars-template">
        <div class="my-4">
            <h3>Total puntos caducados: <span id="historic-expired-points">0</span></h3>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha acreditación</th>
                    <th>Fecha caducidad</th>
                    <th>Tiempo caducidad</th>
                    <th>Puntos caducados</th>
                </tr>
            </thead>
            <tbody>
                {{#mayor this.length 0}}
                    {{#each this}}
                        <tr>
                            <td>
                                {{ fecha_create }}
                            </td>
                            <td>
                                {{ fecha_caducidad }}
                            </td>
                            <td>
                                {{ diferencia }} días
                            </td>
                            <td>
                                {{ puntos }}
                            </td>
                        </tr>
                    {{/each}}
                {{else}}
                    <tr>
                        <td colspan="4" class="text-center">No hay puntos caducados</td>
                    </tr>
                {{/mayor}}
            </tbody>
        </table>
    </script>
    
    <script id="expired-money-template" type="text/x-handlebars-template">
        <div class="my-4">
            <h3>Total dinero caducado: <span id="historic-expired-money"></span></h3>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha acreditación</th>
                    <th>Fecha caducidad</th>
                    <th>Tiempo caducidad</th>
                    <th>Dinero caducado</th>
                </tr>
            </thead>
            <tbody>
                {{#mayor this.length 0}}
                    {{#each this}}
                        <tr>
                            <td>
                                {{ fecha }}
                            </td>
                            <td>
                                {{ fecha_caducidad }}
                            </td>
                            <td>
                                {{ diferencia }} días
                            </td>
                            <td>
                                ${{decimal saldo}}
                            </td>
                        </tr>
                    {{/each}}
                {{else}}
                    <tr>
                        <td colspan="4" class="text-center">No hay dinero caducado</td>
                    </tr>
                {{/mayor}}
            </tbody>
        </table>
    </script>
</body>

</html>