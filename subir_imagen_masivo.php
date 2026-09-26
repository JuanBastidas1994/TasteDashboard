<?php
require_once "funciones.php";
require_once "clases/cl_productos.php";

if(!isLogin()){
    header("location:login.php");
}

$Clproductos = new cl_productos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$productos = $Clproductos->lista() ?: [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style>
        .zonaMasiva {
            border: 2px dashed #bfc9d4;
            border-radius: 8px;
            padding: 30px 15px;
            text-align: center;
            color: #888ea8;
            cursor: pointer;
            transition: all .15s;
        }
        .zonaMasiva.arrastrando { border-color: #1b55e2; background: #1b55e20d; color: #1b55e2; }

        .gridProductos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 15px;
        }
        .cardImagen {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
            position: relative;
            transition: all .15s;
        }
        .cardImagen.arrastrando { border: 2px dashed #1b55e2; background: #1b55e20d; }
        .cardImagen.subiendo { opacity: .5; pointer-events: none; }
        .cardImagen.ok { border-color: #8dbf42; }
        .cardImagen.error { border-color: #e7515a; }
        .cardImagen .contImg {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f1f2f3;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cardImagen .contImg img { width: 100%; height: 100%; object-fit: contain; }
        .cardImagen .contImg .sinImagen { color: #888ea8; font-size: 12px; }
        .cardImagen .nombre { font-size: 13px; font-weight: 600; color: #3b3f5c; margin: 8px 0 4px; min-height: 36px; }
        .cardImagen .archivo {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #515365;
        }
        .cardImagen .archivo code { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cardImagen .btnCopiar { padding: 2px 8px; font-size: 11px; }
        .cardImagen .estadoSubida { font-size: 11px; margin-top: 4px; min-height: 16px; }

        .tablaMasiva td, .tablaMasiva th { padding: 6px 10px !important; font-size: 13px; }
    </style>
</head>
<body>

    <?php echo top() ?>
    <?php echo navbar(); ?>

    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php echo sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <h4>Subir imágenes de productos</h4>
                            <p>Las imágenes se guardan <b>tal cual</b> (sin recortar ni comprimir). Arrastra una foto sobre la tarjeta del producto o suelta varias abajo: se asignan por nombre de archivo (ej. <code>IRRESISTIBLE.jpg</code>).</p>

                            <input type="file" id="fileMasivo" accept="image/jpeg,image/png,image/webp" multiple style="display:none;">
                            <div class="zonaMasiva" id="zonaMasiva">
                                <b>Carga masiva</b><br>
                                Suelta aquí varias imágenes o haz clic para elegirlas
                            </div>

                            <div id="boxMasivo" style="display:none; margin-top: 15px;">
                                <table class="table table-bordered tablaMasiva">
                                    <thead>
                                        <tr>
                                            <th>Archivo</th>
                                            <th>Producto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="respMasivo"></tbody>
                                </table>
                                <div style="text-align: right;">
                                    <button type="button" class="btn btn-outline-primary" id="btnCancelarMasivo">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnSubirMasivo">Subir imágenes</button>
                                </div>
                            </div>

                            <hr>

                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-6 col-sm-8 col-xs-12">
                                    <input type="text" class="form-control" id="txtBuscar" placeholder="Buscar producto o nombre de archivo...">
                                </div>
                                <div class="col-md-6 col-sm-4 col-xs-12" style="padding-top: 8px;">
                                    <label style="cursor:pointer;"><input type="checkbox" id="chkSinImagen"> Solo productos sin imagen</label>
                                    <span style="margin-left: 15px;" id="lblContador"></span>
                                </div>
                            </div>

                            <input type="file" id="fileUnico" accept="image/jpeg,image/png,image/webp" style="display:none;">
                            <div class="gridProductos">
                                <?php
                                foreach($productos as $producto){
                                    $archivo = strtoupper($producto['alias']).'.jpg';
                                    $tieneImagen = $producto['image_min'] !== '';
                                    $img = $tieneImagen
                                        ? '<img src="'.htmlspecialchars($files.$producto['image_min']).'" alt="" loading="lazy">'
                                        : '<span class="sinImagen">Sin imagen</span>';
                                    echo '<div class="cardImagen" data-id="'.intval($producto['cod_producto']).'"
                                                data-alias="'.htmlspecialchars(strtoupper($producto['alias'])).'"
                                                data-nombre="'.htmlspecialchars(mb_strtolower($producto['nombre'], 'UTF-8')).'"
                                                data-sin-imagen="'.($tieneImagen ? 0 : 1).'">
                                            <div class="contImg" title="Clic para elegir una imagen o arrastra una aquí">'.$img.'</div>
                                            <div class="nombre">'.htmlspecialchars($producto['nombre']).'</div>
                                            <div class="archivo">
                                                <code title="'.htmlspecialchars($archivo).'">'.htmlspecialchars($archivo).'</code>
                                                <button type="button" class="btn btn-outline-primary btnCopiar" data-texto="'.htmlspecialchars(strtoupper($producto['alias'])).'" title="Copiar nombre de archivo (sin extensión)">Copiar</button>
                                            </div>
                                            <div class="estadoSubida"></div>
                                          </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/subir_imagen_masivo.js?v=1" type="text/javascript"></script>
</body>
</html>
