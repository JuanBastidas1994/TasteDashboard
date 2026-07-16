<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresa = new cl_empresas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/';
$progressColors = array('primary', 'danger', 'info', 'success', 'warning', 'dark');

$anio_actual = intval(date('Y'));
$mes_actual  = intval(date('n'));
$nombres_meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$meses_disponibles = [];
for ($m = 1; $m < $mes_actual; $m++) {
    $meses_disponibles[] = ['num' => $m, 'nombre' => $nombres_meses[$m - 1]];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/elements/avatar.css" rel="stylesheet" type="text/css" />
    <style>
        .pBot{
            margin-left: 20px;
        }
        .pEmp{
            margin-left: 40px;
        }
        .pSuc{
            margin-left: 60px;
        }
        .feather-16{
            width: 16px;
            height: 16px;
        }
        .card-funciones{
            text-align:center;
            border: 1px solid #1b55e2;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .table-controls svg{
            width: 22px !important;
            height: 22px !important;
        }
        
        .style-3 img.profile-img {
            width: 40px !important;
            height: 40px !important;
        }
        
        table.dataTable td{
            padding: 5px 3px !important;
        }
    </style>
</head>
<body>
    
    <!-- Modal FUnciones -->
    <div class="modal fade bs-example-modal-lg" id="modalFunciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Funciones</h5>
                    <input type="hidden" id="alias-mas-funciones" value=""/>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <script id="lista-funciones" type="text/x-handlebars-template">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="lst_web_paginas.php?id={{alias}}">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="layout"></i></div>
                                    <h5>Esquema</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="empresas_buttonPayment.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="credit-card"></i></div>
                                    <h5>Botón de Pagos</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="empresas_courier.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="truck"></i></div>
                                    <h5>Courries</h5>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="simulador-ordenes.php?alias={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="navigation"></i></div>
                                    <h5>Simulador</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="https://{{alias}}.demo.mie-commerce.com" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="monitor"></i></div>
                                    <h5>Demo</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="{{web}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="sunrise"></i></div>
                                    <h5>Front Page</h5>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones LoginAdmins" href="javascript:void(0);" style="text-align:center;" data-value="{{alias}}">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="unlock"></i></div>
                                    <h5>Login</h5>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="empresa_productos.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="coffee"></i></div>
                                    <h5>Productos</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="reporte_empresa_ventas.php?id={{alias}}" target="_blank" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="pie-chart"></i></div>
                                    <h5>Reporte de ventas</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="https://dashboard.mie-commerce.com/clearProject/limpiar.php?url={{folder}}" target="_blank" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="frown"></i></div>
                                    <h5>Regenerar Página</h5>
                                </a>
                            </div>
                        </div>
                    </script>
                    
                    
                    <div class="row lista-funciones">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="modalConfig" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CONFIGURACIONES</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <h5>Courier <a href="empresas_courier.php" target="_blank"><i class="feather-16" data-feather="settings"></i></a></h5>
                            <div id="divCouriers">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <h5>Bot&oacute;n de Pagos <a href="empresas_buttonPayment.php" target="_blank"><i class="feather-16" data-feather="settings"></i></a></h5>
                            <div id="divBotonPagos">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt-4">
                            <h5>Facturaci&oacute;n Electr&oacute;nica</h5>
                            <div id="divFacturacion">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt-4">
                            <h5>Otros</h5>
                            <div id="divOtros">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Colapso Mensual Masivo -->
    <div class="modal fade" id="modalColapsoMensual" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Colapso Mensual <?= $anio_actual ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (empty($meses_disponibles)): ?>
                        <p class="text-muted">No hay meses finalizados en <?= $anio_actual ?> todavía.</p>
                    <?php else: ?>
                        <p class="text-muted mb-1">Selecciona los meses a colapsar para las empresas marcadas. Solo se muestran meses ya finalizados.</p>
                        <div class="mb-3">
                            <a href="javascript:void(0);" id="btnMarcarTodosMeses" class="text-primary mr-3" style="font-size:13px;">Marcar todos</a>
                            <a href="javascript:void(0);" id="btnDesmarcarTodosMeses" class="text-secondary" style="font-size:13px;">Desmarcar todos</a>
                        </div>
                        <div class="row">
                            <?php foreach ($meses_disponibles as $mes): ?>
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <label class="d-flex align-items-center" style="cursor:pointer; gap:8px;">
                                    <input type="checkbox" class="chk_mes" value="<?= $mes['num'] ?>" checked style="width:20px;height:20px;">
                                    <span><?= $mes['nombre'] ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <p class="text-muted mb-1" style="font-size:13px;">Empresas seleccionadas: <strong id="lblEmpresasSeleccionadas">0</strong></p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <?php if (!empty($meses_disponibles)): ?>
                    <button class="btn btn-primary" id="btnEjecutarColapsoMensual">
                        <i data-feather="archive"></i> Ejecutar colapso
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal Colapso Mensual -->

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(); ?>
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
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                    <h4>Empresas</h4>
                                </div>
                                <div class="col-xl-6 col-md-6 col-sm-6 col-6 text-right">
                                    <button class="btn btn-outline-secondary mr-2" id="btnAbrirColapsoMensual" <?= empty($meses_disponibles) ? 'disabled title="No hay meses finalizados"' : '' ?>>
                                        <i data-feather="archive"></i> Colapso Mensual
                                    </button>
                                    <a href="crear_empresa.php" class="btn btn-primary">Nueva Empresa</a>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-1">
                                <table id="style-3" class="table style-3  table-hover" style="margin-top: 0px !important;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:40px;">
                                                    <input type="checkbox" id="chkTodas" title="Marcar/desmarcar todas" style="width:18px;height:18px;">
                                                </th>
                                                <th class="checkbox-column text-center">Id</th>
                                                <th class="text-center">&nbsp;</th>
                                                <th>Nombre</th>
                                                <th>Representante</th>
                                                <th>Impuesto</th>
                                                <th>Folder</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Prod</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT cod_empresa, nombre, alias, folder,  representante_nombre, logo, url_web, api_key, impuesto, ambiente, estado
                                                        FROM tb_empresas
                                                        WHERE estado IN ('A', 'I')";
                                            $resp = Conexion::buscarVariosRegistro($query);
                                            foreach ($resp as $empresa) {
                                                $imagen = $files.$empresa['alias'].'/'.$empresa['logo'];
                                                $badge= ($empresa['estado'] == 'A') ? 'primary' : 'danger';
                                                $production = ($empresa['ambiente'] == 'production') ? '<i data-feather="check-circle"></i>' : '';
                                                $chk_checked = ($empresa['ambiente'] == 'production') ? 'checked' : '';

                                                echo '<tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="chk_colapso" '.$chk_checked.'
                                                            data-id="'.$empresa['cod_empresa'].'"
                                                            style="width:18px;height:18px;">
                                                    </td>
                                                    <td class="checkbox-column text-center"> '.$empresa['cod_empresa'].' </td>
                                                    <td class="text-center">
                                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                    </td>
                                                    <td>'.$empresa['nombre'].'</td>
                                                    <td>'.$empresa['representante_nombre'].'</td>
                                                    <td>'.$empresa['impuesto'].'%</td>
                                                    <td>
                                                        <a class="btnCopiar" href="javascript:;" data-clipboard-action="copy" data-clipboard-text="'.$empresa['folder'].'"><i data-feather="copy"></i></a>
                                                        '.$empresa['folder'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($empresa['estado']).'</span></td>
                                                    <td class="text-center">'.$production.'</td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="crear_empresa.php?id='.$empresa['alias'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$empresa['cod_empresa'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                                            <li><a data-web="'.$empresa['url_web'].'" data-folder="'.$empresa['folder'].'" href="javascript:void(0);" class="bs-tooltip btnMore" data-toggle="tooltip" data-placement="top" title="Más" data-original-title="Más"><i data-feather="more-horizontal"></i></a></li>
                                                            <li><a data-value="'.$empresa['alias'].'" href="javascript:void(0);" class="bs-tooltip btnConfig" data-toggle="tooltip" data-placement="top" title="Configuraciones" data-original-title="Configuraciones"><i data-feather="settings"></i></a></li>
                                                        </ul>
                                                    </td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    <?php js_mandatory(); ?>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/pages/crear_empresas.js" type="text/javascript"></script>
    <script>
        var myTable = $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [20, 40, 50],
            "pageLength": 20 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    <script>
        // Checkbox "seleccionar todas"
        $("#chkTodas").on("change", function () {
            var checked = $(this).is(":checked");
            $(".chk_colapso").prop("checked", checked);
        });

        // Abrir modal y actualizar contador de empresas seleccionadas
        $("#btnAbrirColapsoMensual").on("click", function () {
            var count = $(".chk_colapso:checked").length;
            if (count === 0) {
                messageDone("Selecciona al menos una empresa", "error");
                return;
            }
            $("#lblEmpresasSeleccionadas").text(count);
            $("#modalColapsoMensual").modal("show");
        });

        // Marcar/desmarcar todos los meses
        $("#btnMarcarTodosMeses").on("click", function () { $(".chk_mes").prop("checked", true); });
        $("#btnDesmarcarTodosMeses").on("click", function () { $(".chk_mes").prop("checked", false); });

        // Ejecutar colapso masivo
        $("#btnEjecutarColapsoMensual").on("click", function () {
            var cod_empresas = [];
            $(".chk_colapso:checked").each(function () {
                cod_empresas.push($(this).data("id"));
            });
            var meses = [];
            $(".chk_mes:checked").each(function () {
                meses.push($(this).val());
            });

            if (cod_empresas.length === 0) { messageDone("Selecciona al menos una empresa", "error"); return; }
            if (meses.length === 0)        { messageDone("Selecciona al menos un mes", "error"); return; }

            Swal.fire({
                title: "¿Ejecutar colapso mensual?",
                text: cod_empresas.length + " empresa(s) × " + meses.length + " mes(es). Esta acción puede tardar varios segundos.",
                icon: "warning", showCancelButton: true,
                confirmButtonText: "Sí, ejecutar", cancelButtonText: "Cancelar"
            }).then(function (result) {
                if (!result.value) return;
                $("#modalColapsoMensual").modal("hide");
                OpenLoad("Ejecutando colapso mensual...");
                $.ajax({
                    url: "controllers/controlador_colapso_historico.php?metodo=ejecutarColapsoMensualMasivo",
                    type: "POST",
                    data: { cod_empresas: cod_empresas, meses: meses },
                    success: function (response) {
                        messageDone(response["mensaje"], response["success"] == 1 ? "success" : "error");
                    },
                    error: function () { messageDone("Error al conectar con el servidor", "error"); },
                    complete: function () { CloseLoad(); }
                });
            });
        });
    </script>
</body>
</html>