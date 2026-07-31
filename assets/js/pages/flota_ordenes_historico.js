// Histórico completo de pedidos de la flota — ver api_flotas/controllers/Flotas.php::historico
const { API_FLOTAS } = window.__CONFIG__;

const ApiKeyFlota = document.getElementById('apikey_flota').value;
let paginaActual = 1;

const ESTADO_STYLE = {
    ASIGNADA: { bg: '#DBEAFE', color: '#1E40AF', label: 'Asignada' },
    ENVIANDO: { bg: '#FEF3C7', color: '#92400E', label: 'Enviando' },
    ENTREGADA: { bg: '#DCFCE7', color: '#166534', label: 'Entregada' },
    NO_ENTREGADA: { bg: '#FEE2E2', color: '#991B1B', label: 'No entregada' },
    ANULADA: { bg: '#F3F4F6', color: '#374151', label: 'Anulada' },
    CANCELADA: { bg: '#F3F4F6', color: '#374151', label: 'Cancelada' },
};

function estadoBadge(estado) {
    const s = ESTADO_STYLE[estado] || { bg: '#F3F4F6', color: '#374151', label: estado };
    return `<span class="shadow-none badge" style="background:${s.bg};color:${s.color};border:1px solid ${s.color}22;">${s.label}</span>`;
}

function formatFecha(fecha) {
    if (!fecha) return '';
    return new Date(fecha.replace(' ', 'T')).toLocaleString('es-EC', { dateStyle: 'short', timeStyle: 'short' });
}

function filtrosActuales() {
    return {
        comercio: $('#cmbComercio').val() || '',
        estado: $('#cmbEstado').val() || '',
        motorizado: $('#cmbMotorizado').val() || '',
        fecha_inicio: $('#txtFechaInicio').val() || '',
        fecha_fin: $('#txtFechaFin').val() || '',
    };
}

function cargarHistorico() {
    const params = new URLSearchParams({ ...filtrosActuales(), pagina: paginaActual });

    $('#historicoBody').html('<tr><td colspan="7" class="text-center text-muted">Cargando...</td></tr>');

    fetch(`${API_FLOTAS}/flotas/historico?${params.toString()}`, {
        headers: { 'Api-Key': ApiKeyFlota },
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) {
                $('#historicoBody').html('<tr><td colspan="7" class="text-center text-muted">No se pudo cargar el histórico</td></tr>');
                return;
            }
            pintarTabla(response.data, response.total, response.pagina, response.por_pagina);
        })
        .catch(error => {
            console.error('Error al cargar histórico:', error);
            $('#historicoBody').html('<tr><td colspan="7" class="text-center text-muted">Error al cargar el histórico</td></tr>');
        });
}

function pintarTabla(filas, total, pagina, porPagina) {
    if (!filas.length) {
        $('#historicoBody').html('<tr><td colspan="7" class="text-center text-muted">Sin pedidos para estos filtros</td></tr>');
    } else {
        const inicio = (pagina - 1) * porPagina;
        $('#historicoBody').html(filas.map((f, i) => `
            <tr>
                <td>${inicio + i + 1}</td>
                <td>${f.empresa_nombre || ''}</td>
                <td>${(f.cliente_nombre || '') + ' ' + (f.cliente_apellido || '')}</td>
                <td>${formatFecha(f.fecha)}</td>
                <td>$${parseFloat(f.total || 0).toFixed(2)}</td>
                <td>${f.motorizado_nombre ? (f.motorizado_nombre + ' ' + (f.motorizado_apellido || '')) : '<span class="text-muted">Sin asignar</span>'}</td>
                <td class="text-center">${estadoBadge(f.estado)}</td>
            </tr>
        `).join(''));
    }

    const totalPaginas = Math.max(1, Math.ceil(total / porPagina));
    $('#historicoResumen').text(`${total} pedido(s) — página ${pagina} de ${totalPaginas}`);
    $('#btnPagAnterior').prop('disabled', pagina <= 1);
    $('#btnPagSiguiente').prop('disabled', pagina >= totalPaginas);
}

$(function () {
    cargarHistorico();

    $('#cmbComercio, #cmbEstado, #cmbMotorizado, #txtFechaInicio, #txtFechaFin').on('change', function () {
        paginaActual = 1;
        cargarHistorico();
    });

    $('#btnLimpiarFiltros').on('click', function () {
        $('#cmbComercio, #cmbEstado, #cmbMotorizado').val('');
        $('#txtFechaInicio, #txtFechaFin').val('');
        paginaActual = 1;
        cargarHistorico();
    });

    $('#btnPagAnterior').on('click', function () {
        if (paginaActual > 1) {
            paginaActual--;
            cargarHistorico();
        }
    });

    $('#btnPagSiguiente').on('click', function () {
        paginaActual++;
        cargarHistorico();
    });
});
