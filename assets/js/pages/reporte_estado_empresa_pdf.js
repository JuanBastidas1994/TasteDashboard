/* ============================================================
   reporte_estado_empresa_pdf.js
   PDF ejecutivo horizontal — Reporte de Estado por Empresa
   Depende de: pdf-utils.js  (TastePDF debe estar cargado)
   ============================================================ */

async function generarPDF() {
    const $btn = $('#btnGenerarPDF');
    $btn.prop('disabled', true).html('Generando PDF…');

    try {
        /* ── 1. Esperar que todos los gráficos terminen de renderizar ── */
        await Promise.all(chartInstances.map(c => c.rendered));

        /* ── 2. Exportar gráficos como PNG manteniendo aspect ratio ──
           Orden en chartInstances (definido en reporte_estado_empresa.js):
           [0] Evolución de Ventas  [1] Ventas/Hora  [2] Ventas/Canal
           [3] Clientes Recurrentes [4] Ventas/Día   [5] Plataforma (opcional)
           [6] Órdenes por Estado                                          ── */
        const charts = await Promise.all(
            chartInstances.map(({ title, el }) =>
                TastePDF.svgToPng(el).then(r => ({ title, img: r }))
            )
        );

        /* ── 3. Logo + iconos KPI + iconos extra — todo en paralelo ── */
        const [
            logoB64,
            kpiIcons,
            iconCheck,
            iconAlert,
            iconStar,
            iconClock,
            iconPackage,
            iconTruck,
            iconUserPlus,
            iconTruckSuccess,
        ] = await Promise.all([
            TastePDF.loadImageBase64('assets/img/logo-dark.png'),
            TastePDF.loadKpiIcons(),
            TastePDF.loadIcon('check-circle',   [22,  163, 74],  48),
            TastePDF.loadIcon('alert-triangle',  [234, 88,  12], 48),
            TastePDF.loadIcon('star',            [234, 179, 8],  48),
            TastePDF.loadIcon('clock',           [30,  30,  30], 48),
            TastePDF.loadIcon('package',         [30,  30,  30], 48),
            TastePDF.loadIcon('truck',           [30,  30,  30], 48),
            TastePDF.loadIcon('user-plus',       [30,  30,  30], 48),
            TastePDF.loadIcon('truck',   [22,  163, 74],  48),
        ]);

        let logoNatW = 120, logoNatH = 40;
        if (logoB64) {
            const d = await TastePDF.imageDimensions(logoB64);
            logoNatW = d.w; logoNatH = d.h;
        }

        /* ── 4. Meta del reporte ── */
        const empresaName  = $('#cmbEmpresa option:selected').text().trim();
        const sucursalName = parseInt($('#cmbSucursal').val()) === 0
            ? 'Todas las sucursales'
            : $('#cmbSucursal option:selected').text().trim();
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin    = $('#fecha_fin').val();
        const hoy  = new Date().toLocaleDateString('es-ES', { day:'2-digit', month:'long', year:'numeric' });
        const hora = new Date().toLocaleTimeString('es-ES', { hour:'2-digit', minute:'2-digit' });

        /* ── 5. Inicializar jsPDF landscape A4 ── */
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        /* Pasar kpiIcons al PDFBuilder como 4to parámetro */
        const B = new TastePDF.PDFBuilder(pdf, {
            pageW: 297, pageH: 210, marginX: 12, hdrH: 20, ftrH: 11
        }, logoB64, kpiIcons);
        B.setLogoDimensions(logoNatW, logoNatH);

        const { PW, PH, M, CW, HDR, FTR, CONT_Y, BOT, C } = B;
        let pageNum = 1;

        /* helper local — cálculo % delivery desde widgetData real */
        function getDeliveryPct() {
            return (typeof widgetData !== 'undefined' && widgetData.deliveryTotals)
                ? (() => {
                    const vals  = Object.values(widgetData.deliveryTotals);
                    const total = vals.reduce((a, b) => a + b, 0);
                    const del   = widgetData.deliveryTotals['Delivery'] || 0;
                    return total > 0 ? del / total : 0.78;
                  })()
                : 0.78;
        }

        /* helper formato dinero para PDF */
        function fmtPDF(v) {
            return '$' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        /* ── Textos dinámicos calculados desde widgetData ── */
        const wd = widgetData || {};

        // 1. "X de cada 10 clientes regresaron…"
        const _cr       = wd.clientes_recurrentes || { nuevos: 0, recurrentes: 0 };
        const _totalCli = (_cr.nuevos + _cr.recurrentes) || 1;
        const _cadaDiez = Math.round((_cr.recurrentes / _totalCli) * 10);
        const txtClientes = `${_cadaDiez} de cada 10 clientes regresaron a comprar este mes.`;

        // 2. "Los X fueron tu mejor día con mayores ventas."
        const _dayMap  = { Lun:'Lunes', Mar:'Martes', 'Mié':'Miércoles', Jue:'Jueves', Vie:'Viernes', 'Sáb':'Sábados', Dom:'Domingos' };
        const _days    = wd.topDaysSales || {};
        const _bestDay = Object.keys(_days).length
            ? Object.keys(_days).reduce((a, b) => _days[a] >= _days[b] ? a : b)
            : null;
        const txtMejorDia = _bestDay
            ? `Los ${_dayMap[_bestDay] || _bestDay} fueron tu mejor día con mayores ventas.`
            : 'Consulta tus ventas por día para identificar los picos.';

        // 3. "El X% de las órdenes fueron entregadas exitosamente."
        const _estados     = wd.ordenesPorEstado || {};
        const _totalOrd    = Object.values(_estados).reduce((a, b) => a + b, 0) || 1;
        const _entKey      = Object.keys(_estados).find(k => k.toLowerCase().includes('entregad')) || null;
        const _entregadas  = _entKey ? _estados[_entKey] : 0;
        const _pctEnt      = Math.round((_entregadas / _totalOrd) * 100);
        const txtEntregadas = `El ${_pctEnt}% de las órdenes fueron entregadas exitosamente.`;

        // 4. Hora más baja para la recomendación de promociones
        const _hours   = wd.topHours || {};
        const _horaKeys = Object.keys(_hours);
        const _horaLow  = _horaKeys.length
            ? _horaKeys.reduce((a, b) => _hours[a] <= _hours[b] ? a : b)
            : null;
        const txtHoraPromo = _horaLow
            ? `Activa promociones en el horario ${_horaLow} para impulsar las horas bajas.`
            : 'Activa promociones en horarios bajos para impulsar tus ventas.';

        /* ============================================================
           PÁGINA 1 — Layout de 3 filas
           ┌─────────────────────────────────────────────────────────┐
           │ F1: Resumen Ejecutivo (30%)  │  4 KPIs en línea (70%)  │
           ├─────────────────────────────────────────────────────────┤
           │ F2: Insights (30%)           │  Evolución de Ventas     │
           ├─────────────────────────────────────────────────────────┤
           │ F3: Ventas/Hora │ Ventas/Canal │ Donut Delivery         │
           └─────────────────────────────────────────────────────────┘
        ============================================================ */
        B.pageBg();
        B.drawHeader(
            'REPORTE EJECUTIVO',
            `PERIODO: ${fechaInicio}  –  ${fechaFin}`,
            `${empresaName}  |  ${sucursalName}`
        );

        /* ── Medidas de filas ── */
        const F1_H   = 25;
        const F1_Y   = CONT_Y;
        const F2_H   = 48;
        const F2_Y   = F1_Y + F1_H + 4;
        const F3_Y   = F2_Y + F2_H + 13;
        const F3_H   = BOT - F3_Y;

        const SPLIT  = 82;
        const R_X    = M + SPLIT + 4;
        const R_W    = CW - SPLIT - 4;

        /* ─── FILA 1: Resumen │ KPIs ─── */
        pdf.setFontSize(11); pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.DARK);
        pdf.text('Resumen Ejecutivo', M, F1_Y + 7);

        pdf.setFontSize(6.5); pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(...C.GRAY);
        const resText = `Este es el desempeño de tu restaurante en Taste durante el período ${fechaInicio} al ${fechaFin}. Los datos te ayudarán a tomar mejores decisiones para seguir creciendo.`;
        pdf.text(pdf.splitTextToSize(resText, SPLIT - 2), M, F1_Y + 14);

        const kpiW = (R_W - 9) / 4;
        const kpiH = F1_H;
        const kpis = [
            { key: 'ventas',   label: 'VENTAS TOTALES',  value: kpiData[0]?.value || '$0', change: '+14%', up: true  },
            { key: 'ordenes',  label: 'ÓRDENES',    value: kpiData[1]?.value || '0',  change: '+9%',  up: true  },
            { key: 'clientes', label: 'CLIENTES',        value: kpiData[2]?.value || '0',  change: '+11%', up: true  },
            { key: 'ticket',   label: 'TICKET PROMEDIO', value: kpiData[3]?.value || '$0', change: '-3%',  up: false },
        ];

        kpis.forEach((kpi, i) => {
            const kx = R_X + i * (kpiW + 3);
            const ky = F1_Y;
            B.card(kx, ky, kpiW, kpiH);

            /* Ícono con color propio del KPI (PNG si disponible, fallback a líneas) */
            const iconR = 5;
            B.kpiIcon(kpi.key, kx + 7, ky + 9, iconR * 2);

            /* Etiqueta */
            pdf.setFontSize(5); pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(...C.GRAY);
            const lblLines = pdf.splitTextToSize(kpi.label, kpiW - 16);
            pdf.text(lblLines, kx + 15, ky + 6);

            /* Valor */
            const valStr  = String(kpi.value);
            const valSize = valStr.length > 9 ? 9 : 11;
            pdf.setFontSize(valSize); pdf.setFont('helvetica', 'bold');
            pdf.setTextColor(...C.DARK);
            pdf.text(valStr, kx + 15, ky + 15);

            /* Badge variación */
            B.variationBadge(kx + 4, ky + 32, kpi.change, kpi.up);
        });

        /* ─── FILA 2: Insights │ Evolución de Ventas ─── */
        B.card(M, F2_Y, SPLIT, F2_H + 11, { bg: [252, 252, 255], border: C.GRAY_LIGHT });

        pdf.setFontSize(8); pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.RED);
        pdf.text('Insights del mes', M + 4, F2_Y + 7);
        pdf.setDrawColor(...C.RED); pdf.setLineWidth(0.5);
        pdf.line(M + 4, F2_Y + 8.5, M + 4 + pdf.getTextWidth('Insights del mes'), F2_Y + 8.5);

        const insights = widgetData.insights || [];

        insights.forEach((ins, i) => {
            const iy = F2_Y + 14 + i * 7;
            if (iy + 5 > F2_Y + F2_H + 9) return;
            if (iconCheck) {
                const iS = 4;
                pdf.addImage(iconCheck, 'PNG', M + 4, iy - 3.5, iS, iS);
            } else {
                pdf.setFillColor(...C.RED_DARK);
                pdf.circle(M + 7, iy - 1.5, 2, 'F');
                pdf.setTextColor(...C.WHITE);
                pdf.setFontSize(4.5); pdf.setFont('helvetica', 'bold');
                pdf.text('✓', M + 5.9, iy - 0.2);
            }
            pdf.setTextColor(...C.DARK2);
            pdf.setFontSize(6.5); pdf.setFont('helvetica', 'normal');
            pdf.text(pdf.splitTextToSize(ins, SPLIT - 14), M + 10, iy);
        });

        if (charts[0]?.img) {
            B.chartCard(charts[0].img, 'Evolución de Ventas', R_X, F2_Y, R_W, F2_H);
        }

        /* ─── FILA 3: 3 bloques col-4 ─── */
        const F3_BW = (CW - 8) / 3;
        const F3_X1 = M;
        const F3_X2 = M + F3_BW + 4;
        const F3_X3 = M + (F3_BW + 4) * 2;
        const F3_CH = F3_H - 15;

        if (charts[1]?.img) {
            B.chartCard(charts[1].img, 'Ventas por Hora del Día', F3_X1, F3_Y, F3_BW, F3_CH);
        }

        if (charts[2]?.img) {
            B.chartCard(charts[2].img, 'Ventas por Canal', F3_X2, F3_Y, F3_BW, F3_CH);
        }

        /* Donut Delivery vs Pickup */
        B.card(F3_X3, F3_Y, F3_BW, F3_CH + 11);
        B.sectionTitle('Delivery vs Pickup', F3_X3 + 4, F3_Y + 7);

        const pct      = getDeliveryPct();
        const dCX      = F3_X3 + F3_BW / 2;
        const dCY      = F3_Y + 5 + F3_CH * 0.42;
        const dR       = Math.min(F3_BW, F3_CH) * 0.27;
        const dRi      = dR * 0.60;

        B.drawDonut(dCX, dCY, dR, dRi, pct, C.RED);

        const pctLabel = Math.round(pct * 100) + '%';
        pdf.setFontSize(13); pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.RED);
        pdf.text(pctLabel, dCX, dCY + 3.5, { align: 'center' });

        const legY = F3_Y + 5 + F3_CH * 0.80;
        pdf.setFontSize(6.5); pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(...C.DARK);
        pdf.text('de tus ventas fueron por Delivery', dCX, legY, { align: 'center' });

        const indY = legY + 5;
        if (indY + 8 < F3_Y + F3_CH + 5) {
            pdf.setFillColor(...C.RED);
            pdf.circle(F3_X3 + 10, indY + 2, 2, 'F');
            pdf.setFontSize(5.8); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
            pdf.text('Delivery', F3_X3 + 14, indY + 3.5);
            pdf.setFontSize(7.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.RED);
            pdf.text(pctLabel, F3_X3 + 14, indY + 9);

            const px = F3_X3 + F3_BW / 2 + 4;
            pdf.setFillColor(...C.GRAY_LIGHT);
            pdf.circle(px, indY + 2, 2, 'F');
            pdf.setFontSize(5.8); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
            pdf.text('Pickup', px + 4, indY + 3.5);
            pdf.setFontSize(7.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.GRAY);
            pdf.text(Math.round((1 - pct) * 100) + '%', px + 4, indY + 9);
        }

        B.drawFooter(hoy, hora, pageNum, 2);

        /* ============================================================
           PÁGINA 2
           ┌─────────────────────────────────────────────────────────┐
           │ F1: Clientes Rec. │ Ventas/Día │ Órdenes/Estado         │
           │     + badge       │ + badge    │ + badge                │
           ├─────────────────────────────────────────────────────────┤
           │ F2: Top 5 Prods   │ Rend. Sucursal │ Calificación       │
           ├─────────────────────────────────────────────────────────┤
           │ F3: Recomendaciones Taste (5 bloques ancho completo)    │
           └─────────────────────────────────────────────────────────┘
        ============================================================ */
        pdf.addPage();
        pageNum++;
        B.pageBg();
        B.drawHeader(
            'REPORTE EJECUTIVO',
            `${empresaName}  |  ${sucursalName}`,
            `${fechaInicio}  –  ${fechaFin}`
        );

        const P2_CY  = CONT_Y;
        const COL3W  = (CW - 8) / 3;
        const C1x    = M;
        const C2x    = M + COL3W + 4;
        const C3x    = M + (COL3W + 4) * 2;

        /* Alturas calculadas para que todo entre en la página */
        const F1_GH  = 42;    // alto del gráfico (interior de chartCard)
        const F1_BH  = 12;    // alto del badge debajo
        const F1_TOT = F1_GH + 11 + 3 + F1_BH;   // chartCard total + gap + badge

        const F2_Y2  = P2_CY + F1_TOT + 4;
        const F2_H2  = 55;

        const F3_Y2  = F2_Y2 + F2_H2 + 4;
        const F3_H2  = BOT - F3_Y2;

        /* ─── FILA 1: 3 gráficos + badge ─── */

        if (charts[3]?.img)
            B.chartCard(charts[3].img, 'Clientes Recurrentes', C1x, P2_CY, COL3W, F1_GH);
        B.insightBadge(C1x, P2_CY + F1_GH + 14, COL3W, F1_BH,
            txtClientes,
            C.ORANGE_BG, C.ORANGE, iconAlert);

        if (charts[4]?.img)
            B.chartCard(charts[4].img, 'Ventas por Día', C2x, P2_CY, COL3W, F1_GH);
        B.insightBadge(C2x, P2_CY + F1_GH + 14, COL3W, F1_BH,
            txtMejorDia,
            C.YELLOW_BG, C.YELLOW, iconStar);

        if (charts[5]?.img)
            B.chartCard(charts[5].img, 'Órdenes por Estado', C3x, P2_CY, COL3W, F1_GH);
        B.insightBadge(C3x, P2_CY + F1_GH + 14, COL3W, F1_BH,
            txtEntregadas,
            C.GREEN_BG, C.GREEN, iconTruck);

        /* ─── FILA 2: Top Productos │ Rendimiento │ Calificación ─── */

        /* --- Top 5 Productos (datos reales) --- */
        const topProds = (widgetData.topProductos || []).map(p => ({
            n: p.nombre,
            c: p.cantidad.toLocaleString('en-US'),
            v: fmtPDF(p.total_ventas),
        }));

        B.sectionTitle('Top 5 Productos', C1x, F2_Y2 + 5);
        const tblY = F2_Y2 + 8;
        B.tableHeader(C1x, tblY, COL3W, [
            { label: 'PRODUCTO',  x: 3 },
            { label: 'CANTIDAD',  x: COL3W - 28 },
            { label: 'VENTAS',    x: COL3W - 13 },
        ]);
        const dotColors = [C.RED, C.BLUE, C.GREEN, C.ORANGE, C.YELLOW];
        topProds.forEach((p, i) => {
            const ry = tblY + 5.5 + i * 7;
            if (ry + 7 > F2_Y2 + F2_H2) return;
            B.tableRow(C1x, ry, COL3W, 7, [
                { value: p.n, x: 6 },
                { value: p.c, x: COL3W - 28 },
                { value: p.v, x: COL3W - 17 },
            ], i % 2 === 1, dotColors[i]);
        });
        if (topProds.length === 0) {
            pdf.setFontSize(6); pdf.setFont('helvetica', 'normal'); pdf.setTextColor(...C.GRAY);
            pdf.text('Sin datos en el período', C1x + 3, tblY + 9);
        }
        B.tableBorder(C1x, tblY, COL3W, 5.5 + Math.max(topProds.length, 1) * 7);

        /* --- Rendimiento por Sucursal (datos reales) --- */
        const sucursales = (widgetData.rendimientoSucursal || []).map(s => ({
            n: s.sucursal,
            v: fmtPDF(s.ventas),
            p: s.porcentaje + '%',
            o: String(s.ordenes),
            t: fmtPDF(s.ticket_promedio),
        }));

        B.sectionTitle('Rendimiento por Sucursal', C2x, F2_Y2 + 5);
        const sucTblY = F2_Y2 + 8;
        B.tableHeader(C2x, sucTblY, COL3W, [
            { label: 'SUCURSAL', x: 3  },
            { label: 'VENTAS',   x: 28 },
            { label: '% VTA',    x: 46 },
            { label: 'ÓRD.',     x: 54 },
            { label: 'T.PROM',   x: 62 },
        ]);
        const sucDots = [C.BLUE, C.RED, C.GREEN, C.ORANGE, C.YELLOW];
        sucursales.forEach((s, i) => {
            const ry = sucTblY + 5.5 + i * 7;
            B.tableRow(C2x, ry, COL3W, 7, [
                { value: s.n, x: 6  },
                { value: s.v, x: 28 },
                { value: s.p, x: 46 },
                { value: s.o, x: 54 },
                { value: s.t, x: 62 },
            ], i % 2 === 1, sucDots[i]);
        });
        if (sucursales.length === 0) {
            pdf.setFontSize(6); pdf.setFont('helvetica', 'normal'); pdf.setTextColor(...C.GRAY);
            pdf.text('Sin datos en el período', C2x + 3, sucTblY + 9);
        }
        B.tableBorder(C2x, sucTblY, COL3W, 5.5 + Math.max(sucursales.length, 1) * 7);

        /* --- Calificación Promedio (datos reales) --- */
        const cal = widgetData.calificacion || { promedio: 0, total_resenas: 0 };
        const calPromedio = cal.promedio ? cal.promedio.toFixed(1) : '0.0';
        const calResenas  = cal.total_resenas
            ? cal.total_resenas.toLocaleString('en-US') + ' reseñas'
            : 'Sin reseñas';

        B.card(C3x, F2_Y2, COL3W, F2_H2, { bg: C.LIGHT_BG, border: C.GRAY_LIGHT });
        B.sectionTitle('Calificación Promedio', C3x + 2, F2_Y2 + 5);

        if (iconStar) {
            pdf.addImage(iconStar, 'PNG', C3x + 2, F2_Y2 + 10, 5, 5);
        }
        pdf.setFontSize(16); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...TastePDF.COLORS.RED);
        pdf.text(calPromedio, C3x + 10, F2_Y2 + 15);
        pdf.setFontSize(9); pdf.setTextColor(...C.GRAY);
        pdf.text('/ 5', C3x + 18, F2_Y2 + 15);
        pdf.setFontSize(5.8); pdf.setFont('helvetica', 'normal'); pdf.setTextColor(...C.GRAY);
        pdf.text('Basado en ' + calResenas, C3x + 25, F2_Y2 + 15);

        /* Divisor */
        pdf.setDrawColor(...C.GRAY_LIGHT); pdf.setLineWidth(0.2);
        pdf.line(C3x + 4, F2_Y2 + 20, C3x + COL3W - 4, F2_Y2 + 20);

        /* Calificación general (sin split por ahora) */
        pdf.setFontSize(6.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
        pdf.text('Calificación General', C3x + 4, F2_Y2 + 25);
        if (iconStar) pdf.addImage(iconStar, 'PNG', C3x + 4, F2_Y2 + 28, 5, 5);
        pdf.setFontSize(8); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.YELLOW);
        pdf.text(calPromedio + ' / 5', C3x + 11, F2_Y2 + 31);

        /* Alertas del mes */
        // pdf.setFontSize(7); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
        // pdf.text('Alertas del mes', C3x + 4, F2_Y2 + 40);
        // B.insightBadge(C3x + 2, F2_Y2 + 42, COL3W - 4, 10,
        //     'El ticket promedio disminuyó 3% respecto al mes anterior.',
        //     C.ORANGE_BG, C.ORANGE, iconAlert);

        /* ─── FILA 3: Recomendaciones Taste ─── */
        if (F3_H2 > 14) {
            /* Un solo bloque ancho con 5 items */
            B.card(M, F3_Y2, CW, F3_H2, { bg: [248, 246, 255], border: C.GRAY_LIGHT });
            B.sectionTitle('Recomendaciones Taste', M + 6, F3_Y2 + 7);

            const recItems = [
                { icon: iconClock,    text: txtHoraPromo },
                { icon: iconPackage,  text: 'Crea combos y menús especiales para aumentar tu ticket promedio.' },
                { icon: iconTruck,    text: 'Tu canal de delivery funciona muy bien, ¡sigue potenciándolo!' },
                { icon: iconUserPlus, text: 'Aumenta tus clientes recurrentes con cupones o beneficios exclusivos.' },
                { icon: iconStar,     text: 'Mantén la calidad del delivery para mejorar tus calificaciones.' },
            ];

            const recW  = (CW - 16) / 5;
            const recH  = F3_H2 - 14;
            const recY0 = F3_Y2 + 12;

            recItems.forEach((rec, i) => {
                const rx = M + 6 + i * (recW + 2);

                /* Sin card — icono a la izquierda, texto a la derecha */
                const iS   = Math.min(recH * 0.55, 9);
                const iY   = recY0 + (recH - iS) / 2;
                const txtX = rx + iS + 4;
                const txtW = recW - iS - 6;

                if (rec.icon) {
                    pdf.addImage(rec.icon, 'PNG', rx, iY, iS, iS);
                }

                pdf.setFontSize(6); pdf.setFont('helvetica', 'normal');
                pdf.setTextColor(...C.DARK2);
                const lines    = pdf.splitTextToSize(rec.text, txtW);
                const totalTxt = lines.length * 7 * 0.352;
                const txtY     = recY0 + (recH - totalTxt) / 2 + 4;
                pdf.text(lines, txtX, txtY);
            });
        }

        B.drawFooter(hoy, hora, pageNum, 2);

        /* ── 6. Guardar ── */
        pdf.save(`reporte_${empresaName.replace(/\s+/g, '_')}_${fechaInicio}_${fechaFin}.pdf`);

    } catch (err) {
        console.error('Error al generar PDF:', err);
        if (typeof messageDone === 'function')
            messageDone('Error al generar el PDF. Revisa la consola.', 'error');
        else
            alert('Error al generar el PDF. Revisa la consola.');
    }

    $btn.prop('disabled', false).html('<i data-feather="file-text" style="width:16px;height:16px;"></i>&nbsp;Descargar PDF');
    if (typeof feather !== 'undefined') feather.replace();
}
