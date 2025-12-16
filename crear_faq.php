<?php
require_once "funciones.php";
require_once "clases/cl_faq.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clfaq = new cl_faq(NULL);
$Clempresas = new cl_empresas();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$estado = "checked";

if(isset($_GET['id'])){
    $cod_faq = $_GET['id'];
    $emp = $Clempresas->get(cod_empresa);
    $tipo_emp = $emp['cod_tipo_empresa'];
    if($Clfaq->getArray($cod_faq, $faq)){
        $titulo = $faq['titulo'];
        $desc_corta = $faq['desc_corta'];
        $desc_larga = editor_decode($faq['desc_larga']);      
        $cod_tipo_empresa = $faq['cod_tipo_empresa'];
        $status = $faq['estado'];
        if($status == 'I')
            $estado = "";
    }
    else{
        header("location:index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
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

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true, "faq_lista.php"); ?>
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
                            <div><span id="btnBack" data-module-back="faq_lista.php" style="cursor: pointer;">
                                <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">FAQS</span></span>
                            </div>
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Crear FAQ</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div>
                            
                            <div class="table-responsive mb-4 mt-4">
                                <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="<?php echo $cod_faq;?>"/>
                                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                                    <div class="x_content">    
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Escribe el titulo" name="txt_titulo" id="txt_titulo" class="form-control" autocomplete="off" value="<?php echo $titulo;?>"/>
                                        </div>
                                        
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Para empresas tipo<span class="asterisco">*</span></label>
                                            <select id="cmb_faq" name="cmb_faq" class="form-control">
                                                <option value="0">Todas</option>
                                                <?php 
                                                    $tipos = $Clempresas->get_tipoem();
                                                    foreach ($tipos as $tipo) {
                                                        $select="";
                                                        if($cod_tipo_empresa == $tipo['cod_tipo_empresa'])
                                                            $select = "selected";
                                                        echo '<option '.$select.' value="'.$tipo['cod_tipo_empresa'].'">'.$tipo['tipo'].'</option>';
                                                    }
                                                ?>                              
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <!-- <div class="col-md-9 col-sm-9 col-xs-12" style="margin-bottom:10px;">
                                            <label>Tags <span class="asterisco">*</span></label>
                                            <select multiple="multiple" id="cmb_tag" name="cmb_tag[]" class="form-control tagging" required="required">
                                                </select>
                                        </div> -->
                                        <div class="col-md-9 col-sm-9 col-xs-12" style="margin-bottom:10px;">
                                            <label>Descripci&oacute;n Corta<span class="asterisco">*</span></label>
                                            <textarea id="txt_descripcion" name="txt_descripcion" class="form-control"><?php echo $desc_corta; ?></textarea>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px; text-align: center;">
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
                                            <label>Descripci&oacute;n  Larga<span class="asterisco">*</span></label>
                                            <textarea name="txt_descripcion_larga" id="editor1" class="form-control"><?php echo $desc_larga; ?></textarea>
                                        </div>
                                        
                                    </div>
                                    </div>
                                </form> 
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
    <script src="assets/js/pages/faq.js?v=1" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>   
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>