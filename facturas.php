<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/apps/invoice.css" rel="stylesheet" type="text/css" />
    <!--  END CUSTOM STYLE FILE  -->
    
</head>
<body>
    
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
                <div class="row invoice layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="app-hamburger-container">
                            <div class="hamburger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu chat-menu d-xl-none"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></div>
                        </div>
                        <div class="doc-container">
                            <div class="tab-title">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="search">
                                            <input type="text" class="form-control" placeholder="Search...">
                                        </div>
                                        <ul class="nav nav-pills inv-list-container d-block itemOrdenes" id="pills-tab" role="tablist">
                                            <?php
                                            $resp = $Clordenes->lista();
                                            foreach ($resp as $orden) {
                                                $id = $orden['cod_orden'];
                                                $nombre = $orden['nombre'].' '.$orden['apellido'];
                                                $fecha = fechaLatinoShort($orden['fecha']);
                                                echo '<li class="nav-item">
                                                        <div class="nav-link list-actions orden-detalle" id="invoice-00001" data-invoice-id="'.$id.'">
                                                            <div class="f-m-body">
                                                                <div class="f-head">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                                                </div>
                                                                <div class="f-body">
                                                                    <p class="invoice-number">Invoice #00001</p>
                                                                    <p class="invoice-customer-name"><span>To:</span> '.$nombre.'</p>
                                                                    <p class="invoice-generated-date">Date: '.$fecha.'</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>';
                                            }    
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="invoice-container">
                                <div class="invoice-inbox">

                                    <div class="inv-not-selected">
                                        <p>Abra una orden de la lista.</p>
                                    </div>

                                    <div class="invoice-header-section">
                                        <h4 class="inv-number"></h4>
                                        <div class="invoice-action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Reply"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        </div>
                                    </div>
                                    
                                    <!--DETALLE DE LA ORDEN-->
                                    <div id="ct" class="respDetalleOrden">
                                        
                                    </div>
                                    <!--DETALLE DE LA ORDEN-->

                                </div>

                                <div class="inv--thankYou">
                                    <div class="row">
                                        <div class="col-sm-12 col-12">
                                            <p class="">Thank you for doing Business with us.</p>
                                        </div>
                                    </div>
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
    <script src="plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/apps/invoice.js"></script>
    <script src="assets/js/pages/gestion_ordenes.js" type="text/javascript"></script>
</body>
</html>