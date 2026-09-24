<?php
/*
 * Secciones "Características" y "Variantes" de crear_productos.php.
 * Se usan al cargar la página y desde controllers/controlador_productos.php?metodo=html_variantes
 * para refrescarlas por AJAX sin recargar toda la página.
 */

function variantes_e($texto)
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

/* Clave para comparar combinaciones por texto (variantes antiguas sin códigos de característica) */
function variantes_clave_textos($textos)
{
    $textos = array_map(function ($t) {
        return mb_strtolower(trim($t), 'UTF-8');
    }, $textos);
    sort($textos);
    return implode('|', $textos);
}

function variantes_clave_codigos($codigos)
{
    $codigos = array_map('intval', $codigos);
    sort($codigos);
    return implode(',', $codigos);
}

/* Producto cartesiano de los valores de cada característica */
function variantes_combinaciones($caracteristicas)
{
    $combinaciones = [['textos' => [], 'codigos' => []]];
    foreach (($caracteristicas ?: []) as $caracteristica) {
        $detalles = $caracteristica['detalle'] ?: [];
        if (count($detalles) == 0)
            continue;
        $nuevas = [];
        foreach ($combinaciones as $combinacion) {
            foreach ($detalles as $detalle) {
                $nuevas[] = [
                    'textos' => array_merge($combinacion['textos'], [$detalle['detalle']]),
                    'codigos' => array_merge($combinacion['codigos'], [intval($detalle['cod_producto_caracteristica_detalle'])]),
                ];
            }
        }
        $combinaciones = $nuevas;
    }
    return count($combinaciones[0]['codigos']) > 0 ? $combinaciones : [];
}

/* Combinaciones que todavía no tienen una variante creada */
function variantes_pendientes($Clproductos, $cod_producto, $caracteristicas)
{
    $existentes = $Clproductos->getCombinacionesVariantes($cod_producto);
    $clavesCodigos = [];
    $clavesTextos = [];
    foreach ($existentes as $variante) {
        if (!empty($variante['codigos']))
            $clavesCodigos[variantes_clave_codigos($variante['codigos'])] = true;
        if (!empty($variante['textos']))
            $clavesTextos[variantes_clave_textos($variante['textos'])] = true;
    }

    $pendientes = [];
    foreach (variantes_combinaciones($caracteristicas) as $combinacion) {
        if (isset($clavesCodigos[variantes_clave_codigos($combinacion['codigos'])]))
            continue;
        if (isset($clavesTextos[variantes_clave_textos($combinacion['textos'])]))
            continue;
        $pendientes[] = $combinacion;
    }
    return $pendientes;
}

function variantes_badges($caracteristica)
{
    $items = "";
    foreach (($caracteristica['detalle'] ?: []) as $detalle) {
        $style = '';
        if (strtoupper($caracteristica['tipo']) == "COLOR")
            $style = 'style="background:' . variantes_e($detalle['detalle2']) . '40; color:' . variantes_e($detalle['detalle2']) . '; border: 0.1px solid #d1cfcf;"';
        $items .= '<span class="shadow-none badge badge-primary" ' . $style . '>' . variantes_e($detalle['detalle']) . '</span> &nbsp;';
    }
    return $items;
}

function variantes_fila_caracteristica($index)
{
    return '<div class="row filaCaracteristica">
                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                    <label>Caracter&iacute;stica <span class="asterisco">*</span></label>
                    <input type="text" placeholder="Ej. Talla" name="txt_opcion_titulo[]" class="form-control" required="required" autocomplete="off" value="">
                </div>
                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                    <label>Atributos <span class="asterisco">*</span>
                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escriba los valores de la característica"></span><span><i>&nbsp;Separar las opciones con una coma</i></span></label>
                    <select multiple="multiple" name="cmb_variante_productos[' . intval($index) . '][]" class="form-control taggingCaracteristica" required="required">
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-2 col-xs-12">
                    <label>Tipo</label>
                    <select name="cmb_variante_tipo[]" class="form-control" required="required">
                        <option value="texto">Texto</option>
                        <option value="color">Color</option>
                    </select>
                </div>
            </div>';
}

function html_producto_caracteristicas($Clproductos, $cod_producto)
{
    $caracteristicas = $cod_producto > 0 ? $Clproductos->getCaracteristicas($cod_producto) : [];
    $tieneVariantes = $cod_producto > 0 && (bool)$Clproductos->lista_variantes($cod_producto);

    $html = '';
    foreach (($caracteristicas ?: []) as $caracteristica) {
        $cod = intval($caracteristica['cod_producto_caracteristica']);
        $esColor = strtoupper($caracteristica['tipo']) == "COLOR";

        if ($esColor) {
            $inputs = '<div class="form-group col-md-5 col-sm-5 col-xs-12">
                            <input type="text" class="form-control txtNuevoValor" placeholder="Nuevo color, ej. Rojo" autocomplete="off">
                       </div>
                       <div class="form-group col-md-2 col-sm-2 col-xs-12">
                            <input type="color" class="form-control txtNuevoColor" value="#000000">
                       </div>';
        } else {
            $inputs = '<div class="form-group col-md-7 col-sm-7 col-xs-12">
                            <select multiple="multiple" class="form-control taggingValores"></select>
                       </div>';
        }

        $html .= '<div class="itemCaracteristica" style="margin-bottom: 15px;">
                    <h5>' . variantes_e($caracteristica['caracteristica']) . '</h5>
                    <div style="margin-bottom: 8px;">' . variantes_badges($caracteristica) . '</div>
                    <div class="row frmAgregarValores" data-caracteristica="' . $cod . '" data-tipo="' . ($esColor ? 'color' : 'texto') . '">
                        ' . $inputs . '
                        <div class="form-group col-md-3 col-sm-3 col-xs-12">
                            <button type="button" class="btn btn-outline-primary btnAgregarValores">Agregar valor' . ($esColor ? '' : 'es') . '</button>
                        </div>
                    </div>
                  </div>';
    }

    // Nuevas características solo mientras no existan variantes (las variantes creadas no tendrían ese atributo)
    if (!$tieneVariantes) {
        $titulo = $caracteristicas ? '<p style="margin-top: 10px;"><b>Nueva característica</b></p>' : '';
        $html .= $titulo . '<form id="frmCaracteristicas" class="frmCaracteristicas" method="POST" action="#">
                    <div class="VariantesSeleccion">' . variantes_fila_caracteristica(0) . '</div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 tablaColores" style="display:none;">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Valor</th>
                                        <th>Color</th>
                                    </tr>
                                </thead>
                                <tbody class="respAtributosValidar"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                            <button type="button" class="btn btn-outline-primary" id="btnAgregarVariante">Agregar otra caracter&iacute;stica</button>
                            <button type="button" class="btn btn-primary" id="btnGuardarCaracteristicas">Guardar Características</button>
                        </div>
                    </div>
                </form>';
    } else {
        $html .= '<p style="margin-top: 10px;"><i>Para agregar una opción nueva (ej. "Extra grande") añádela a la característica y luego crea la variante que aparecerá en la sección Variantes.</i></p>';
    }

    return $html;
}

function html_producto_variantes($Clproductos, $cod_producto, $sku_padre, $files)
{
    $caracteristicas = $cod_producto > 0 ? $Clproductos->getCaracteristicas($cod_producto) : [];
    if (!$caracteristicas)
        return '<p>Para crear variantes el producto debe tener creada características</p>';

    $html = '';
    $variantes = $Clproductos->lista_variantes($cod_producto);
    if ($variantes) {
        $filas = '';
        foreach ($variantes as $variante) {
            $htmlOpciones = ($variante['atributos']) ? implode("/", $variante['atributos']) : "";
            $filas .= '<tr>
                        <td class="text-center">
                            <span><img src="' . variantes_e($files . $variante['image_min']) . '" class="profile-img" alt="' . variantes_e($variante['nombre']) . '"></span>
                        </td>
                        <td>' . variantes_e($htmlOpciones) . '</td>
                        <td>$' . variantes_e($variante['precio']) . '</td>
                        <td>' . variantes_e($variante['sku']) . '</td>
                        <td>
                            <a target="_blank" href="crear_productos.php?id=' . variantes_e($variante['alias']) . '" class="bs-tooltip" title="Editar Variante"><i data-feather="edit-2"></i></a>
                        </td>
                      </tr>';
        }
        $html .= '<table class="table table-hover table-bordered style-3">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Variaciones</th>
                            <th>Precio</th>
                            <th>SKU</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="respVariantes">' . $filas . '</tbody>
                  </table>';
    }

    $pendientes = variantes_pendientes($Clproductos, $cod_producto, $caracteristicas);
    if (count($pendientes) == 0) {
        if ($variantes)
            $html .= '<p><i>Todas las combinaciones ya tienen variante. Para crear más, agrega valores a una característica.</i></p>';
        return $html;
    }

    $numero = $variantes ? count($variantes) : 0;
    $filas = '';
    foreach ($pendientes as $combinacion) {
        $numero++;
        $sku = $sku_padre != "" ? $sku_padre . '-' . $numero : "";
        $filas .= '<tr class="filaVariantePendiente">
                    <td class="text-center"><input type="checkbox" class="chkCrearVariante" checked></td>
                    <td>' . variantes_e(implode("/", $combinacion['textos'])) . '</td>
                    <td>
                        <input type="number" step="0.01" min="0" placeholder="0.00" name="txt_precio_variante[]" class="form-control txtPrecioVariante"/>
                        <input type="hidden" value="' . variantes_e(json_encode($combinacion['codigos'])) . '" name="txt_atributos_codigo[]"/>
                    </td>
                    <td>
                        <input type="text" name="txt_variante_sku[]" class="form-control" placeholder="SKU" value="' . variantes_e($sku) . '"/>
                    </td>
                  </tr>';
    }

    $titulo = $variantes ? '<p style="margin-top: 15px;"><b>Combinaciones sin variante</b> — marca las que quieras crear</p>' : '';
    $html .= $titulo . '<form id="frmVariantes" class="frmVariantes" method="POST" action="#">
                <table class="table table-hover table-bordered style-3">
                    <thead>
                        <tr>
                            <th class="text-center"><input type="checkbox" class="chkCrearTodas" checked></th>
                            <th>Variante</th>
                            <th>Precio</th>
                            <th>SKU</th>
                        </tr>
                    </thead>
                    <tbody>' . $filas . '</tbody>
                </table>
                <div style="text-align: right;">
                    <button type="button" class="btn btn-primary" id="btnGuardarVariante">Crear variantes</button>
                </div>
            </form>';
    return $html;
}
