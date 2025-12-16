<?php
require_once "funciones.php";
error_reporting(E_ALL);
require_once "clases/cl_promociones.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_sucursales.php";


if(!isLogin()){
    header("location:login.php");
}

$Clpromociones = new cl_marketing_envios(NULL);
$Clsucursales = new cl_sucursales(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style type="text/css">
      .dropify-wrapper {
          display: block;
          position: relative;
          cursor: pointer;
          overflow: hidden;
          width: 100%;
          max-width: 100%;
          height: 110px !important;
          padding: 5px 10px;
          font-size: 14px;
          line-height: 22px;
          color: #777;
          background-color: #fff;
          background-image: none;
          text-align: center;
          border: 0 !important;
          -webkit-transition: border-color .15s linear;
          transition: border-color .15s linear;
      }

     .select2-container {
	    z-index: 999999999 !important;
	}
    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR DESCUENTO ENVIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">
                    	<div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <div class="d-flex align-items-center">
                                    <label class="switch s-icons s-outline s-outline-success m-0">
                                        <input type="checkbox" class="ckHours" name="ckHours" id="ckHours">
                                        <span class="slider round"></span>
                                    </label>
                                    <label>Respetar sólo en horario específico</label>
                                </div>
                                <div class="row px-5 lstDays" style="display: none;">
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="1" checked> Lunes
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="2" checked> Martes
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="3" checked> Miércoles
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="4" checked> Jueves
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="5" checked> Viernes
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="6" checked> Sábado
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input ckDays" type="checkbox" value="7" checked> Domingo
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Descuento <span class="asterisco">*</span></label>
                            </div>
                          	<div class="form-group col-md-6 col-sm-6 col-xs-6" style="margin-bottom:10px;">
                            		<div>
                            			<label class="new-control new-radio radio-classic-primary">
              								      <input type="radio" class="new-control-input" name="rb_descuento" checked="checked" value="0">
              								      <span class="new-control-indicator"></span>Gratis (100%)
              								    </label>
                            		</div>
                                <div>
                            			<label class="new-control new-radio radio-classic-primary">
              								      <input type="radio" class="new-control-input" name="rb_descuento" value="1">
              								      <span class="new-control-indicator"></span>Porcentaje
              								    </label>
                            		</div>
                          	</div>
                          	<div class="form-group col-md-6 col-sm-6 col-xs-6" style="margin-bottom:10px;">
                              <div class="input-group mb-5">
              								  <input type="text" name="txt_porcentaje" id="txt_porcentaje" class="form-control" placeholder="Porcentaje de descuento" aria-label="Porcentaje de descuento" aria-describedby="basic-addon2">
              								  <div class="input-group-append">
              								    <span class="input-group-text" id="basic-addon6">%</span>
              								  </div>
              								</div>
                          	</div>
                      </div>

                      <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Monto M&iacute;nimo <span class="asterisco">*</span></label>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-xs-6" style="margin-bottom:10px;">
                                <div>
                                  <label class="new-control new-radio radio-classic-primary">
                                    <input type="radio" class="new-control-input" name="rb_monto" checked="checked" value="1">
                                    <span class="new-control-indicator"></span>Mayor o igual a
                                  </label>
                                </div>
                                <div>
                                  <label class="new-control new-radio radio-classic-primary">
                                    <input type="radio" class="new-control-input" name="rb_monto" value="0">
                                    <span class="new-control-indicator"></span>sin restricci&oacute;n (Desde $0 en adelante)
                                  </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-xs-6" style="margin-bottom:10px;">
                              <div class="input-group mb-5">
                                <div class="input-group-append">
                                  <span class="input-group-text" id="basic-addon6">$</span>
                                </div>
                                <input type="text" name="txt_monto" id="txt_monto" class="form-control" placeholder="Monto" aria-label="Monto" aria-describedby="basic-addon2">
                                
                              </div>
                            </div>
                      </div>

                      <div class="form-group">
                          <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              	<label>Sucursales <span class="asterisco">*</span>
                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoja las sucursales donde se aplicar&aacute; el descuento"></span></label>
                              	<select multiple="multiple" name="cmb_sucursales[]" id="cmb_sucursales" class="form-control" required="required">
                            	<?php
                            	    $resp = $Clsucursales->lista();
                            	    foreach ($resp as $sucursal) {
                            	    	echo '<option value="'.$sucursal['cod_sucursal'].'">'.$sucursal['nombre'].'</option>';
                            	  	}
                            	?>
                            	</select>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Inicio <span class="asterisco">*</span></label>
                              <input name="fecha_inicio" id="hora_ini" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="" required>
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Fin <span class="asterisco">*</span></label>
                              <input name="fecha_fin" id="hora_fin" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="" required>
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
                                    <h4>Env&iacute;os</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#crearModal">Nueva Promoci&oacute;n</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Descuento</th>
                                                <th>Monto M&iacute;nimo</th>
                                                <th>Inicio</th>
                                                <th>Fin</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $Clpromociones->lista();
                                            foreach ($resp as $promocion) {
                                                echo '<tr>
                                                    <td>'.$promocion['sucursal'].'</td>
                                                    <td>'.$promocion['porcentaje'].'%</td>
                                                    <td>$'.number_format($promocion['monto'],2).'</td>
                                                    <td>'.$promocion['fecha_inicio'].'</td>
                                                    <td>'.$promocion['fecha_fin'].'</td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="'.$promocion['cod_marketing_envio'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a></li>
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
    <script src="assets/js/pages/marketing_envios.js" type="text/javascript"></script>
    <script>
        $('#style-3').DataTable( {
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
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>