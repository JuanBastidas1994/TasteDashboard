<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if (!isLogin()) {
    header("location:login.php");
}

$session    = getSession();
$Clempresas = new cl_empresas();
$empresa    = $Clempresas->get($session['cod_empresa']);
$iva        = isset($empresa['impuesto']) ? intval($empresa['impuesto']) : 15;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php css_mandatory(); ?>
</head>
<body>
    <?php top(); ?>
    <?php navbar(); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12">
                        <div style="background-color: white; border-radius: 10px; padding: 24px;">

                            <h4>Importar Productos Masivamente</h4>
                            <hr/>

                            <!-- Descripción del formato -->
                            <div class="mb-4">
                                <h6 class="mb-2">Formato esperado del archivo Excel</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" style="font-size: 13px;">
                                        <thead style="background-color: #3b3f5c; color: white;">
                                            <tr>
                                                <th style="width: 80px;">A — ID</th>
                                                <th>B — Categoría</th>
                                                <th>C — Nombre del producto</th>
                                                <th>D — Descripción</th>
                                                <th style="width: 110px;">E — Precio sin IVA</th>
                                                <th style="width: 110px;">F — Precio con IVA</th>
                                                <th>G — Opciones / Variantes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-warning">
                                                <td class="text-muted"><em>vacío</em></td>
                                                <td>Bolones</td>
                                                <td>Bolón de chicharrón</td>
                                                <td>Masa de verde majada con trozos de chicharrón</td>
                                                <td>3.13</td>
                                                <td>3.50</td>
                                                <td>Salsa de queso, aji</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td>142</td>
                                                <td>Bebidas</td>
                                                <td>Jugo de naranja</td>
                                                <td>Jugo natural sin azúcar</td>
                                                <td>1.50</td>
                                                <td>1.50</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-info mt-2 mb-0" style="font-size: 13px;">
                                    <strong>Columna A — ID:</strong> Dejar vacío para <strong>crear</strong> el producto. Poner el ID numérico del producto para <strong>actualizarlo</strong>.<br>
                                    <strong>Columna B — Categoría:</strong> Escribe el nombre. Si no existe en la base de datos, se crea automáticamente.<br>
                                    <strong>Columnas E y F — IVA:</strong>
                                    Si F está vacío <em>o</em> F = E → el producto <strong>no cobra IVA</strong>.
                                    Si F &gt; E → el producto <strong>cobra IVA (<?= $iva ?>%)</strong>, E es el precio base y F el precio final.
                                </div>
                            </div>

                            <!-- Formulario de carga -->
                            <form name="frmImportar" id="frmImportar" enctype="multipart/form-data">
                                <div class="row align-items-end">
                                    <div class="col-xl-5 col-md-6 col-sm-8 col-12 mb-3">
                                        <label>Archivo Excel (.xls / .xlsx)</label>
                                        <input type="file" name="excel" id="excel" class="form-control" accept=".xls,.xlsx">
                                    </div>
                                    <div class="col-xl-3 col-md-3 col-sm-4 col-12 mb-3">
                                        <button type="button" class="btn btn-primary btn-block" id="btn_importar">
                                            <i class="fa fa-upload mr-1"></i> Importar productos
                                        </button>
                                    </div>
                                    <div class="col-xl-1 col-12 mb-3"></div>
                                    <div class="col-xl-3 col-md-3 col-sm-4 col-12 mb-3">
                                        <a href="controllers/controlador_importar_v3.php?metodo=exportar"
                                           class="btn btn-success btn-block" target="_blank">
                                            <i class="fa fa-download mr-1"></i> Descargar plantilla
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Cargando -->
                            <div id="divCargando" style="display:none; padding: 30px 0; text-align: center;">
                                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                                <p class="mt-2 text-muted">Procesando el archivo, por favor espere…</p>
                            </div>

                            <!-- Resultados -->
                            <div id="divDatos" style="display:none;" class="mt-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 mr-3">Resultados</h6>
                                    <span id="resumen"></span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover style-3">
                                        <thead>
                                            <tr>
                                                <th>Fila</th>
                                                <th>Categoría</th>
                                                <th>Nombre</th>
                                                <th>Precio base</th>
                                                <th>Estado</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody id="datos"></tbody>
                                    </table>
                                </div>

                                <script id="itemTabla" type="text/x-handlebars-template">
                                {{#each this}}
                                    <tr>
                                        <td>{{fila}}</td>
                                        <td>{{categoria}}</td>
                                        <td>{{nombre}}</td>
                                        <td>$ {{precio}}</td>
                                        {{#if importado}}
                                            <td><span class="badge badge-success">OK</span></td>
                                            <td style="color: #28a745;">{{motivo}}</td>
                                        {{else}}
                                            <td><span class="badge badge-danger">ERROR</span></td>
                                            <td style="color: #dc3545;">{{motivo}}</td>
                                        {{/if}}
                                    </tr>
                                {{/each}}
                                </script>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/importar_v3.js"></script>
</body>
</html>
