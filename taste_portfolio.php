<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
</head>
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
                <div><span id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                        <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                </div>
                <h3 id="titulo">Restaurantes</h3>
            </div>
            <div class="row layout-top-spacing">

                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                    <div class="widget-content widget-content-area br-6">
                        <div class="table-responsive mb-4 mt-4">
                            <table id="style-3" class="table style-3  table-hover">
                                <thead>
                                    <tr>
                                        <th>Restaurantes</th>
                                        <th>URL</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    
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



<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<?php js_mandatory(); ?>
<script src="./assets/js/libs/handlebars/handlebars.js"></script>
<script src="./assets/js/libs/handlebars/helpers.js"></script>

<script src="assets/js/pages/taste_portfolio.js" type="text/javascript"></script>

<script id="portfolio-row-template" type="text/x-handlebars-template">
    {{#each this}}
        <tr>
            <td>{{nombre}}</td>
            <td>{{url_web}}</td>
            <td class="text-center">
                <ul class="table-controls">
                    <li>
                        <a href="taste_portfolio_detail.php?id={{cod_taste_portafolio}}" target="_blank" >
                            <i data-feather="eye"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
    {{/each}}
</script>

<!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>