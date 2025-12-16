$(document).ready(function() {
  
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });
    
    $(".tagging").select2({
        closeOnSelect: false,
        tags: true,
        tokenSeparators: [',']
      });
      
    //COMPONENTES
    CKEDITOR.replace("editor1");

    $(".btnEditar").on("click",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          messageDone("No se pudo traer el codigo promocional, por favor intentelo mas tarde",'error');
          return;
        }
        var parametros = {
          "cod_faq": id
        }
        
        //$('#cmb_tag').select2('destroy');
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_faq.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#id").val(data['cod_faq']);
                    $("#txt_titulo").val(data['titulo']);
                    $("#txt_descripcion").val(data['desc_corta']);
                    $("#editor1").val(data['desc_larga']);
                    $("#cmb_faq").val(data['cod_tipo_empresa']);
                    // $("#txt_url").val(data['video']);
                    // $('#cmb_tag').html("");
                    // $('#cmb_tag').html(response['html']);
                    if(CKEDITOR.instances.editor1 != null){
						CKEDITOR.instances.editor1.destroy(true);
					}
					CKEDITOR.replace('editor1');
                    $("#crearModal").modal();
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
        //   $('#cmb_tag').select2();
        //   $(".tagging").select2({
        //     closeOnSelect: false,
        //     tags: true,
        //     tokenSeparators: [',']
        //   });
     });

     $(".btnEliminar").on("click",function(event){
        event.preventDefault();
        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          messageDone("No se pudo traer la FAQ, por favor intentelo mas tarde",'error');
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
        }).then(function(result) {
          if (result.value) {
            eliminar(id);
          }
        });
     }); 

     function eliminar(id){
          var parametros = {
            "cod_faq": id,
            "estado": "D"
          }
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_faq.php?metodo=set_estado',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
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
     }

    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
    
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_faq', id);
          }
          var data = CKEDITOR.instances.editor1.getData();
          formData.append('desc_larga', data);

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_faq.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    $("#id").val(response['id']);
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
    
    $("#lstBanners").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#lstBanners>tr').each(function() {
                selectedData.push($(this).attr("data-codigo"));
            });
            ordenarItems(selectedData);
        }
    });
    
    function ordenarItems(data){
	  var parametros = {
	      "helpdesk": data,
	    }
	    console.log(parametros);
	   
	  $.ajax({
	      url:'controllers/controlador_faq.php?metodo=actualizar',
	      type:'POST',
	      data:parametros,
	      success:function(response){
	        console.log(response);
	        if(response['success']==1){
	          notify("Actualizado correctamente", "success", 2);
	        }
	      },
	      error: function(data){
	        console.log(data);
	      }
	  });
	}

   $('body').on('click', '.openVideo', function(){
            var src = $(this).data("src");
            $('#videoMedia1').modal('show');
            $('<iframe>').attr({
                'src': src,
                'width': '560',
                'height': '315',
                'allow': 'encrypted-media'
            }).css('border', '0').appendTo('#videoMedia1 .video-container');
        });

        $("#btnBack").on("click",function(){
          var link = $(this).attr("data-module-back");
          if (typeof link === "undefined") {
            link = "index.php";
          }
          swal({
                title: '¿Estas seguro?',
                text: "¡Perderas todos los cambios que no hayas guardado!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Salir',
                cancelButtonText: 'Cancelar',
                padding: '2em'
              }).then(function(result) {
                if (result.value) {
                  window.location.href = link;
                }
               });
        });

});