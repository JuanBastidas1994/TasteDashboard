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
<?php echo navbar(true); ?>
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
                        <div class="form-row mb-4 d-none" id="business-row">
                            <div class="col-4">
                                <label for="business">Empresa</label>
                                <select name="business" id="business" class="form-control">
                                    <option value="">Seleccione un restaurante</option>
                                </select>
                            </div>
                        </div>    

                        <div class="form-row mb-4">
                            <div class="col-4">
                                <input type="text" id="cod_taste_portfolio" class="form-control">
                                <input type="text" id="businessId" class="form-control">
                                <input type="text" id="path" class="form-control">
                                <label for="image">Imagen</label>
                                <div>
                                    <img src="" alt="Imagen del restaurante" id="img" class="img-fluid mb-2" width="100">
                                </div>
                                <input type="file" name="image" id="image" class="form-control">
                            </div>
                        </div>
                        <div class="form-row mb-4">
                            <div class="col-4">
                                <label for="name">Nombre del Restaurante</label>
                                <input type="text" id="name" class="form-control" readonly>
                            </div>
                            <div class="col-4">
                                <label for="name">URL</label>
                                <input type="text" id="url" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-row mb-4">
                            <div class="col-4">
                                <label for="name">Categorías</label>
                                <select name="categories[]" id="categories" class="form-control" required="required" multiple>
                                    <option value="hamburguesas"> Hamburguesas </option>
                                    <option value="sánduches"> Sánduches </option>
                                    <option value="brunch & breakfrast"> Brunch & Breakfrast </option>
                                    <option value="fast fruit"> Fast Fruit </option>
                                    <option value="árabe"> Árabe </option>
                                    <option value="platos fuertes"> Platos Fuertes </option>
                                    <option value="fast food"> Fast Food </option>
                                    <option value="pizza"> Pizza </option>
                                    <option value="market"> Market </option>
                                    <option value="antojo dulce"> Antojo Dulce </option>
                                    <option value="japonesa"> Japonesa </option>
                                    <option value="cortes de carne"> Cortes De Carne </option>
                                    <option value="típicos"> Típicos </option>
                                    <option value="hamburguesas"> Hamburguesas </option>
                                    <option value="panadería"> Panadería </option>
                                    <option value="sea food"> Sea Food </option>
                                    <option value="helados"> Helados </option>
                                    <option value="cafetería"> Cafetería </option>
                                </select>

                            </div>
                            <div class="col-4">
                                <label for="name">Ciudades</label>
                                <select name="cities[]" id="cities" class="form-control" required multiple>
                                    <option value="guayaquil"> Guayaquil </option>
                                    <option value="cuenca"> Cuenca </option>
                                    <option value="quito"> Quito </option>
                                    <option value="salinas"> Salinas </option>
                                    <option value="manta"> Manta </option>
                                    <option value="ambato"> Ambato </option>
                                    <option value="macas"> Macas </option>
                                </select>
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



<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<?php js_mandatory(); ?>
<script src="./assets/js/libs/handlebars/handlebars.js"></script>
<script src="./assets/js/libs/handlebars/helpers.js"></script>

<script src="assets/js/pages/taste_portfolio_detail.js" type="text/javascript"></script>

<script id="portfolio-row-template" type="text/x-handlebars-template">
    {{#each this}}
        <tr>
            <td>{{nombre}}</td>
            <td>{{url_web}}</td>
            <td class="text-center">
                <ul class="table-controls">
                    <li>
                        <a href="taste_portfolio_detail.php?id={{cod_taste_portafolio}}" target="_blank">
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