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
      
     * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }

    .order-container {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    .order-card {
      width: 100%;
      padding: 20px;
      border-bottom: 1px solid #ddd;
      background-color: #fff;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .order-card:hover {
      background-color: #f0f8ff;
    }

    .order-card h2 {
      font-size: 1.5rem;
      color: #007BFF;
      margin-bottom: 10px;
    }

    .order-card p {
      font-size: 1rem;
      margin: 6px 0;
    }

    .status.asignada { color: orange; font-weight: bold; }
    .status.entregado { color: green; font-weight: bold; }
    .status.enviando { color: red; font-weight: bold; }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      width: 90%;
      max-width: 500px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    .close-btn {
      float: right;
      font-size: 1.2rem;
      font-weight: bold;
      color: #888;
      cursor: pointer;
    }

    .close-btn:hover {
      color: #000;
    }

    @media (max-width: 600px) {
      .order-card h2 { font-size: 1.3rem; }
      .order-card p { font-size: 0.95rem; }
    }
    
    .select2-result-client .title {
          font-weight: bold;
          font-size: 1rem;
        }
        .select2-result-client .desc {
          font-size: 0.85rem;
          color: #555;
        }
        
    #btnAsignar {
  background-color: #007BFF;      /* Azul bootstrap */
  color: white;
  border: none;
  padding: 12px 25px;
  font-size: 1.1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,123,255,0.4);
  font-weight: 600;
  display: inline-block;
}

#btnAsignar:hover {
  background-color: #0056b3;      /* Azul más oscuro */
  box-shadow: 0 5px 15px rgba(0,86,179,0.6);
}

#btnAsignar:active {
  background-color: #004494;
  box-shadow: none;
  transform: translateY(2px);
}

#asignarMotorizadoContainer {
  display: flex;
  flex-direction: column;
  align-items: center; /* Centra horizontalmente */
  gap: 10px; /* espacio entre select y botón */
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
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            
                             <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                              <div style="flex: 1;">
                                <label for="selectComercio">Filtrar por comercio:</label>
                                <select id="selectComercio" multiple="multiple" style="width:100%">
                                  <option></option>
                                  <?php
                                  $empresas = $Clempresas->getComercios();
                                  foreach ($empresas as $empresa) {
                                      $img = $files . $empresa['logo'];
                                      $nombre = $empresa['nombre'];
                                      echo "<option value='{$empresa['cod_empresa']}' data-image_path='{$img}'>{$nombre}</option>";
                                  }
                                  ?>
                                </select>
                              </div>
                            
                              <div style="flex: 1;">
                                <label for="selectEstado">Filtrar por estado:</label>
                               <select id="selectEstado"  style="width:100%">
                                  <option value="Asignada">Asignada</option>
                                  <option value="Enviando">Enviando</option>
                                  <option value="Entregado">Entregada</option>
                                </select>
                              </div>
                            </div>

                        <div class="order-container">

                              <?php
                                $resp = $Clordenes->getListOrdersFlota(0, 0);
                                foreach ($resp as $orden) {
                                    // Normalizar estado a clase CSS
                                    $estado_clase = strtolower(trim($orden['estado'])); 
                                
                                    $cod = $orden['cod_orden'];
                                    $comercio = $orden['comercio'] . ' - ' . $orden['sucursal'];
                                    $cliente = $orden['nom_cliente'];
                                    $fecha = $orden['fecha'];
                                    $total = $orden['total'];
                                    $estado = $orden['estado'];
                                
                                    echo "<div class='order-card' onclick=\"openModal('$cod', '$cliente', '$fecha', '$total', '$estado', '-2.2388208', '-80.0753308', 'Efectivo')\">
                                            <h2>Orden #$cod</h2>
                                            <p><strong style='color:#007bff; font-size: 1.1rem;'>Comercio:</strong> <span style='font-weight:bold;'>$comercio</span></p>
                                            <p><strong>Cliente:</strong> $cliente</p>
                                            <p><strong>Fecha:</strong> $fecha</p>
                                            <p><strong>Total:</strong> $total</p>
                                            <p><strong>Estado:</strong> <span class='status $estado_clase'>$estado</span></p>
                                          </div>";
                                }
                                ?>

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
    
    <!-- Modal -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2 id="modalOrden">Orden</h2>
    <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
    <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
    <p><strong>Total:</strong> <span id="modalTotal"></span></p>
    <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
    
    <div class="widget-content widget-content-area br-6" style="margin-top: 15px; display: none;" id="mapaContainer">
        <div>
            <h4>Ubicación</h4>
        </div>
        <div class="row">
            <div id="mapa" class="gllpMap" style="width: 100%; height: 250px;" data-latitud="" data-longitud="">Google Maps</div>

        </div>
    </div>
    
    
        <!-- Datos de forma de pago -->
    <div id="formaPagoContainer" style="margin-top: 20px;">
      <h4>Forma de Pago</h4>
      <p id="formaPagoText">--</p>
    </div>
    
    <!-- Asignar motorizado -->
    <div id="asignarMotorizadoContainer" style="margin-top: 20px;">
      <h4>Asignar Motorizado</h4>
      <select id="selectMotorizado" style="width: 100%; padding: 8px; font-size: 1rem;">
        <!-- Opciones generadas dinámicamente -->
      </select>
      <button id="btnAsignar" style="margin-top: 10px; padding: 10px 15px; font-size: 1rem; cursor: pointer;">
        Asignar
      </button>
    </div>

  </div>
</div>
    
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/usuarios.js?v=2" type="text/javascript"></script>
     <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    
    
    <script>
    
    function openModal(id, cliente, fecha, total, estado, latitud, longitud, formaPago) {
    document.getElementById('modalOrden').innerText = 'Orden #' + id;
    document.getElementById('modalCliente').innerText = cliente;
    document.getElementById('modalFecha').innerText = fecha;
    document.getElementById('modalTotal').innerText = total;
    document.getElementById('modalEstado').innerText = estado;
    document.getElementById('formaPagoText').innerHTML = `<strong>${formaPago}</strong>`;
    
      const btnAsignar = document.getElementById('btnAsignar');
    btnAsignar.setAttribute('data-id-orden', id);
    cargarMotorizados();
    document.getElementById('orderModal').style.display = 'block';

    const mapa = $('#mapa');
    const mapaContainer = document.getElementById('mapaContainer');

    if (latitud && longitud) {
        mapaContainer.style.display = 'block';

        // Setear datos en el div (no es obligatorio pero para consistencia)
        mapa.attr('data-latitud', latitud);
        mapa.attr('data-longitud', longitud);

        // Inicializar o reiniciar el gllpMap
        mapa.gmapsLatLonPicker(); // Inicializa el plugin
        // Luego actualizar el centro y el marcador:
        mapa.data('plugin_gmapsLatLonPicker').setPosition(latitud, longitud);

    } else {
        mapaContainer.style.display = 'none';
    }
    
   
}

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
    
// Escucha cambios en los filtros
$('#selectComercio, #selectEstado').on('change', function() {
    fetchFilteredOrders();
});

function fetchFilteredOrders() {
    const comercios = $('#selectComercio').val() || [];
    const estados = $('#selectEstado').val() || [];
    $.ajax({
        url: 'controllers/controlador_ordenes.php?metodo=getOrdersFlota',
        type: 'GET',
        data: {
            comercios: JSON.stringify(comercios),
            estados: JSON.stringify(estados)
        },
        dataType: 'json',
        
        success: function(response) {
            console.log(response)
            if(response.success === 1) {
                renderOrders(response.data);
            } else {
                alert('No se pudieron cargar las órdenes filtradas');
            }
        },
        error: function() {
            alert('Error al obtener las órdenes filtradas');
        }
    });
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


document.getElementById('btnAsignar').addEventListener('click', () => {
  const select = document.getElementById('selectMotorizado');
  const motorizadoId = select.value;
  if (!motorizadoId) {
    alert('Por favor selecciona un motorizado.');
    return;
  }
  
  // Aquí haces la llamada AJAX para asignar o simplemente muestra confirmación
//  alert(`Orden asignada al motorizado ID: ${motorizadoId}`);
 const ordenId = document.getElementById('btnAsignar').getAttribute('data-id-orden');

  $.ajax({
        url: 'controllers/controlador_ordenes.php?metodo=asignarFlota',
        type: 'GET',
        data: {
            orden: ordenId,
            motorizado: motorizadoId
        },
        dataType: 'json',
        
        success: function(response) {
            console.log(response)
            if(response.success === 1) {
                
                //fetchFilteredOrders();
                alert("asignado - cambiar el alert por algo mas bonito")
            } else {
                alert('No se pudieron cargar las órdenes filtradas');
            }
        },
        error: function() {
            alert('Error al obtener las órdenes filtradas');
        }
    });
    
  // Opcional: cerrar modal o actualizar estado en la UI
  closeModal();
});

    // Cierra el modal al hacer clic fuera del contenido
    window.onclick = function(event) {
      const modal = document.getElementById('orderModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }
    
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>