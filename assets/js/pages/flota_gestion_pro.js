const { API_FLOTAS } = window.__CONFIG__;
const ApiUrl = API_FLOTAS;
let ApiKey = "";

let pedidoActualId = null;
let pedidoActualData = null;
let mapaPedido = null;
let markersMotos = [];
let intervalPedidosId = 0;
let intervalMotosId = 0;

const COLUMNAS = [
    { key: 'incidencia', color: 'danger' },
    { key: 'pendiente', color: 'primary' },
    { key: 'en_curso', color: 'warning' },
    { key: 'enviando', color: 'info' },
    { key: 'entregada', color: 'success' },
];

const FORMA_PAGO = { E: 'Efectivo', T: 'Tarjeta', TB: 'Transferencia' };
const ESTADO_TRABAJO_LABEL = { disponible: 'Disponible', en_carrera: 'En carrera', no_disponible: 'No disponible' };
const ESTADO_TRABAJO_COLOR = { disponible: 'success', en_carrera: 'warning', no_disponible: 'danger' };
const ICONO_MOTO = {
    disponible: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
    en_carrera: 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png',
};

// Mismos 4 motivos que usa la app del motorizado para reportar y que el admin vuelve a elegir
// al resolver — ver cl_ordenes::MOTIVOS_INCIDENCIA (api_flotas).
const MOTIVO_INCIDENCIA_LABEL = {
    NO_CONTESTA: 'El cliente no contesta',
    DIRECCION_INCORRECTA: 'La dirección es incorrecta',
    CLIENTE_RECHAZO: 'El cliente no quiso recibir el pedido',
    NO_QUISO_PAGAR: 'El cliente no quiso pagar',
};

Handlebars.registerHelper('badgeEstado', estado => ESTADO_TRABAJO_COLOR[estado] || 'secondary');
Handlebars.registerHelper('labelEstado', estado => ESTADO_TRABAJO_LABEL[estado] || estado);
Handlebars.registerHelper('motivoLabel', motivo => MOTIVO_INCIDENCIA_LABEL[motivo] || motivo);
Handlebars.registerHelper('fechaFormateada', raw => formatFecha(raw));

// El backend guarda fechas como "YYYY-MM-DD HH:MM:SS"; el espacio en vez de "T" hace que
// Date() falle en algunos navegadores, por eso el replace.
function formatFecha(raw) {
    if (!raw) return '--';
    const d = new Date(raw.replace(' ', 'T'));
    if (isNaN(d.getTime())) return raw;
    return d.toLocaleString('es-EC', { day: '2-digit', month: 'short', hour: 'numeric', minute: '2-digit' });
}

function construirHistorial(orden) {
    const pasos = [
        { titulo: 'Asignado', rawFecha: orden.fecha_asignacion },
        { titulo: 'Camino al local', rawFecha: orden.fecha_aceptacion },
        { titulo: 'Llegó al local', rawFecha: orden.fecha_llegada_local },
        { titulo: 'Camino al cliente', rawFecha: orden.fecha_salida },
        { titulo: 'Entregado', rawFecha: orden.fecha_llegada },
    ];
    let ultimoCompleto = -1;
    pasos.forEach((p, i) => {
        p.complete = !!p.rawFecha;
        p.fecha = formatFecha(p.rawFecha);
        if (p.complete) ultimoCompleto = i;
    });
    pasos.forEach((p, i) => { p.current = i === ultimoCompleto; });
    return pasos;
}

$(function () {
    ApiKey = $("#apikey_flota").val();
    cargarPedidos();
    intervalPedidosId = setInterval(cargarPedidos, 15000);
});

function cargarPedidos() {
    const comercio = $('#selectComercio').val() || '';
    fetch(`${ApiUrl}/flotas/pedidos${comercio ? '?comercio=' + comercio : ''}`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) return;
            const template = Handlebars.compile($("#flota-card-template").html());
            COLUMNAS.forEach(({ key, color }) => {
                const items = response.data[key] || [];
                $('#col-' + key).html(template({ items, colorColumna: color }));
                $('#count-' + key).text(items.length);
            });
            feather.replace();
        })
        .catch(error => console.error('Error al cargar pedidos:', error));
}

function openPedido(cod_orden) {
    pedidoActualId = cod_orden;
    $("#pedidoDetailTitle").html(`Pedido #${cod_orden}`);

    fetch(`${ApiUrl}/flotas/pedido/${cod_orden}`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) {
                alert(response.mensaje || 'No se pudo cargar el pedido');
                return;
            }
            const orden = response.data;
            orden.pago_label = orden.pago ? (FORMA_PAGO[orden.pago.forma_pago] || orden.pago.forma_pago) : '';
            orden.entregada = orden.estado === 'ENTREGADA';
            orden.esEfectivo = !!(orden.pago && orden.pago.forma_pago === 'E');
            pedidoActualData = orden;

            renderInfoTab(orden);
            renderHistorialTab(orden);
            actualizarTabAsignacion(orden);

            // Siempre reabre en "Información" — evita quedar en una pestaña que ya no aplica
            // (ej. Asignación, si el pedido anterior no tenía motorizado y este sí).
            $('#pedidoTabsNav a[href="#tab-info"]').tab('show');

            feather.replace();
            $("#pedidoDetailModal").modal();
        })
        .catch(error => console.error('Error al cargar el pedido:', error));
}

function renderInfoTab(orden) {
    const template = Handlebars.compile($("#pedido-info-template").html());
    $("#pedido-info").html(template(orden));
    feather.replace();
}

function renderHistorialTab(orden) {
    const pasos = construirHistorial(orden);
    const template = Handlebars.compile($("#pedido-historial-template").html());
    $("#pedido-historial").html(template(pasos));
}

// La pestaña "Asignación" solo tiene sentido si el pedido todavía no tiene motorizado — una vez
// asignado (o entregado) se usa "Quitar asignación" desde Información en vez de reabrirla acá.
function actualizarTabAsignacion(orden) {
    const mostrar = !orden.cod_motorizado && !orden.entregada;
    $('#navAsignacionItem').toggle(mostrar);
}

// El mapa se inicializa recién cuando la pestaña se muestra (no al abrir el modal) — Google
// Maps necesita que el contenedor tenga tamaño real, y un tab-pane inactivo tiene display:none.
$('a[href="#tab-asignacion"]').on('shown.bs.tab', function () {
    if (pedidoActualData) initMapaPedido(pedidoActualData);
});

function initMapaPedido(orden) {
    const el = document.getElementById('mapaPedido');
    if (!el) return;

    const sucursalLat = parseFloat(orden.sucursal_lat);
    const sucursalLon = parseFloat(orden.sucursal_lon);
    const clienteLat = parseFloat(orden.cliente_lat);
    const clienteLon = parseFloat(orden.cliente_lon);

    mapaPedido = new google.maps.Map(el, {
        zoom: 13,
        center: { lat: sucursalLat, lng: sucursalLon },
    });

    new google.maps.Marker({
        position: { lat: sucursalLat, lng: sucursalLon },
        icon: 'assets/img/marker-office.png',
        map: mapaPedido,
    });

    if (!isNaN(clienteLat) && !isNaN(clienteLon)) {
        new google.maps.Marker({ position: { lat: clienteLat, lng: clienteLon }, map: mapaPedido });
    }

    cargarMotorizados();
    clearInterval(intervalMotosId);
    intervalMotosId = setInterval(cargarMotorizados, 8000);
}

function cargarMotorizados() {
    fetch(`${ApiUrl}/flotas/motorizados`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) return;
            renderListaMotorizados(response.data);
            dibujarMotosEnMapa(response.data);
        })
        .catch(error => console.error('Error al cargar motorizados:', error));
}

function renderListaMotorizados(motorizados) {
    const template = Handlebars.compile($("#motorizados-template").html());
    $("#lista-motorizados-cercanos").html(template(motorizados));
    feather.replace();
}

function dibujarMotosEnMapa(motorizados) {
    markersMotos.forEach(m => m.setMap(null));
    markersMotos = [];

    if (!mapaPedido) return;

    motorizados
        .filter(moto => moto.estado_trabajo !== 'no_disponible')
        .forEach(moto => {
            const marker = new google.maps.Marker({
                map: mapaPedido,
                icon: ICONO_MOTO[moto.estado_trabajo],
                label: moto.nombres,
                position: { lat: parseFloat(moto.latitud), lng: parseFloat(moto.longitud) },
                info_moto: moto,
            });
            marker.addListener('click', function () {
                asignarDesdeMapa(this.info_moto.id);
            });
            markersMotos.push(marker);
        });
}

function asignarDesdeMapa(cod_motorizado) {
    if (!confirm('¿Asignar este pedido a este motorizado?')) return;

    fetch(`${ApiUrl}/flotas/asignar`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ cod_orden: pedidoActualId, cod_motorizado }),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                cargarPedidos();
                openPedido(pedidoActualId);
            } else {
                alert(response.mensaje || 'No se pudo asignar');
            }
        })
        .catch(error => console.error('Error al asignar:', error));
}

$("body").on('click', '#btnQuitarAsignacion', function () {
    if (!confirm('¿Quitar la asignación de este pedido?')) return;

    fetch(`${ApiUrl}/flotas/quitar-asignacion`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ cod_orden: pedidoActualId }),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                cargarPedidos();
                openPedido(pedidoActualId);
            } else {
                alert(response.mensaje || 'No se pudo quitar la asignación');
            }
        })
        .catch(error => console.error('Error al quitar asignación:', error));
});

$("body").on('click', '#btnMostrarCancelar', function () {
    $('#panelCancelarPedido').slideToggle(150);
});

$("body").on('click', '#btnConfirmarCancelar', function () {
    const motivo = $('input[name="motivoCancelar"]:checked').val();
    if (!motivo) {
        alert('Elige un motivo antes de confirmar.');
        return;
    }
    const comentario = $('#comentarioCancelar').val() || '';
    if (!confirm('¿Confirmas que este pedido NO se pudo entregar? Esta acción no se puede deshacer.')) return;

    fetch(`${ApiUrl}/flotas/cancelar-pedido`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ cod_orden: pedidoActualId, motivo, comentario }),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                cargarPedidos();
                $('#pedidoDetailModal').modal('hide');
            } else {
                alert(response.mensaje || 'No se pudo cancelar el pedido');
            }
        })
        .catch(error => console.error('Error al cancelar pedido:', error));
});

$('#pedidoDetailModal').on('hidden.bs.modal', function () {
    clearInterval(intervalMotosId);
    pedidoActualId = null;
    pedidoActualData = null;
    mapaPedido = null;
});
