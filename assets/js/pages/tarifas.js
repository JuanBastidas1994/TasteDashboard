/**
 * tarifas.js
 * Maneja la UI de Tarifas dentro del tab "Costo de envío" de crear_sucursales.php
 * 
 * Dependencias: Handlebars, Swal (SweetAlert2), feather, messageDone(), OpenLoad(), CloseLoad()
 * Templates requeridos en crear_sucursales.php:
 *   #tarifa-tab-template   → tab pill de una tarifa
 *   #tarifa-pane-template  → contenido del panel de una tarifa
 *   #rango-template        → fila de un rango
 */

/* ═══════════════════════════════════════
 *  CARGA INICIAL
 * ═══════════════════════════════════════ */
function initTarifas() {
    const cod_sucursal = parseInt($("#cod_sucursal").val());
    if (cod_sucursal === 0) return;

    OpenLoad("Cargando tarifas...");

    fetch(`controllers/controlador_tarifas.php?metodo=getTarifas&cod_sucursal=${cod_sucursal}`)
        .then(r => r.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                if (response.data.length === 0) {
                    // Sucursal nueva sin tarifas: creamos una por defecto
                    _crearTarifaUI({ cod_tarifa: 0, nombre: 'Estándar', peso_max_kg: '', rangos: [] }, true);
                } else {
                    response.data.forEach((tarifa, idx) => {
                        _crearTarifaUI(tarifa, idx === 0);
                    });
                }
            }
        })
        .catch(err => {
            CloseLoad();
            console.error(err);
        });
}

/* ═══════════════════════════════════════
 *  CREAR TARIFA (UI solamente)
 * ═══════════════════════════════════════ */
function _crearTarifaUI(tarifa, activa = false) {
    const tabTemplate   = Handlebars.compile($("#tarifa-tab-template").html());
    const paneTemplate  = Handlebars.compile($("#tarifa-pane-template").html());

    const tabData = {
        cod_tarifa : tarifa.cod_tarifa,
        nombre     : tarifa.nombre,
        peso_max_kg     : tarifa.peso_max_kg,
        active     : activa ? 'active' : '',
        show       : activa ? 'show active' : ''
    };

    $("#tarifas-tabs").append(tabTemplate(tabData));
    $("#tarifas-content").append(paneTemplate(tabData));
    feather.replace();

    // Pintar rangos si ya existen (al cargar desde BD)
    if (tarifa.rangos && tarifa.rangos.length > 0) {
        tarifa.rangos.forEach(rango => _addRangoUI(tarifa.cod_tarifa, rango));
    }
}

/* ═══════════════════════════════════════
 *  BOTÓN "NUEVA TARIFA"
 * ═══════════════════════════════════════ */
function nuevaTarifa() {
    const cod_sucursal = parseInt($("#cod_sucursal").val());
    if (cod_sucursal === 0) {
        messageDone('Guarda la sucursal antes de agregar tarifas', 'error');
        return;
    }

    Swal.fire({
        title: 'Nueva tarifa',
        html: `
            <div style="display: flex; flex-direction: column; align-items: flex-start; width: 80%; margin: 0 auto;">
                <label style="margin-bottom: 5px; font-weight: bold;">Nombre</label>
                <input id="swal-nombre" class="swal2-input" style="width: 100%; margin: 0 0 15px 0;" value="" placeholder="Nombre">
                
                <label style="margin-bottom: 5px; font-weight: bold;">Peso máximo en Kg</label>
                <input id="swal-peso" class="swal2-input" style="width: 100%; margin: 0;" type="number" value="" placeholder="Peso máximo">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Crear',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = document.getElementById('swal-nombre').value.trim();
            const peso = document.getElementById('swal-peso').value.trim();
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            if (!peso) {
                Swal.showValidationMessage('El peso es obligatorio');
                return false;
            }
            return {
                nombre,
                peso_max_kg: peso
            };
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        OpenLoad("Creando tarifa...");
        fetch('controllers/controlador_tarifas.php?metodo=saveTarifa', {
            method: 'POST',
            body: JSON.stringify({
                cod_sucursal,
                nombre     : result.value.nombre,
                peso_max_kg: result.value.peso_max_kg
            })
        })
        .then(r => r.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                _crearTarifaUI({
                    cod_tarifa : response.cod_tarifa,
                    nombre     : result.value.nombre,
                    peso_max_kg: result.value.peso_max_kg,
                    rangos     : []
                }, false);
                messageDone('Tarifa creada', 'success');
            } else {
                messageDone(response.mensaje, 'error');
            }
        })
        .catch(err => {
            CloseLoad();
            console.error(err);
        });
    });
}

/* ═══════════════════════════════════════
 *  EDITAR NOMBRE / PESO DE UNA TARIFA
 * ═══════════════════════════════════════ */
$("body").on("click", ".btnEditarTarifa", function () {
    const cod_tarifa    = $(this).data("tarifa");
    const cod_sucursal  = parseInt($("#cod_sucursal").val());
    const nombreActual  = $(this).closest(".tarifa-tab-header").find(".tarifa-label").text().trim();
    const pesoActual = $(this).data("peso") || '';

    Swal.fire({
        title: 'Editar tarifa',
        html: `
            <div style="display: flex; flex-direction: column; align-items: flex-start; width: 80%; margin: 0 auto;">
                <label style="margin-bottom: 5px; font-weight: bold;">Nombre</label>
                <input id="swal-nombre" class="swal2-input" style="width: 100%; margin: 0 0 15px 0;" value="${nombreActual}" placeholder="Nombre">
                
                <label style="margin-bottom: 5px; font-weight: bold;">Peso máximo en kilogramos</label>
                <input id="swal-peso" class="swal2-input" style="width: 100%; margin: 0;" type="number" value="${pesoActual}" placeholder="Peso">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = document.getElementById('swal-nombre').value.trim();
            const peso = document.getElementById('swal-peso').value.trim();
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            if (!peso) {
                Swal.showValidationMessage('El peso es obligatorio');
                return false;
            }
            return { nombre, peso_max_kg: peso };
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        OpenLoad("Guardando...");
        fetch('controllers/controlador_tarifas.php?metodo=saveTarifa', {
            method: 'POST',
            body: JSON.stringify({
                cod_tarifa,
                cod_sucursal,
                nombre     : result.value.nombre,
                peso_max_kg: result.value.peso_max_kg
            })
        })
        .then(r => r.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                // Actualizar label del tab
                $(`#tab-tarifa-${cod_tarifa} .tarifa-label`).text(result.value.nombre);
                $(`#tab-tarifa-${cod_tarifa} .btnEditarTarifa`).data("peso", result.value.peso_max_kg); // ← agregar
                $(`#pane-tarifa-${cod_tarifa} .tarifa-peso`).html(result.value.peso_max_kg + " kg");
                messageDone('Tarifa actualizada', 'success');
            } else {
                messageDone(response.mensaje, 'error');
            }
        });
    });
});

/* ═══════════════════════════════════════
 *  ELIMINAR TARIFA
 * ═══════════════════════════════════════ */
$("body").on("click", ".btnEliminarTarifa", function () {
    const cod_tarifa = $(this).data("tarifa");

    // No permitir eliminar si es la única tarifa
    if ($("#tarifas-tabs .tarifa-tab-item").length <= 1) {
        messageDone('Debe existir al menos una tarifa', 'error');
        return;
    }

    Swal.fire({
        title: '¿Eliminar tarifa?',
        text: 'Se eliminarán también todos sus rangos. Esta acción no se puede revertir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(result => {
        if (!result.value) return;

        OpenLoad("Eliminando...");
        fetch(`controllers/controlador_tarifas.php?metodo=removeTarifa&cod_tarifa=${cod_tarifa}`)
            .then(r => r.json())
            .then(response => {
                CloseLoad();
                if (response.success == 1) {
                    $(`#tab-tarifa-${cod_tarifa}`).remove();
                    $(`#pane-tarifa-${cod_tarifa}`).remove();
                    // Activar el primer tab que quede
                    $("#tarifas-tabs .tarifa-tab-item:first a").tab('show');
                    messageDone('Tarifa eliminada', 'success');
                } else {
                    messageDone(response.mensaje, 'error');
                }
            })
            .catch(err => {
                CloseLoad();
                console.error(err);
            });
    });
});

/* ═══════════════════════════════════════
 *  AGREGAR RANGO (UI)
 * ═══════════════════════════════════════ */
function addRango(cod_tarifa) {
    _addRangoUI(cod_tarifa, { id: 0, distancia_ini: '', distancia_fin: '', precio: '' });
}

function _addRangoUI(cod_tarifa, rango) {
    const template = Handlebars.compile($("#rango-template").html());
    const html     = template({
        cod_tarifa,
        id           : rango.id,
        distancia_ini: rango.distancia_ini,
        distancia_fin: rango.distancia_fin,
        precio       : rango.precio
    });
    $(`#pane-tarifa-${cod_tarifa} .lst-rangos`).append(html);
    feather.replace();
}

/* ═══════════════════════════════════════
 *  GUARDAR RANGOS DE UNA TARIFA
 * ═══════════════════════════════════════ */
function saveRangos(cod_tarifa) {
    const cod_sucursal = parseInt($("#cod_sucursal").val());
    if (cod_sucursal === 0) {
        messageDone('Guarda la sucursal antes de guardar rangos', 'error');
        return;
    }

    const contenedor = $(`#pane-tarifa-${cod_tarifa}`);
    const rangosEl   = contenedor.find(".rango");

    if (rangosEl.length === 0) {
        messageDone('Agrega al menos un rango', 'error');
        return;
    }

    const rangos    = [];
    let   validated = true;

    rangosEl.each(function () {
        const id            = $(this).find('.rango-id').val();
        const distancia_ini = $(this).find('.distancia-ini').val();
        const distancia_fin = $(this).find('.distancia-fin').val();
        const precio        = $(this).find('.rango-precio').val();

        if (distancia_ini.trim() === '') { messageDone('Ingresa distancia inicial', 'error'); validated = false; return false; }
        if (distancia_fin.trim() === '') { messageDone('Ingresa distancia final',   'error'); validated = false; return false; }
        if (precio.trim()        === '') { messageDone('Ingresa precio',            'error'); validated = false; return false; }

        rangos.push({ id, distancia_ini, distancia_fin, precio });
    });

    if (!validated) return;

    OpenLoad("Guardando rangos...");
    fetch('controllers/controlador_tarifas.php?metodo=saveRangos', {
        method: 'POST',
        body: JSON.stringify({ cod_tarifa, rangos })
    })
    .then(r => r.json())
    .then(response => {
        CloseLoad();
        if (response.success == 1) {
            messageDone(response.mensaje, 'success');
        } else {
            messageDone(response.mensaje, 'error');
        }
    })
    .catch(err => {
        CloseLoad();
        console.error(err);
    });
}

/* ═══════════════════════════════════════
 *  ELIMINAR RANGO
 * ═══════════════════════════════════════ */
$("body").on("click", ".btnRemoverRango", function () {
    const element = $(this);
    const id      = parseInt(element.data("id"));

    if (id === 0) {
        element.closest('.rango').remove();
        return;
    }

    Swal.fire({
        title: 'Se eliminará este rango, ¿continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(result => {
        if (!result.value) return;

        OpenLoad("Eliminando rango...");
        fetch(`controllers/controlador_tarifas.php?metodo=removeRango&id=${id}`)
            .then(r => r.json())
            .then(response => {
                CloseLoad();
                if (response.success == 1) {
                    element.closest('.rango').remove();
                    messageDone(response.mensaje, 'success');
                } else {
                    messageDone(response.mensaje, 'error');
                }
            })
            .catch(err => {
                CloseLoad();
                console.error(err);
            });
    });
});
