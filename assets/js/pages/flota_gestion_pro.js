const { API_FLOTAS } = window.__CONFIG__;
const ApiUrl = API_FLOTAS;
let ApiKey = "";

let pedidoActualId = null;
let pedidoActualData = null;
let ultimosPedidosData = null;
let mapaPedido = null;
let markersMotos = [];
let intervalPedidosId = 0;
let intervalMotosId = 0;
let motorizadosActuales = [];

// Modal "Ubicación de mis motorizados" — mapa de solo lectura, independiente de cualquier pedido.
let mapaUbicacion = null;
let markersUbicacion = [];
let intervalUbicacionId = 0;
let motorizadosUbicacionActuales = [];
let ubicacionMapaAjustado = false;

// Modal de detalle de un motorizado (verDetalleMoto) — abrible tanto desde la pestaña Asignación
// como desde el modal de Ubicación, así que guarda su propio motorizado activo.
let motorizadoDetalleActualId = null;

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
    disponible: 'assets/img/moto_disponible.png',
    en_carrera: 'assets/img/moto_encarrera.png',
    no_disponible: 'assets/img/moto_ocupada.png',
};

// Mismos 4 motivos que usa la app del motorizado para reportar y que el admin vuelve a elegir
// al resolver — ver cl_ordenes::MOTIVOS_INCIDENCIA (api_flotas).
const MOTIVO_INCIDENCIA_LABEL = {
    NO_CONTESTA: 'El cliente no contesta',
    DIRECCION_INCORRECTA: 'La dirección es incorrecta',
    CLIENTE_RECHAZO: 'El cliente no quiso recibir el pedido',
    NO_QUISO_PAGAR: 'El cliente no quiso pagar',
};

const RESUELTO_POR_LABEL = { MOTORIZADO: 'el motorizado', FLOTA: 'un administrador de flota', SUCURSAL: 'un administrador de sucursal' };

Handlebars.registerHelper('badgeEstado', estado => ESTADO_TRABAJO_COLOR[estado] || 'secondary');
Handlebars.registerHelper('labelEstado', estado => ESTADO_TRABAJO_LABEL[estado] || estado);
Handlebars.registerHelper('motivoLabel', motivo => MOTIVO_INCIDENCIA_LABEL[motivo] || motivo);
Handlebars.registerHelper('fechaFormateada', raw => formatFecha(raw));
Handlebars.registerHelper('fechaRelativa', raw => formatFechaRelativa(raw));
Handlebars.registerHelper('resueltoPorLabel', quien => RESUELTO_POR_LABEL[quien] || quien);
Handlebars.registerHelper('if_eq', function (a, b, options) {
    return a === b ? options.fn(this) : options.inverse(this);
});

// El backend guarda fechas como "YYYY-MM-DD HH:MM:SS"; el espacio en vez de "T" hace que
// Date() falle en algunos navegadores, por eso el replace.
function formatFecha(raw) {
    if (!raw) return '--';
    const d = new Date(raw.replace(' ', 'T'));
    if (isNaN(d.getTime())) return raw;
    return d.toLocaleString('es-EC', { day: '2-digit', month: 'short', hour: 'numeric', minute: '2-digit' });
}

// "Hace 5 minutos" / "Hace 2 horas" para la última ubicación reportada por el motorizado — así el
// admin detecta de un vistazo si un motorizado se quedó sin datos/GPS en vez de que esté quieto.
function formatFechaRelativa(raw) {
    if (!raw) return 'Sin ubicación reportada';
    const d = new Date(raw.replace(' ', 'T'));
    if (isNaN(d.getTime())) return raw;

    const segundos = Math.round((Date.now() - d.getTime()) / 1000);
    if (segundos < 60) return 'Hace instantes';
    const minutos = Math.round(segundos / 60);
    if (minutos < 60) return `Hace ${minutos} min`;
    const horas = Math.round(minutos / 60);
    if (horas < 24) return `Hace ${horas} h`;
    const dias = Math.round(horas / 24);
    return `Hace ${dias} d`;
}

// Iniciales para el label del pin en el mapa (máx. 2 letras) — "Pepito Perez" -> "PP".
function iniciales(nombreCompleto) {
    if (!nombreCompleto) return '';
    return nombreCompleto
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map(palabra => palabra.charAt(0).toUpperCase())
        .join('');
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
    cargarOpcionesMotorizado();
});

// Llena el <select> de "Filtrar por motorizado" — se pide una sola vez al cargar la página, no
// necesita refrescarse junto con la posición/estado de los motorizados.
function cargarOpcionesMotorizado() {
    fetch(`${ApiUrl}/flotas/motorizados`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) return;
            const $select = $('#selectMotorizado');
            ordenarMotorizados(response.data).forEach(m => {
                $select.append(new Option(m.nombres, m.id));
            });
            $select.trigger('change'); // refresca la lista visible de Select2 con las opciones recién agregadas
        })
        .catch(error => console.error('Error al cargar motorizados para el filtro:', error));
}

function cargarPedidos() {
    const comercio = $('#selectComercio').val() || '';
    const motorizado = $('#selectMotorizado').val() || '';
    const params = new URLSearchParams();
    if (comercio) params.set('comercio', comercio);
    if (motorizado) params.set('motorizado', motorizado);
    const qs = params.toString();

    fetch(`${ApiUrl}/flotas/pedidos${qs ? '?' + qs : ''}`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) return;
            ultimosPedidosData = response.data;
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

// Pedido(s) activo(s) que un motorizado tiene AHORA MISMO, según el último fetch de cargarPedidos()
// — usado por el modal de detalle del motorizado (verDetalleMoto). Puede ser más de uno si tiene
// varios pedidos simultáneos.
function buscarPedidosActivosDeMotorizado(cod_motorizado) {
    if (!ultimosPedidosData) return [];
    const id = Number(cod_motorizado);
    const encontrados = [];
    ['incidencia', 'en_curso', 'enviando'].forEach(col => {
        (ultimosPedidosData[col] || []).forEach(p => {
            if (Number(p.cod_motorizado) === id) encontrados.push(p);
        });
    });
    return encontrados;
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

    const templateIncidencias = Handlebars.compile($("#pedido-historial-incidencias-template").html());
    $("#pedido-historial-incidencias").html(templateIncidencias(orden.incidencias || []));
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
            motorizadosActuales = ordenarMotorizados(response.data);
            renderListaMotorizadosFiltrada();
            dibujarMotosEnMapa(motorizadosActuales);
        })
        .catch(error => console.error('Error al cargar motorizados:', error));
}

// No disponibles al final — los disponibles y en carrera son los que interesan asignar primero.
function ordenarMotorizados(motorizados) {
    const disponibles = motorizados.filter(m => m.estado_trabajo !== 'no_disponible');
    const noDisponibles = motorizados.filter(m => m.estado_trabajo === 'no_disponible');
    return disponibles.concat(noDisponibles);
}

function renderListaMotorizadosFiltrada() {
    const filtro = ($('#filtroMotorizado').val() || '').toLowerCase().trim();
    const lista = filtro
        ? motorizadosActuales.filter(m => (m.nombres || '').toLowerCase().includes(filtro))
        : motorizadosActuales;
    renderListaMotorizados(lista);
}

function renderListaMotorizados(motorizados) {
    const template = Handlebars.compile($("#motorizados-template").html());
    $("#lista-motorizados-cercanos").html(template(motorizados));
    feather.replace();
}

$("body").on('input', '#filtroMotorizado', renderListaMotorizadosFiltrada);

// ---- Modal "Ubicación de mis motorizados" — mismo patrón que la pestaña Asignación, pero sin
// pedido de por medio y sin poder asignar: es solo para ver dónde está cada quien ahora mismo. ----

$('#btnUbicacionMotorizados').on('click', function () {
    $('#modalUbicacionMotorizados').modal();
});

// El mapa necesita el contenedor con tamaño real, igual que el de asignación — se inicializa
// recién cuando el modal terminó de mostrarse.
$('#modalUbicacionMotorizados').on('shown.bs.modal', function () {
    initMapaUbicacion();
});

$('#modalUbicacionMotorizados').on('hidden.bs.modal', function () {
    clearInterval(intervalUbicacionId);
    mapaUbicacion = null;
    markersUbicacion = [];
    motorizadosUbicacionActuales = [];
    ubicacionMapaAjustado = false;
    $('#filtroMotorizadoUbicacion').val('');
});

function initMapaUbicacion() {
    const el = document.getElementById('mapaUbicacionMotorizados');
    if (!el) return;

    // Centro por defecto (Guayaquil) mientras cargan los motorizados — luego se ajusta a sus
    // posiciones reales con fitBounds() la primera vez que hay datos.
    mapaUbicacion = new google.maps.Map(el, { zoom: 12, center: { lat: -2.170998, lng: -79.922359 } });

    cargarMotorizadosUbicacion();
    clearInterval(intervalUbicacionId);
    intervalUbicacionId = setInterval(cargarMotorizadosUbicacion, 8000);
}

function cargarMotorizadosUbicacion() {
    fetch(`${ApiUrl}/flotas/motorizados`, {
        method: 'GET',
        headers: { 'Api-Key': ApiKey },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) return;
            motorizadosUbicacionActuales = ordenarMotorizados(response.data);
            renderListaMotorizadosFiltradaUbicacion();
            markersUbicacion = dibujarMotosEnMapaGenerico(mapaUbicacion, markersUbicacion, motorizadosUbicacionActuales, false);

            // Solo la primera vez: encuadra el mapa para que se vean todos los pines. Si se repite
            // en cada refresco de 8s, le arruina al admin el zoom/paneo que haya hecho a mano.
            if (!ubicacionMapaAjustado && markersUbicacion.length) {
                const bounds = new google.maps.LatLngBounds();
                markersUbicacion.forEach(m => bounds.extend(m.getPosition()));
                mapaUbicacion.fitBounds(bounds);
                ubicacionMapaAjustado = true;
            }
        })
        .catch(error => console.error('Error al cargar motorizados:', error));
}

function renderListaMotorizadosFiltradaUbicacion() {
    const filtro = ($('#filtroMotorizadoUbicacion').val() || '').toLowerCase().trim();
    const lista = filtro
        ? motorizadosUbicacionActuales.filter(m => (m.nombres || '').toLowerCase().includes(filtro))
        : motorizadosUbicacionActuales;
    const template = Handlebars.compile($("#motorizados-ubicacion-template").html());
    $("#lista-motorizados-ubicacion").html(template(lista));
    feather.replace();
}

$("body").on('input', '#filtroMotorizadoUbicacion', renderListaMotorizadosFiltradaUbicacion);

// Dibuja los pines de motorizados en CUALQUIER mapa (el del pedido con click-para-asignar, o el
// de solo-lectura de "Ubicación de mis motorizados" con click-para-ver-detalle). Devuelve el
// array de markers nuevo — el caller debe reasignarlo (markersX = dibujarMotosEnMapaGenerico(...)).
function dibujarMotosEnMapaGenerico(mapa, markersActuales, motorizados, permitirAsignar) {
    markersActuales.forEach(m => m.setMap(null));
    if (!mapa) return [];

    return motorizados
        .filter(moto => !isNaN(parseFloat(moto.latitud)) && !isNaN(parseFloat(moto.longitud)))
        .map(moto => {
            const marker = new google.maps.Marker({
                map: mapa,
                icon: {
                    url: ICONO_MOTO[moto.estado_trabajo] || ICONO_MOTO.no_disponible,
                    scaledSize: new google.maps.Size(40, 40),
                    labelOrigin: new google.maps.Point(20, 48),
                },
                label: {
                    text: iniciales(moto.nombres),
                    color: '#1f2937',
                    fontSize: '11px',
                    fontWeight: 'bold',
                    className: 'flota-marker-label',
                },
                position: { lat: parseFloat(moto.latitud), lng: parseFloat(moto.longitud) },
                info_moto: moto,
            });
            marker.addListener('click', function () {
                if (permitirAsignar) {
                    asignarDesdeMapa(this.info_moto.id);
                } else {
                    verDetalleMoto(this.info_moto.id);
                }
            });
            return marker;
        });
}

function dibujarMotosEnMapa(motorizados) {
    markersMotos = dibujarMotosEnMapaGenerico(mapaPedido, markersMotos, motorizados, true);
}

// Centra un mapa en el marker de un motorizado y lo resalta con un rebote breve.
function centrarMotoEnMapaGenerico(mapa, markers, cod_motorizado) {
    if (!mapa) return;
    const id = Number(cod_motorizado);
    const marker = markers.find(m => m.info_moto && Number(m.info_moto.id) === id);
    if (!marker) return;

    mapa.panTo(marker.getPosition());
    mapa.setZoom(Math.max(mapa.getZoom(), 15));
    marker.setAnimation(google.maps.Animation.BOUNCE);
    setTimeout(() => marker.setAnimation(null), 1400);
}

function centrarMotoEnMapa(cod_motorizado, evt) {
    if (evt) evt.stopPropagation();
    centrarMotoEnMapaGenerico(mapaPedido, markersMotos, cod_motorizado);
}

function centrarMotoEnMapaUbicacion(cod_motorizado, evt) {
    if (evt) evt.stopPropagation();
    centrarMotoEnMapaGenerico(mapaUbicacion, markersUbicacion, cod_motorizado);
}

// Busca el motorizado (con su posición/estado actuales) en la lista que esté cargada en ese
// momento — puede venir de la pestaña Asignación o del modal de Ubicación, según desde dónde se
// haya abierto el detalle.
function buscarMotoPorId(cod_motorizado) {
    const id = Number(cod_motorizado);
    return motorizadosActuales.find(m => Number(m.id) === id)
        || motorizadosUbicacionActuales.find(m => Number(m.id) === id);
}

function verDetalleMoto(cod_motorizado, evt) {
    if (evt) evt.stopPropagation();
    const moto = buscarMotoPorId(cod_motorizado);
    if (!moto) return;

    motorizadoDetalleActualId = cod_motorizado;
    const datos = Object.assign({}, moto, { pedidos_actuales: buscarPedidosActivosDeMotorizado(cod_motorizado) });
    const template = Handlebars.compile($("#detalle-moto-template").html());
    $("#detalleMotoTitle").html(moto.nombres);
    $("#detalleMotoContenido").html(template(datos));
    feather.replace();

    $('#modalDetalleMoto').modal();
}

// El mini-mapa se inicializa recién cuando el modal terminó de mostrarse — mismo motivo que el
// mapa de asignación: el contenedor necesita tamaño real, y un modal en transición no lo tiene.
$('#modalDetalleMoto').on('shown.bs.modal', function () {
    const moto = buscarMotoPorId(motorizadoDetalleActualId);
    if (moto) initMapaDetalleMoto(moto);
});

$('#modalDetalleMoto').on('hidden.bs.modal', function () {
    motorizadoDetalleActualId = null;
});

function initMapaDetalleMoto(moto) {
    const el = document.getElementById('mapaDetalleMoto');
    if (!el) return;

    const lat = parseFloat(moto.latitud);
    const lng = parseFloat(moto.longitud);
    if (isNaN(lat) || isNaN(lng)) {
        el.innerHTML = '<div class="text-muted text-center pt-5">Sin ubicación disponible</div>';
        return;
    }

    const mapa = new google.maps.Map(el, { zoom: 15, center: { lat, lng } });
    new google.maps.Marker({
        map: mapa,
        position: { lat, lng },
        icon: {
            url: ICONO_MOTO[moto.estado_trabajo] || ICONO_MOTO.no_disponible,
            scaledSize: new google.maps.Size(40, 40),
        },
    });
}

// Cierra el modal de detalle y abre el pedido — usado desde la tarjeta de "pedido activo" dentro
// del detalle del motorizado (típicamente al abrirse desde el modal de Ubicación).
function cerrarDetalleYAbrirPedido(cod_orden) {
    $('#modalDetalleMoto').modal('hide');
    openPedido(cod_orden);
}

function irATabAsignacion() {
    $('#pedidoTabsNav a[href="#tab-asignacion"]').tab('show');
}

function asignarDesdeMapa(cod_motorizado, evt) {
    if (evt) evt.stopPropagation();
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

$("body").on('click', '#btnResolverIncidencia', function () {
    const comentario = prompt('¿Cómo se resolvió? (opcional, deja en blanco para omitir)') || '';
    if (!confirm('¿Confirmas que el problema se resolvió y el pedido continúa?')) return;

    fetch(`${ApiUrl}/flotas/resolver-incidencia`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ cod_orden: pedidoActualId, comentario }),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                cargarPedidos();
                openPedido(pedidoActualId);
            } else {
                alert(response.mensaje || 'No se pudo resolver el problema');
            }
        })
        .catch(error => console.error('Error al resolver incidencia:', error));
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
    markersMotos = [];
    motorizadosActuales = [];
    $('#filtroMotorizado').val('');
});
