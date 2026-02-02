<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_clientes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();

$cod_empresa = $session['cod_empresa']; 
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <!--<link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
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
                <div  >
                    <div><a id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></a>
                    </div>
                    <h3 id="titulo">Calificaciones</h3>
                </div>
              </div>
              <div class="row">
                <div class="col-4">
                  <label for="">Sucursales</label>
                  <select class="form-control  basic" id="cmb_sucursal">
                      <option value="0">Todas las sucursales</option>
                        <?php
                        $resp = $Clsucursales->lista();
                        foreach ($resp as $sucursales) {
                          echo'<option value="'.$sucursales['cod_sucursal'].'">'.$sucursales['nombre'].'</option> ';
                        }
                        
                        ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 input-group">
                    <label>Fecha inicio</label>
                    <div class="input-group" style="background: white;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                        </div>
                        <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                    </div>
                </div>

                <div class="col-md-3 col-sm-3 col-xs-12 input-group">
                    <label>Fecha fin</label>
                    <div class="input-group" style="background: white;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                        </div>
                        <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_fin" id="fecha_fin">
                    </div>
                </div>
                <div class="col-1 d-flex align-items-center">
                  <button id="btnSearch" class="btn btn-primary ">Buscar</button>
                </div>
                <div class="col-12">
                  <hr style="border-top: 1px solid #d0d3d6; " />
                </div>
              </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="text-center card py-4">
                        <label style="margin: 0;">Promedio Total</label>
                        <div id="promedioTotal">

                          
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="text-center card py-4">
                        <label style="margin: 0;">Numero calificaciones</label>
                        <div id="total_calificaciones" style="font-size: 30px; font-weight: bold;">0</div>
                    </div>
                  </div>
                </div>
                <div class="row layout-top-spacing">
                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Lista de los mejores -->
                        <div class="widget-content widget-content-area br-6">
                          <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Experiencias Positivas</h4>
                            <span class="fw-bold" id="percent_positiva" style="font-size:18px;">0%</span>
                          </div>
                          <div class="x_content table-responsive">
                                <table id="style-3" class="table style-3  table-hover">
                                  <thead>
                                    <tr>
                                      <th>Calificacion</th>
                                      <th>Texto</th>
                                      <th>Fecha</th>
                                      <th>Tipo</th>
                                      <th>Sucursal</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </thead>
                                  <tbody id="tablaPositivas">
                                  </tbody>
                                </table>  

                              </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Ordenes --> 
                        <div class="widget-content widget-content-area br-6">
                          <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Experiencias Negativas</h4>
                            <span class="fw-bold" id="percent_negativa" style="font-size:18px;">0%</span>
                          </div>
                              <div class="x_content  table-responsive">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>Calificacion</th>
                                      <th>Texto</th>
                                      <th>Fecha</th>
                                      <th>Tipo</th>
                                      <th>Sucursal</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </thead>
                                  <tbody id="tablaNegativas">
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
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAHa67r_2hPqR_URtU8zsibmJx9Ahq7yGQ"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/orden_detalle.js" type="text/javascript"></script>
    <script src="assets/js/rating.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script>
      var inicio = flatpickr("#fecha_inicio", {
          dateFormat: "Y-m-d",
          onChange: function(selectedDates) {
              fin.set('minDate', selectedDates[0]);
          }
      });

      var fin = flatpickr("#fecha_fin", {
          dateFormat: "Y-m-d"
      });

      $("#btnSearch").on('click', function (event) {
          event.preventDefault();
          var parametros = {
              "cod_sucursal": $("#cmb_sucursal").val(),
              "fecha_inicio": $("#fecha_inicio").val(),
              "fecha_fin": $("#fecha_fin").val()
          }

          $.ajax({
              beforeSend: function () {
                  OpenLoad("Cargando datos, por favor espere...");
              },
              url: 'controllers/controlador_ordenes.php?metodo=getCalificacionesReport',
              type: 'POST',
              data: parametros,
              success: function (response) {
                  console.log(response);
                  let resumen = response.resumen;
                  $("#total_calificaciones").html(resumen.total);
                  $("#percent_positiva").html(resumen.porcentaje_positivas + '%');
                  $("#percent_negativa").html(resumen.porcentaje_negativas + '%');
                  $("#tablaPositivas").html('');
                  $("#tablaNegativas").html('');
                  response.positivas.forEach(item => agregarFilaCalificacion(item, 'tablaPositivas'));
                  response.negativas.forEach(item => agregarFilaCalificacion(item, 'tablaNegativas'));
                  $("#promedioTotal").html(`
                      <div class="review" 
                        data-rating-stars="5" 
                        data-rating-value="${resumen.promedio}" 
                        data-rating-half="true" 
                        data-rating-readonly="true" 
                        style="font-size: 30px;"
                      ></div>
                  `);
                  initRatings();
                  feather.replace();
              },
              error: function (data) {
                  console.log(data);
              },
              complete: function (resp) {
                  CloseLoad();
              }
          });
      });

      function agregarFilaCalificacion(item, tbodyId) {
        let envio = item.is_envio == 1 ? 'Enviado' : 'Pendiente';

        let tr = `
            <tr>
                <td>${item.calificacion}
                    <div class="review" 
                        data-rating-stars="5" 
                        data-rating-value="${item.calificacion}" 
                        data-rating-half="true" 
                        data-rating-readonly="true">
                    </div>
                </td>
                <td>${item.texto}</td>
                <td>${item.fecha}</td>
                <td>${envio}</td>
                <td>${item.sucursal}</td>
                <td>
                    <ul class="table-controls">
                        <li>
                            <a href="orden_detalle.php?id=${item.cod_orden}" target="_blank"
                                  class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="Ver Detalles">
                                <i data-feather="eye"></i>
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        `;

        $('#' + tbodyId).append(tr);
    }

    function initRatings() {
        $("[data-rating-stars]").each(function () {
            if (!$(this).attr("rating")) { // evita duplicar
                let d = {},
                    re_dataAttr = /^data-rating\-(.+)$/;

                $.each(this.attributes, function () {
                    if (re_dataAttr.test(this.nodeName)) {
                        let key = this.nodeName.match(re_dataAttr)[1];
                        d[key] = this.nodeValue;
                    }
                });

                $(this).rating(d);
            }
        });
    }

    </script>


</body>
</html>