<?php
require_once "funciones.php";
require_once "clases/cl_frontscript.php";

if(!isLogin()){
    header("location:login.php");
}

$ClScript = new cl_frontscript();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];

if(isset($_POST['btnSave'])){
    extract($_POST);
    if($id == 0)
        $ClScript->crear($nombre, $posicion, $codigo);
    else
        $ClScript->editar($id, $nombre, $posicion, $codigo);
}

if(isset($_POST['script_id'])){
     $ClScript->eliminar($_POST['script_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <?php css_mandatory(); ?>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Crear/Editar Script</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <form id="frmSave" name="frmSave" method="POST" class="form-horizontal form-label-left"> 
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                   
                    <div class="x_content">    
                        <div class="row mb-3">
                            <div class="col-9 ">
                                <label>Nombre <span class="asterisco">*</span></label>
                                <input type="text" placeholder="Nombre" name="nombre" id="nombre" class="form-control" required="required" autocomplete="off"/>
                            </div>
                            <div class="col-3">
                                <label>Posición <span class="asterisco">*</span></label>
                                <select name="posicion" id="posicion" class="form-control" required="required">
                                    <option value="head">HEAD</option>
                                    <option value="body">BODY</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label>Script <span class="asterisco">*</span></label>
                                <textarea class="form-control" placeholder="Pega aquí el script" name="codigo" id="script" required="required" autocomplete="off" style="height: 250px;"></textarea>
                            </div>
                        </div>
                  
                    </div>
                 
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="btnSave" id="btnGuardar">Guardar</button>
                </div>
                 </form>  
                <form id="frmEliminar" name="frmEliminar" method="POST" style="display:none;"> 
                    <input type="hidden" id="script_id" name="script_id" value="">
                </form>
            </div>
        </div>
    </div>

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
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Scripts para mi Front</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary btnEditar" 
                                        data-id=""
                                        data-nombre=""
                                        data-ubicacion="head"
                                        data-codigo=""
                                    >Nuevo script</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div>
                                Cualquier cambio se verá reflejado después de replicar la página
                            </div>
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table  class="table style-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Ubicación</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $ClScript->lista();
                                            foreach ($resp as $script) {
                                                $badge='primary';
                                                if($script['estado'] == 'I')
                                                    $badge='danger';
                                                echo '<tr>
                                                    <td>'.$script['nombre'].'</td>
                                                    <td>'.$script['ubicacion'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($script['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li>
                                                                <a href="javascript:void(0);"  class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"
                                                                    data-id="'.$script['id'].'"
                                                                    data-nombre="'.$script['nombre'].'"
                                                                    data-ubicacion="'.$script['ubicacion'].'"
                                                                    data-codigo="'.$script['codigo'].'"
                                                                >
                                                                    <i data-feather="edit-2"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"
                                                                    data-id="'.$script['id'].'"
                                                                    data-nombre="'.$script['nombre'].'"
                                                                >
                                                                    <i data-feather="trash"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>';
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
    
    <?php js_mandatory(); ?>
    <script>
        $(".btnEditar").on('click', function(){
            let data = $(this).data();
            console.log(data);
            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
            $("#posicion").val(data.ubicacion);
            $("#script").val(data.codigo);
            $("#addModal").modal();
        });
        
        $(".btnEliminar").on('click', function(){
            let data = $(this).data();
            console.log(data);
            
             messageConfirm("¿Deseas continuar?", "El script "+data.nombre+" se eliminará", "question")
            .then(function(result) {
                if (result) {
                    
                    
                    $("#script_id").val(data.id);
                    $("#frmEliminar").submit();
                }
            });
            
            
            
        });
        
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>