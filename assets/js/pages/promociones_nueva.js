$(document).ready(function () {

  let promoTable = null;

  $('body').on("click", ".btnProgreso", function (event) {
    event.preventDefault();
    var id = parseInt($(this).attr("data-value"));
    var desc = $(this).attr("data-desc");

    if (id == 0) {
      alert("No se pudo traer la promoción, por favor inténtelo más tarde");
      return;
    }

    $("#promofilter").val(id);
    $("#promofilterText").html(desc);
    $("#promoProgreso").modal();

    if (!promoTable) {
      promoTable = $('#table-ordenes-promo').DataTable({
        processing: true,
        serverSide: true,
        dom: 'Bfrtip',
        buttons: {
          buttons: [
            { extend: 'excel', className: 'btn' },
            { extend: 'print', className: 'btn' }
          ]
        },
        ajax: {
          url: './controllers/controlador_promociones.php?metodo=datatable_promocion',
          type: 'GET',
          data: function (d) {
            d.cod_promocion = $("#promofilter").val();
          },
          dataSrc: function (json) {
            $('#totalPromoOrders').text(json.recordsTotal);
            return json.data;
          },
          error: function (e) {
            console.log(e);
          },
          complete: function () {
            feather.replace();
          }
        }
      });
    } else {
      promoTable.ajax.reload();
    }
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
