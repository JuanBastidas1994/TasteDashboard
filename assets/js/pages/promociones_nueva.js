$(document).ready(function () {


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
    }
    $.ajax({
      beforeSend: function () {
        OpenLoad("Buscando informacion, por favor espere...");
      },
      url: 'controllers/controlador_promociones.php?metodo=delete',
      type: 'GET',
      data: parametros,
      success: function (response) {
        console.log(response);
        if (response['success'] == 1) {
          messageDone(response['mensaje'], 'success');
          // $("#trItem"+id).remove();
          var table = $('#style-3').DataTable();
          table.row('#trItem' + id).remove().draw(false);
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

});
