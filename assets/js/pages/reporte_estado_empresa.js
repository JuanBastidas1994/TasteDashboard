/* ============================================================
   Reporte Estado Empresa — PDF diseño profesional
   ============================================================ */

var template;
let chartInstances = [];
let kpiData        = [];

$(function () {
    template = Handlebars.compile($("#widget-template").html());

    const today    = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    $('#fecha_inicio').val(isoDate(firstDay));
    $('#fecha_fin').val(isoDate(today));

    $('#cmbEmpresa').on('change', function () { loadSucursales($(this).val()); });
    $('#btnGenerar').on('click', generarReporte);
    $('#btnGenerarPDF').on('click', generarPDF);

    feather.replace();
});

function isoDate(d) { return d.toISOString().split('T')[0]; }

/* ------------------------------------------------------------------ */
/*  SUCURSALES                                                          */
/* ------------------------------------------------------------------ */

function loadSucursales(codEmpresa) {
    const $sel = $('#cmbSucursal');
    $sel.html('<option value="0">Todas las sucursales</option>');
    if (!codEmpresa) return;
    fetch('controllers/controlador_reporte_estado_empresa.php?metodo=getSucursales', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cod_empresa: parseInt(codEmpresa) })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success && resp.data)
            resp.data.forEach(s => $sel.append(`<option value="${s.cod_sucursal}">${s.nombre}</option>`));
    })
    .catch(() => {});
}

/* ------------------------------------------------------------------ */
/*  GENERAR REPORTE                                                     */
/* ------------------------------------------------------------------ */

function generarReporte() {
    const codEmpresa  = $('#cmbEmpresa').val();
    const codSucursal = $('#cmbSucursal').val();
    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin    = $('#fecha_fin').val();

    if (!codEmpresa)               { messageDone('Selecciona una empresa.', 'error'); return; }
    if (!fechaInicio || !fechaFin) { messageDone('Ingresa el rango de fechas.', 'error'); return; }
    if (fechaInicio > fechaFin)    { messageDone('La fecha inicio no puede ser mayor a la fecha fin.', 'error'); return; }

    const $btn = $('#btnGenerar');
    $btn.prop('disabled', true).html('Cargando…');
    $('#reportContent').hide();
    $('#widgetsInfo').html('');

    fetch('controllers/controlador_reporte_estado_empresa.php?metodo=getWidgetsEmpresa', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
        body: JSON.stringify({
            cod_empresa: parseInt(codEmpresa), cod_sucursal: parseInt(codSucursal),
            fecha_inicio: fechaInicio, fecha_fin: fechaFin
        })
    })
    .then(r => r.json())
    .then(response => {
        if (!response.success) { messageDone(response.mensaje || 'Error al obtener datos.', 'error'); return; }

        const empresaName  = $('#cmbEmpresa option:selected').text().trim();
        const sucursalName = parseInt(codSucursal) === 0
            ? 'Todas las sucursales'
            : $('#cmbSucursal option:selected').text().trim();

        $('#reportTitle').text('Reporte de Estado — ' + empresaName);
        $('#reportSubtitle').html(
            `<i data-feather="map-pin" style="width:14px;height:14px;vertical-align:middle;"></i> ${sucursalName}
             &nbsp;&nbsp;
             <i data-feather="calendar" style="width:14px;height:14px;vertical-align:middle;"></i>
             Del ${fechaInicio} al ${fechaFin}`
        );

        processWidgets(response);
        feather.replace();
        $('#reportContent').fadeIn(300);
        $('html, body').animate({ scrollTop: $('#reportContent').offset().top - 20 }, 400);
    })
    .catch(() => messageDone('Error de conexión. Intenta de nuevo.', 'error'))
    .finally(() => {
        $btn.prop('disabled', false).html('<i data-feather="bar-chart-2" style="width:16px;height:16px;"></i>&nbsp;Generar');
        feather.replace();
    });
}

/* ------------------------------------------------------------------ */
/*  WIDGETS EN PANTALLA                                                 */
/* ------------------------------------------------------------------ */

function trackChart(el, data, title) {
    data.chart.animations = { enabled: false };
    const chart = new ApexCharts(el, data);
    chartInstances.push({ title, el, chart, rendered: chart.render() });
}

function processWidgets(widget) {
    chartInstances = [];
    kpiData        = [];

    const totalClientes = (widget.clientes_recurrentes.nuevos || 0)
                        + (widget.clientes_recurrentes.recurrentes || 0);

    // 4 KPIs — col-3 = 4 por fila en pantalla
    kpiData.push({ title: 'Ventas Totales',  value: `$${widget.totalAmount.toFixed(2)}`, iconKey: 'ventas',   bg: '#ede9fe', fg: '#7c3aed' });
    kpiData.push({ title: 'Órdenes',         value: `${widget.total}`,                   iconKey: 'ordenes',  bg: '#dbeafe', fg: '#2563eb' });
    kpiData.push({ title: 'Clientes',        value: `${totalClientes}`,                  iconKey: 'clientes', bg: '#d1fae5', fg: '#059669' });
    kpiData.push({ title: 'Ticket Promedio', value: `$${widget.ticketPromedio}`,          iconKey: 'ticket',   bg: '#fef3c7', fg: '#d97706' });
    kpiData.forEach(k => addWidgetText(k.title, k.value, '', 'text', [], 120, 3));

    let chartData, $div;

    chartData = createLineChartData(widget.trend, 'Ventas Totales', 300);
    $div = addWidgetText('Evolución de Ventas', '', '', 'barchart', chartData, 300, 12);
    trackChart($div.find('.widget-chart')[0], chartData, 'EVOLUCIÓN DE VENTAS');

    chartData = createBarChartData(widget.topHours, 'Órdenes por Hora', 300);
    chartData.plotOptions.bar.horizontal = true;
    $div = addWidgetText('Ventas por Hora del día', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR HORA');

    chartData = createBarChartData(widget.deliveryTotals, 'Ventas por Tipo', 300);
    $div = addWidgetText('Ventas por Tipo de Entrega', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR CANAL');

    const pieData = createPieChartData(widget.clientes_recurrentes, 300);
    $div = addWidgetText('Clientes Recurrentes', '', '', 'piechart', pieData, 320);
    trackChart($div.find('.widget-chart')[0], pieData, 'CLIENTES RECURRENTES');

    chartData = createBarChartData(widget.topDaysSales, 'Ventas por Día', 300);
    $div = addWidgetText('Ventas por Día', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR DÍA');

    if (Object.keys(widget.topPlatforms).length > 1) {
        const piePlat = createPieChartData(widget.topPlatforms, 300);
        $div = addWidgetText('Ventas por Plataforma', '', '', 'piechart', piePlat, 320);
        trackChart($div.find('.widget-chart')[0], piePlat, 'VENTAS POR PLATAFORMA');
    }

    chartData = createBarChartData(widget.ordenesPorEstado, 'Órdenes por Estado', 300);
    chartData.plotOptions.bar.horizontal = true;
    $div = addWidgetText('Órdenes por Estado', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'ÓRDENES POR ESTADO');
}

/* ------------------------------------------------------------------ */
/*  UTILIDADES DE EXPORTACIÓN                                           */
/* ------------------------------------------------------------------ */

/** SVG del DOM → PNG base64 (evita Tainted canvas de ApexCharts v3.6) */
async function svgToPng(containerEl) {
    const svgEl = containerEl.querySelector('svg');
    if (!svgEl) return null;

    const w = svgEl.clientWidth  || containerEl.clientWidth  || 600;
    const h = svgEl.clientHeight || containerEl.clientHeight || 300;
    if (w === 0 || h === 0) return null;

    const clone = svgEl.cloneNode(true);
    clone.setAttribute('xmlns',  'http://www.w3.org/2000/svg');
    clone.setAttribute('width',  w);
    clone.setAttribute('height', h);
    clone.querySelectorAll('style').forEach(n => n.remove()); // evita taint por @font-face externos
    clone.querySelectorAll('image').forEach(n => n.remove());

    const dataUri = 'data:image/svg+xml;base64,' +
                    btoa(unescape(encodeURIComponent(new XMLSerializer().serializeToString(clone))));

    return new Promise(resolve => {
        const scale = 2, canvas = document.createElement('canvas');
        canvas.width  = w * scale;
        canvas.height = h * scale;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const img = new Image();
        img.onload  = () => { ctx.drawImage(img, 0, 0, canvas.width, canvas.height); resolve(canvas.toDataURL('image/png')); };
        img.onerror = () => resolve(null);
        img.src = dataUri;
    });
}

/** Carga una imagen del servidor como base64 */
async function loadImageBase64(url) {
    try {
        const res = await fetch(url);
        if (!res.ok) return null;
        const blob = await res.blob();
        return new Promise(resolve => {
            const r = new FileReader();
            r.onload  = () => resolve(r.result);
            r.onerror = () => resolve(null);
            r.readAsDataURL(blob);
        });
    } catch (e) { return null; }
}

/** Devuelve dimensiones naturales de una imagen base64 */
function imgDims(base64) {
    return new Promise(resolve => {
        const i = new Image();
        i.onload  = () => resolve({ w: i.naturalWidth, h: i.naturalHeight });
        i.onerror = () => resolve({ w: 120, h: 40 });
        i.src = base64;
    });
}

/* ------------------------------------------------------------------ */
/*  ICONOS KPI — SVG inline → PNG                                       */
/* ------------------------------------------------------------------ */

const KPI_ICON_SVGS = {
    ventas: (bg, fg) => `
        <circle cx="28" cy="28" r="28" fill="${bg}"/>
        <g transform="translate(16,16)" fill="none" stroke="${fg}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1" fill="${fg}"/><circle cx="20" cy="21" r="1" fill="${fg}"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </g>`,
    ordenes: (bg, fg) => `
        <circle cx="28" cy="28" r="28" fill="${bg}"/>
        <g transform="translate(16,16)" fill="none" stroke="${fg}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </g>`,
    clientes: (bg, fg) => `
        <circle cx="28" cy="28" r="28" fill="${bg}"/>
        <g transform="translate(16,16)" fill="none" stroke="${fg}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </g>`,
    ticket: (bg, fg) => `
        <circle cx="28" cy="28" r="28" fill="${bg}"/>
        <g transform="translate(16,16)" fill="none" stroke="${fg}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
            <circle cx="7" cy="7" r="1.2" fill="${fg}" stroke="${fg}"/>
        </g>`
};

async function renderIcon(svgContent) {
    const S = 56;
    const dataUri = 'data:image/svg+xml;base64,' +
        btoa(unescape(encodeURIComponent(
            `<svg xmlns="http://www.w3.org/2000/svg" width="${S}" height="${S}" viewBox="0 0 ${S} ${S}">${svgContent}</svg>`
        )));
    return new Promise(resolve => {
        const sc = 2, c = document.createElement('canvas');
        c.width = S * sc; c.height = S * sc;
        const ctx = c.getContext('2d');
        const img = new Image();
        img.onload  = () => { ctx.drawImage(img, 0, 0, c.width, c.height); resolve(c.toDataURL('image/png')); };
        img.onerror = () => resolve(null);
        img.src = dataUri;
    });
}

/* ------------------------------------------------------------------ */
/*  GENERAR PDF — DISEÑO PROFESIONAL                                    */
/* ------------------------------------------------------------------ */

async function generarPDF() {
    const $btn = $('#btnGenerarPDF');
    $btn.prop('disabled', true).html('Generando PDF…');

    try {
        await Promise.all(chartInstances.map(c => c.rendered));

        // Cargar logo, renderizar iconos KPI y exportar charts en paralelo
        const [logoBase64, kpiIconPngs, ...chartImages] = await Promise.all([
            loadImageBase64('assets/img/logo.png'),
            Promise.all(kpiData.map(k => renderIcon(KPI_ICON_SVGS[k.iconKey](k.bg, k.fg)))),
            ...chartInstances.map(async ({ title, el }) => ({
                title, imgURI: await svgToPng(el)
            }))
        ]);

        // Calcular dimensiones reales del logo
        let logoW = 32, logoH = 16;
        if (logoBase64) {
            const d = await imgDims(logoBase64);
            // logoH = 16;
            logoW = Math.min(logoH * (d.w / d.h), 52);
        }

        // Datos del reporte
        const empresaName  = $('#cmbEmpresa option:selected').text().trim();
        const sucursalName = parseInt($('#cmbSucursal').val()) === 0
            ? 'Todas las sucursales'
            : $('#cmbSucursal option:selected').text().trim();
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin    = $('#fecha_fin').val();
        const hoy  = new Date().toLocaleDateString('es-ES',  { day:'2-digit', month:'long', year:'numeric' });
        const hora = new Date().toLocaleTimeString('es-ES',  { hour:'2-digit', minute:'2-digit' });

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        /* ── Paleta ── */
        const DARK    = [15,  14,  50];     // header / footer bg
        const DARK2   = [245, 246, 252];     // KPI band bg
        const CARD_BG = [42,  38, 116];     // KPI card inner
        const ACCENT  = [27,  85, 226];     // azul marca
        const WHITE   = [255, 255, 255];
        const PAGE_BG = [245, 246, 252];    // fondo gris muy suave
        const HINT    = [168, 180, 230];    // texto claro en fondos oscuros
        const TXT     = [22,  22,  55];     // texto oscuro en fondos claros
        const MUTED   = [140, 148, 180];
        const BORDER  = [218, 222, 238];

        /* ── Dimensiones de página ── */
        const PW = 210, PH = 297;
        const M  = 14;
        const CW = PW - M * 2;             // 182mm ancho contenido

        const HDR1_H  = 42;                // header p1
        const KPI_CH  = 26;                // altura tarjeta KPI
        const HDR2_H  = 24;                // header p2+
        const FTR_H   = 22;                // footer
        const BOT     = PH - FTR_H;        // fin contenido = 275mm

        let pageNum = 1;

        /* ── HELPERS ── */

        function pageBg() {
            pdf.setFillColor(...PAGE_BG);
            pdf.rect(0, 0, PW, PH, 'F');
        }

        function watermark() {
            pdf.setTextColor(222, 224, 240);
            pdf.setFontSize(65);
            pdf.setFont('helvetica', 'bold');
            pdf.text('TASTE', PW / 2, PH / 2, { align: 'center', angle: 45 });
            pdf.setTextColor(0, 0, 0);
        }

        function headerP1() {
            pdf.setFillColor(...DARK);
            pdf.rect(0, 0, PW, HDR1_H, 'F');
            // Línea de acento en la base del header
            pdf.setFillColor(...ACCENT);
            pdf.rect(0, HDR1_H - 2, PW, 2, 'F');

            // Logo
            if (logoBase64) {
                const lH = 10, lW = lH * (logoW / logoH);
                pdf.addImage(logoBase64, 'PNG', M, (HDR1_H - lH) / 2, lW, lH);
            } else {
                pdf.setTextColor(...WHITE); pdf.setFontSize(18); pdf.setFont('helvetica', 'bold');
                pdf.text('TASTE', M, HDR1_H / 2 + 4);
            }

            // Título derecha — líneas más juntas
            pdf.setTextColor(...WHITE);
            pdf.setFontSize(12); pdf.setFont('helvetica', 'bold');
            pdf.text('REPORTE DE INDICADORES', PW - M, 12, { align: 'right' });

            pdf.setDrawColor(...ACCENT); pdf.setLineWidth(0.4);
            pdf.line(PW - M - 72, 14, PW - M, 14);

            pdf.setTextColor(...HINT); pdf.setFontSize(8); pdf.setFont('helvetica', 'normal');
            pdf.text(`${fechaInicio}  —  ${fechaFin}`, PW - M, 20, { align: 'right' });
            pdf.text(empresaName,  PW - M, 26, { align: 'right' });
            pdf.text(sucursalName, PW - M, 32, { align: 'right' });
        }

        /** KPIs: tarjetas blancas con borde gris + icono + título + valor */
        function kpiRow() {
            const n   = kpiData.length;
            const cW  = (CW - (n - 1) * 4) / n;   // ~42.5mm c/u
            const cH  = KPI_CH;
            const cY  = HDR1_H + 8;
            const IR  = 12;  // tamaño del icono en mm

            kpiData.forEach((kpi, i) => {
                const cx = M + i * (cW + 4);

                // Sombra suave
                pdf.setFillColor(210, 214, 232);
                pdf.roundedRect(cx + 0.8, cY + 0.8, cW, cH, 4, 4, 'F');
                // Card blanca
                pdf.setFillColor(...WHITE);
                pdf.roundedRect(cx, cY, cW, cH, 4, 4, 'F');
                // Borde gris suave
                pdf.setDrawColor(...BORDER); pdf.setLineWidth(0.25);
                pdf.roundedRect(cx, cY, cW, cH, 4, 4, 'S');

                // Icono (SVG renderizado como PNG)
                const iconX = cx + 5;
                const iconY = cY + (cH - IR) / 2;
                if (kpiIconPngs && kpiIconPngs[i]) {
                    pdf.addImage(kpiIconPngs[i], 'PNG', iconX, iconY, IR, IR);
                }

                // Texto
                const textX = iconX + IR + 4;
                pdf.setFontSize(6.5); pdf.setFont('helvetica', 'bold');
                pdf.setTextColor(120, 130, 165);
                pdf.text(kpi.title.toUpperCase(), textX, cY + 10);

                pdf.setFontSize(14); pdf.setFont('helvetica', 'bold');
                pdf.setTextColor(...TXT);
                pdf.text(String(kpi.value), textX, cY + 21);
            });

            return cY + cH + 8;  // Y donde empieza el contenido
        }

        function headerP2() {
            pdf.setFillColor(...DARK);
            pdf.rect(0, 0, PW, HDR2_H, 'F');
            pdf.setFillColor(...ACCENT);
            pdf.rect(0, HDR2_H - 1.5, PW, 1.5, 'F');

            if (logoBase64) {
                const lH = 12, lW = lH * (logoW / logoH);
                pdf.addImage(logoBase64, 'PNG', M, (HDR2_H - lH) / 2, lW, lH);
            }
            pdf.setTextColor(...WHITE); pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
            pdf.text('REPORTE DE INDICADORES', PW - M, 10, { align: 'right' });
            pdf.setTextColor(...HINT); pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
            pdf.text(`${empresaName}  |  ${fechaInicio} — ${fechaFin}`, PW - M, 17, { align: 'right' });
        }

        function footer() {
            const fy = PH - FTR_H;
            pdf.setFillColor(...DARK);
            pdf.rect(0, fy, PW, FTR_H, 'F');
            pdf.setFillColor(...ACCENT);
            pdf.rect(0, fy, PW, 1.5, 'F');

            if (logoBase64) {
                const lH = 10, lW = lH * (logoW / logoH);
                pdf.addImage(logoBase64, 'PNG', M, fy + (FTR_H - lH) / 2, lW, lH);
            }
            pdf.setTextColor(...HINT); pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
            pdf.text(`Reporte generado el ${hoy}, ${hora}`, PW - M, fy + 9, { align: 'right' });
            pdf.text(`© ${new Date().getFullYear()} Taste. Todos los derechos reservados.`, PW - M, fy + 16, { align: 'right' });
            pdf.setTextColor(110, 120, 170); pdf.setFontSize(7);
            pdf.text(`${pageNum}`, PW / 2, fy + 13, { align: 'center' });
        }

        function chartCard(imgURI, title, cx, cy, cw, ch) {
            const PT = 12;  // padding top para el título
            const PL = 4;

            // Sombra
            // pdf.setFillColor(210, 214, 232);
            // pdf.roundedRect(cx + 1, cy + 1, cw, ch + PT, 3, 3, 'F');
            // Card blanca
            pdf.setFillColor(...WHITE);
            pdf.roundedRect(cx, cy, cw, ch + PT, 3, 3, 'F');
            // Borde
            pdf.setDrawColor(...BORDER); pdf.setLineWidth(0.2);
            pdf.roundedRect(cx, cy, cw, ch + PT, 3, 3, 'S');
            // // Barra de acento arriba
            // pdf.setFillColor(...ACCENT);
            // pdf.roundedRect(cx, cy, cw, 2.5, 1, 1, 'F');

            // Título
            pdf.setFontSize(7.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...TXT);
            pdf.text(title, cx + PL + 2, cy + 9);

            if (imgURI) {
                pdf.addImage(imgURI, 'PNG', cx + PL, cy + PT, cw - PL * 2, ch);
            } else {
                pdf.setFontSize(7); pdf.setFont('helvetica', 'italic'); pdf.setTextColor(...MUTED);
                pdf.text('Sin datos en el período seleccionado', cx + cw / 2, cy + PT + ch / 2, { align: 'center' });
                pdf.setTextColor(0, 0, 0);
            }
        }

        function nextPage() {
            footer();
            pdf.addPage();
            pageNum++;
            pageBg();
            watermark();
            headerP2();
            return HDR2_H + 5;
        }

        /* ── CONSTRUIR PDF ── */

        // Página 1
        pageBg();
        watermark();
        headerP1();
        let y = kpiRow();  // dibuja KPIs y devuelve Y de inicio de contenido

        // Chart 0: Evolución de Ventas — ancho completo
        if (chartImages.length > 0) {
            const ch = 60;
            if (y + ch + 14 > BOT) y = nextPage();
            chartCard(chartImages[0].imgURI, chartImages[0].title, M, y, CW, ch);
            y += ch + 16;
        }

        // Charts restantes: 2 columnas
        const colW = (CW - 5) / 2;
        const colH = 50;
        const rowH = colH + 16;
        let   col  = 0;
        let   rowY = y;

        for (let i = 1; i < chartImages.length; i++) {
            if (col === 0 && rowY + rowH > BOT) {
                rowY = nextPage();
            }
            chartCard(chartImages[i].imgURI, chartImages[i].title, M + col * (colW + 5), rowY, colW, colH);
            col++;
            if (col === 2) { col = 0; rowY += rowH; }
        }

        footer();

        pdf.save(`reporte_${empresaName.replace(/\s+/g, '_')}_${fechaInicio}_${fechaFin}.pdf`);

    } catch (err) {
        console.error('Error al generar PDF:', err);
        messageDone('Error al generar el PDF. Revisa la consola.', 'error');
    }

    $btn.prop('disabled', false).html('<i data-feather="file-text" style="width:16px;height:16px;"></i>&nbsp;Descargar PDF');
    feather.replace();
}
