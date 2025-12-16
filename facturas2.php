<?php
require_once "funciones.php";

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
/* if(!userGrant()){
    header("location:index.php");
} */

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gb18030">
    <?php css_mandatory(); ?>
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

      .respGalery > div {
          margin-top: 15px;
      }

      .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
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
                
                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Facturas</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-4 col-12">
                                        <label>RUC</label>
                                        <select id="cmbRuc" class="form-control">

                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-12">
                                        <label>Fecha inicio</label>
                                        <input type="date" id="fecha_inicio" class="form-control picker">
                                    </div>
                                    <div class="col-lg-3 col-12">
                                        <label>Fecha fin</label>
                                        <input type="date" id="fecha_fin" class="form-control picker">
                                    </div>
                                    <div class="col-lg-2 col-12 align-items-end d-flex">
                                        <button class="btn btn-primary" onclick="getFacturas();">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Orden</th>
                                                <th>Tipo</th>
                                                <th>Num. Doc</th>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- LISTA DE FACTURAS -->
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
    
    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <script src="./assets/js/pages/facturas.js"></script>

    <!-- TEMPLATES -->
    <script id="rucs-template" type="text/x-handlebars-template">
        {{#each this}}
            <option value="{{cod_contifico_empresa}}">{{razon_social}} - {{ruc}}</option>
        {{/each}}
    </script>
    <script id="documentos-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr>
                <td>{{cod_orden_factura_electronica}}</td>
                <td>{{cod_orden}}</td>
                <td>{{tipo}}</td>
                <td>{{num_factura}}</td>
                <td>{{fecha}}</td>
                <td>{{cliente}}</td>
                <td class="text-center">
                    <span class="badge badge-{{colorStatus estado}}">
                        {{estado}}
                    </span>
                </td>
                <td class="text-center">
                    <ul class="table-controls">
                        <li>
                            <a href="./orden_detalle.php?id={{cod_orden}}" target="_blank">
                                <i data-feather="eye"></i>
                            </a>
                           <!--  <a href="javascript:void(0);">
                                <i data-feather="refresh-cw"></i>
                            </a> -->
                        </li>
                    </ul>
                </td>
            </tr>
        {{/each}}
    </script>
</body>
</html>