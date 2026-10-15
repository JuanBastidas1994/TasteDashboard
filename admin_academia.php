<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}

$session = getSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .thumb-preview {
            width: 120px;
            height: 68px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e9e9e9;
            background: #f1f2f3;
        }
        .thumb-preview.hidden { display: none; }
        .badge-estado-A { background: #FD6550; color: #fff; }
        .badge-estado-I { background: #E4E3EA; color: #202020; }
        #sortable-body tr { cursor: grab; }
        #sortable-body tr:active { cursor: grabbing; }
        .drag-handle { color: #aaa; cursor: grab; }
    </style>
</head>
<body>
    <?php top(); ?>
    <?php navbar(true, "academia.php"); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-12 layout-spacing">

                        <div class="widget-content widget-content-area br-6">

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h4 class="mb-0">Gestión de Tutoriales</h4>
                                <button class="btn btn-primary" id="btnNuevoTutorial">
                                    <i data-feather="plus" style="width:16px;height:16px;margin-right:4px;"></i>
                                    Nuevo tutorial
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="tbl-academia" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:32px;"></th>
                                            <th>#</th>
                                            <th>Miniatura</th>
                                            <th>Título</th>
                                            <th>Categoría</th>
                                            <th>Duración</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-body">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL: Crear / Editar tutorial
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="modalTutorial" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nuevo tutorial</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cod_tutorial" value="">

                    <div class="row">
                        <!-- Título -->
                        <div class="form-group col-md-12">
                            <label>Título <span class="text-danger">*</span></label>
                            <input type="text" id="titulo" class="form-control" placeholder="Ej: Cómo crear una promoción">
                        </div>

                        <!-- Descripción -->
                        <div class="form-group col-md-12">
                            <label>Descripción</label>
                            <textarea id="descripcion" class="form-control" rows="2"
                                      placeholder="Breve descripción del tutorial..."></textarea>
                        </div>

                        <!-- URL YouTube + preview -->
                        <div class="form-group col-md-8">
                            <label>URL o ID de YouTube <span class="text-danger">*</span></label>
                            <input type="text" id="youtube_url" class="form-control"
                                   placeholder="https://www.youtube.com/watch?v=... o solo el ID">
                            <input type="hidden" id="youtube_id">
                            <small class="text-muted">Pega la URL completa o solo el ID del video.</small>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-center align-items-center">
                            <img id="youtube_thumb" src="" alt="preview"
                                 class="thumb-preview hidden mb-1">
                            <small id="thumb_label" class="text-muted" style="display:none;">Vista previa</small>
                        </div>

                        <!-- Categoría -->
                        <div class="form-group col-md-4">
                            <label>Categoría <span class="text-danger">*</span></label>
                            <select id="categoria" class="form-control">
                                <option value="">— Selecciona —</option>
                                <option>Primeros pasos</option>
                                <option>Sucursales</option>
                                <option>Productos</option>
                                <option>Órdenes</option>
                                <option>Promociones</option>
                                <option>Clientes</option>
                                <option>Usuarios</option>
                                <option>Mis motorizados</option>
                                <option>Reportes</option>
                                <option>Configuración</option>
                            </select>
                        </div>

                        <!-- Duración -->
                        <div class="form-group col-md-4">
                            <label>Duración</label>
                            <input type="text" id="duracion" class="form-control" placeholder="Ej: 1:30">
                        </div>

                        <!-- Fecha -->
                        <div class="form-group col-md-4">
                            <label>Fecha de publicación</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i data-feather="calendar" style="width:14px;height:14px;"></i>
                                    </span>
                                </div>
                                <input type="text" id="fecha" class="form-control"
                                       placeholder="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="form-group col-md-12 d-flex align-items-center" style="gap:12px;">
                            <label class="mb-0">Estado</label>
                            <label class="switch s-icons s-outline s-outline-success mb-0">
                                <input type="checkbox" id="chk_estado" checked>
                                <span class="slider round"></span>
                            </label>
                            <span id="lbl_estado" class="text-muted" style="font-size:13px;">Activo</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-primary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnGuardarTutorial">
                        <i data-feather="save" style="width:15px;height:15px;margin-right:4px;"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="plugins/sweetalerts/promise-polyfill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>

    <script>
    const CTRL = 'controllers/controlador_academia.php';

    /* ══════ utilidades ══════ */
    function extractYoutubeId(input) {
        input = input.trim();
        const patterns = [
            /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_\-]{11})/,
            /^([a-zA-Z0-9_\-]{11})$/
        ];
        for (const p of patterns) {
            const m = input.match(p);
            if (m) return m[1];
        }
        return null;
    }

    function thumbUrl(id) {
        return `https://img.youtube.com/vi/${id}/mqdefault.jpg`;
    }

    function badgeEstado(estado) {
        return estado === 'A'
            ? '<span class="badge badge-status-entregada">Activo</span>'
            : '<span class="badge badge-status-aceptada">Inactivo</span>';
    }

    /* ══════ cargar tabla ══════ */
    function cargarTabla() {
        $.get(CTRL + '?metodo=lista', function(resp) {
            const tbody = $('#sortable-body').empty();
            if (!resp.data || !resp.data.length) {
                tbody.append('<tr><td colspan="9" class="text-center text-muted py-4">Sin tutoriales cargados</td></tr>');
                feather.replace();
                return;
            }
            resp.data.forEach(function(t) {
                tbody.append(`
                    <tr data-id="${t.cod_tutorial}">
                        <td><i data-feather="menu" class="drag-handle" style="width:16px;height:16px;"></i></td>
                        <td>${t.cod_tutorial}</td>
                        <td>
                            <img src="${thumbUrl(t.youtube_id)}" alt=""
                                 style="width:80px;height:46px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td>
                            <strong>${t.titulo}</strong><br>
                            <small class="text-muted">${(t.descripcion||'').substring(0,60)}${t.descripcion&&t.descripcion.length>60?'…':''}</small>
                        </td>
                        <td><span class="badge" style="background:#E4E3EA;color:#202020;">${t.categoria}</span></td>
                        <td>${t.duracion||'—'}</td>
                        <td>${t.fecha||'—'}</td>
                        <td class="text-center">${badgeEstado(t.estado)}</td>
                        <td class="text-center">
                            <ul class="table-controls" style="justify-content:center;">
                                <li>
                                    <a href="javascript:void(0)" title="Editar"
                                       onclick="abrirEditar(${t.cod_tutorial})">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" title="Eliminar"
                                       onclick="confirmarEliminar(${t.cod_tutorial}, '${t.titulo.replace(/'/g,"\\'")}')">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>`);
            });
            feather.replace();
        });
    }

    /* ══════ limpiar / abrir modal ══════ */
    function limpiarModal() {
        $('#cod_tutorial').val('');
        $('#titulo').val('');
        $('#descripcion').val('');
        $('#youtube_url').val('');
        $('#youtube_id').val('');
        $('#youtube_thumb').addClass('hidden');
        $('#thumb_label').hide();
        $('#categoria').val('');
        $('#duracion').val('');
        $('#fecha').val('<?= date('Y-m-d') ?>');
        $('#chk_estado').prop('checked', true);
        $('#lbl_estado').text('Activo');
        $('#modalTitulo').text('Nuevo tutorial');
    }

    $('#btnNuevoTutorial').on('click', function() {
        limpiarModal();
        $('#modalTutorial').modal('show');
    });

    /* ══════ preview YouTube ══════ */
    $('#youtube_url').on('input', function() {
        const id = extractYoutubeId($(this).val());
        if (id) {
            $('#youtube_id').val(id);
            $('#youtube_thumb').attr('src', thumbUrl(id)).removeClass('hidden');
            $('#thumb_label').show();
        } else {
            $('#youtube_id').val('');
            $('#youtube_thumb').addClass('hidden');
            $('#thumb_label').hide();
        }
    });

    /* ══════ toggle estado label ══════ */
    $('#chk_estado').on('change', function() {
        $('#lbl_estado').text($(this).is(':checked') ? 'Activo' : 'Inactivo');
    });

    /* ══════ editar ══════ */
    function abrirEditar(id) {
        $.get(CTRL + '?metodo=get&cod_tutorial=' + id, function(resp) {
            if (!resp.success) { messageDone(resp.mensaje, 'error'); return; }
            const t = resp.data;
            $('#cod_tutorial').val(t.cod_tutorial);
            $('#titulo').val(t.titulo);
            $('#descripcion').val(t.descripcion);
            $('#youtube_url').val(t.youtube_id);
            $('#youtube_id').val(t.youtube_id);
            $('#youtube_thumb').attr('src', thumbUrl(t.youtube_id)).removeClass('hidden');
            $('#thumb_label').show();
            $('#categoria').val(t.categoria);
            $('#duracion').val(t.duracion);
            $('#fecha').val(t.fecha);
            $('#chk_estado').prop('checked', t.estado === 'A');
            $('#lbl_estado').text(t.estado === 'A' ? 'Activo' : 'Inactivo');
            $('#modalTitulo').text('Editar tutorial');
            $('#modalTutorial').modal('show');
        });
    }

    /* ══════ guardar (crear / actualizar) ══════ */
    $('#btnGuardarTutorial').on('click', function() {
        const titulo = $('#titulo').val().trim();
        const ytId   = $('#youtube_id').val().trim();
        const cat    = $('#categoria').val();

        if (!titulo) { messageDone('El título es obligatorio', 'warning'); return; }
        if (!ytId)   { messageDone('Ingresa una URL o ID de YouTube válido', 'warning'); return; }
        if (!cat)    { messageDone('Selecciona una categoría', 'warning'); return; }

        const codTutorial = $('#cod_tutorial').val();
        const metodo = codTutorial ? 'actualizar' : 'crear';
        const data = {
            cod_tutorial: codTutorial,
            titulo:       titulo,
            descripcion:  $('#descripcion').val().trim(),
            youtube_id:   ytId,
            duracion:     $('#duracion').val().trim(),
            fecha:        $('#fecha').val(),
            categoria:    cat,
            estado:       $('#chk_estado').is(':checked') ? 'A' : 'I',
        };

        OpenLoad('Guardando...');
        $.post(CTRL + '?metodo=' + metodo, data, function(resp) {
            CloseLoad();
            if (resp.success) {
                messageDone(resp.mensaje, 'success');
                $('#modalTutorial').modal('hide');
                cargarTabla();
            } else {
                messageDone(resp.mensaje, 'error');
            }
        });
    });

    /* ══════ eliminar ══════ */
    function confirmarEliminar(id, titulo) {
        Swal.fire({
            title: '¿Eliminar tutorial?',
            html: `<b>${titulo}</b><br><small>Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FD6550',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, eliminar',
        }).then(function(result) {
            if (result.isConfirmed) {
                OpenLoad('Eliminando...');
                $.get(CTRL + '?metodo=eliminar&cod_tutorial=' + id, function(resp) {
                    CloseLoad();
                    messageDone(resp.mensaje, resp.success ? 'success' : 'error');
                    if (resp.success) cargarTabla();
                });
            }
        });
    }

    /* ══════ flatpickr fecha ══════ */
    $(function() {
        flatpickr('#fecha', { dateFormat: 'Y-m-d', enableTime: false });
        feather.replace();
        cargarTabla();
    });
    </script>
</body>
</html>
