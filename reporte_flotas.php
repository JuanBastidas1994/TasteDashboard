<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_reporte_ventas.php";

require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);

$ClEmpresas = new cl_empresas(NULL);

$Clreportes = new cl_reporte_ventas(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
$alias = $session['alias'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
</head>

<body>
    <!--MODAL BIENVENIDA -->
    <div class="modal fade bs-example-modal-lg" id="detalleModal" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="x_content">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 id="name-delivery">Juan Bastidas</h3>
                                <p id="numitems-delivery">1 Carrera</p>
                            </div>
                            <div>
                                <span id="total-delivery" style="font-size: 30px; font-weight: bolder;">$10.00</span>
                            </div>
                        </div>    
                        <div class="row">
                            
                        <div class="container">
                            
                    
                        </div>

                            <table id="table-detalles" class="table style-3">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id Orden</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Envío</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lstDetalle">
                                    <!-- LISTA DE ÓRDENES -->
                                </tbody>
                            </table>

                        </div>    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary"  >Omitir</button>
                </div>
            </div>
        </div>
    </div>
    
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
            <div class="layout-px-spacing bg-white">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Reporte envíos por Flota</h3>
                </div>

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12">
                        <div class="">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                                <label>Sucursales <span class="asterisco">*</span></label>
                                                <select class="form-control  basic" id="cmb_sucursal">
                                                    <option value="0">Todas las sucursales</option>
                                                    <?php
                                                    $resp = $clsucursales->lista();
                                                    foreach ($resp as $sucursales) {
                                                        echo '<option value="' . $sucursales['cod_sucursal'] . '">' . $sucursales['nombre'] . '</option>';
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <label>Flota <span class="asterisco">*</span></label>
                                                <select class="form-control  basic" id="cmb_flota">
                                                    <?php
                                                    $flotas = $ClEmpresas->getFlotas();
                                                    foreach ($flotas as $flota) {
                                                        echo '<option value="' . $flota['cod_flota'] . '">' . $flota['nombre'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-md-2 col-sm-2 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha inicio</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                                                </div>
                                            </div>

                                            <div class="col-md-2 col-sm-2 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha fin</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_fin" id="fecha_fin">
                                                </div>
                                            </div>
                                            
                                            <div class="col-xl-2 col-md-2 col-sm-3 col-12" style="text-align: right;">
                                                <button class="btn btn-primary btnGenerar" style="margin-top: 30px;" data-empresa="<?= $cod_empresa ?>" data-alias="<?= $alias ?>">Generar reporte</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>
                    
                    <div class="col-12">
                        <div class="">
                            <div>
                                <h4>Ordenes</h4>
                            </div>
                            <table id="table-ordenes" class="table style-3">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Sucursal</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Distancia</th>
                                        <th class="text-center">Envio</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pagos</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lstOrdenes">
                                </tbody>
                                <tfoot id="footerResumenPagos">
                                </tfoot>
                            </table>
                            </br>
                            </br>
                            
                            
                            
                        </div>
                    </div>
                    
                    <div class="px-4 py-2 w-100" style="text-align: right; font-size: 28px; font-weight: bold; position: sticky; bottom: 0; background: #f1f2f3; color: gray;">
                            Total a pagar: $<span id="totalAmount">0.00</span>
                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/reporte_courier.js?v=1" type="text/javascript"></script>
    <script>
        $("body").on('click', ".btnGenerar", function (event) {
            event.preventDefault();
            var cod_empresa = $(this).data("empresa");
            var alias = $(this).data("alias");
            var sucursal = $("#cmb_sucursal").val();
            var flota = $("#cmb_flota").val();
            var f_inicio = $("#fecha_inicio").val();
            var f_fin = $("#fecha_fin").val();
        
            if (f_inicio !== "" && f_fin !== "") {
                if (f_fin < f_inicio) {
                    messageDone("Las fecha fin no puede ser menor que la de incio,intentelo nuevamente ", 'error');
                    return;
                }
            } else {
                messageDone("Debe completar todos los campos, intentelo nuevamente", 'error');
                return;
            }
        
            var parametros = {
                "cod_empresa": cod_empresa,
                "alias": alias,
                "cod_sucursal": sucursal,
                "cod_flota": flota,
                "fechaInicio": f_inicio,
                "fechaFin": f_fin
            }
        
            $.ajax({
                beforeSend: function () {
                    OpenLoad("Cargando datos, por favor espere...");
                },
                url: 'controllers/controlador_reporte_courier.php?metodo=getOrdenesFlota',
                type: 'POST',
                data: parametros,
                success: function (response) {
                    if (response['success'] == 1) {
                        $('#table-ordenes').DataTable().destroy();
                        $("#lstOrdenes").html(response['tabla']);
                        $("#totalAmount").html(response['total']);
                        
                        //RESUMEN
                        // 🔥 Construir resumen del footer
                        let resumen = "";
                        if (response.resumen && response.resumen.length > 0) {
                            $("#lstOrdenes").append(`
                                    <tr>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right">TOTALES</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                    </tr>
                                `);
                            response.resumen.forEach(item => {
                                $("#lstOrdenes").append(`
                                    <tr>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right">${item.metodo_pago}</th>
                                        <th class="text-right">$${parseFloat(item.total).toFixed(2)}</th>
                                        <th class="text-right"></th>
                                    </tr>
                                `);
                            });
                        } else {
                            resumen = "<strong>No hay Detalle para mostrar.</strong>";
                        }
                    
        
                        initDatatable($('#table-ordenes'));
                        feather.replace();
                    }
                    else {
                        notify(response['mensaje'], "info", 2);
                        $("#lstOrdenes").html("");
                        $("#totalAmount").html("0.00");
                    }
        
                },
                error: function (data) {
                    console.log(data);
        
                },
               complete: function (resp) {
                    CloseLoad();
                }
            });
        });

    </script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

</body>

</html>