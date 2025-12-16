<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_usuarios.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];

$rucs = $Clempresas->getRucs($session['cod_empresa']);
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <?php css_mandatory(); ?>
    <style>
        .addRuc{
            cursor: pointer;
        }
        
        .addRuc:hover{
            background: #c5cdf7;
        }
    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-md" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Ruc</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                        <div class="form-group">
                            <!-- RAZON SOCIAL -->
                            <div class="col-md-12 col-sm-12 col-xs-12 input-group" style="margin-bottom:10px;">
                                <label>Razón Social <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="El rol define que puede hacer el usuario dentro del sistema"></span>
                                </label>

                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i data-feather="user-check"></i></span>
                                    </div>
                                    <input type="text" placeholder="RAZON SOCIAL" name="txt_razon_social" id="txt_razon_social" class="form-control" required="required" autocomplete="off"/>
                                </div>
                            </div>
                            
                            <!-- RUC -->
                            <div class="col-md-12 col-sm-12 col-xs-12 input-group" style="margin-bottom:10px;">
                                <label>RUC <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="El rol define que puede hacer el usuario dentro del sistema"></span>
                                </label>

                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i data-feather="credit-card"></i></span>
                                    </div>
                                    <input type="text" placeholder="RUC" name="txt_ruc" id="txt_ruc" class="form-control" required="required" autocomplete="off"/>
                                </div>
                            </div>
                            
                            <!-- RUC -->
                            <div class="col-md-12 col-sm-12 col-xs-12 input-group" style="margin-bottom:10px;">
                                <label>Api token "Contífico" <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este valor te lo proporciona contífico cuando se solicita la integración vía API"></span>
                                </label>

                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i data-feather="git-pull-request"></i></span>
                                    </div>
                                    <textarea placeholder="TOKEN" name="txt_api_token" id="txt_api_token" class="form-control" required="required" autocomplete="off"></textarea>
                                    
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar" onclick="saveNewRuc()">Guardar</button>
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
                
                <div class="col-12 mt-4">
                    <h3 id="titulo">Mis Rucs</h3>
                    
                </div>
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <?php
                        foreach($rucs as $ruc){
                            echo '
                            <!-- Primer Ruc -->
                            <div class="widget-content widget-content-area br-6 mb-4">
                                <div>
                                    <div style="position: absolute; right: 10px;">
                                        <a href="ruc_detalle.php?id='.$ruc['cod_contifico_empresa'].'" class="btn btn-primary">Detalles</a>
                                    </div>
                                    <h4>'.$ruc['razon_social'].'</h4>
                                    <h5>'.$ruc['ruc'].'</h5>
                                    <div>
                                        <p>'.$ruc['ambiente'].'</p>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div><i data-feather="coffee"></i></div>
                                        <div class="ml-2">43 de 50 Productos ligados</div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <div><i data-feather="home"></i></div>
                                        <div class="ml-2">1 Punto de emisión</div>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                        
                        <div class="widget-content widget-content-area br-6 mb-4 addRuc">
                            <div class="text-center">
                                <h3>Agregar <i data-feather="plus-circle"></i></h3>
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
    <!--<script src="assets/js/pages/usuarios.js?v=2" type="text/javascript"></script>-->
    <script>
    $(document).ready(function() {
        feather.replace();
    });
    
    $(".addRuc").on("click", function(){
        $("#crearModal").modal();
    });
    
    function saveNewRuc() {
        OpenLoad("Guardando...");
        
        let info = {
            apitoken: $("#txt_api_token").val(),
            razon_social: $("#txt_razon_social").val(),
            ruc: $("#txt_ruc").val()
        };
        console.log(info);
        
        fetch(`controllers/controlador_contifico.php?metodo=addRuc`, {
            method: 'POST',
            body: JSON.stringify(info)
        })
            .then(res => res.json())
            .then(response => {
                console.log(response);
                if (response.success == 1) {
                    notify(response.mensaje, "success", 2);
                    $("#crearModal").modal('hide');
                    let ruc_id = response.ruc_id;
                    
                }
                else {
                    messageDone(response.mensaje,'error');
                }
                CloseLoad();
            })
            .catch(error => {
                CloseLoad();
                console.log(error);
            });
    }
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>