<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_paymentez.php";

if(!isLogin()){
    header("location:login.php");
}

/*
$session = getSession();
$Clusuarios = new cl_usuarios($session['cod_usuario']);
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = $files.$Clusuarios->imagen;
$nombres = $Clusuarios->nombre.' '.$Clusuarios->apellido;
$cod_empresa = $session['cod_empresa'];
$Clempresa = new cl_empresas();*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="plugins/pricing-table/css/component.css" rel="stylesheet" type="text/css" />
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
      
      #my-card input {
        height: 50px !important;
    }
    </style>
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

                <div class="row layout-spacing">

                    <div class="col-lg-12 layout-spacing layout-top-spacing">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <h4>Planes</h4>
                                    </div>           
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">

                                <div class="container">
                                    
                                    <!-- Billing Cycle  -->
                                    <div class="billing-cycle-radios">
                                        <div class="radio billed-yearly-radio">
                                            <div class="d-flex justify-content-center">
                                                <span class="txt-monthly">Mensual</span>
                                                <label class="switch s-icons s-outline  s-outline-primary">
                                                    <input type="checkbox" id="radio-6">
                                                    <span class="slider round"></span>
                                                </label>

                                                <span class="txt-yearly">Anual <span class="badge badge-pill badge-success">15% Off</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing Plans Container -->
                                    <div class="pricing-plans-container mt-5 d-md-flex d-block">
                                        <!-- Plan -->
                                        <div class="pricing-plan mb-5">
                                            <h3>Starter</h3>
                                            <p class="margin-top-10" style="height: 80px;">Incluye tienda, carrito de compras, botón de pagos.</p>
                                            <div class="pricing-plan-label billed-monthly-label"><strong>$45</strong>/ mensual</div>
                                            <div class="pricing-plan-label billed-yearly-label"><strong>$459</strong>/ anual</div>
                                            <div class="pricing-plan-features mb-4">
                                                <strong>Caracter&iacute;sticas</strong>
                                                <ul>
                                                    <li>Productos Administrables</li>
                                                    <li>Disponibilidad de productos</li>
                                                    <li>Gestión de Ordenes en tiempo real</li>
                                                    <li>Soporte</li>
                                                    <li>Mantenimiento</li>
                                                </ul>
                                            </div>
                                            <a href="javascript:void(0);" class="button btn btn-dark btn-block margin-top-20">Buy Now</a>
                                        </div>
                                        <!-- Plan -->
                                        <div class="pricing-plan mb-5 mt-md-0 recommended">
                                            <div class="recommended-badge">M&aacute;s Popular</div>
                                            <h3>Starter Plus</h3>
                                            <p class="margin-top-10" style="height: 80px;">Incluye tienda, carrito de compras, botón de pagos, courriers, banners, promociones, tracking en vivo.</p>
                                            <div class="pricing-plan-label billed-monthly-label"><strong>$65</strong>/ mensual</div>
                                            <div class="pricing-plan-label billed-yearly-label"><strong>$663</strong>/ anual</div>
                                            <div class="pricing-plan-features mb-4">
                                                <strong>Caracter&iacute;sticas</strong>
                                                <ul>
                                                    <li>Productos Administrables</li>
                                                    <li>Disponibilidad de productos</li>
                                                    <li>Gestión de Ordenes en tiempo real</li>
                                                    <li>Aplicación para motorizados</li>
                                                    <li>Promociones y Cupones de descuento</li>
                                                    <li>Tracking en vivo</li>
                                                    <li>Soporte</li>
                                                    <li>Mantenimiento</li>
                                                </ul>
                                            </div>
                                            <a href="javascript:void(0);" class="button btn btn-default btn-block margin-top-20">Buy Now</a>
                                        </div>
                                        <!-- Plan -->
                                        <div class="pricing-plan mb-5">
                                            <h3>A la medida</h3>
                                            <p class="margin-top-10" style="height: 80px;">Incluye tienda, carrito de compras, botón de pagos, courriers, banners, promociones, tracking en vivo, blog, diseño a la medida, UX/UI.</p>
                                            <div class="pricing-plan-label billed-monthly-label"><strong>$79</strong>/ mensual</div>
                                            <div class="pricing-plan-label billed-yearly-label"><strong>$809</strong>/ anual</div>
                                            <div class="pricing-plan-features mb-4">
                                                <strong>Caracter&iacute;sticas</strong>
                                                <ul>
                                                    <li>Productos Administrables</li>
                                                    <li>Disponibilidad de productos</li>
                                                    <li>Gestión de Ordenes en tiempo real</li>
                                                    <li>Aplicación para motorizados</li>
                                                    <li>Promociones y Cupones de descuento</li>
                                                    <li>Tracking en vivo</li>
                                                    <li>Diseño UX/UI</li>
                                                    <li>Front Page a la medida</li>
                                                    <li>Soporte</li>
                                                    <li>Mantenimiento</li>
                                                </ul>
                                            </div>
                                            <a href="javascript:void(0);" class="button btn btn-dark btn-block margin-top-20">Buy Now</a>
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
    <script>        
        var getInputStatus = document.getElementById('radio-6');
        var getPricingContainer = document.getElementsByClassName('pricing-plans-container')[0];
        var getYearlySwitch = document.getElementsByClassName('billed-yearly-radio')[0];
        getInputStatus.addEventListener('change', function() {
            var isChecked = getInputStatus.checked;
            if (isChecked) {
                getPricingContainer.classList.add("billed-yearly");
                getYearlySwitch.classList.add("billed-yearly-switch");
            } else {
                getYearlySwitch.classList.remove("billed-yearly-switch");
                getPricingContainer.classList.remove("billed-yearly");
            }
        })
    </script>
    
</body>
</html>