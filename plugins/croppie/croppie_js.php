<script type="text/javascript">
	
</script>
<?php
/*
<div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Actual<span class="asterisco">*</span></label>
                              <img src="" />
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Recortada<span class="asterisco">*</span></label>
                              <img src="" />
                          </div>
*/
echo '
  <script src="croppie/croppie.js"></script>

<script type="text/javascript">

var InsertImgBase64 = null;  //VARIABLE IMAGEN EN BASE 64
var sufijo;
var fileImg;
var resize = null;
 var ImagenAnterior = null;
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
    	ImagenAnterior = e.target.result;
      $(\'#my-image\').attr(\'src\', e.target.result);
      resize = new Croppie($(\'#my-image\')[0], {
        viewport: { width: 600, height: 600 }, //tamaño de la foto que se va a obtener
        boundary: { width: 600, height: 600 }, //la imagen total
        showZoomer: true, // hacer zoom a la foto
        enableResize: false,
        enableOrientation: true // para q funcione girar la imagen 
      });
      $(\'#crop-get\').on(\'click\', function() { // boton recortar
        resize.result(\'base64\').then(function(dataImg) {
          InsertImgBase64 = dataImg;
          $(\'#croppie_img_grande_\'+sufijo).attr(\'src\', ImagenAnterior);
          $(\'#croppie_img_recortada_\'+sufijo).attr(\'src\', dataImg);
          $("#modalCroppie").modal(\'hide\');

        });
      });
      $(\'.crop-rotate\').on(\'click\', function(ev) {
        resize.rotate(parseInt($(this).data(\'deg\')));
      });
    }
    reader.readAsDataURL(input.files[0]);
  }
  else{
  	alert("Debe escoger una imagen");
  	$("#modalCroppie").modal(\'hide\');
  }
}

$(".croppie_perfil").change(function() {
	fileImg = this;

	var titulo = $(this).attr("target-titulo");
	$("#myTitleCroppie").html(titulo);

	sufijo = $(this).attr("target-croppie");
	var htmlCroppie = \'<div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;"> \
                              <label>Actual</label> \
                              <img id="croppie_img_grande_\'+sufijo+\'" src="" width="150px"/> \
                            </div> \
                            <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;"> \
                            	<label>Recortada</label>\
                              <img id="croppie_img_recortada_\'+sufijo+\'"  src="" width="150px" /> \
                          </div>\';
	$("#croppie_result_"+sufijo).html(htmlCroppie);

  $("#modalCroppie").modal({
  		closeExisting: false,
  		backdrop: \'static\',
        keyboard: false,
  	});

  if (resize != null)
  resize.destroy();
  
  
});

function abrirModal(){
	$("#modalCroppie").modal();
}

$(\'#modalCroppie\').on(\'shown.bs.modal\', function() {	
    readURL(fileImg);
});
</script>
';

?>