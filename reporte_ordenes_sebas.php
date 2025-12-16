<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}

$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
</head>
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
            <div class="col-md-12" style="margin-top:25px; ">
                <h3 id="titulo">Reporte</h3>
            </div>
            <div class="row layout-top-spacing">

                <div class="col-12  layout-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div>
                            <h4>Filtros</h4>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <select id="cmbTipoReporte" class="form-control">
                                    <option value="totales">Totales</option>
                                    <option value="ENTREGADA">Entregadas</option>
                                    <option value="E">Sólo efectivo</option>
                                    <option value="T">Sólo tarjeta</option>
                                    <option value="P">Sólo puntos</option>
                                    <option value="TB">Sólo transferencia</option>
                                    <option value="DB">Sólo depósito bancario</option>
                                    <option value="TC">Sólo tarjeta en casa</option>
                                    <option value="LP">Sólo link de pago</option>
                                    <option value="G">Sólo giftcards</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <select id="cmbTipo" class="form-control">
                                    <option value="month">Mensual</option>
                                    <option value="week">Semanal</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="month" id="date" class="form-control">
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary" onclick="getData()">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 layout-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <table id="style-3" class="table style-3 no-footer">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Cant. órdenes</th>
                                </tr>
                            </thead>
                            <tbody>
    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php footer(); ?>
    </div>
    <!--  END CONTENT AREA  -->
</div>
<!-- END MAIN CONTAINER -->



<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<?php js_mandatory(); ?>
<script src="./assets/js/libs/handlebars/handlebars.js"></script>
<script src="./assets/js/libs/handlebars/helpers.js"></script>
<script src="./assets/js/libs/moment/moment.min.js"></script>

<script id="registro-template" type="text/x-handlebars-template">
    {{#each this}}
        <tr>
            <td>{{nombre}}</td>
            <td class="text-right">{{cant_ordenes}}</td>
        </tr>
    {{/each}}
</script>

<script>
    let tipo = "month";
    moment().locale('es');

    $("#cmbTipo").on("change", function() {
        tipo = $(this).val();
        $("#date").attr("type", tipo);
    });

    function getData() {
        let data = $("#date").val();

        if(data == "") {
            notify("Escoja una fecha", "error", 2);
            return;
        }

        fechaInicio = moment(data).startOf(tipo).format('YYYY-MM-DD') + " " + "00:00:00";
        fechaFin = moment(data).endOf(tipo).format('YYYY-MM-DD') + " " + "23:59:59";

        let info = {
            fechaInicio,
            fechaFin,
            tipoReporte: $("#cmbTipoReporte").val()
        }

        OpenLoad("Buscando datos...");
        fetch(`controllers/controlador_reporte_ordenes_sebas.php?metodo=getReport`, {
            method: 'POST',
            headers: {
                'Api-Key': config.apikey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            $('#style-3').DataTable().clear().destroy();
            if (response.success == 1) {
                let target = $("#style-3 tbody");
                let template = Handlebars.compile($("#registro-template").html());
                target.html(template(response.data));

                
                $('#style-3').DataTable({
                    dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                    buttons: {
                        buttons: [{
                                extend: 'copy',
                                className: 'btn'
                            },
                            {
                                extend: 'excel',
                                className: 'btn'
                            },
                            {
                                extend: 'pdf',
                                className: 'btn'
                            },
                        ]
                    },
                    "stripeClasses": [],
                    "lengthMenu": [7, 10, 20, 50],
                    "pageLength": 20,
                    "order": [[0, "asc"]]
                });

            } else {
                notify(response.mensaje, "info", 2);
            }
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
        });

        console.log(fechaInicio, fechaFin);
    }

    function datatableReInit() {
    $('#style-3').DataTable().clear().destroy();
    $('#style-3').DataTable({
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
            ]
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 20,
        "order": [[0, "desc"]]
    });
}
</script>

<script src="assets/js/pages/reporte_ordenes_sebas.js" type="text/javascript"></script>
<!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>