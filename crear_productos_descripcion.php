<?php
require_once "funciones.php";
require_once "clases/cl_productos_descripcion.php";
require_once "clases/cl_productos.php";

if (!isLogin()) {
  header("location:login.php");
}


$ClproductosDescripcion = new cl_productos_descripcion(NULL);
$Clproductos = new cl_productos(NULL);

$session = getSession();

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$cod_producto_descripcion = 0;
$imagen = url_sistema . '/assets/img/200x200.jpg';

if (isset($_GET['id']) && $_GET['id'] != 0) {
  $cod_producto = $_GET['id'];
  $producto = $Clproductos->get($cod_producto);
  $productosDescripciones = $ClproductosDescripcion->listarPorProducto($producto['cod_producto']);
} else {
  header("location: ./crear_productos_hb.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="gb18030">
  <?php css_mandatory(); ?>
  <style type="text/css">
    .croppie-container .cr-boundary {
      background-image: url(assets/img/transparent.jpg);
      background-position: center;
      background-size: cover;
    }

    .dropify-wrapper {
      display: block;
      position: relative;
      cursor: pointer;
      overflow: hidden;
      width: 100%;
      max-width: 100%;
      height: 110px !important;
      padding: 5px 10px;
      font-size: 14px;
      line-height: 22px;
      color: #777;
      background-color: #fff;
      background-image: none;
      text-align: center;
      border: 0 !important;
      -webkit-transition: border-color .15s linear;
      transition: border-color .15s linear;
    }

    .respGalery>div {
      margin-top: 15px;
    }

    .overFlowCustom {
      height: 450px;
      overflow-y: scroll;
    }
  </style>
  <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
  <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>

<body>
  <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">RECORTADOR</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        <div class="modal-body">

          <div class="x_content">
            <div class="form-group">

              <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                <input type="hidden" id="txt_crop" name="txt_crop" value="" />
                <input type="hidden" id="txt_crop_min" name="txt_crop_min" value="" />
                <img id="my-image" src="#" style="width: 100%; max-height: 400px;" />
              </div>

            </div>
          </div>

        </div>
        <div class="modal-footer">
          <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
          <button class="btn btn-dark crop-rotate" data-deg="-90">Rotate Left</button>
          <button class="btn btn-dark crop-rotate" data-deg="90">Rotate Right</button>
          <button type="button" class="btn btn-primary" id="crop-get">Recortar</button>
        </div>
      </div>
    </div>
  </div>
  <!--  BEGIN NAVBAR  -->
  <?php echo top() ?>
  <!--  END NAVBAR  -->

  <!--  BEGIN NAVBAR  -->
  <?php echo navbar(true, "categorias.php"); ?>
  <!--  END NAVBAR  -->

  <!--  BEGIN MAIN CONTAINER  -->
  <div class="main-container" id="container">

    <div class="overlay"></div>
    <div class="search-overlay"></div>

    <!--  BEGIN SIDEBAR  -->
    <?php echo sidebar(); ?>
    <!--  END SIDEBAR  -->

    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
      <div class="">
        <div class="col-md-12" style="margin-top:25px; ">
          <div><span id="btnBack" style="cursor: pointer;">
              <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Descripción del producto</span></span>
          </div>
          <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agrega una descripción a tu producto"; ?></h3>
        </div>
      </div>
      <div class="row layout-top-spacing px-3">
        <div class="col-xl-4 layout-spacing">
          <div class="widget-content widget-content-area br-6 p-5">
            <h5><?php echo $producto['nombre']; ?> </h5>
            <button class="btn btn-outline-primary" id="nuevaDescription" style="width: 100%">Nueva descripción</button>
            <div class="row text-left pt-4 pr-2 overFlowCustom" id="outPut">
              <?php foreach ($productosDescripciones as $pd) : ?>
                <div class="col-12 row border border mx-auto my-2 py-2  rounded"  style="max-height: 80px;" data-id="<?php echo $pd['cod_productos_descripciones']; ?>">
                  <div class="col-8 align-self-center">
                    <p><?php echo $pd['titulo'] ?> </p>
                  </div>
                  <div class="col-2 align-self-center">
                    <i data-feather="edit-2" class="update-description"  style="cursor: pointer "
                      data-descriptionid="<?php echo $pd['cod_productos_descripciones'] ?>"></i>
                  </div>
                  <div class="col-2 align-self-center">
                    <i data-feather="trash-2" class="delete-description" style="cursor: pointer "
                       data-descriptionid="<?php echo $pd['cod_productos_descripciones'] ?>" ></i>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <script type="text/x-handlebars-template" id="listaDescripcion">
            {{#each this}}
              <div class="col-12 row border border mx-auto my-2 py-2  rounded" style="max-height: 80px;" data-id="{{cod_productos_descripciones}}">
                <div class="col-8 align-self-center">
                  <p> {{titulo}}</p>
                </div>
                <div class="col-2 align-self-center">
                  <i data-feather="edit-2"class="update-description" data-descriptionid="{{cod_productos_descripciones}}" style="cursor: pointer "></i>
                </div>
                <div class="col-2 align-self-center">
                  <i data-feather="trash-2" class="delete-description"  data-descriptionid="{{cod_productos_descripciones}}" style="cursor: pointer "></i>
                </div>
              </div>
            {{/each}}
        </div>
        </script>
      </div>
      <div class="col-xl-8">
        <div class="widget-content widget-content-area br-6">
          <div class="form-row">
            <input type="hidden" id="txt_codProducto" value="<?php echo $producto['cod_producto'] ?>">
            <input type="hidden" id="txt_codProductoDescripcion" value="0">
            <div class="form-group col-md-12 col-sm-12 col-xs-12">
              <label>Título <span class="asterisco">*</span></label>
              <input type="text" name="txt_titulo" id="txt_titulo" class="form-control" value="">

            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12 col-sm-12 col-xs-12">
              <label>Descripción <span class="asterisco">*</span></label>
              <textarea name="txt_descripcion_larga" id="editor1" class="form-control" autocomplete="off" style="resize: none;"></textarea>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12 col-sm-12 col-xs-12">
              <button class="btn btn-primary" id="buttonSave">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php footer(); ?>
  </div>
  <!--  END CONTENT AREA  -->
  </div>
  <!-- END MAIN CONTAINER -->

  <?php js_mandatory(); ?>

  <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
  <script src="assets/js/scrollspyNav.js"></script>
  <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
  <script src="plugins/ckeditor-standar/ckeditor.js"></script>
  <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
  <script src="plugins/croppie/croppie.js"></script>
  <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
  <script src="assets/js/pages/crear_productos_descripcion.js" type="text/javascript"></script>

</body>

</html>