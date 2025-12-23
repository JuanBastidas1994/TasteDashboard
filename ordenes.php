<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$Clempresas = new cl_empresas(NULL);
$empresa = $Clempresas->get($session['cod_empresa']);
if($empresa){
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
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>&Oacute;rdenes</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right" style="display:none;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#crearCliente">Nueva orden</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="row">
                                <div class="col-2 <?php if($session['cod_rol']==3) echo "d-none"; ?> ">
                                    <label>Sucursal <i data-feather="map-pin"></i></label>
                                    <select id="cmbSucursal" class="form-control basic" >
                                        <option value="">Todas</option>
                                        <?php
                                        $resp = $clsucursales->lista();
                                        foreach ($resp as $sucursales) {
                                            echo '<option value="' . $sucursales['cod_sucursal'] . '">' . $sucursales['nombre'] . '</option>';
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Tipo <i data-feather="truck"></i></label>
                                    <select id="cmbType" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="1">Delivery</option>
                                        <option value="0">Pickup</option>
                                        <?php
                                            if(in_array("OFFICE_INSITE", $permisos)){
                                                echo '<option value="2">En mesa</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Pago <i data-feather="credit-card"></i></label>
                                    <select id="cmbPayment" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="E">Efectivo</option>
                                        <option value="T">Tarjeta</option>
                                        <option value="TB">Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Entrega <i data-feather="clock"></i></label>
                                    <select id="cmbTiempo" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="programadas">Programadas por entregar</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Buscar <i data-feather="search"></i></label>
                                    <input type="text" id="customSearch" class="form-control" placeholder="Buscar orden...">
                                </div>
                            </div>
                            
                            <div class="table-responsive mb-4">
                                <input type="hidden" id="apikey_empresa" value="<?= $apikey?>">
                                <table  id="table-ordenes" class="table style-3  table-hover" data-order='[[ 0, "desc"]]' style="margin-top: 10px !important;">
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
            
            $('#cmbPayment, #cmbType, #cmbTiempo, #cmbSucursal').on('change', function(){
                table.ajax.reload();
            });
        });
    
        //config = DatatableConfig();
        function loadDatatable(){
            table = $('#table-ordenes').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: {
                    buttons: [
                        { extend: 'excel', className: 'btn' },
                        { extend: 'pdf', className: 'btn' },
                        { extend: 'print', className: 'btn' }
                    ]
                },
                ajax: {
                    url:'./controllers/controlador_ordenes.php?metodo=datatable',
                    type:'GET',
                    data: function(d){
                        d.payment = $('#cmbPayment').val();
                        d.tipo = $('#cmbType').val();
                        d.tiempo = $('#cmbTiempo').val();
                        d.sucursal = $('#cmbSucursal').val();
                    },
                    error: function(e){
                        console.log(e);
                    },
                    complete: function(){
                        feather.replace();
                    }
                }
            });
            
            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        }

        $("body").on("click", ".btnSetStatus", function(){
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
            }).then(function(result){
                if (result.value) {
                    setStatusOrder(data);
                }
            });
        });

        function setStatusOrder(data){

            console.log("enviar", data);
            OpenLoad("Cambiando estado Orden");
           
            let ApiUrl = "https://api.mie-commerce.com/taste/v1";
            let ApiKey = $("#apikey_empresa").val();

            let url = `${ApiUrl}/ordenes/set-estado`;
            if(data.estado == "ANULADA")
                url = `${ApiUrl}/ordenes/cancelar`;
            
            fetch(url,{
                    method: 'POST',
                    headers: {
                        'Api-Key':ApiKey
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    CloseLoad();
                    console.log("ORDEN CAMBIO ESTADO", response);
                    if(response.success == 1){
                        notify(response.mensaje, "success", 2);
                       
                        $(".btnSetStatus").parent().remove();
                        if(data.estado == "ENTREGADA")
                            $(".badgeOrder" + data.cod_orden).removeClass("badge-primary").addClass("badge-success").html(data.estado);
                        else 
                            $(".badgeOrder" + data.cod_orden).removeClass("badge-primary").addClass("badge-danger").html(data.estado);    
                    }else{
                        messageDone(response.mensaje,'error');
                    }
                })
                .catch(error=>{
                    CloseLoad();
                    messageDone('Ocurrió un error','error');
                }
            );
        }

    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>