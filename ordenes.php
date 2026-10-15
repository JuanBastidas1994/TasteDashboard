<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$Clempresas = new cl_empresas(NULL);
$empresa = $Clempresas->get($session['cod_empresa']);
if ($empresa) {
    $apikey = $empresa['api_key'];
    $permisos = $Clempresas->getIdPermisionByBusiness($session['cod_empresa']);
}

$clsucursales = new cl_sucursales(NULL);

//echo $apikey;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
</head>

<body>
    <style>
        .dataTables_filter {
            display: none;
        }
    </style>
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

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12 mb-3">
                                <h4>Órdenes</h4>
                            </div>

                            <div class="row align-items-end mb-3">
                                <div class="col-md-2 col-12 mb-md-0 mb-3<?php if ($session['cod_rol'] == 3) echo " d-none"; ?>">
                                    <label class="mb-1">Sucursal <i data-feather="map-pin" style="width:14px;height:14px;"></i></label>
                                    <select id="cmbSucursal" class="form-control basic">
                                        <option value="">Todas</option>
                                        <?php
                                        $resp = $clsucursales->lista();
                                        foreach ($resp as $sucursales) {
                                            echo '<option value="' . $sucursales['cod_sucursal'] . '">' . $sucursales['nombre'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <label class="mb-1">Tipo <i data-feather="truck" style="width:14px;height:14px;"></i></label>
                                    <select id="cmbType" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="1">Delivery</option>
                                        <option value="0">Pickup</option>
                                        <?php
                                        if (in_array("OFFICE_INSITE", $permisos)) {
                                            echo '<option value="2">En mesa</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <label class="mb-1">Pago <i data-feather="credit-card" style="width:14px;height:14px;"></i></label>
                                    <select id="cmbPayment" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="E">Efectivo</option>
                                        <option value="T">Tarjeta</option>
                                        <option value="TB">Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <label class="mb-1">Entrega <i data-feather="clock" style="width:14px;height:14px;"></i></label>
                                    <select id="cmbTiempo" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="programadas">Programadas por entregar</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-12 mb-md-0 mb-3">
                                    <label class="mb-1">Buscar <i data-feather="search" style="width:14px;height:14px;"></i></label>
                                    <input type="text" id="customSearch" class="form-control" placeholder="Buscar orden...">
                                </div>
                            </div>

                            <div class="mb-4">
                                <input type="hidden" id="apikey_empresa" value="<?= $apikey ?>">
                                <table id="table-ordenes" class="table style-3  table-hover" data-order='[[ 0, "desc"]]' style="margin-top: 10px !important;">
                                    <thead>
                                        <tr>
                                            <th>N.</th>
                                            <th>Cliente</th>
                                            <th>Sucursal</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Pago</th>
                                            <th>Tipo</th>
                                            <th>Entrega</th>
                                            <th>Teléfono</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

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

    <?php js_mandatory(); ?>

    <script>
        let table;
        $(function() {
            loadDatatable();

            $('#cmbPayment, #cmbType, #cmbTiempo, #cmbSucursal').on('change', function() {
                table.ajax.reload();
            });
        });

        //config = DatatableConfig();
        function loadDatatable() {
            table = $('#table-ordenes').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                dom: 'Bfrtip',
                buttons: {
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg> Excel',
                            className: 'btn btn-primary'
                        },
                        {
                            extend: 'print',
                            text: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Print',
                            className: 'btn btn-primary'
                        }
                    ]
                },
                ajax: {
                    url: './controllers/controlador_ordenes.php?metodo=datatable',
                    type: 'GET',
                    data: function(d) {
                        d.payment = $('#cmbPayment').val();
                        d.tipo = $('#cmbType').val();
                        d.tiempo = $('#cmbTiempo').val();
                        d.sucursal = $('#cmbSucursal').val();
                    },
                    error: function(e) {
                        console.log(e);
                    },
                    complete: function() {
                        feather.replace();
                    }
                }
            });

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        }

        $("body").on("click", ".btnSetStatus", function() {
            let btn = $(this);
            let data = btn.data("status")
            swal({
                title: 'Cambiar estado de la orden a ' + data.estado,
                text: '¿Continuar?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                padding: '2em'
            }).then(function(result) {
                if (result.value) {
                    setStatusOrder(data);
                }
            });
        });

        function setStatusOrder(data) {

            console.log("enviar", data);
            OpenLoad("Cambiando estado Orden");

            let ApiUrl = "https://api.mie-commerce.com/taste/v1";
            let ApiKey = $("#apikey_empresa").val();

            let url = `${ApiUrl}/ordenes/set-estado`;
            if (data.estado == "ANULADA")
                url = `${ApiUrl}/ordenes/cancelar`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Api-Key': ApiKey
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    CloseLoad();
                    console.log("ORDEN CAMBIO ESTADO", response);
                    if (response.success == 1) {
                        notify(response.mensaje, "success", 2);

                        $(".btnSetStatus").parent().remove();
                        if (data.estado == "ENTREGADA")
                            $(".badgeOrder" + data.cod_orden).removeClass("badge-primary").addClass("badge-success").html(data.estado);
                        else
                            $(".badgeOrder" + data.cod_orden).removeClass("badge-primary").addClass("badge-danger").html(data.estado);
                    } else {
                        messageDone(response.mensaje, 'error');
                    }
                })
                .catch(error => {
                    CloseLoad();
                    messageDone('Ocurrió un error', 'error');
                });
        }
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>