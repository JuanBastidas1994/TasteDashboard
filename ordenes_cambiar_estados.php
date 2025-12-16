<?php
require_once "funciones.php";
require_once "clases/cl_clientes.php";
require_once "clases/cl_usuarios.php";

if(!isLogin()){
    header("location:login.php");
}

$Clclientes = new cl_clientes(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
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
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-8 col-8">
                                    <h4>Cambiar estado a órdenes</h4>
                                </div>
                                
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="table-orders-change-status" class="table style-3 table-hover" data-order='[[ 0, "desc"]]'>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Empresa</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Fecha Creación</th>
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
    
    
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <!-- <script src="assets/js/pages/ordenes.js" type="text/javascript"></script>                                         -->
    <script>
    $(function() {
        if($('#table-orders-change-status').length){
            loadDatatableChangeStatus();
        }
    });

    function loadDatatableChangeStatus(){
        //config = DatatableConfig();
        $('#table-orders-change-status').dataTable({
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'pdf', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            //oLanguage: config.oLanguage,
            ajax: {
                url:'./controllers/controlador_ordenes.php?metodo=datatableChangeStatus',
                type:'GET',
                error: function(e){
                    console.log(e);
                },
                complete: function(){
                    feather.replace();
                }
            }
        });
    }
    
    function changeStatusConfirm(orden, estado) {
        Swal.fire({
           title: `Se moverá la orden`,
           text: `¿Está seguro de cambiar el estado de la orden ${orden} a ${estado}?`,
           type: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Aceptar',
           cancelButtonText: 'Cancelar',
           padding: '2em'
        }).then(function(result){
           if (result.value) {
              changeStatus(orden, estado);
           }
        }); 

        /* if(confirm("¿Está seguro de cambiar el estado de la orden "+orden+" a "+estado+"?")){
            $.ajax({
                url: './controllers/controlador_ordenes.php?metodo=changeStatus',
                type: 'POST',
                data: {orden: orden, estado: estado},
                success: function(response){
                    if(response.success){
                        toastr.success(response.mensaje);
                        $('#table-orders-change-status').DataTable().ajax.reload();
                    }else{
                        toastr.error(response.mensaje);
                    }
                },
                error: function(e){
                    console.log(e);
                    toastr.error("Error al cambiar el estado de la orden");
                }
            });
        } */
    }

    function changeStatus(orden, estado) {
        let info = {
           orden,
           estado
        }
        fetch(`controllers/controlador_ordenes.php?metodo=changeStatus`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1) {
                notify(response.mensaje, "success", 2);
                $('#table-orders-change-status').DataTable().ajax.reload();
            }
            else{
                notify(response.mensaje, "error", 2);
            }
        })
        .catch(error=>{
            console.log(error);
            notify(error.message, "error", 2);
        });
    }
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>