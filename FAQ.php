<?php
require_once "funciones.php";
require_once "clases/cl_faq.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

//Clases
$Clempresas = new cl_empresas(NULL);
$Clfaq = new cl_faq(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
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
            <a class="navbar-brand" href="#">FAQ</a>
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
                        <h4 class="">MI ECOMMERCE</h4>
                        <p class="">Preguntas frecuentes del Dashboard </p>

                        <div class="row">
                            <div class="col-xl-6 col-lg-7 col-md-7 col-sm-11 col-11 mx-auto">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="bfilter"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                                    </div>
                                    <input type="text" id="pfilter" class="form-control" placeholder="Encontremos tu pregunta de manera rápida">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hd-tab-section">
                <div class="row">
                    <div class="col-md-12 mb-5 mt-5">
                        <div class="accordion" id="hd-statistics">
                            
                        </div>
                    </div>
                </div>                            
            </div>

            <div class="hd-contact-section">
                <div class="hd-slider">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                </ol>
                                <div class="carousel-inner">
                                  <div class="carousel-item active community-help">
                                      <div class="carousel-item-content">
                                          <h4 class="hd-slide-header">Get help by community.</h4>
                                          <div class="row">
                                              <div class="col-md-6">
                                                  <div class="media">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                      <div class="media-body">
                                                          <h5>CORK Forum</h5>
                                                          <p class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</p>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="col-md-6">
                                                  <div class="media">
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-terminal"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>
                                                      <div class="media-body">
                                                          <h5>How to Code</h5>
                                                          <p class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</p>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="carousel-item news-updates ">
                                      <div class="carousel-item-content">
                                          <h4 class="hd-slide-header">Latest news and updates</h4>
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="media">
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                                      <div class="media-body">
                                                          <h5>CORK Blog</h5>
                                                          <p class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="miniFooterWrapper" class="">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="position-relative">
                        <div class="arrow text-center">
                            <p class="">Up</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 mx-auto col-lg-6 col-md-6 site-content-inner text-md-left text-center copyright align-self-center">
                            <p class="mt-md-0 mt-4 mb-0">2021 &copy; <a target="_blank" href="https://digitalmindtec.com">Digital Mind</a>.</p>
                        </div>
                        <div class="col-xl-5 mx-auto col-lg-6 col-md-6 site-content-inner text-md-right text-center align-self-center">
                            <p class="mb-0">Entre Ríos, Calle Segunda, Samborondón</p>
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
            let filter = $("#pfilter").val();
            var parametros = {
                "filter": filter
            }

            // if(filter.trim() == "")
            //     return;
            
            $.ajax({
                url:'controllers/controlador_faq.php?metodo=lista',
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