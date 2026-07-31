<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];
$empresa = $Clempresas->get($session['cod_empresa']);
if(!$empresa)
    header("location:login.php");

$isBusinessCourier = ($empresa['cod_tipo_empresa'] == 4) ? true : false;
$apikey = $empresa['api_key'] ?? '';
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
                    <h5 class="modal-title" id="exampleModalLabel">CREAR MOTORIZADO</h5>
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
                            <input type="hidden" name="cmbRol" value="17" >
                               
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
                              <label>Placa
                                   <span class="asterisco">*</span>
                                </label>

                            <div class="input-group mb-4">
                                <input type="text" class="form-control" placeholder="Placa" aria-label="notification" aria-describedby="basic-addon1" name="txt_placa" id="txt_placa">
                            </div>
                            
                          </div>
                          <div class="col-md-4" style="<?php echo ($isBusinessCourier) ? 'display:none;' : ''; ?>">
                              <label>Sucursales</label>
                              <select class="form-control" id="cmbSucursal" name="cmbSucursal">
                                  <option value="0">Todas las sucursales</option>
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
                    <button type="button" class="btn btn-primary" id="btnGuardarFlota">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: código generado (invitación o reactivación) -->
    <div class="modal fade" id="codigoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="codigoModalTitle">Código generado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="codigoModalDesc" class="text-muted"></p>
                    <div class="text-center mb-3">
                        <span id="codigoModalValor" style="font-size:28px; font-weight:bold; letter-spacing:3px; background:#f4f4f4; padding:8px 16px; border-radius:8px; display:inline-block;"></span>
                    </div>
                    <p class="text-muted" style="font-size:12px;">Válido hasta: <span id="codigoModalExpira"></span></p>
                    <label>Mensaje listo para compartir por WhatsApp</label>
                    <textarea id="codigoModalMensaje" class="form-control" rows="4" readonly></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnCopiarMensaje">Copiar mensaje</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: notificar a un motorizado (push) -->
    <div class="modal fade" id="notificarModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notificar a <span id="notificarModalNombre"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Mensaje</label>
                    <textarea id="notificarModalMensaje" class="form-control" rows="3" maxlength="200" placeholder="Ej: Por favor confirma tu ubicación"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnEnviarNotificacion">Enviar</button>
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
                                    <h4>
                                        <?php
                                            echo (!$isBusinessCourier) ? 'Mis Motorizados' : 'Mi Flota';
                                        ?>
                                    </h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <input type="hidden" id="apikey_flota" value="<?= htmlspecialchars($apikey) ?>">
                                    <button class="btn btn-outline-primary" id="btnInvitar" type="button">Invitar a un motorizado</button>
                                    <button class="btn btn-primary" id="btnOpenModal">Nuevo Motorizado</button>
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
                                            <th>Correo</th>
                                            <?php if (!$isBusinessCourier): ?>
                                                <th>Sucursal</th>
                                            <?php endif; ?>
                                            <th>Tel&eacute;fono</th>
                                            <th>Placa</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $resp = $Clusuarios->listaDeMotorizados();
                                        foreach ($resp as $cliente) {
                                            $sucursal = $cliente['sucursal'] !== NULL ? $cliente['sucursal'] : "Todas las sucursales";
                                            $badge='primary';
                                            if($cliente['estado'] == 'I')
                                                $badge='danger';
                                            // badge-info no está estilado en este tema (se ve "Activo"/"Inactivo" pero
                                            // "Invitado" salía invisible) — estilo inline para no depender de eso.
                                            $badgeInvitado = !empty($cliente['es_invitado'])
                                                ? ' <span class="shadow-none badge" style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">Invitado</span>'
                                                : '';
                                            echo '<tr id="' . $cliente['cod_usuario'] . '">
                                                <td><img src="'.$cliente['imagen_url'].'" class="profile-img" alt="Imagen"></td>
                                                <td>'.$cliente['nombre'].' '.$cliente['apellido'].'</td>
                                                <td>'.$cliente['correo'].'</td>';
                                                if (!$isBusinessCourier) {
                                                    echo '<td>'.$sucursal.'</td>';
                                                }

                                                echo '
                                                <td>'.$cliente['telefono'].'</td>
                                                <td>'.$cliente['placa'].'</td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($cliente['estado']).'</span>'.$badgeInvitado.'</td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                        <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" class="bs-tooltip btnEditarFlota" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-6 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a></li>
                                                        <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a></li>
                                                        <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" class="bs-tooltip btnReactivar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Generar codigo de reactivacion"><i data-feather="key" class="p-1 br-6 mb-1"></i></a></li>
                                                        <li><a href="javascript:void(0);" data-value="'.$cliente['cod_usuario'].'" data-nombre="'.htmlspecialchars($cliente['nombre'].' '.$cliente['apellido']).'" class="bs-tooltip btnNotificar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Notificar"><i data-feather="bell" class="p-1 br-6 mb-1"></i></a></li>
                                                        <li><a href="usuario_detalle.php?id='.$cliente['cod_usuario'].'"  class="bs-tooltip btnDetalle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver detalle">
                                                        <i data-feather="eye"></i>
                                                        </a></li>
                                                    </ul>
                                                </td>
                                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <script id="useritem-template" type="text/x-handlebars-template">
                                    <tr id="{{cod_usuario}}">
                                        <td>
                                            <img src="{{imagen_url}}" class="profile-img" alt="Imagen" style="width:50px;height:auto;">
                                        </td>
                                        <td>{{nombre}} {{apellido}}</td>
                                        <td>{{correo}}</td>
                                        {{#ifNotBusinessCourier}}
                                            <td>{{sucursal}}</td>
                                        {{/ifNotBusinessCourier}}
                                        <td>{{telefono}}</td>
                                         
                                        <td>{{placa}}</td>
                                        <td class="text-center">
                                            {{{estadoBadge estado}}}
                                            {{#if es_invitado}}<span class="shadow-none badge" style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">Invitado</span>{{/if}}
                                        </td>
                                        <td class="text-center">
                                            <ul class="table-controls">
                                                <li>
                                                    <a href="javascript:void(0);" data-value="{{cod_usuario}}" class="btnEditar">
                                                        <i data-feather="edit-2"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" data-value="{{cod_usuario}}" class="btnEliminar">
                                                        <i data-feather="trash"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" data-value="{{cod_usuario}}" class="btnReactivar">
                                                        <i data-feather="key"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" data-value="{{cod_usuario}}" data-nombre="{{nombre}} {{apellido}}" class="btnNotificar">
                                                        <i data-feather="bell"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="usuario_detalle.php?id={{cod_usuario}}">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </script>
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
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="assets/js/pages/usuarios.js?v=5" type="text/javascript"></script>
    <script src="assets/js/pages/flota_codigos.js?v=1" type="text/javascript"></script>
    <script>
        const isBusinessCourier = <?= $isBusinessCourier ? 'true' : 'false' ?>;
        let tablaUsuarios;
        tablaUsuarios = $('#style-3').DataTable( {
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
            "lengthMenu": [50, 70, 100],
            "pageLength": 50 
        } );

        Handlebars.registerHelper('ifNotBusinessCourier', function (options) {
            if (!isBusinessCourier) {
                return options.fn(this);
            }
            return '';
        });
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>