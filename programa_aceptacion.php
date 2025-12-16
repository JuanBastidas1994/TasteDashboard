<?php
require_once "funciones.php";
require_once "clases/cl_programas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clprogramas = new cl_programas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(isset($_GET['id'])){
    $cod_usuario = $_GET['id'];
    $htmlProgramas = "";
    $programas = $Clprogramas->getProgramasByUser($cod_usuario);
    if($programas){
        foreach ($programas as $programa) {
            $nombre = $programa['nombre'];
            $correo = $programa['correo'];
            $telefono = $programa['telefono'];
            $boton = '<span class="text-success">Aceptado</span>';
            if("D" == $programa['estado'])
                $boton = '<span class="text-danger">Rechazado</span>';
            else if("I" == $programa['estado'])
                $boton = '  <button class="btn btn-primary btnAceptarPrograma" data-value="'.$programa['cod_programa_usuario'].'" data-aprobar="A"><i data-feather="check"></i></button>
                <button class="btn btn-danger btnAceptarPrograma" data-value="'.$programa['cod_programa_usuario'].'" data-aprobar="D"><i data-feather="x"></i></button>';
            $htmlProgramas.='   <tr>
                                    <td>'.$programa['programa'].'</td>
                                    <td>'.$programa['nombre_alumno'].'</td>
                                    <td><input type="number" id="textPrecio-'.$programa['cod_programa_usuario'].'" class="form-control" value="'.$programa['precio'].'"></td>
                                    <td>'.$boton.'</td>
                                </tr>';
        }
    }
    else{
        header("location: index.php");
    }
}
else{
    header("location: index.php");
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
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
                    <div class="col-xl-4 col-lg-4 col-sm-12 col-xs-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <input type="hidden" id="txtAlias" value="<?=$session['alias']?>">
                                    <h4>Información</h4>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <label>Nombre</label>
                                    <p id="nombrePadre"><?=$nombre?></p>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <label>Correo</label>
                                    <p id="correoPadre"><?=$correo?></p>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <label>Tel&eacute;fono</label>
                                    <p id="telefonoPadre"><?=$telefono?></p>
                                </div>
                            </div>           
                        </div>
                    </div>     
                    <div class="col-xl-8 col-lg-8 col-sm-12 col-xs-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Programas</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Programa</th>
                                                <th>Nombre</th>
                                                <th>Precio</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= $htmlProgramas?>
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
    <script src="assets/js/pages/programas.js" type="text/javascript"></script>
    <script>
        var myTable = $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>