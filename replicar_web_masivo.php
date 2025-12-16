<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = url_sistema.'/assets/img/200x200.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
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

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/elements/alert.css">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
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
                
                <div class="row layout-top-spacing">
                    <div class="col-md-12" style="margin-top:25px; ">
                        <h3 id="titulo">Replicador Masivo</h3>
                        
                        <div class="d-flex">
                            <div>
                                <select class="form-control" id="cmbVersion" >
                                <?php
                                    $versiones = $Clempresas->getVersionesWeb();
                                    foreach($versiones as $version){
                                        extract($version);
                                        $name = "$title ($version)";
                                        echo '<option value="'.$filename.'">'.$name.' ('.$descripcion.')</option>';
                                    }
                                ?>
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary btnReplicar">Replicar</button>
                            </div>
                        </div>
                        
                        
                        <p>Sólo las empresas con una carpeta asignada serán actualizadas</p>
                    </div>

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-lg-12 col-ms-12 col-xs-12">   
                                <table class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>chk</th>
                                            <th>Empresa</th>
                                            <th>Carpeta</th>
                                            <th class="text-center">Ambiente</th>
                                            <th class="text-center">Hosting</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $empresas = $Clempresas->getEmpresasPorTipo(1); //RESTAURANTES
                                        foreach ($empresas as $empresa) {
                                            $badge = ($empresa['ambiente'] == 'production') ? 'success' : 'danger';
                                            $web = ($empresa['url_web'] == '') ? '' : 'https://'.$empresa['url_web'];
                                            $hosting = ($empresa['hosting'] !== '') ? $empresa['hosting'] : 'NO DEFINIDO';
                                            
                                            $disabled = '';
                                            $checked = ($empresa['ambiente'] == 'production') ? 'checked' : '';
                                            
                                            if($empresa['folder'] == ''){
                                                $checked = '';
                                                $disabled = 'disabled';
                                            }
                                            if($hosting !== 'taste'){
                                                $checked = '';
                                                $disabled = 'disabled';
                                            }
                                            $badgeHosting = ($hosting == 'taste') ? 'success' : 'danger';
                                            
                                            echo '
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="chk_replicar" '.$checked.' '.$disabled.'
                                                        data-id="'.$empresa['cod_empresa'].'"
                                                        data-alias="'.$empresa['alias'].'"
                                                        data-folder="'.$empresa['folder'].'"
                                                        style="width: 30px; height: 30px;"
                                                    />
                                                </td>
                                                <td>'.$empresa['nombre'].'</td>
                                                <td>'.$empresa['folder'].'</td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.$empresa['ambiente'].'</span></td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badgeHosting.'">'.$hosting.'</span></td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                        <li><a href="crear_empresa.php?id='.$empresa['alias'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                        <li><a href="'.$web.'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="globe"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>';
                                        }
                                        ?>
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div>&nbsp;</div>    
                                        
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
    <!-- Mapas -->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDe9LjbQR0UAc8PMVJXc66flE7yqrJbD6o&libraries=places"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/replicar_web_masivo.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>