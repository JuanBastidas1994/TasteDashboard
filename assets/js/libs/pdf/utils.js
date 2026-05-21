/* ============================================================
   pdf-utils.js  —  Utilidades reutilizables para PDFs con jsPDF
   Incluye: conversión SVG→PNG, carga de imágenes, paleta,
   primitivos de dibujo (header, footer, card, sectionTitle, etc.)
   ============================================================
   USO: cargar ANTES del archivo específico de cada reporte PDF.
   Expone el objeto global TastePDF con todo lo necesario.
   ============================================================ */

const TastePDF = (() => {

    /* ----------------------------------------------------------------
       PALETA DE COLORES
    ---------------------------------------------------------------- */
    const COLORS = {
        RED:        [220, 38,  38],
        RED_DARK:   [185, 28,  28],
        DARK:       [30,  30,  30],
        DARK2:      [50,  50,  50],
        WHITE:      [255, 255, 255],
        LIGHT_BG:   [248, 248, 248],
        GRAY:       [120, 120, 120],
        GRAY_LIGHT: [220, 220, 220],
        GREEN:      [22,  163, 74],
        GREEN_BG:   [240, 253, 244],
        ORANGE:     [234, 88,  12],
        ORANGE_BG:  [255, 247, 237],
        YELLOW:     [234, 179, 8],
        YELLOW_BG:  [255, 251, 235],
        BLUE:       [37,  99,  235],
        BLUE_BG:    [239, 246, 255],
    };

    /* ----------------------------------------------------------------
       ESTILOS POR KPI — bg del círculo, color del icono, archivo SVG
    ---------------------------------------------------------------- */
    const KPI_STYLES = {
        ventas:   { bg: [254, 226, 226], fg: [220, 38,  38], icon: 'assets/icons/shopping-cart.svg' },
        ordenes:  { bg: [219, 234, 254], fg: [37,  99,  235], icon: 'assets/icons/file-text.svg'    },
        clientes: { bg: [209, 250, 229], fg: [22,  163, 74],  icon: 'assets/icons/users.svg'        },
        ticket:   { bg: [254, 243, 199], fg: [217, 119, 6],   icon: 'assets/icons/tag.svg'          },
    };

    /* Mapa de iconos extra */
    const ICON_PATHS = {
        'check-circle':   'assets/icons/check-circle.svg',
        'alert-triangle': 'assets/icons/alert-triangle.svg',
        'clock':          'assets/icons/clock.svg',
        'package':        'assets/icons/package.svg',
        'truck':          'assets/icons/truck.svg',
        'user-plus':      'assets/icons/user-plus.svg',
        'star':           'assets/icons/star.svg',
        'shopping-cart':  'assets/icons/shopping-cart.svg',
        'file-text':      'assets/icons/file-text.svg',
        'users':          'assets/icons/users.svg',
        'tag':            'assets/icons/tag.svg',
    };

    /* ----------------------------------------------------------------
       CARGA DE IMAGEN COMO BASE64
    ---------------------------------------------------------------- */
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

    async function imageDimensions(base64) {
        return new Promise(resolve => {
            const i = new Image();
            i.onload  = () => resolve({ w: i.naturalWidth, h: i.naturalHeight });
            i.onerror = () => resolve({ w: 120, h: 40 });
            i.src = base64;
        });
    }

    /* ----------------------------------------------------------------
       SVG icono con fondo circular de color → PNG base64
       Carga el SVG, aplica el color al stroke, lo renderiza sobre
       un círculo de fondo en un canvas y devuelve PNG base64.
    ---------------------------------------------------------------- */
    async function svgIconWithBg(svgUrl, fgColor, bgColor, size = 64) {
        try {
            const res = await fetch(svgUrl);
            if (!res.ok) return null;
            let svgText = await res.text();

            const fgHex = Array.isArray(fgColor)
                ? `rgb(${fgColor[0]},${fgColor[1]},${fgColor[2]})`
                : fgColor;
            const bgHex = Array.isArray(bgColor)
                ? `rgb(${bgColor[0]},${bgColor[1]},${bgColor[2]})`
                : bgColor;

            // Aplicar color de trazo al SVG (Feather/Lucide usan stroke)
            svgText = svgText.replace(/<svg([^>]*)>/, (match, attrs) => {
                // Quitar stroke/fill existentes para no duplicar
                const cleaned = attrs
                    .replace(/stroke="[^"]*"/g, '')
                    .replace(/fill="[^"]*"/g, '');
                return `<svg${cleaned} stroke="${fgHex}" fill="none">`;
            });

            if (!svgText.includes('viewBox')) {
                svgText = svgText.replace('<svg', '<svg viewBox="0 0 24 24"');
            }

            const dataUri = 'data:image/svg+xml;base64,' +
                btoa(unescape(encodeURIComponent(svgText)));

            return new Promise(resolve => {
                const scale  = 3;
                const canvas = document.createElement('canvas');
                canvas.width  = size * scale;
                canvas.height = size * scale;
                const ctx = canvas.getContext('2d');

                // Círculo de fondo
                ctx.fillStyle = bgHex;
                ctx.beginPath();
                ctx.arc(canvas.width / 2, canvas.height / 2,
                        canvas.width / 2, 0, Math.PI * 2);
                ctx.fill();

                // Icono encima con padding
                const pad = size * scale * 0.22;
                const img = new Image();
                img.onload = () => {
                    ctx.drawImage(img, pad, pad,
                        canvas.width  - pad * 2,
                        canvas.height - pad * 2);
                    resolve(canvas.toDataURL('image/png'));
                };
                img.onerror = () => resolve(null);
                img.src = dataUri;
            });
        } catch (e) { return null; }
    }

    /* ----------------------------------------------------------------
       SVG icono simple (sin fondo) → PNG base64, con color aplicado
    ---------------------------------------------------------------- */
    async function svgIconToPng(svgUrl, color = [30, 30, 30], size = 48) {
        try {
            const res = await fetch(svgUrl);
            if (!res.ok) return null;
            let svgText = await res.text();

            const hexColor = Array.isArray(color)
                ? `rgb(${color[0]},${color[1]},${color[2]})`
                : color;

            svgText = svgText.replace(/<svg([^>]*)>/, (match, attrs) => {
                const cleaned = attrs
                    .replace(/stroke="[^"]*"/g, '')
                    .replace(/fill="[^"]*"/g, '');
                return `<svg${cleaned} stroke="${hexColor}" fill="none">`;
            });

            if (!svgText.includes('viewBox')) {
                svgText = svgText.replace('<svg', '<svg viewBox="0 0 24 24"');
            }

            const dataUri = 'data:image/svg+xml;base64,' +
                btoa(unescape(encodeURIComponent(svgText)));

            return new Promise(resolve => {
                const scale  = 2;
                const canvas = document.createElement('canvas');
                canvas.width  = size * scale;
                canvas.height = size * scale;
                const ctx = canvas.getContext('2d');
                const img = new Image();
                img.onload  = () => {
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    resolve(canvas.toDataURL('image/png'));
                };
                img.onerror = () => resolve(null);
                img.src = dataUri;
            });
        } catch (e) { return null; }
    }

    /* ----------------------------------------------------------------
       Cargar los 4 iconos KPI de una sola vez (en paralelo)
       Devuelve { ventas, ordenes, clientes, ticket } como PNG base64
    ---------------------------------------------------------------- */
    async function loadKpiIcons() {
        const entries = await Promise.all(
            Object.entries(KPI_STYLES).map(async ([key, st]) => {
                const png = await svgIconWithBg(st.icon, st.fg, st.bg, 64);
                return [key, png];
            })
        );
        return Object.fromEntries(entries);
    }

    /* ----------------------------------------------------------------
       Cargar un icono extra por nombre, con color libre
    ---------------------------------------------------------------- */
    async function loadIcon(name, color = [30, 30, 30], size = 48) {
        const url = ICON_PATHS[name];
        if (!url) return null;
        return svgIconToPng(url, color, size);
    }

    /* ----------------------------------------------------------------
       CONVERSIÓN SVG de gráfico ApexCharts → PNG (sin cambios)
    ---------------------------------------------------------------- */
    async function svgToPng(containerEl) {
        const svgEl = containerEl.querySelector('svg');
        if (!svgEl) return null;

        const srcW = svgEl.clientWidth  || containerEl.clientWidth  || 600;
        const srcH = svgEl.clientHeight || containerEl.clientHeight || 300;
        if (srcW === 0 || srcH === 0) return null;

        const clone = svgEl.cloneNode(true);
        clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        clone.setAttribute('width',  srcW);
        clone.setAttribute('height', srcH);
        clone.querySelectorAll('style').forEach(n => n.remove());
        clone.querySelectorAll('image').forEach(n => n.remove());

        const dataUri = 'data:image/svg+xml;base64,' +
            btoa(unescape(encodeURIComponent(new XMLSerializer().serializeToString(clone))));

        return new Promise(resolve => {
            const scale  = 2;
            const canvas = document.createElement('canvas');
            canvas.width  = srcW * scale;
            canvas.height = srcH * scale;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            const img = new Image();
            img.onload  = () => {
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                resolve({ dataUrl: canvas.toDataURL('image/png'), aspectRatio: srcW / srcH });
            };
            img.onerror = () => resolve(null);
            img.src = dataUri;
        });
    }

    /* ================================================================
       CLASE PDFBuilder
       Acepta kpiIcons como 4to parámetro (objeto con PNGs de iconos)
    ================================================================ */
    class PDFBuilder {
        constructor(pdf, opts = {}, logoB64 = null, kpiIcons = {}) {
            this.pdf      = pdf;
            this.C        = COLORS;
            this.KPI      = KPI_STYLES;
            this.kpiIcons = kpiIcons;   // { ventas, ordenes, clientes, ticket } → PNG

            this.PW     = opts.pageW   || 297;
            this.PH     = opts.pageH   || 210;
            this.M      = opts.marginX || 12;
            this.CW     = this.PW - this.M * 2;
            this.HDR    = opts.hdrH    || 20;
            this.FTR    = opts.ftrH    || 11;
            this.CONT_Y = this.HDR + 5;
            this.BOT    = this.PH - this.FTR - 3;

            this.logo  = logoB64;
            this.logoW = 32;
            this.logoH = 12;
        }

        setLogoDimensions(naturalW, naturalH) {
            this.logoH = 12;
            this.logoW = Math.min(this.logoH * (naturalW / naturalH), 60);
        }

        pageBg(color = COLORS.WHITE) {
            const p = this.pdf;
            p.setFillColor(...color);
            p.rect(0, 0, this.PW, this.PH, 'F');
        }

        drawHeader(titulo, sub1 = '', sub2 = '') {
            const { pdf: p, PW, M, HDR: H, logo, logoW, logoH, C } = this;
            p.setFillColor(...C.RED);
            p.rect(0, 0, PW, H, 'F');

            if (logo) {
                const lH = logoH, lW = lH * (logoW / logoH);
                p.addImage(logo, 'PNG', M, (H - lH) / 2, lW, lH);
            } else {
                p.setTextColor(...C.WHITE);
                p.setFontSize(15); p.setFont('helvetica', 'bold');
                p.text('taste', M, H / 2 + 5);
            }

            p.setTextColor(...C.WHITE);
            p.setFontSize(9.5); p.setFont('helvetica', 'bold');
            p.text(titulo, PW - M, 8, { align: 'right' });
            p.setFontSize(7); p.setFont('helvetica', 'normal');
            if (sub1) p.text(sub1, PW - M, 14, { align: 'right' });
            if (sub2) p.text(sub2, PW - M, 19, { align: 'right' });
        }

        drawFooter(hoy, hora, pageNum, totalPages = 2) {
            const { pdf: p, PW, PH, M, FTR, logo, logoW, logoH, C } = this;
            const fy = PH - FTR;
            p.setFillColor(...C.RED);
            p.rect(0, fy, PW, FTR, 'F');

            if (logo) {
                const lH = 7, lW = lH * (logoW / logoH);
                p.addImage(logo, 'PNG', M, fy + (FTR - lH) / 2, lW, lH);
            }

            p.setTextColor(...C.WHITE);
            p.setFontSize(6.5); p.setFont('helvetica', 'normal');
            p.text(`Reporte generado el ${hoy}, ${hora}`, PW * 0.28, fy + 7.5, { align: 'center' });
            p.text(`\u00A9 ${new Date().getFullYear()} Taste. Todos los derechos reservados.`, PW * 0.65, fy + 7.5, { align: 'center' });
            p.setFontSize(7.5); p.setFont('helvetica', 'bold');
            p.text(`${pageNum} / ${totalPages}`, PW - M, fy + 7.5, { align: 'right' });
        }

        card(x, y, w, h, { bg = COLORS.WHITE, border = COLORS.GRAY_LIGHT, radius = 3 } = {}) {
            const p = this.pdf;
            p.setFillColor(...bg);
            p.roundedRect(x, y, w, h, radius, radius, 'F');
            if (border) {
                p.setDrawColor(...border);
                p.setLineWidth(0.18);
                p.roundedRect(x, y, w, h, radius, radius, 'S');
            }
        }

        sectionTitle(text, x, y) {
            const p = this.pdf;
            p.setFontSize(7.5); p.setFont('helvetica', 'bold');
            p.setTextColor(...COLORS.RED);
            p.text(text, x, y);
            const tw = p.getTextWidth(text);
            p.setDrawColor(...COLORS.RED);
            p.setLineWidth(0.55);
            p.line(x, y + 1.2, x + tw, y + 1.2);
        }

        chartCard(imgResult, title, x, y, w, h) {
            const p = this.pdf;
            const C = this.C;
            const totalH = h + 11;
            this.card(x, y, w, totalH);
            this.sectionTitle(title, x + 4, y + 7);

            if (imgResult && imgResult.dataUrl) {
                const ar = imgResult.aspectRatio || (w / h);
                let drawW = w - 6;
                let drawH = drawW / ar;
                if (drawH > h) { drawH = h; drawW = drawH * ar; }
                const offsetX = x + 3 + (w - 6 - drawW) / 2;
                p.addImage(imgResult.dataUrl, 'PNG', offsetX, y + 10, drawW, drawH);
            } else {
                p.setTextColor(...C.GRAY);
                p.setFontSize(6.5); p.setFont('helvetica', 'italic');
                p.text('Sin datos en el per\u00EDodo', x + w / 2, y + 11 + h / 2, { align: 'center' });
            }
        }

        variationBadge(x, y, text, up) {
            const p   = this.pdf;
            const col = up ? this.C.GREEN : this.C.RED;
            p.setTextColor(...col);
            p.setFontSize(6); p.setFont('helvetica', 'bold');
            const arrow = up ? '\u25B2 ' : '\u25BC ';
            p.text(arrow + text, x, y);
            p.setTextColor(...this.C.GRAY);
            p.setFontSize(5.5); p.setFont('helvetica', 'normal');
            p.text(' vs mes ant.', x + p.getTextWidth(arrow + text) + 0.3, y);
        }

        /* ── Ícono KPI — usa PNG si está disponible, fallback a líneas ── */
        kpiIcon(key, cx, cy, size = 9) {
            const p   = this.pdf;
            const st  = KPI_STYLES[key] || KPI_STYLES.ventas;
            const r   = size / 2;
            const png = this.kpiIcons?.[key];

            if (png) {
                // PNG: círculo ya viene con fondo desde svgIconWithBg
                p.addImage(png, 'PNG', cx - r, cy - r, size, size);
                return;
            }

            // Fallback: círculo de color + líneas (código original)
            p.setFillColor(...st.bg);
            p.circle(cx, cy, r, 'F');
            p.setDrawColor(...st.fg);
            p.setLineWidth(0.6);
            const s = r * 0.45;
            switch (key) {
                case 'ventas':
                    p.line(cx - s, cy - s * 0.8, cx - s * 0.4, cy - s * 0.8);
                    p.line(cx - s * 0.4, cy - s * 0.8, cx + s, cy - s * 0.8);
                    p.line(cx - s * 0.4, cy - s * 0.8, cx - s * 0.2, cy + s * 0.4);
                    p.line(cx - s * 0.2, cy + s * 0.4, cx + s * 0.8, cy + s * 0.4);
                    p.setFillColor(...st.fg);
                    p.circle(cx - s * 0.1, cy + s * 0.8, s * 0.25, 'F');
                    p.circle(cx + s * 0.7, cy + s * 0.8, s * 0.25, 'F');
                    break;
                case 'ordenes':
                    p.roundedRect(cx - s * 0.7, cy - s, s * 1.4, s * 2, 0.5, 0.5, 'S');
                    p.line(cx - s * 0.4, cy - s * 0.3, cx + s * 0.4, cy - s * 0.3);
                    p.line(cx - s * 0.4, cy + s * 0.15, cx + s * 0.4, cy + s * 0.15);
                    p.line(cx - s * 0.4, cy + s * 0.6, cx + s * 0.1, cy + s * 0.6);
                    break;
                case 'clientes':
                    p.circle(cx, cy - s * 0.4, s * 0.45, 'S');
                    p.lines([[s * 0.7, 0, s * 0.7, s * 0.5, s * 0.7, s]], cx - s * 0.7, cy + s * 0.2, [1,1], 'S');
                    p.lines([[-s * 0.7, 0, -s * 0.7, s * 0.5, -s * 0.7, s]], cx + s * 0.7, cy + s * 0.2, [1,1], 'S');
                    break;
                case 'ticket':
                    p.lines([
                        [0,0,0,0, s*0.8,0],[0,0,0,0, 0,s*0.8],
                        [0,0,0,0,-s*0.6,s*0.6],[0,0,0,0,-s*0.8,0],
                        [0,0,0,0, 0,-s*0.8],[0,0,0,0,s*0.6,-s*0.6],
                    ], cx - s*0.15, cy - s*0.5, [1,1], 'S');
                    p.setFillColor(...st.fg);
                    p.circle(cx - s*0.05, cy - s*0.15, s*0.18, 'F');
                    break;
            }
        }

        drawDonut(cx, cy, r, ri, pct, colorFill, colorBg = COLORS.GRAY_LIGHT) {
            const p = this.pdf;
            p.setFillColor(...colorBg);
            p.circle(cx, cy, r, 'F');

            const startA = -Math.PI / 2;
            const endA   = startA + 2 * Math.PI * pct;
            const steps  = 72;
            const pts    = [[cx, cy]];
            for (let i = 0; i <= steps; i++) {
                const a = startA + (endA - startA) * (i / steps);
                pts.push([cx + r * Math.cos(a), cy + r * Math.sin(a)]);
            }
            pts.push([cx, cy]);

            p.setFillColor(...colorFill);
            p.lines(
                pts.slice(1).map((pt, i) => {
                    const prev = pts[i];
                    return [0, 0, 0, 0, pt[0] - prev[0], pt[1] - prev[1]];
                }),
                pts[0][0], pts[0][1], [1, 1], 'F', true
            );

            p.setFillColor(...COLORS.WHITE);
            p.circle(cx, cy, ri, 'F');
        }

        tableHeader(x, y, w, cols) {
            const p = this.pdf;
            p.setFillColor(245, 245, 245);
            p.rect(x, y, w, 5.5, 'F');
            p.setFontSize(5.2); p.setFont('helvetica', 'bold');
            p.setTextColor(...COLORS.GRAY);
            cols.forEach(c => p.text(c.label, x + c.x, y + 4));
        }

        tableRow(x, y, w, h, cols, even, dotColor = null) {
            const p = this.pdf;
            if (even) {
                p.setFillColor(250, 250, 250);
                p.rect(x, y, w, h, 'F');
            }
            if (dotColor) {
                p.setFillColor(...dotColor);
                p.circle(x + 3, y + h / 2, 1.4, 'F');
            }
            p.setFontSize(5.8); p.setFont('helvetica', 'normal');
            p.setTextColor(...COLORS.DARK);
            cols.forEach(c => p.text(String(c.value), x + c.x, y + h - 2));
        }

        tableBorder(x, y, w, h) {
            const p = this.pdf;
            p.setDrawColor(...COLORS.GRAY_LIGHT);
            p.setLineWidth(0.18);
            p.rect(x, y, w, h, 'S');
        }

        /* ── insightBadge — acepta iconPng opcional ── */
        insightBadge(x, y, w, h, text, bgColor, dotColor, iconPng = null) {
            const p = this.pdf;
            this.card(x, y, w, h, { bg: bgColor, border: null });

            if (iconPng) {
                const iS = h * 0.65;
                p.addImage(iconPng, 'PNG', x + 3, y + (h - iS) / 2, iS, iS);
            } else {
                p.setFillColor(...dotColor);
                p.circle(x + 5, y + h / 2, 3.2, 'F');
            }

            p.setTextColor(...COLORS.DARK);
            p.setFontSize(6.5); p.setFont('helvetica', 'normal');
            p.text(p.splitTextToSize(text, w - 14), x + 15, y + h / 2 - 1);
        }
    }

    /* ── API pública ── */
    return {
        COLORS,
        KPI_STYLES,
        ICON_PATHS,
        svgToPng,
        loadImageBase64,
        imageDimensions,
        svgIconToPng,
        svgIconWithBg,
        loadKpiIcons,
        loadIcon,
        PDFBuilder,
    };

})();