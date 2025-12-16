<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];

$Clordenes = new cl_ordenes(NULL);
$Clempresas = new cl_empresas(NULL);
$empresa = $Clempresas->get($session['cod_empresa']);
if ($empresa) {
    $apikey = $empresa['api_key'];
    $tipoEmpresa = $empresa['cod_tipo_empresa'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="euc-jp">
    <?php css_mandatory(); ?>
    <style type="text/css">

     * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    
    .card-hover {
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    
    </style>
</head>
<body>
   
   <!--MODAL BIENVENIDA -->
    <div class="modal fade bs-example-modal-lg" id="orderDetailModal" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" id="orderDetailTitle" style="width: 100%;">Orden detalle #2132121</h5>
                </div>
                
                <div class="modal-body" style="padding: 0px 15px;">
                    <div class="x_content" style="height: 100%;" id="order-detail"></div>
                    
                    <script id="order-detail-template" type="text/x-handlebars-template">
                        <input type="hidden" id="orderId" value="{{id}}" />
                        <div class="row mt-3">
                            <div class="col-md-7 col-12">
                                <div>
                                    <h5>Lugar de Retiro</h5>
                                    <div>
                                        <img src="{{empresa.logo}}" style="width:50px" /> {{empresa.nombre}}
                                    </div>
                                    <div>
                                        Sucursal: {{sucursal.nombre}}
                                    </div>
                                    <div>
                                        Dirección: {{sucursal.direccion}}
                                    </div>
                                    <div><a href="tel:{{sucursal.telefono}}" data-toggle="tooltip" data-placement="top" title="Llamar">Telefono: {{sucursal.telefono}}</a></div>
                                    
                                </div>
                                <div class="mt-3">
                                    <div id="mapa" class="gllpMap" 
                                        style="margin-left: 0; width: 100%; height: 250px;"  
                                        data-latitud="{{latitud}}" 
                                        data-longitud="{{longitud}}">Google Maps</div>
                                </div>
                                
                                
                            </div>
                            <div class="col-md-5 col-12">
                                <div class="mt-3">
                                    <h5>Cliente</h5>
                                    <div>Juan Bastidas</div>
                                    <div><a href="tel:{{cliente.telefono}}" data-toggle="tooltip" data-placement="top" title="Llamar">Telefono: {{cliente.telefono}}</a></div>
                                </div>
                                
                                <div class="mt-3">
                                    <h5>Formas de Pago</h5>
                                    <table class="" style="width: 100%;">
                                        <tbody>
                                            {{#each pagos}}
                                            <tr>
                                                <td>{{ descripcion }}</td>
                                                <td class="text-right">{{ observacion }}</td>
                                                <td class="text-right">${{ monto }}</td>
                                            </tr>
                                            {{/each}}
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{#eq motorizado false}}
                                    <div class="mt-3">
                                        <h5>Asigna a un motorizado</h5>
                                        <div>
                                            <select class="form-control" id="motoId">
                                                <option value="0">Selecciona un motorizado</option>
                                                {{#each mis_motos}}
                                                    <option value="{{id}}">{{nombres}}</option>
                                                {{/each}}
                                            </select>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-success" id="btnAsignar">Asignar</button>
                                        </div>
                                    </div>
                                {{else}}
                                    <div class="mt-3">
                                        <h5>Motorizado</h5>
                                        <div>Nombre: {{motorizado.nombre}}</div>
                                        <div>DNI: {{motorizado.num_documento}}</div>
                                        <div>Telefono: {{motorizado.telefono}}</div>
                                        <div>Placa: {{motorizado.placa}}</div>
                                        <div>
                                            <div><b>Enlace</b></div>
                                            <div>{{link.url}}</div>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-warning" id="btnDeleteAsignar">Quitar asignación</button>
                                        </div>
                                    </div>
                                {{/eq}}
                            </div>
                        </div>
                    </script>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"
                        onclick="">Omitir</button>
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
                    <input id="apikey_empresa" type="hidden" value="<?= $apikey ?>">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            
                             <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                              <div style="flex: 1;">
                                <label for="selectComercio">Filtrar por comercio:</label>
                                <select id="selectComercio" style="width:100%">
                                  <option></option>
                                  <?php
                                  $empresas = $Clempresas->getComercios();
                                  foreach ($empresas as $empresa) {
                                      $img = url_sistema.'assets/empresas/'.$empresa['alias'].'/' . $empresa['logo'];
                                      $nombre = $empresa['nombre'];
                                      echo "<option value='{$empresa['cod_empresa']}' data-image_path='{$img}'>{$nombre}</option>";
                                  }
                                  ?>
                                </select>
                              </div>
                            
                              <div style="flex: 1;">
                                <label for="selectEstado">Filtrar por estado:</label>
                               <select id="selectEstado"  style="width:100%">
                                  <option value="ASIGNADA">Asignada</option>
                                  <option value="ENVIANDO">Enviando</option>
                                  <option value="ENTREGADA">Entregada</option>
                                </select>
                              </div>
                            </div>

                        
                            
                        </div>
                        
                        <div>
                            <div id="order-list"></div>
                            <script id="order-list-template" type="text/x-handlebars-template">
                                {{#each this}}
                                    <div class="mt-3 card card-hover py-2 px-4 ordenItem" data-value="{{cod_orden}}" data-estado="{{estado}}">
                                        <h2>Orden #{{cod_orden}}</h2>
                                        <p><img src="{{imagen}}" style="width:50px" /> <span style='font-weight:bold;'>{{empresa}} - {{sucursal}}</span></p>
                                        <p><strong>Cliente:</strong> {{nom_cliente}}</p>
                                        <p><strong>Fecha:</strong> {{fecha}}</p>
                                        <p><strong>Total:</strong> ${{total}}</p>
                                        {{#eq motorizado false}}
                                            <p><strong>Estado:</strong> <span class="badge outline-badge-{{colorStatus NO_ASIGNADA}}">NO ASIGNADA</span></p>
                                        {{else}}
                                            <p><strong>Estado:</strong> <span class="badge outline-badge-{{colorStatus estado}}">{{estado}}</span></p>
                                            <p><strong>Motorizado: {{motorizado.nombre}}</strong></p>
                                        {{/eq}}
                                      </div>
                                {{/each}}
                            </script>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script src="assets/js/pages/usuarios.js?v=2" type="text/javascript"></script>
     <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    
    <script src="assets/js/pages/flota_gestion.js?v=0" type="text/javascript"></script>
    <script>
        Handlebars.registerHelper('eq', function(arg1, arg2, options) {
            return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('colorStatus', function(status) {
            if (status == "NO_ASIGNADA")
                return "primary";
            else if (status == "ASIGNADA")
                return "warning";
            else if (status == "ENVIANDO")
                return "secondary";
            else if (status == "ENTREGADA")
                return "success";
            else if (status == "ANULADA")
                return "danger";
            else if (status == "NO_ENTREGADA")
                return "danger";
            else if (status == "PUNTO_RECOGIDA")
                return "info";
            else if (status == "PUNTO_ENTREGA")
                return "dark";
            else
                return "info";
        });
    </script>
    
    <script>
    

    function cargarMotorizados2(){
          $.ajax({
              url: 'controllers/controlador_usuario.php?metodo=lista_motorizados',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
              },
              error: function(data){
                console.log(data);
                 
              },
          });
     }

    function cargarMotorizados() {
    $.ajax({
        url: 'controllers/controlador_usuario.php?metodo=lista_motorizados',
        type: 'GET',
        success: function(response) {
            console.log(response);

            // Validar que exista la data y sea un array
            if (response.success === 1 && Array.isArray(response.data)) {
                const select = document.getElementById('selectMotorizado');
                select.innerHTML = ''; // Limpiar opciones previas

                response.data.forEach(moto => {
                    // Crear opción con id y nombre + apellido
                    const option = document.createElement('option');
                    option.value = moto.cod_usuario;  // o el id que uses para identificar
                    option.textContent = moto.nombre + ' ' + moto.apellido;
                    select.appendChild(option);
                });
            } else {
                console.error('No se encontraron motorizados o respuesta incorrecta.');
            }
        },
        error: function(error) {
            console.error('Error al obtener motorizados:', error);
        }
    });
}


    function closeModal() {
      document.getElementById('orderModal').style.display = 'none';
    }
    
    function templateServicesResult(comercioInfo){
        if (comercioInfo.loading) return comercioInfo.text;
    
        const image = $(comercioInfo.element).data("image_path");
        const sku = $(comercioInfo.element).data("sku");
    
        return $(`
          <div class='select2-result-client d-flex align-items-center'>
            <div style="width: 50px; margin-right: 10px;">
              <img src="${image}" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;" />
            </div>
            <div>
              <div class='title'>${comercioInfo.text}</div>
            </div>
          </div>
        `);
    }
    

function renderOrders(orders) {
    const container = document.querySelector('.order-container');
    container.innerHTML = ''; // limpia contenido

    if(orders.length === 0){
      container.innerHTML = '<p>No se encontraron órdenes con esos filtros.</p>';
      return;
    }

    orders.forEach(orden => {
        const estadoClase = orden.estado.toLowerCase().trim();
        const comercio = `${orden.comercio} - ${orden.sucursal}`;
        const cod = orden.cod_orden;
        const cliente = orden.nom_cliente;
        const fecha = orden.fecha;
        const total = orden.total;
        const estado = orden.estado;

        const div = document.createElement('div');
        div.className = 'order-card';
        div.setAttribute('onclick', `openModal('${cod}', '${cliente}', '${fecha}', '${total}', '${estado}', '-2.2388208', '-80.0753308', 'Efectivo')`);
        div.innerHTML = `
            <h2>Orden #${cod}</h2>
            <p><strong style='color:#007bff; font-size: 1.1rem;'>Comercio:</strong> <span style='font-weight:bold;'>${comercio}</span></p>
            <p><strong>Cliente:</strong> ${cliente}</p>
            <p><strong>Fecha:</strong> ${fecha}</p>
            <p><strong>Total:</strong> ${total}</p>
            <p><strong>Estado:</strong> <span class='status ${estadoClase}'>${estado}</span></p>
        `;

        container.appendChild(div);
    });
}

        
        $(document).ready(function() {
            $('#selectComercio').select2({
                placeholder: 'Seleccione un comercio',
                allowClear: true,
                templateResult: templateServicesResult,
                templateSelection: templateServicesResult
            });
            
            $('#selectEstado').select2({
                placeholder: 'Filtrar por estado',
                allowClear: true,
                width: 'resolve'
            });
            
            $('#selectMotorizado').select2({
          templateResult: formatMotorizado,
          templateSelection: formatMotorizado,
          escapeMarkup: function(markup) { return markup; }
        });

        });


    
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>