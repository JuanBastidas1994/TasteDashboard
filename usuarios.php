<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_usuarios.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];


?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="euc-jp">
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
    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR USUARIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                          <div class="col-md-3 col-sm-3 col-xs-12">
                              <div class="upload mt-1 pr-md-1">
                                  <input type="file" name="img_profile" id="input-file-max-fs" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="1M" />
                                  <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                              </div>
                          </div>
                          <div class="col-md-5 col-sm-5 col-xs-12" style="margin-bottom:10px;">
                              <label>Nombres <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off"/>
                          </div>
                         <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                              <label>Apellidos <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Apellidos" name="txt_apellido" id="txt_apellido" class="form-control" required="required" autocomplete="off"/>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-5 col-sm-5 col-xs-12" style="margin-bottom:10px;">
                              <label>Correo <span class="asterisco">*</span></label>
                              <input type="email" placeholder="Correo" name="txt_correo" id="txt_correo" class="form-control" required="required" autocomplete="off"/>
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                              <label>Contrase&ntilde;a <span class="asterisco">*</span></label>
                              <input type="password" placeholder="Escriba su contrase&ntilde;a" name="txt_password" id="txt_password" class="form-control" required="required" autocomplete="off"/>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Rol <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="El rol define que puede hacer el usuario dentro del sistema"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="layers"></i></span>
                                </div>
                                <select name="cmbRol" id="cmbRol" class="form-control" required="required" placeholder="Notification" aria-label="notification" aria-describedby="basic-addon1">
                                  <?php
                                  if ($cor_rol <= 2)
                                    $resp = $Clusuarios->lst_roles();
                                else
                                    $resp = $Clusuarios->lst_roles_publicador();
                                  foreach ($resp as $rol) {
                                      if($rol['cod_rol'] == 4 || $rol['cod_rol'] == 6 || $rol['cod_rol'] == 17 || $rol['cod_rol'] == 21)
                                        continue;
                                        echo '<option value="'.$rol['cod_rol'].'">'.$rol['nombre'].'</option>';
                                  }  
                                  ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Telefono <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este n&uacute;mero servir&aacute; para cualquier tipo de comunicacion con el usuario"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="phone-call"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Telefono" aria-label="notification" aria-describedby="basic-addon1" name="txt_telefono" id="txt_telefono">
                            </div>
                          </div>

                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Fecha de nacimiento
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Puede recordarle los cumpleaños de los usuarios registrados, este campo no es obligatorio"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                </div>
                                <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_nacimiento" id="fecha_nacimiento">
                            </div>
                          </div>
                      </div>

                      <div class="form-group">
                        <div class="col-md-4 col-sm-4 col-xs-12 onlyAdmin">
                            <label>Sucursal <span class="asterisco">*</span></label> 
                            <select class="form-control" id="cmbSucursal" name="cmbSucursal">
                              <option value="0">Escoja una sucursal</option>
                              <?php
                              $sucursales = $Clsucursales->lista();
                              foreach ($sucursales as $suc) {
                                  echo '<option value="'.$suc['cod_sucursal'].'">'.$suc['nombre'].'</option>';
                              }    
                              ?>
                            </select>
                        </div>
                      </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
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
                                    <h4>Usuarios</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" id="btnOpenModal">Nuevo Usuario</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>Nombres</th>
                                                <th>Rol</th>
                                                <th>Correo</th>
                                                <th>Fecha de nacimiento</th>
                                                <th>Tel&eacute;fono</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstUsuarios">
                                            <?php
                                            if($cod_rol <= 2)
                                              $resp = $Clusuarios->lista();
                                            else
                                              $resp = $Clusuarios->lista_publicador();
                                            
                                            foreach ($resp as $cliente) {
                                                $imagen = $files.$cliente['imagen'];
                                                $badge='primary';
                                                if($cliente['estado'] == 'I')
                                                    $badge='danger';
                                                echo '<tr id="' . $cliente['cod_usuario'] . '">
                                                    <td><img src="'.$imagen.'" class="profile-img" alt="Imagen"></td>
                                                    <td>'.$cliente['nombre'].' '.$cliente['apellido'].'</td>
                                                    <td>'.$cliente['rol'].'</td>
                                                    <td>'.$cliente['correo'].'</td>
                                                    <td>'.$cliente['fecha_nacimiento'].'</td>
                                                    <td>'.$cliente['telefono'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($cliente['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-6 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a></li>
                                                            <li><a href="usuario_detalle.php?id='.$cliente['cod_usuario'].'"  class="bs-tooltip btnDetalle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notificar">
                                                            <i data-feather="eye"></i>
                                                            </a></li>
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
    <script src="assets/js/pages/usuarios.js?v=2" type="text/javascript"></script>
    <script>
        $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                "sInfoEmpty": "Mostrando pag. 1",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
               "sEmptyTable": "No se encontraron resultados",
               "sZeroRecords": "No se encontraron resultados",
               "buttons": {
                    "copy": "Copiar",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir",
                    "create": "Crear",
                    "edit": "Editar",
                    "remove": "Remover",
                    "upload": "Subir"
                }
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>