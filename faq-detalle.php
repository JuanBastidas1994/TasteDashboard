<?php
require_once "funciones.php";
require_once "clases/cl_faq.php";
require_once "clases/cl_empresas.php";

$Clfaq = new cl_faq(NULL);
$Clempresas = new cl_empresas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(!isLogin()){
    header("location:login.php");
}

if(isset($_GET['id'])){
    $cod_faq = $_GET['id'];
    $emp = $Clempresas->get(cod_empresa);
    $tipo_emp = $emp['cod_tipo_empresa'];
    if($Clfaq->getArrayDetalle($cod_faq, $tipo_emp, $faq)){
        $titulo = $faq['titulo'];
        $desc_corta = $faq['desc_corta'];
        $desc_larga = editor_decode($faq['desc_larga']);        
    }
    else{
        header("location:index.php");
    }
}
else{
    header("location:index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | Mi Ecommerce </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/main.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="assets/css/pages/helpdesk.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components/custom-modal.css" rel="stylesheet" type="text/css" />
    <!--  END CUSTOM STYLE FILE  -->    
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

    <div class="helpdesk container">
        <nav class="navbar navbar-expand navbar-light">
            <a class="navbar-brand" href="FAQ.php">FAQS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="contactanos.php">Cont&aacute;ctanos</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="helpdesk layout-spacing">

            <div class="hd-header-wrapper">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4 class=""><?php echo $titulo;?></h4>

                        <div class="row" style="margin-bottom: 50px;">
                            <div class="col-xl-6 col-lg-7 col-md-7 col-sm-11 col-11 mx-auto">
                                <div class="input-group mb-3">
                                
                                    <div class="input-group-prepend">
                                    &nbsp;<!-- <span class="input-group-text" id="bfilter"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span> -->
                                    </div>
                                    &nbsp;<!-- <input type="text" id="pfilter" class="form-control" placeholder="Encontremos tu pregunta de manera rápida"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hd-tab-section">
                <div class="row">
                    <div class="col-md-12 mb-5 mt-5">
                        <div class="">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div>
                                    <p><?php echo $desc_larga; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                            
            </div>

           

        </div>
    </div>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <script src="assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script>    
    
        $(document).on('click', '.arrow', function(event) {
          event.preventDefault();
          var body = $("html, body");
          body.stop().animate({scrollTop:0}, 500, 'swing');
        });

        $('body').on('click', '.openVideo', function(){
            var src = $(this).data("src");
            $('#videoMedia1').modal('show');
            $('<iframe>').attr({
                'src': src,
                'width': '560',
                'height': '315',
                'allow': 'encrypted-media'
            }).css('border', '0').appendTo('#videoMedia1 .video-container');
        });
        
        $(document).ready(function(){
            loadItems();
        });
        
        function loadItems(){
            var parametros = {
                filter: $("#pfilter").val()
            }
            
            $.ajax({
                url:'controllers/controlador_helpdesk.php?metodo=lista',
                data: parametros,
                type: "GET",
                success: function(response){
                    console.log(response);
                    if(response['success']==1){
                    	$("#hd-statistics").html(response['html']);
                    }else{
                        $("#hd-statistics").html('No hay resultados');
                    }
                },
                error: function(data){
                  console.log(data);  
                },
                complete: function()
                {
                  
                }
            });
        }
        
        $("#pfilter").on("keyup", function(){
            loadItems();
        });
        
        $("#bfilter").on("click", function(){
            loadItems();
        });
    </script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
</body>
</html>