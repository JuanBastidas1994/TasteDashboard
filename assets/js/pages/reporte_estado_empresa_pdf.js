/* ============================================================
   reporte_estado_empresa_pdf.js
   PDF ejecutivo horizontal — Reporte de Estado por Empresa
   Depende de: pdf-utils.js  (TastePDF debe estar cargado)
   ============================================================ */

async function generarPDF() {
    const $btn = $('#btnGenerarPDF');
    $btn.prop('disabled', true).html('Generando PDF\u2026');

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
            `PERIODO: ${fechaInicio}  \u2013  ${fechaFin}`,
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
        const resText = `Este es el desempe\u00F1o de tu restaurante en Taste durante el per\u00EDodo ${fechaInicio} al ${fechaFin}. Los datos te ayudar\u00E1n a tomar mejores decisiones para seguir creciendo.`;
        pdf.text(pdf.splitTextToSize(resText, SPLIT - 2), M, F1_Y + 14);

        const kpiW = (R_W - 9) / 4;
        const kpiH = F1_H;
        const kpis = [
            { key: 'ventas',   label: 'VENTAS TOTALES',  value: kpiData[0]?.value || '$0', change: '+14%', up: true  },
            { key: 'ordenes',  label: '\u00D3RDENES',    value: kpiData[1]?.value || '0',  change: '+9%',  up: true  },
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

        const insights = [
            'Tus ventas aumentaron 14% respecto al mes anterior.',
            'El horario m\u00E1s fuerte fue de 15h00 a 17h00.',
            'Delivery representa el 78% de tus ventas.',
            'Los mi\u00E9rcoles fueron tu mejor d\u00EDa.',
            'Tienes 42% de clientes recurrentes.',
            'Tu ticket promedio baj\u00F3 $1.20.',
            'La sucursal Urdesa gener\u00F3 el 61% de tus ventas.',
        ];

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
                pdf.text('\u2713', M + 5.9, iy - 0.2);
            }
            pdf.setTextColor(...C.DARK2);
            pdf.setFontSize(6.5); pdf.setFont('helvetica', 'normal');
            pdf.text(pdf.splitTextToSize(ins, SPLIT - 14), M + 10, iy);
        });

        if (charts[0]?.img) {
            B.chartCard(charts[0].img, 'Evoluci\u00F3n de Ventas', R_X, F2_Y, R_W, F2_H);
        }

        /* ─── FILA 3: 3 bloques col-4 ─── */
        const F3_BW = (CW - 8) / 3;
        const F3_X1 = M;
        const F3_X2 = M + F3_BW + 4;
        const F3_X3 = M + (F3_BW + 4) * 2;
        const F3_CH = F3_H - 15;

        if (charts[1]?.img) {
            B.chartCard(charts[1].img, 'Ventas por Hora del D\u00EDa', F3_X1, F3_Y, F3_BW, F3_CH);
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
            `${fechaInicio}  \u2013  ${fechaFin}`
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
            '4 de cada 10 clientes regresaron a comprar este mes.',
            C.ORANGE_BG, C.ORANGE, iconAlert);

        if (charts[4]?.img)
            B.chartCard(charts[4].img, 'Ventas por D\u00EDa', C2x, P2_CY, COL3W, F1_GH);
        B.insightBadge(C2x, P2_CY + F1_GH + 14, COL3W, F1_BH,
            'El mi\u00E9rcoles fue tu mejor d\u00EDa con mayores ventas.',
            C.YELLOW_BG, C.YELLOW, iconStar);

        if (charts[5]?.img)
            B.chartCard(charts[5].img, '\u00D3rdenes por Estado', C3x, P2_CY, COL3W, F1_GH);
        B.insightBadge(C3x, P2_CY + F1_GH + 14, COL3W, F1_BH,
            'El 89% de las \u00F3rdenes fueron entregadas exitosamente.',
            C.GREEN_BG, C.GREEN, iconTruck);

        /* ─── FILA 2: Top Productos │ Rendimiento │ Calificación ─── */

        /* Top 5 Productos */
        B.sectionTitle('Top 5 Productos', C1x, F2_Y2 + 5);
        const tblY = F2_Y2 + 8;
        B.tableHeader(C1x, tblY, COL3W, [
            { label: 'PRODUCTO',  x: 3 },
            { label: 'CANTIDAD',  x: COL3W - 28 },
            { label: 'VENTAS',    x: COL3W - 13 },
        ]);
        const topProds = [
            { n: 'Caja de 3 Rolls', c: '5,285', v: '$51,595.25' },
            { n: 'Caja de 6 rolls', c: '2,304', v: '$44,957.50' },
            { n: 'Caja de 3 Rolls', c: '2,511', v: '$24,497.25' },
            { n: 'Caja de 6 rolls', c: '1,095', v: '$21,364.00' },
            { n: 'Ferrero rocher',  c: '3,335', v: '$12,249.90' },
        ];
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
        B.tableBorder(C1x, tblY, COL3W, 5.5 + topProds.length * 7);

        /* Rendimiento por Sucursal */
        B.sectionTitle('Rendimiento por Sucursal', C2x, F2_Y2 + 5);
        const sucTblY = F2_Y2 + 8;
        B.tableHeader(C2x, sucTblY, COL3W, [
            { label: 'SUCURSAL', x: 3  },
            { label: 'VENTAS',   x: 28 },
            { label: '% VTA',    x: 46 },
            { label: '\u00D3RD.',x: 54 },
            { label: 'T.PROM',   x: 62 },
        ]);
        const sucursales = [
            { n: 'Plaza Sports Garden', v: '$7,432.10',  p: '26%', o: '412', t: '$18.03' },
            { n: 'Urdesa',              v: '$17,289.45', p: '61%', o: '942', t: '$18.34' },
            { n: 'HiperMarket Buijo',   v: '$2,941.30',  p: '10%', o: '178', t: '$16.52' },
            { n: 'La Marquesa',         v: '$1,105.40',  p: '4%',  o: '52',  t: '$21.26' },
            { n: 'Aventura Plaza',      v: '$445.38',    p: '2%',  o: '25',  t: '$17.82' },
        ];
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
        B.tableBorder(C2x, sucTblY, COL3W, 5.5 + sucursales.length * 7);

        /* Calificación Promedio */
        B.card(C3x, F2_Y2, COL3W, F2_H2, { bg: C.LIGHT_BG, border: C.GRAY_LIGHT });
        B.sectionTitle('Calificaci\u00F3n Promedio', C3x + 2, F2_Y2 + 5);
        pdf.setFontSize(7.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
        // pdf.text('Calificaci\u00F3n Promedio', C3x + 2, F2_Y2 + 7);

        /* Estrella SVG real + número */
        if (iconStar) {
            pdf.addImage(iconStar, 'PNG', C3x + 2, F2_Y2 + 10, 5, 5);
        }
        pdf.setFontSize(16); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...TastePDF.COLORS.RED);
        pdf.text('4.7', C3x + 10, F2_Y2 + 15);
        pdf.setFontSize(9); pdf.setTextColor(...C.GRAY);
        pdf.text('/ 5', C3x + 18, F2_Y2 + 15);
        pdf.setFontSize(5.8); pdf.setFont('helvetica', 'normal'); pdf.setTextColor(...C.GRAY);
        pdf.text('Basado en 1.328 rese\u00F1as', C3x + 25, F2_Y2 + 15);

        /* Divisor */
        pdf.setDrawColor(...C.GRAY_LIGHT); pdf.setLineWidth(0.2);
        pdf.line(C3x + 4, F2_Y2 + 20, C3x + COL3W - 4, F2_Y2 + 20);

        /* Restaurante | Delivery sub-métricas */
        const halfX = C3x + COL3W / 2;
        [
            { label: 'Restaurante', val: '4.8 / 5', x: C3x + 4 },
            { label: 'Delivery',    val: '4.5 / 5', x: halfX + 4 },
        ].forEach(item => {
            pdf.setFontSize(6.5); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
            pdf.text(item.label, item.x, F2_Y2 + 25);
            if (iconStar) pdf.addImage(iconStar, 'PNG', item.x, F2_Y2 + 27, 5, 5);
            pdf.setFontSize(8); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.YELLOW);
            pdf.text(item.val, item.x + 7, F2_Y2 + 30);
        });

        /* Alertas del mes */
        pdf.setFontSize(7); pdf.setFont('helvetica', 'bold'); pdf.setTextColor(...C.DARK);
        pdf.text('Alertas del mes', C3x + 4, F2_Y2 + 40);
        B.insightBadge(C3x + 2, F2_Y2 + 42, COL3W - 4, 10,
            'El ticket promedio disminuy\u00F3 3% respecto al mes anterior.',
            C.ORANGE_BG, C.ORANGE, iconAlert);

        /* ─── FILA 3: Recomendaciones Taste ─── */
        if (F3_H2 > 14) {
            /* Un solo bloque ancho con 5 items */
            B.card(M, F3_Y2, CW, F3_H2, { bg: [248, 246, 255], border: C.GRAY_LIGHT });
            B.sectionTitle('Recomendaciones Taste', M + 6, F3_Y2 + 7);

            const recItems = [
                { icon: iconClock,    text: 'Activa promociones entre 18h y 20h para impulsar las horas bajas.' },
                { icon: iconPackage,  text: 'Crea combos y men\u00fas especiales para aumentar tu ticket promedio.' },
                { icon: iconTruck,    text: 'Tu canal de delivery funciona muy bien, \u00A1sigue potenci\u00E1ndolo!' },
                { icon: iconUserPlus, text: 'Aumenta tus clientes recurrentes con cupones o beneficios exclusivos.' },
                { icon: iconStar,     text: 'Mant\u00E9n la calidad del delivery para mejorar tus calificaciones.' },
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