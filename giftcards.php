<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_giftcards.php";

if(!isLogin()){
    header("location:login.php");
}

$Clcategorias = new cl_categorias(NULL);
$Clgiftcards = new cl_giftcards(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
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
      </style>
</head>
<body>
     <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ordenar items</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                
                <div class="modal-body">
                    <table class="table style-3  table-hover">
                        <tbody id="moveCategorias" class="connectedSortable"> 
                            <?php
                         
                            $resp = $Clgiftcards->lista();
                            foreach ($resp as $g) {
                                echo '<tr data-id="'.$g['cod_giftcard'].'">
                                        <td>* '.$g['nombre'].' </td>
                                      </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    
                 
                </div>
                
                
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
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

   <div class="modal fade bs-example-modal-lg" id="crearGiftcards" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR GIFTCARDS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">

                      <div class="form-group">
                           <div class="col-md-3 col-sm-3 col-xs-12">
                                    <div class="upload mt-1 pr-md-1">
                                        <input type="file" name="img_product" id="dropifyPerfil" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="15M" data-allowed-file-extensions="jpeg jpg png"/>
                                        <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                    </div>
                            </div>

                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Nombre <span class="asterisco">*</span></label>
                              <input name="txt_nombre" id="txt_nombre" class="form-control flatpickr-input active" type="text" placeholder="" value="" required="required">
                          </div>
                          <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Montos <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoja los productos que el usuario tendra que decidir a escoger"></span><span><i>&nbsp;Separar las opciones con una coma</i></span></label>
                                        <select multiple="multiple" name="cmb_montos[]" id="cmb_montos" class="form-control tagging" >
                                          </select>
                          </div>
                           <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                        <label>Estado <span class="asterisco">*</span></label>
                                        <div>
                                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                <input type="checkbox" name="chk_estado" id="chk_estado" checked>
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
                                <div class="col-xl-4 col-md-4 col-sm-8 col-8">
                                    <h4>Giftcards</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <a class="btn btn-primary"  id="btnOpenModal">Ordenar Giftcards</a>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <a class="btn btn-primary btnCrearG" id="btnCrearG">Nueva Giftcards</a>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th class="checkbox-column text-center"> Record Id </th>
                                                <th class="text-center">Image</th>
                                                <th>Montos</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $Clgiftcards->lista();
                                            foreach ($resp as $giftcard) {
                                                $imagen = $files.$giftcard['imagen'];
                                                $badge='primary';
                                                if($giftcard['estado'] == 'I')
                                                    $badge='danger';
                                                echo '<tr>
                                                    <td class="checkbox-column text-center"> '.$giftcard['cod_giftcard'].' </td>
                                                    <td class="text-center">
                                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                    </td>
                                                    <td>'.$giftcard['nombre'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($giftcard['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a class="bs-tooltip btnEditarG" data-toggle="tooltip" data-placement="top" data-value="'.$giftcard['cod_giftcard'].'" data-info="'.$giftcard.'" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$giftcard['cod_giftcard'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
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
    <script src="assets/js/pages/giftcards.js" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
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
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>