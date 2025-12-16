<?php
require_once "funciones.php";
require_once "clases/cl_codigos_promocionales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clcodigos = new cl_codigos_promocionales(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <!-- END PAGE LEVEL CUSTOM STYLES -->
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR CUPON</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                              <label>Tipo <span class="asterisco">*</span></label>
                              <select name="cmbTipo" id="cmbTipo" class="form-control" required="required" autocomplete="off">
                                <option value="0">Porcentaje</option>
                                <option value="1">Dinero</option>
                              </select>
                          </div>
                         <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                              <label>Monto<span class="asterisco">*</span>
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Monto a regalar al usuario (porcentaje o dinero), debe ser un numero entero"></span>
                              </label>
                              <input type="text" placeholder="" name="txt_monto" id="txt_monto" class="form-control maxlength" required="required" autocomplete="off" maxlength="4"/>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                              <label>Cant. de Usos <span class="asterisco">*</span></label>
                              <input type="text" placeholder="" name="txt_cantidad" id="txt_cantidad" class="form-control maxlength" required="required" autocomplete="off" maxlength="4"/>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                              <label>Restriccion <span class="asterisco">*</span>
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este campo sirve para validar a partir de cuanto se puede aplicar el cup&oacute;n"></span>
                              </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">></span>
                                    </div>
                                  <input type="text" name="txt_restriccion" id="txt_restriccion" class="form-control" placeholder="" aria-describedby="basic-addon2" value="0">
                                  <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon6">$</span>
                                  </div>
                                </div>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                            <label>C&oacute;digo
                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Si este campo no se llena, el sistema crear&aacute; un c&oacute;digo aleatorio"></span>
                            </label>
                              <input type="text" name="txt_codigo" id="txt_codigo" class="form-control maxlength" required="required" autocomplete="off" maxlength="25" placeholder="Ej. MIEMPRESA2020" />
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Fecha de Expiraci&oacute;n <span class="asterisco">*</span></label>
                              <input name="fecha_expiracion" id="fecha_expiracion" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Seleccione fecha" value="">
                          </div>
                      </div>
                      <div class="form-group">
                                <div class="col-12 col-lg-3">
                                    <label>
                                        Uso Ilimitado 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Los clientes pueden utilizar este cupón sin límites"></span></label>
                                    <div>
                                        <label class="switch s-icons s-outline s-outline-success  mb-0">
                                            <input type="checkbox" class="ckIlimitado">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!--Modal Reporte-->
    <div class="modal fade bs-example-modal-lg" id="ventasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle usos del cupón <span id="cuponfilterText"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <h3>Total Ordenes con este cupón: <span id="totalOrders"></span></h3>
                    </div>
                    <input type="hidden" id="cuponfilter" value=""/>
                    <table  id="table-ordenes" class="table style-3 table-hover table-responsive " data-order='[[ 0, "desc"]]' style="margin-top: 10px !important;">
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
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
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
            <div class="layout-px-spacing">
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>C&oacute;digos Promocionales</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" id="btnOpenModal">Nuevo c&oacute;digo</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>C&oacute;digo</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                                <th>Cant. Inicial</th>
                                                <th>Usos restantes</th>
                                                <th>Restricci&oacute;n</th>
                                                <th>Expiraci&oacute;n</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $Clcodigos->lista();
                                            foreach ($resp as $cupon) {
                                                $badge='primary';
                                                if($cupon['estado'] == 'I')
                                                    $badge='danger';

                                                $tipo='Porcentaje (%)';  
                                                if($cupon['por_o_din'] == 1)
                                                    $tipo='Dinero ($)';
                                                echo '<tr data-value="'.$cupon['cod_codigo_promocional'].'">
                                                    <td>
                                                        <a class="btnCopiar" href="javascript:;" data-clipboard-action="copy" data-clipboard-text="'.$cupon['codigo'].'"><i data-feather="copy"></i></a>
                                                        '.$cupon['codigo'].'
                                                    </td>
                                                    <td>'.$tipo.'</td>
                                                    <td>'.$cupon['monto'].'</td>
                                                    <td>'.$cupon['cantidad'].'</td>
                                                    <td>'.$cupon['usos_restantes'].'</td>
                                                    <td>> $'.$cupon['restriccion'].'</td>
                                                    <td>'.$cupon['fecha_expiracion'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($cupon['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="'.$cupon['cod_codigo_promocional'].'" data-codigo="'.$cupon['codigo'].'"  class="bs-tooltip btnVentas" data-toggle="tooltip" data-placement="top" title="" data-original-title="Usos"><i data-feather="bar-chart"></i></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$cupon['cod_codigo_promocional'].'" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$cupon['cod_codigo_promocional'].'"  class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
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
    
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/codigo_promocional.js?v=2" type="text/javascript"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script>
        var myTable =  $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'pdf', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                "sInfoEmpty": "Mostrando pag. 1",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
               "sEmptyTable": "No se encontraron resultados",
               "sZeroRecords": "No se encontraron resultados",
               "buttons": {
                    "copy": "Copiar",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir",
                    "create": "Crear",
                    "edit": "Editar",
                    "remove": "Remover",
                    "upload": "Subir"
                }
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
        
        
        $('.bs-tooltip').tooltip();
        
        let tableProcessing = null;
        function loadDatatable(){
            tableProcessing = $('#table-ordenes').DataTable({
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
                    url:'./controllers/controlador_codigo_promociona.php?metodo=datatable',
                    type:'GET',
                    data: function(d){
                        // d.sucursal = $('#cmbSucursal').val();
                        d.codigo = $("#cuponfilter").val();
                    },
                    dataSrc: function(json) {
                        console.log('RESPONDIO EL AJAX', json);
                        $('#totalOrders').text(json.recordsTotal);
                        return json.data; // Muy importante retornar los datos
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
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>