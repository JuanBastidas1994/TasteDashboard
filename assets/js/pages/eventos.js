$(document).ready(function() {
    loadCalendar();
    loadComponents();
}); 

function loadCalendar(){
    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        //lang: 'es',
        defaultView: 'agendaWeek',
        allDaySlot: false,
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        slotDuration: '00:30:00',
        slotLabelInterval: '00:30:00',
        //slotLabelFormat: 'h (:mm)a',
        slotLabelFormat: 'H:mm',
        minTime: '07:00:00',
        maxTime: '23:00:00',
        eventSources: [{
            url: 'json-eventos.php', // use the `url` property
            type: 'GET',
        }],
        timeFormat: 'H(:mm)',
        selectable: true,
        select: function(start, end, jsEvent, view) {
            var eventHour = start.format("H");
	        if(eventHour !== 0){
	       		if( (new Date().getTime() <= new Date(start.format("Y-MM-DD HH:mm")).getTime())){
				    $("#crearModal").modal({closeExisting: false});
				    $("#fecha_evento").val(start.format("Y-MM-DD"));
				    $("#hora_inicio").val(start.format("HH:mm"));
				    $("#hora_fin").val(end.format("HH:mm"));
			    }else
			        notify("No se puede crear eventos en fechas posteriores", "error", 2);
	        }
        },
        eventClick: function(info) {
            verEvento(info.id);
        },
        eventResize: function(event, delta, revertFunc) {
            editarHorasEvento(event, revertFunc);
	    },
	    eventDrop: function(event, delta, revertFunc) {
	        editarHorasEvento(event, revertFunc);
	    },
        eventMouseover: function(event, jsEvent, view) {
            $(this).attr('id', event.id);

            $('#'+event.id).popover({
                template: '<div class="popover popover-primary" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
                title: event.title,
                content: event.description,
                placement: 'top',
            });

            $('#'+event.id).popover('show');
        },
        eventMouseout: function(event, jsEvent, view) {
            $('#'+event.id).popover('hide');
        },
    });
}

function reloadCalendar(){
    var sources = {
        url: 'json-eventos.php', // use the `url` property
        type: 'GET',
    }
    /*
    $('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('addEventSource', sources);
    $('#calendar').fullCalendar('rerenderEvents');*/

    //$('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('removeEventSource', sources);
    $('#calendar').fullCalendar('addEventSource', sources);
    $('#calendar').fullCalendar('refetchEvents');
    $('#calendar').fullCalendar('render');

}

$("#btnGuardar").on("click",function(event){
      event.preventDefault();
      
      var form = $("#frmSave");
      form.validate();
     var isForm = form.valid();

      if(isForm ==false){
        notify("Falta llenar informacion", "success", 2);
        return false;
      }
      
      var formData = new FormData($("#frmSave")[0]);
      //var data = CKEDITOR.instances.editor1.getData();
      //formData.append('desc_larga', data);
      formData.append('txt_crop', $("#txt_crop").val());
      formData.append('txt_crop_min', $("#txt_crop_min").val());

      $.ajax({
          beforeSend: function(){
              OpenLoad("Guardando datos, por favor espere...");
           },
          url: 'controllers/controlador_eventos.php?metodo=crear',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response){
              console.log(response);
              
              if( response['success'] == 1)
              {
                messageDone(response['mensaje'],'success');
                $("#crearModal").modal('hide');
                reloadCalendar();
                
              } 
              else
              {
                messageDone(response['mensaje'],'error');
              } 
                                       
          },
          error: function(data){
            console.log(data);
             
          },
          complete: function(resp)
          {
            CloseLoad();
          }
      });
});

$("#btnEliminarEvento").on("click",function(event){
      event.preventDefault();
      
      Swal.fire({
          title: '¿Deseas eliminar el evento?',
          text: "¡No podrás revertir esto!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
              var parametros = {
                    "id": $(".idEvento").val(),
                }
                console.log(parametros);
                $.ajax({
                      beforeSend: function(){
                          OpenLoad("Eliminando evento, por favor espere...");
                       },
                      url: 'controllers/controlador_eventos.php?metodo=delete',
                      type: 'GET',
                      data: parametros,
                      success: function(response){
                          console.log(response);
                          if( response.success == 1){
                            messageDone(response['mensaje'],'success');
                            reloadCalendar();
                            $("#modalInfoEvento").modal('hide');
                          } 
                          else{
                            messageDone(response['mensaje'],'error');
                          } 
                                                   
                      },
                      error: function(data){
                        console.log(data);
                      },
                      complete: function(resp)
                      {
                        CloseLoad();
                      }
                  });
          }else{
              reversa();
          }
        });
});

function verEvento(id){
    var parametros = {
        "id": id
    }
    $.ajax({
          beforeSend: function(){
              OpenLoad("Guardando datos, por favor espere...");
           },
          url: 'controllers/controlador_eventos.php?metodo=get',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log(response);
              if( response.success == 1){
                var data = response.data;
                $(".idEvento").val(data.cod_agenda);
                $(".imgEvento").attr('src', data.imagen);
                $(".tituloEvento").html(data.titulo);
                $(".descEvento").html(data.descripcion);
                $(".fechaEvento").html(data.fecha);
                $(".inicioEvento").html(data.hora_inicio);
                $(".finEvento").html(data.hora_fin);
                $("#modalInfoEvento").modal();
              } 
              else{
                messageDone(response['mensaje'],'error');
              } 
                                       
          },
          error: function(data){
            console.log(data);
          },
          complete: function(resp)
          {
            CloseLoad();
          }
      });
}

function editarHorasEvento(evento, reversa){
    Swal.fire({
          title: '¿Deseas actualizar el evento?',
          text: "¡No podrás revertir esto!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
              console.log(evento);
              var parametros = {
                    "id": evento.id,
                    "fecha": evento.start.format("Y-MM-DD"),
                    "hora_inicio": evento.start.format("HH:mm"),
                    "hora_fin": evento.end.format("HH:mm")
                }
                console.log(parametros);
                $.ajax({
                      beforeSend: function(){
                          OpenLoad("Actualizando datos, por favor espere...");
                       },
                      url: 'controllers/controlador_eventos.php?metodo=editHoras',
                      type: 'POST',
                      data: parametros,
                      success: function(response){
                          console.log(response);
                          if( response.success == 1){
                            messageDone(response['mensaje'],'success');
                          } 
                          else{
                            messageDone(response['mensaje'],'error');
                          } 
                                                   
                      },
                      error: function(data){
                        console.log(data);
                      },
                      complete: function(resp)
                      {
                        CloseLoad();
                      }
                  });
          }else{
              reversa();
          }
        });
}

/*LOAD COMPONENTS*/
function loadComponents(){
    var selectDropify = "PERFIL";
    //DROPIFY PERFIL
    var resize = null;
    var drEvent = $('.dropify').dropify({
      messages: { 
        'default': 'Click para subir o arrastra', 
        'remove':  'X', 
        'replace': 'Sube o Arrastra y suelta'
      },
      error:{
        'imageFormat': 'Solo se adminte imagenes cuadradas.'
      }
    });
    drEvent.on('dropify.beforeImagePreview', function(event, element){
      if (resize != null)
        resize.destroy();
        
        selectDropify = "PERFIL";

        $("#modalCroppie").modal({
          closeExisting: false,
          backdrop: 'static',
          keyboard: false,
        });
    });
    
    /*time picker*/
    flatpickr(document.getElementById('fecha_evento'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });
    
    flatpickr(document.getElementById('hora_inicio'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        minTime: "07:00",
        maxTime: "22:30"
    });
    
    flatpickr(document.getElementById('hora_fin'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        minTime: "07:30",
        maxTime: "23:00"
    });
}


$('#modalCroppie').on('shown.bs.modal', function() {  
      var aux = $(".dropify").get(0);
      var file = aux.files[0];
      var reader = new FileReader();
      reader.onload = function (e) { 
        $('#my-image').attr('src', e.target.result);
        
        resize = new Croppie($('#my-image')[0], {
          enableExif: true,
          viewport: { width: 512, height: 288 }, //tamaño de la foto que se va a obtener
          boundary: { width: 600, height: 600 }, //la imagen total
          showZoomer: true, // hacer zoom a la foto
          enableResize: false,
          enableOrientation: true // para q funcione girar la imagen 
        });
        $('#crop-get').on('click', function() { // boton recortar
          resize.result({type: 'base64', size: {width: 1024,height: 576}, format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              console.log("BASE 64");
              console.log(InsertImgBase64);
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              /*MINIATURA*/
              resize.result({type: 'base64', size: {width: 500,height: 281}, format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
                console.log("IMAGEN MINIATURAAAAA");
                console.log(dataImg);
                $("#txt_crop_min").val(dataImg);
              });
              $("#modalCroppie").modal('hide');
          });
          
          
          
        });
        $('.crop-rotate').on('click', function(ev) {
          resize.rotate(parseInt($(this).data('deg')));
        });

        
      } 
      reader.readAsDataURL(file);
  });