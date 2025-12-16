$(document).ready(function() {
    //App.init();
    loadItems();
    
    $('.dropify').dropify({
      messages: { 'default': 'Click para subir archivo', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Suba o arrastre archivo' }
    });
    
    $(".tagging").select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });
    
});

$("body").on("change","#txt_titulo_tarea, #txt_desc_tarea",function() {
    console.log("CHANGE PARA UPDATE TASK");
    setTaskInfo();
});

function loadItems(){
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=listaTareas',
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                var items = response['items'];
                for(var x=0; x<items.length;x++){
                    var item = items[x];
                    console.log(item);
                    $(".columnOrder"+item['columna']).append(item['html']);
                }
            	feather.replace();
            }else{
                notify("No hay resultados", "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
}

$("body").on("click", ".taskDetail", function(){
    var idTask = $(this).data("id");
    openTask(idTask);
});

function openTask(idTask){
    $("#tituloTareaModalLabel").html("");
    $("#contentTareaModal").html("Cargando información...");
    $("#contentComentarios").html('Cargando comentarios...');
    $("#ModalDetalleTarea").modal();
    getTask(idTask);
}

function getTask(idTask){
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=get&id='+idTask,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#contentTareaModal").html(response['html']);
                $("#tituloTareaModalLabel").html(response['header']);
                $("#idTask").val(idTask);
                feather.replace();
                
                //Light Box
                if($('.fancybox').length>0){
                    $('.fancybox').fancybox();  
                }
                
                //CARGAR COMENTARIOS
            	getCommentTask(idTask);
            }else{
                notify("No hay resultados", "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
}

function getCommentTask(idTask){
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=getCommentsByTask&id='+idTask,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#contentComentarios").html(response['html']);
                //$("#tituloTareaModalLabel").html(response['header']);
                //$("#idTask").val(idTask);
                feather.replace();
                
                //Light Box
                if($('.fancybox').length>0){
                    $('.fancybox').fancybox();  
                }
            }else{
                $("#contentComentarios").html(response['html']);
                notify("No hay comentarios", "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
}

$(".btnAddCommentTask").on("click", function(e){
    e.preventDefault();
    
    var idTask = $("#idTask").val();
    var comentario = $("#txtCommentTask").val();
    if(comentario.length == 0){
        alert("Debe estar lleno el comentario");
    }
    
    var parametros = {
        id: idTask,
        comment: comentario
    }
    console.log(parametros);
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=addComment',
        type: 'POST',
        data: parametros,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
                $("#txtCommentTask").val("");
                getCommentTask(idTask);
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
});

$("body").on("click", ".btnEditComment", function(e){
    e.preventDefault();
    var id = $(this).data("id");
    $("#showComment"+id).hide();
    $("#zoneEditComment"+id).show();
});    

$("body").on("click", ".btnUpdateComment", function(e){
    e.preventDefault();
    var id = $(this).data("id");
    var comentario = $("#editComment"+id).val();
    if(comentario.length == 0){
        alert("Debe estar lleno el comentario");
    }
    
    var parametros = {
        id: id,
        comment: comentario
    }
    console.log(parametros);
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=updateComment',
        type: 'POST',
        data: parametros,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
                var idTask = $("#idTask").val();
                getCommentTask(idTask);
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
});

$("body").on("click", ".btnDeleteComment", function(e){
    e.preventDefault();
    var id = $(this).data("id");
    
    Swal.fire({
        title: '¿Deseas eliminar el comentario?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function(result) {
        if (result.value) {
            
            var parametros = {
                id: id
            }
            console.log(parametros);
            $.ajax({
                url:'controllers/controlador_clickup.php?metodo=removeComment',
                type: 'POST',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if(response['success']==1){
                        notify(response['mensaje'], "success", 2);
                        var idTask = $("#idTask").val();
                        getCommentTask(idTask);
                    }else{
                        notify(response['mensaje'], "error", 2);
                    }
                },
                error: function(data){
                  console.log(data);  
                },
                complete: function()
                {
                  
                }
            });
        
        }
    });
});

$("#btnCrearTarea").on("click", function(e){
    e.preventDefault();
    var formData = new FormData($("#frmCrearTarea")[0]);
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=crear',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
                $("#ModalCrearTarea").modal('hide');
                openTask(response['id']);
                loadItems();
                //$("#txtCommentTask").data("emojioneArea").setText('');
                $(".emojionearea-editor").html('');
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
});


$("#btnSubirAdjunto").on("click", function(e){
    e.preventDefault();
    
    var idTask = $("#idTask").val();
    var formData = new FormData($("#frmSubirAdjunto")[0]);
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=uploadAdjunto',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                getTask(idTask);
                notify(response['mensaje'], "success", 2);
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
});

function setTaskInfo(){
    var idTask = $("#idTask").val();
    
    var parametros = {
        id: idTask,
        titulo: $("#txt_titulo_tarea").val(),
        descripcion: $("#txt_desc_tarea").val()
    }
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=update_task_info',
        type: "POST",
        data: parametros,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
}

function setTaskStatus(id, index, name){
    var parametros = {
        id: id,
        index: index,
        name: name
    }
    $.ajax({
        url:'controllers/controlador_clickup.php?metodo=set_estado_tarea',
        type: "POST",
        data: parametros,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
            }else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log(data);  
        },
        complete: function()
        {
          
        }
    });
}


/*FUNCION DE MOVIMIENTO HORIZONTAL CON CLICK*/
const slider = document.querySelector('.task-list-section');
let mouseDown = false;
let startX, scrollLeft;

let startDragging = function (e) {
  mouseDown = true;
  startX = e.pageX - slider.offsetLeft;
  scrollLeft = slider.scrollLeft;
};
let stopDragging = function (event) {
  mouseDown = false;
};

slider.addEventListener('mousemove', (e) => {
  e.preventDefault();
  
  if($("#dragTask").val() == "START"){return;}
  
  if(!mouseDown) { return; }
  const x = e.pageX - slider.offsetLeft;
  const scroll = x - startX;
  slider.scrollLeft = scrollLeft - scroll;
});

// Add the event listeners
slider.addEventListener('mousedown', startDragging, false);
slider.addEventListener('mouseup', stopDragging, false);
slider.addEventListener('mouseleave', stopDragging, false);