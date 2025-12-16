$(document).ready(function () {
  $("#btnOpenModal").on("click", function (event) {
    $("#id").val(0);
    $("#txt_password").attr("required", "required");
    $("#frmSave").trigger("reset");
    $(".dropify-render img").attr("src", 'assets/img/200x200.jpg');
    $("#crearModal").modal();
  });

  var resize = null;
  var drEvent = $('#dropify-profile').dropify({
    messages: {
      'default': 'Click para subir o arrastra', 'remove': 'X', 'replace': 'Sube o Arrastra y suelta'
    }, error: {
      'imageFormat': 'Solo se adminte imagenes cuadradas.'
    }
  });
  drEvent.on('dropify.beforeImagePreview', function (event, element) {
    if (resize != null)
      resize.destroy();

    $("#modalCroppie").modal({
      closeExisting: false, backdrop: 'static', keyboard: false,
    });
  });
  $('#modalCroppie').on('shown.bs.modal', function () {
    var aux = $(".dropify").get(0);
    var file = aux.files[0];
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#my-image').attr('src', e.target.result);

      resize = new Croppie($('#my-image')[0], {
        enableExif: true,
        viewport: { width: 300, height: 300 }, //tamaño de la foto que se va a obtener
        boundary: { width: 600, height: 600 }, //la imagen total
        showZoomer: false, // hacer zoom a la foto
        enableResize: false,
        enableOrientation: false // para q funcione girar la imagen 
      });
      $('#crop-get').on('click', function () { // boton recortar
        resize.result({ type: 'base64', size: { width: 300, height: 300 }, format: 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF' }).then(function (dataImg) {
          $("#crop_result").val(dataImg);
          var imagen = $(".dropify-render img")[0];
          $(imagen).attr("src", dataImg);
          $("#modalCroppie").modal('hide');
        });
      });
      $('.crop-rotate').on('click', function (ev) {
        resize.rotate(parseInt($(this).data('deg')));
      });


    }
    reader.readAsDataURL(file);

  });

  $("body").on("click", ".btnEditar", function (event) {
    event.preventDefault();

    var id = parseInt($(this).attr("data-value"));
    if (id == 0) {
      alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
      return;
    }

    $("#txt_password").removeAttr("required");

    var parametros = {
      "cod_usuario": id
    }
    $.ajax({
      beforeSend: function () {
        OpenLoad("Buscando informacion, por favor espere...");
      },
      url: 'controllers/controlador_usuario.php?metodo=get',
      type: 'GET',
      data: parametros,
      success: function (response) {
        console.log(response);
        if (response['success'] == 1) {
          var data = response['data'];
          $("#id").val(data['cod_usuario']);
          $("#txt_nombre").val(data['nombre']);
          $("#txt_apellido").val(data['apellido']);
          $("#txt_telefono").val(data['telefono']);
          $("#cmbRol").val(data['cod_rol']);
          $("#fecha_nacimiento").val(data['fecha_nacimiento']);
          $("#txt_correo").val(data['correo']);
          $("#cmbSucursal").val(data['cod_sucursal']);

          $(".dropify-render img").attr("src", data['imagen']);
          $("#crearModal").modal();
        }
        else {
          messageDone(response['mensaje'], 'error');
        }

      },
      error: function (data) {
        console.log(data);

      },
      complete: function (resp) {
        CloseLoad();
      }
    });
  });

  $("body").on("click", ".btnEliminar", function (event) {
    event.preventDefault();
    var id = parseInt($(this).attr("data-value"));
    if (id == 0) {
      alert("No se pudo traer el usuario, por favor intentelo mas tarde");
      return;
    }

    swal({
      title: '¿Estas seguro?',
      text: "¡No podrás revertir esto!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar',
      padding: '2em'
    }).then(function (result) {
      if (result.value) {
        eliminar(id);
      }
    });
  });

  function eliminar(id) {
    var parametros = {
      "cod_usuario": id,
      "estado": "D"
    }
    $.ajax({
      beforeSend: function () {
        OpenLoad("Buscando informacion, por favor espere...");
      },
      url: 'controllers/controlador_usuario.php?metodo=set_estado',
      type: 'GET',
      data: parametros,
      success: function (response) {
        console.log(response);
        if (response['success'] == 1) {
          messageDone(response['mensaje'], 'success');
        }
        else {
          messageDone(response['mensaje'], 'error');
        }

      },
      error: function (data) {
        console.log(data);

      },
      complete: function (resp) {
        CloseLoad();
      }
    });
  }

  $("#btnGuardar").on("click", function (event) {
    event.preventDefault();

    var form = $("#frmSave");
    form.validate();
    if (form.valid() == false) {
      notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
      return false;
    }

    var formData = new FormData($("#frmSave")[0]);
    var id = parseInt($("#id").val());
    if (id > 0) {
      formData.append('cod_usuario', id);
    }

    if ($("#cmbRol").val() == 3 || $("#cmbRol").val() == 5) {
      if ($("#cmbSucursal").val() == 0) {
        messageDone("Debes escoger una sucursal para un usuario administrador o Personal", 'error');
        return;
      }
    }

    $.ajax({
      beforeSend: function () {
        OpenLoad("Guardando datos, por favor espere...");
      },
      url: 'controllers/controlador_usuario.php?metodo=crear',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log(response);

        if (response['success'] == 1) {
          messageDone(response['mensaje'], 'success');
          $("#id").val(response['id']);
        }
        else {
          messageDone(response['mensaje'], 'error');
        }
      },
      error: function (data) {
        console.log(data);

      },
      complete: function (resp) {
        CloseLoad();
      }
    });
  });

  $("#cmbRol").on("change", function () {
    var rol = $(this).val();
    if (rol != 3 && rol != 5)
      $(".onlyAdmin").hide();
    else
      $(".onlyAdmin").show();
  })

  /*time picker*/
  var f4 = flatpickr(document.getElementById('fecha_nacimiento'), {
    enableTime: false,
    dateFormat: "Y-m-d"
  });





});