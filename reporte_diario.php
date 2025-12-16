<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_productos.php";

require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);
$Clcategorias = new cl_categorias(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session["cod_rol"];
$cod_sucursal = 0;

$selectedOffice = "selected";
$enabled = "";
if($cod_rol == 3){
    $enabled = "disabled";
    $selectedOffice = "";
    $cod_sucursal = $session["cod_sucursal"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
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

      .respGalery > div {
          margin-top: 15px;
      }

      .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
      }
      .align-right{
          text-align: right
      }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>
<body>
    <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RECORTADOR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                            <input type="hidden" id="txt_crop" name="txt_crop" value="" />
                            <input type="hidden" id="txt_crop_min" name="txt_crop_min" value="" />
                             <img id="my-image" src="#" style="width: 100%; max-height: 400px;"/>
                          </div>
                         
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
                    <button class="btn btn-dark crop-rotate" data-deg="-90">Rotate Left</button>
                    <button class="btn btn-dark crop-rotate" data-deg="90">Rotate Right</button>
                    <button type="button" class="btn btn-primary" id="crop-get">Recortar</button>
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Reporte diario</h3>
                </div>

                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                              <div class="x_content">   
                               

                                <div class="form-row">
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                          <label>Sucursales <span class="asterisco">*</span></label>
                                          <select class="form-control  basic" id="cmb_sucursal" <?=$enabled?>>
                                              <option value="0" <?=$selectedOffice?>>Todas las sucursales</option>
                                               <?php
                                               $resp = $clsucursales->lista();
                                               foreach ($resp as $sucursales) {
                                                $selectedOffice = "";
                                                if($sucursales["cod_sucursal"] == $cod_sucursal)
                                                    $selectedOffice = "selected";

                                                 echo'<option value="'.$sucursales['cod_sucursal'].'" '.$selectedOffice.'>'.$sucursales['nombre'].'</option> ';
                                               }
                                               
                                               ?>
                                          </select>
                                      </div>
                                    
                                    
                                      <div class="col-md-3 col-sm-3 col-xs-12 input-group" style="margin-bottom:10px;">
                                          <label>Fecha</label>
            
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                            </div>
                                            <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                                        </div>
                                      </div>

                                      <div class="col-xl-3 col-md-3 col-sm-3 col-3">
                                          <button class="btn btn-primary btnReporte" style="margin-top: 30px;" >Generar reporte</button>
                                      </div>
                                      
                                      
                                </div>

                               

                                
                                </div>  
                              </form>
                        </div>
                    </div>

                    <div id="Content-tabs" style="display: none;">
                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-9 layout-spacing">
                            <div id="bloqueTabla" class="widget-content widget-content-area br-6">
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha Creaci&oacute;n</th>
                                            <th>Nombres</th>
                                            <th>N&#186; de Identificaci&oacute;n</th>
                                            <th>Monto</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbInfo">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">
                                                <ul class="table-controls">
                                                    <li><a href="orden_detalle.php?id='.$orden['cod_orden'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver m&aacute;s informaci&oacute;n"><i data-feather="eye"></i></a></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3 layout-spacing">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing" id="Content-tabs" style="display:initial">
                                <div id="bloque1" class="widget-content widget-content-area br-6 align-right">
                                    
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                <div id="bloque2" class="widget-content widget-content-area br-6 align-right">
                                    <p align="center"><strong>Formas de Pago</strong></p>
                                    
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                <div id="bloque3" class="widget-content widget-content-area br-6 align-right">
                                    
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
    <script src="assets/js/pages/reporte_diario.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
 <script>
    function cargarComponentes(){
            $('#style-3').DataTable( {
                dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                buttons: {
                    buttons: [
                        { extend: 'copy', className: 'btn' },
                        { extend: 'excel', className: 'btn' },
                        { extend: 'pdf', className: 'btn' },
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
                        "excel": "Excel",
                        "pdf": "PDF",
                        "create": "Crear",
                        "edit": "Editar",
                        "remove": "Remover",
                        "upload": "Subir"
                    }
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 20,
                "order": [[ 0, "desc" ]]
            } );    
        }
</script>
</body>
</html>