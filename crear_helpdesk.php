<?php
require_once "funciones.php";
require_once "clases/cl_helpdesk.php";

if(!isLogin()){
    header("location:login.php");
}

$clHelp = new cl_helpdesk(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$estado = "checked";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="assets/css/components/custom-modal.css" rel="stylesheet" type="text/css" />
    <!--  END CUSTOM STYLE FILE  -->   
    
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
      .select2-container{
          z-index: 999999999 !important;
      }
    </style>
</head>
<body>
    <!-- Modal -->
    <div id="modalVideo" class="col-lg-12 layout-spacing modal-video">
        <div class="modal fade" id="videoMedia1" tabindex="-1" role="dialog" aria-labelledby="videoMedia1Label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" id="videoMedia1Label">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="video-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
    
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR HELPDESK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>T&iacute;tulo <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Escribe el titulo" name="txt_titulo" id="txt_titulo" class="form-control" autocomplete="off"/>
                          </div>
                          
                           <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>URL Video<span class="asterisco">*</span></label>
                              <input type="text" placeholder="https://" name="txt_url" id="txt_url" class="form-control" autocomplete="off"/>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-9 col-sm-9 col-xs-12" style="margin-bottom:10px;">
                              <label>Tags <span class="asterisco">*</span></label>
                              <select multiple="multiple" id="cmb_tag" name="cmb_tag[]" class="form-control tagging" required="required">
                                </select>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                             <label>Estado <span class="asterisco">*</span></label>
                              <div>
                                  <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                      <input type="checkbox" name="chk_estado" id="chk_estado" <?php echo $estado; ?> />
                                      <span class="slider round"></span>
                                  </label>
                              </div>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Descripci&oacute;n Corta<span class="asterisco">*</span></label>
                              <textarea id="txt_descripcion" name="txt_descripcion" class="form-control"></textarea>
                          </div>

                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Descripci&oacute;n  Larga<span class="asterisco">*</span></label>
                              <textarea name="txt_descripcion_larga" id="editor1" class="form-control"></textarea>
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
                                    <h4>Helpdesk</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" data-toggle="modal" id="btnOpenModal">Nuevo item</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div>
                            
                            <div class="table-responsive mb-4 mt-4">
                            <table id="style-3" class="table style-3 table-hover dt-responsive">
                              <!--  <table class="table style-3 table-hover dt-responsive">-->
                                        <thead>
                                            <tr>
                                                <th>Titulo</th>
                                                <th>Descripci&oacute;n</th>
                                                <th>Posici&oacute;n</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstBanners" class="connectedSortable">
                                            <?php
                                            $resp = $clHelp->lista();
                                            foreach ($resp as $help) {
                                                $video = $help['video'];
                                                $badge='primary';
                                                if($help['estado'] == 'I')
                                                    $badge='danger';
                                                echo '<tr id="'.$help['cod_helpdesk'].'"  data-codigo="'.$help['cod_helpdesk'].'">
                                                    <td>'.$help['titulo'].'</td>
                                                    <td>'.$help['desc_corta'].'</td>
                                                    <td>'.$help['posicion'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($help['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="'.$help['cod_helpdesk'].'" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>

                                                            <li><a href="javascript:void(0);" data-value="'.$help['cod_helpdesk'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                                            
                                                            <li><a href="javascript:void(0);" data-value="'.$help['cod_helpdesk'].'" class="bs-tooltip btnVideo openVideo" data-src="'.$video.'" data-toggle="tooltip" data-placement="top" title="Ver Video" data-original-title="Delete"><i data-feather="video"></i></a></li>
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
    <script src="assets/js/pages/helpdesk.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="plugins/ckeditor/ckeditor.js"></script>
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
            "pageLength": 7,
            "order": [[ 3, "asc" ]],
            responsive: true
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>