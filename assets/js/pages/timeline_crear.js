var templateDetalleTimeline;
let idTimeline = "";

$(function(){
    let urlParams = new URLSearchParams(window.location.search);
    idTimeline = urlParams.get("id");

    //TODO QUITAR
    if(idTimeline != null){
        $(".timelineDetalles").removeClass("d-none");
        loadTemplate('templates/timeline-detalle.html').then(resp => {
            templateDetalleTimeline = resp;
            getDetails(idTimeline);
        });

        if( $(".lstDetalle").length > 0){
            $(".lstDetalle").sortable({
                connectWith: ".connectedSortable",
                update: function (event, ui) {
                    var selectedData = new Array();
                    $('.lstDetalle .itemDetalle').each(function() {
                        selectedData.push($(this).attr("data-row"));
                    });
                    ordenarItemsTimeline(selectedData);
                }
            });
        }
    }
});

$("body").on("click", ".btnAdd", function(e){
    e.preventDefault();
    let i = $(".itemDetalle").length;
    console.log("i",i);
    $(this).parents(".itemDetalle").clone().appendTo(".lstDetalle");

    {/* <input type="file" name="image[]" id="image0" class="form-control fileImg dropify" required class="dropify" data-allowed-file-extensions="jpeg jpg png"></input> */}

    $(".itemDetalle").last().find(".divImg").html('<input type="file" name="image[]" id="image0" class="form-control fileImg dropify" required class="dropify" data-allowed-file-extensions="jpeg jpg png"></input>');

    $(".itemDetalle").last().find(".idHidden").attr("value", "0");
    $(".itemDetalle").last().find(".txtTitulo").attr("id", "titulo" + i);
    $(".itemDetalle").last().find(".txtSubtitulo").attr("id", "subtitulo" + i);
    $(".itemDetalle").last().find(".fileImg").attr("id", "image" + i);
    let imgDrop = $(".itemDetalle").last().find(".fileImg");
    initDropify(imgDrop);
});

$("body").on("click", ".btnDel", function(e){
    e.preventDefault();
    if($(".itemDetalle").length > 1){
        let parent = $(this).parents(".itemDetalle");
        let id = parent.find(".idHidden").val();
        if(id == 0)
            parent.remove();
        else{
            Swal.fire({
               title: '¿Está seguro?',
               text: '¡No podrá revertir esto!',
               icon: 'warning',
               showCancelButton: true,
               confirmButtonText: 'Aceptar',
               cancelButtonText: 'Cancelar',
               padding: '2em'
            }).then(function(result){
               if (result.value) {
                  eliminarDetalle(id);
               }
            }); 
        }
    }
});

$("#btnGuardar").on("click", function(){
    let id = $("#id").val();
    let nombre = $("#nombre").val();
    let alias = $("#alias").val();
    let estado = "A";
    if(!$("#chk_estado").is(":checked"))
        estado = "I";
    
    if(nombre.trim() == ""){
        messageDone("Ingrese un nombre", "error");
        return;
    }

    let data = {
        id: id,
        nombre: nombre,
        alias: alias,
        estado: estado
    }

    $.ajax({
        url:'controllers/controlador_timeline.php?metodo=crear',
        data: data,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response["mensaje"], "success");
                if(id == 0)
                    window.location.href = "?id=" + response["id"] + "&alias=" + idTimeline;
            }
            else{
                messageDone(response["mensaje"], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
});

$("#btnGuardarDetalles").on("click", function(e){
    e.preventDefault();
    let form = $("#frmTimelineDet");
    form.validate();
    if(!form.valid()){
        return;
    }

    let formData = new FormData(form[0]);
    formData.append("id", $("#id").val());
    $.ajax({
        url:'controllers/controlador_timeline.php?metodo=crearDetalle',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response["mensaje"], "success");
            }
            else{
                messageDone(response["mensaje"], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
        
});

function validarTitulo(){
    $(".txtTitulo").each(function(){
        if($(this).val() == ""){
            $(this).focus();
            messageDone("Asegúrese de llenar todos los campos requeridos", "error");
            return;
        }
    });
}

function getDetails(id) {
    let parametros = {
        id: id
    }
    $.ajax({
        url:'controllers/controlador_timeline.php?metodo=obtenerDetalles',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                if(response.data.length > 0)
                    $(".lstDetalle").html("");
                $(".lstDetalle").append(templateDetalleTimeline(response.data));
                feather.replace();
                initDropify();
            }
            else{
                console.log(response["mensaje"]);
                $(".itemDetalle").last().find(".divImg").html('<input type="file" name="image[]" id="image0" class="form-control fileImg dropify" required class="dropify" data-allowed-file-extensions="jpeg jpg png"></input>');
                initDropify($("#image0")); 
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}

function ordenarItemsTimeline(data){
    let parametros = {
        data: data
    }
    $.ajax({
        url:'controllers/controlador_timeline.php?metodo=ordenar',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response["mensaje"], "success");
            }
            else{
                messageDone(response["mensaje"], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}

function initDropify(element = ""){
    if(element == "")
        element = $('.dropify');
    var drEvent = element.dropify({
        messages: { 
            'default': 'Click para subir o arrastra', 
            'remove':  'X', 
            'replace': 'Sube o Arrastra y suelta'
        },
        error:{
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
}

function eliminarDetalle(id){
    let parametros = {
        id: id
    }
    $.ajax({
        url:'controllers/controlador_timeline.php?metodo=eliminarDetalle',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response["mensaje"], "success");
                window.location.reload();
            }
            else{
                messageDone(response["mensaje"], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
        });
}