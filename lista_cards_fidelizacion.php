<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_cards_fidelizacion.php";
require_once "clases/cl_clientes.php";

error_reporting(E_ALL);

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
$cod_empresa = $session['cod_empresa'];
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

//CLASES
$Clempresas = new cl_empresas();
$Clcards = new cl_cards();
$Clclientes = new cl_clientes();
$empresa = $Clempresas->get($session['cod_empresa']);
$cod_tipo_empresa = $empresa['cod_tipo_empresa'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style>
        .text-yellow{
            color: yellow;
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
                    <div style="background-color: white; border-radius: 10px;">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Tarjetas de Fidelizaci&oacute;n</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4" id="divDatos">
                                <h3 style="margin-top: 50px;">C&oacute;digos</h3>
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th>C&oacute;digos</th>
                                            <th>Clientes</th>
                                            <th>Acci&oacute;n</th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        <?php
                                            $cards = $Clcards->lista($cod_empresa);
                                            foreach ($cards as $card) {
                                                $accion = "";
                                                $nombreCliente = '<span class="text-info campoNombre"> Sin asignar </span>';
                                                if($card['cod_cliente'] > 0){
                                                    $row = $Clclientes->getById($card['cod_cliente']);
                                                    if($row){
                                                        $nombreCliente = '<span class="text-success campoNombre">'.$Clclientes->nombre.'</span>';
                                                        $accion = ' <ul class="table-controls">
                                                                        <li><a target="_blank" href="cliente_detalle.php?id='.$Clclientes->cod_usuario.'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"><i data-feather="eye"></i></a></li>
                                                                        
                                                                        <li><a href="javascript:void(0);" class="bs-tooltip btnVaciarCard" data-tarjeta="'.$card['cod_card_fidelizacion'].'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Limpiar"><i data-feather="x"></i></a></li>
                                                                    </ul>';
                                                    }
                                                    else
                                                        $nombreCliente = '<span class="text-danger"> Cliente no existe </span>';
                                                }

                                                echo "  <tr>
                                                            <td>".$card['codigo']."</td>
                                                            <td>".$nombreCliente."</td>
                                                            <td>".$accion."</td>
                                                        </tr>";
                                            }
                                        ?>
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
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>    
    <?php js_mandatory(); ?>

    <script src="assets/js/pages/importar_cards_fidelizacion.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>