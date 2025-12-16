$(document).ready(function () {
  $("#btnOpenModal").on("click", function (event) {
    $("#id").val(0);
    $("#frmSave").trigger("reset");
    $("#crearModal").modal();
  });

  $(".btnEditar").on("click", function (event) {
    event.preventDefault();

    var id = parseInt($(this).attr("data-value"));
    if (id == 0) {
      alert("No se pudo traer informacion de la promocion, por favor intentelo mas tarde");
      return;
    }
    var parametros = {
      "cod_promocion": id
    }
    $.ajax({
      beforeSend: function () {
        OpenLoad("Buscando informacion, por favor espere...");
      },
      url: 'controllers/controlador_promociones.php?metodo=get',
      type: 'GET',
      data: parametros,
      success: function (response) {
        console.log(response);
        if (response['success'] == 1) {
          var data = response['data'];
          $("#id").val(data['cod_producto_descuento']);
          $("#hora_ini").val(data['fecha_inicio']);
          $("#hora_fin").val(data['fecha_fin']);
          $("#txt_valor").val(data['valor']);
          $("#cmb_productos").val(data['cod_producto']);
          $('#cmb_productos').trigger('change');
          $("#cmb_sucursales").val(data['cod_sucursal']);
          $('#cmb_sucursales').trigger('change');
          //$("#cmbRol").val(data['cod_rol']);
          //$("#fecha_nacimiento").val(data['fecha_nacimiento']);
          //$("#txt_correo").val(data['correo']);

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

  $("body").on("click", "", function () {

  });

  $('body').on("click", ".btnEliminar", function (event) {
    event.preventDefault();
    var id = parseInt($(this).attr("data-value"));
    if (id == 0) {
      alert("No se pudo traer la promocion, por favor intentelo mas tarde");
      return;
    }

    Swal.fire({
      title: '¿Estas seguro?',
      text: "¡No podrás revertir esto!",
      icon: 'warning',
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
      "cod_promocion": id,
      "estado": "D"
    }
    $.ajax({
      beforeSend: function () {
        OpenLoad("Buscando informacion, por favor espere...");
      },
      url: 'controllers/controlador_promociones.php?metodo=set_estado',
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
      notify("Falta llenar informacion", "success", 2);
      return false;
    }

    if ($("#txt_valor").val().trim().length == 0) {
      notify("Debes llenar el campo Porcentaje", "success", 2);
      return false;
    }

    if ($("#rbCustom").is(':checked')) {
      if ($("#txt_cantidad").val().trim().length == 0) {
        notify("Debes llenar el campo cantidad", "success", 2);
        return false;
      }
    }

    var formData = new FormData($("#frmSave")[0]);
    var id = parseInt($("#id").val());
    if (id > 0) {
      formData.append('cod_usuario', id);
    }

    $.ajax({
      beforeSend: function () {
        OpenLoad("Guardando datos, por favor espere...");
      },
      url: 'controllers/controlador_promociones.php?metodo=crear',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log(response);

        if (response['success'] == 1) {
          messageDone(response['mensaje'], 'success');
          $("#id").val(response['id']);
          window.location.reload();
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

  $(".tipo").on("change", function () {
    var tipo = $(this).val();
    if (tipo == 1) {
      $("#txt_valor").val("");
      $("#txt_cantidad").val("");
      $(".onlyPerc").show();
      $(".onlyNxM").hide();
    }
    else if (tipo == 0) {
      $("#txt_valor").val("100");
      $("#txt_cantidad").val("2");
      $(".onlyPerc").hide();
      $(".onlyNxM").hide();
    }
    else if (tipo == 2) {
      $("#txt_valor").val("100");
      $("#txt_cantidad").val("3");
      $(".onlyPerc").hide();
      $(".onlyNxM").hide();
    }
    else if (tipo == 3) {
      $("#txt_valor").val("100");
      $("#txt_cantidad").val("4");
      $(".onlyPerc").hide();
      $(".onlyNxM").hide();
    }
    else if (tipo == 4) {
      $("#txt_valor").val("100");
      $("#txt_cantidad").val("5");
      $(".onlyPerc").hide();
      $(".onlyNxM").hide();
    }
  });

  $("#cmb_productos").select2();
  $("#cmb_sucursales").select2();

  /*time picker*/
  $('#crearModal').removeAttr('tabindex');
  var picker = document.getElementById("hora_ini");
  flatpickr(picker, {
    enableTime: true,
    dateFormat: "Y-m-d H:i"
  });

  var picker2 = document.getElementById("hora_fin");
  flatpickr(picker2, {
    enableTime: true,
    dateFormat: "Y-m-d H:i"
  });

  $('.dropify').dropify({
    messages: { 'default': 'Click to Upload or Drag n Drop', 'remove': '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
  });


  $('.input-category').on('click', function () {
    let isChecked = $(`.cat-${$(this).data('category')}`).prop('checked')
    // $(`.cat-${$(this).data('category')}`).prop('checked', !isChecked)
    $(`.cat-${$(this).data('category')}`).prop('checked', $(this).prop('checked'))
  })

  $('.input-padre').on('click', function () {
    let isChecked = $(`.padre-${$(this).data('padre')}`).prop('checked')
    // $(`.padre-${$(this).data('padre')}`).prop('checked', !isChecked)
    $(`.padre-${$(this).data('padre')}`).prop('checked', $(this).prop('checked'))
    isChecked = $(`.cat-${$(this).data('categoria')}`).prop('checked')
    $(`.cat-${$(this).data('categoria')}`).prop('checked', !isChecked)
  })

  $('.input-hijo').on('change', function () {
    if($(`.padre-${$(this).data('padre')}`).length == 0)
      $(`.input-padre${$(this).data('padre')}`).prop('checked', false)
    else
      $(`.input-padre${$(this).data('padre')}`).prop('checked', true)
  })

  // $('#txtCategoria').on('keyup', function(){
  //   const txtCategory = this
  //   $('h5').each(function(){
  //     const texto = txtCategory.value
  //     let val = this.innerText.search(texto)
  //     console.log(texto)
  //     if(val == -1){
  //       this.className += ' d-none'
  //     }else{
  //       console.log(this.innerText)
  //     }
  //   })
  // })

});

$(".chkAllOfficess").on('change', function(){
    if($(this).is(':checked')){
        var options = $("#cmb_sucursales").find("option");
        let values = [];
        for(let x = 0; x < options.length; x++){
            values.push(options[x].value);
        }
        $("#cmb_sucursales").val(values);
        $('#cmb_sucursales').trigger('change');
    }else{
        $("#cmb_sucursales").val([]);
        $('#cmb_sucursales').trigger('change');
    }
});


