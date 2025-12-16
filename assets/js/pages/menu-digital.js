$("#cmbMenus").on("change", function(){
    let cod_menu_digital = $(this).val();
    let alias = $("#alias").val();
    // let cod_empresa = $("#cod_empresa").val();

    if("" == cod_menu_digital){
        $("#lstImagenes").html("");
        return;
    }

    let parametros = {
        "cod_menu_digital": cod_menu_digital,
        "alias": alias
    }
    $.ajax({
        url:'controllers/controlador_menu_digital.php?metodo=getImagenes',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#lstImagenes").html(response['html']);
                $("#lstImagenes").sortable({
                    connectWith: ".connectedSortable",
                    update: function (event, ui) {
                        var selectedData = new Array();
                        $('#lstImagenes>tr').each(function() {
                            selectedData.push($(this).attr("data-id"));
                        });
                        ordenarItems(selectedData);
                    }
                });
                feather.replace();
            }
            else{
                messageDone(response['mensaje'], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
});

function ordenarItems(data){
    console.log(data);
    let parametros = {
        "cod_menu_digital": $("#cmbMenus").val(),
        "imagenes": data
    }

    console.log(parametros);

    $.ajax({
        url:'controllers/controlador_menu_digital.php?metodo=actualizarPosicionImagenes',
        type:'POST',
        data:parametros,
        success:function(response){
          console.log(response);
          if(response['success']==1){
            $("#cmbMenus").trigger("change");
            notify("Actualizado correctamente", "success", 2);
          }
            //alert(response['mensaje']);
        },
        error: function(data){
          console.log(data);
        }
    });
  }

  $(".btnSubirImg").on("click", function(e){
      e.preventDefault();
    let formData = new FormData();
    let alias = $("#alias").val();
    let imagen = $("#img_menu")[0].files[0];
    let cod_menu_digital = $("#cmbMenus").val();

    if("" == cod_menu_digital){
        messageDone("Escoja un menú", "error");
        return;
    }

    if(undefined == imagen){
        messageDone("Seleccione una imagen", "error");
        return;
    }

    /* VALIDAR TAMAÑO DE LA IAMGEN */
    let mb = Math.pow(1024, 2);
    let tamanioImg = imagen.size / mb;
    console.log(tamanioImg);
    /*if(tamanioImg > 1){
        messageDone("La imagen no puede pesar más de 1MB", "error");
        return;
    }*/

    formData.append("cod_menu_digital", cod_menu_digital);
    formData.append("alias", alias);
    formData.append("imagen", imagen);
    $.ajax({
        url:'controllers/controlador_menu_digital.php?metodo=subirImagen',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#img_menu").val("");
                $("#cmbMenus").trigger("change");
                notify(response['mensaje'], "success", 2);
            }
            else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
  });

$("body").on("click", ".btnEliminarImagen", function(){
    let cod_menu_digital_imagen = $(this).data("value");  
    let nomImagen = $(this).data("imagen");  
    swal({
        title: 'Eliminar',
        text: '¿Seguro que desea eliminar la imagen?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function(result){
        if (result.value) {
            eliminarImagen(cod_menu_digital_imagen, nomImagen);
        }
    }); 
});

function eliminarImagen(cod_menu_digital_imagen, nomImagen) {
    let alias = $("#alias").val();
    let parametros = {
        "alias": alias,
        "cod_menu_digital_imagen": cod_menu_digital_imagen,
        "nomImagen": nomImagen
    }
    $.ajax({
        url:'controllers/controlador_menu_digital.php?metodo=eliminarImagen',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#cmbMenus").trigger("change");
                notify(response['mensaje'], "success", 2);
            }
            else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}