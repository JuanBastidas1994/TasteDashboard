var templateSeccionBanners, templateCategorias, templateSeccionSlider, templateBloqueRight, templateVideo, templateHtml, templateBlog;
loadTemplate('templates/home-seccion-banners.html').then(resp => {templateSeccionBanners = resp;});
loadTemplate('templates/home-categorias.html').then(resp => {templateCategorias = resp;});
loadTemplate('templates/home-seccion-slider.html').then(resp => {templateSeccionSlider = resp;});
loadTemplate('templates/home-bloque-right.html').then(resp => {templateBloqueRight = resp;});
loadTemplate('templates/home-video.html').then(resp => {templateVideo = resp;});
loadTemplate('templates/home-html.html').then(resp => {templateHtml = resp;});
loadTemplate('templates/home-blog.html').then(resp => {templateBlog = resp;});

let resize = null;
$(document).ready(function(){
    if($("#pagina-secciones").length){
            //COMPONENTES
            CKEDITOR.replace("htmlEditor");
            ListSections();

            $("#lstEsquema").sortable({
                connectWith: ".connectedSortable",
                update: function (event, ui) {
                    var selectedData = new Array();
                    $('#lstEsquema>tr').each(function() {
                        selectedData.push($(this).attr("data-id"));
                    });
                    ordenarItems(selectedData);
                }
            });
    }
    
    $("#lstDisponibles").sortable({
          connectWith: ".connectedSortable"
    });
    initCroppie();
});



function ordenarItems(data){
    console.log(data);

    var parametros = {
        "codigos": data,
      }
     
    $.ajax({
        url:'controllers/controlador_paginas.php?metodo=actualizarPosicion',
        type:'POST',
        data:parametros,
        success:function(response){
          console.log(response);
          if(response['success']==1){
            notify("Actualizado correctamente", "success", 2);
          }
            //alert(response['mensaje']);
        },
        error: function(data){
          console.log(data);
        }
    });
}

$(".btn-refresh").on("click", function(){
    ListSections();
});

$(".btn-reordenar").on("click", function(){
    $("#reordenarSeccionesModal").modal();
});


$("#btnNuevo").on("click", function(){
    $("#modalWebPage").modal();
});

$(".btn-nuevo").on("click", function(){
    $("#frmSave").trigger("reset");
    $("#idEsquema").val("");
    $("#cmbTipo").removeAttr("readonly");
    $(".cmbTipoSeccion").trigger("change");
    CKEDITOR.instances.htmlEditor.setData("");
    $("#crearSeccionModal").modal();
});

$(".cmbTipoSeccion").on("change", function(){
    let valor = $(this).val();
    $(".div-html").hide();
    if(valor=="video" || valor=="script" || valor=="html"){
        $(".div-html").show();

        $(".textareaInput").hide();
        $(".htmlInput").hide();
        if(valor=="html"){
            $(".htmlInput").show();
        }else{
            $(".textareaInput").show();
        }
    }
});

/* CAMBIAR HOME PAGINA */
$("#btnHome").on("click", function(){
    let idPage = $("#cod_pagina").val();
    console.log(idPage);
    let parametros = {
        id: idPage
    }

    Swal.fire({
        title: '¿Deseas poner esta página como Home?',
        text: "¡Al aceptar esta información se pondrá en el página de inicio de la Front Page!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
        }).then(function(result) {
        if (result.value) {
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Aplicando cambios, por favor espere...");
                },
            url:'controllers/controlador_paginas.php?metodo=changeHome',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    messageDone(response['mensaje'], "success");
                }
                else{
                    messageDone(response['mensaje'], "error");
                }
            },
            error: function(data){
            },
            complete: function(){
                    CloseLoad();
            },
            });
        }
    });
});

/*GUARDAR NUEVA SECCION EN PAGINA*/
$("#btnGuardar").on("click", function(){
    let html = "";
    let formData = new FormData($("#frmSave")[0]);
    formData.append("cod_empresa", $("#cod_empresa").val());
    formData.append("cod_pagina", $("#cod_pagina").val());

    var desc = CKEDITOR.instances.htmlEditor.getData();
    formData.append('desc_larga', desc);

    let EndPoint = 'controllers/controlador_paginas.php?metodo=guardar';
    let idEsquema = $("#idEsquema").val();
    if(idEsquema !== ""){
        EndPoint = 'controllers/controlador_paginas.php?metodo=editar';
    }

    $.ajax({
       url:EndPoint,
       data: formData,
       type: "POST",
       contentType: false,
       processData: false,
       success: function(response){
          console.log(response);
          if(response['success']==1){
            ListSections(); 
            feather.replace(); 
            $("#frmSave").trigger("reset");       
            messageDone(response['mensaje'], "success");
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


/* EDITAR SECCION */
$("body").on("click", ".btnEditarSeccion", async function(){
    let id = $(this).data("value");
    try {
        const response = await obtenerInformacion(id);
        let tipo = response['datos']['cod_tipo'];
        $("#idEsquema").val(response['datos']['cod_front_pagina_detalle']);
        $("#txt_titulo").val(response['datos']['titulo']);
        $("#cmbTipo").val(response['datos']['cod_tipo']);
        $("#cmbTipo").trigger("change");
        $("#cmbTipo").attr("readonly","readonly");
        $("#cmbForma").val(response['datos']['forma']);

        $("#txt_detalle").val(response['datos']['html']);

        if(tipo == "html"){
            if (CKEDITOR.instances.htmlEditor) {
                CKEDITOR.instances.htmlEditor.destroy();
            }
            $("#htmlEditor").val(response['datos']['html']);
            CKEDITOR.replace("htmlEditor");
        }

        $("#crearSeccionModal").modal();
    } catch (error) {
        console.error('Error:', error);
        messageDone(error, "error");
    }
});

/* ELIMINAR SECCION */
$("body").on("click", ".btnEliminarSeccion", function(){
    let id = $(this).data("value");
    let parametros = {
        "id": id
        }

    Swal.fire({
        title: '¿Estas seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
        }).then(function(result) {
        if (result.value) {
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Eliminando sección, por favor espere...");
                },
            url:'controllers/controlador_paginas.php?metodo=eliminar',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    ListSections();
                    messageDone(response['mensaje'], "success");
                }
                else{
                    messageDone(response['mensaje'], "error");
                }
            },
            error: function(data){
            },
            complete: function(){
                    CloseLoad();
            },
            });
        }
    });
});

$("body").on("click", ".btnAgregarSeccion", async function(){
    const id = $(this).data("value");
    
    try {
        const response = await obtenerInformacion(id);
        const data = response.datos;
        if(data.cod_tipo == "ordenar"){
            var template = Handlebars.compile($("#product-table-ordenar").html());
            $("#lstAgotados").html(template(data.items));
            $("#lstAgotados").attr('front-detalle', id);
            $("#ordenarProductsModal").modal();
            
            $("#lstAgotados").sortable({
                connectWith: ".connectedSortable",
                update: function (event, ui) {
                    var selectedData = new Array();
                    $('#lstAgotados>tr').each(function() {
                        selectedData.push($(this).attr("data-id"));
                    });
                    agregarContenidoDetalleOrdenar($("#lstAgotados").attr('front-detalle'), selectedData);
                }
            });
        }
        else if(data.cod_tipo == "anuncios"){
            $("#frontDetallePromoId").val(id);
            $("#addCardPromoModal").modal();
        }
        console.log('Datos recibidos:', response);
    } catch (error) {
        console.error('Error:', error);
    }
});

function obtenerInformacion(id) {
    return new Promise((resolve, reject) => {
        OpenLoad("Buscando informacion, por favor espere...");
        
        fetch('controllers/controlador_paginas.php?metodo=get&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success === 1) {
                    resolve(data);
                } else {
                    reject('Error en la respuesta');
                }
            })
            .catch(error => {
                reject(error);
            })
            .finally(() => {
                CloseLoad();
            });
    });
}

/*FRON PAGE DETALLE*/
function agregarContenidoDetalleOrdenar(detalle_id, data){
    var parametros = {
      "detalle_id": detalle_id,
      "items": data
    }
     $.ajax({
          url:'controllers/controlador_paginas.php?metodo=agregarContenidoDetalle',
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

function agregarContenidoDetallePromo(){
    if(!resize){
        messageDone('Debe cargar una imagen', "error");
        return;
    }
    resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 1, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
            $("#txt_crop").val(dataImg);
            let imagen = $(".dropify-render img")[0];
            $(imagen).attr("src",dataImg);
            
            const action_id = $("#cmbAccion").val();
            let action_desc = $("#txt_accion_desc").val();
            if(action_id == 'PRODUCTO'){
                action_desc = $("#cmbProductos").val();
            }else if(action_id == 'INFO'){
                action_desc = "";
            }
          
            let parametros = {
                "accion_id": action_id,
                "accion_desc": action_desc,
                "detalle_id": $("#frontDetallePromoId").val(),
                "image": dataImg
            }
            
             $.ajax({
                  url:'controllers/controlador_paginas.php?metodo=agregarContenidoDetallePromo',
                  type:'POST',
                  data:parametros,
                  success:function(response){
                    console.log(response);
                    if(response['success']==1){
                      notify("Actualizado correctamente", "success", 2);
                      $("#addCardPromoModal").modal('hide');
                      ListSections();
                    }
                  },
                  error: function(data){
                    console.log(data);
                  }
             });
    });
}

function eliminarContenidoPagina(contenido_id){
    messageConfirm('Estas seguro de eliminar el contenido?', 'Esta función es irreversible', 'question')
        .then(function(result) {
            if (result) {
                $.ajax({
                  url:`controllers/controlador_paginas.php?metodo=eliminarContenidoDetalle&detalle_id=${contenido_id}`,
                  type:'GET',
                  success:function(response){
                    console.log(response);
                    if(response['success']==1){
                      notify("Eliminado correctamente", "success", 2);
                      ListSections();
                    }
                  },
                  error: function(data){
                    console.log(data);
                  }
                });
            }
        });
}


/*LISTA DE SECCIONES POR PAGINA*/
function ListSections(){
    let api_key = $("#api_key").val();
    let aliasPagina = $("#aliasPagina").val();
    // fetch(`https://api.mie-commerce.com/v10/app/pagina/${aliasPagina}`,{
    fetch(`https://api.mie-commerce.com/taste-front/v7/app/pagina/${aliasPagina}`,{
        method: 'GET',
        headers: {
          'Api-Key':api_key
        },
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        
        if(response.success == 1){
            $('.appSection').html("");
            $("#lstEsquema").html("");
            var data = response.data;
            for(var x=0; x<data.length; x++){
                let item = data[x];
                addItemTable(item);
                if(item.tipo == "banner"){
                    $('.appSection').append(templateSeccionBanners(item));
                }
                if(item.tipo == "ordenar" || item.tipo == "anuncios"){
                    $('.appSection').append(templateSeccionSlider(item));
                }
                if(item.tipo == "categorias"){
                    $('.appSection').append(templateCategorias(item));
                }
                if(item.tipo == "blog"){
                    $('.appSection').append(templateBlog(item));
                }
                if(item.tipo == "html" || item.tipo == "script"){
                    $('.appSection').append(templateHtml(item));
                }

                if(item.forma == "BLOQUE_RIGHT"){
                    $('.appSection').append(templateBloqueRight(data[x]));
                }
            }
            initCarousel('.wrap-slider');
            feather.replace();
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function addItemTable(item){
    let html =` <tr data-id="`+item.id+`" id="`+item.id+`">
                                <td>`+item.titulo+`</td>
                            </tr>`; 
            $("#lstEsquema").append(html); 
}

function initCarousel(className){
    if($(className).length>0){
		$(className).each(function(){
			var data = $(this).data();
			$(this).owlCarousel({
				addClassActive:true,
				stopOnHover:true,
				lazyLoad:true,
				itemsCustom:data.itemscustom,
				autoPlay:data.autoplay,
				transitionStyle:data.transition, 
				paginationNumbers:data.paginumber,
				beforeInit:background,
				afterAction:animated,
				navigationText:['<i class="icon ion-ios-arrow-thin-left"></i>','<i class="icon ion-ios-arrow-thin-right"></i>'],
			});
		});
	}
}

//Slider Background
function background(){
	$('.bg-slider .item-slider').each(function(){
		var src=$(this).find('.banner-thumb a img').attr('src');
		$(this).css('background-image','url("'+src+'")');
	});	
}
function animated(){
	$('.banner-slider .owl-item').each(function(){
		var check = $(this).hasClass('active');
		if(check==true){
			$(this).find('.animated').each(function(){
				var anime = $(this).attr('data-animated');
				$(this).addClass(anime);
			});
		}else{
			$(this).find('.animated').each(function(){
				var anime = $(this).attr('data-animated');
				$(this).removeClass(anime);
			});
		}
	});
	var owl = this;
	var visible = this.owl.visibleItems;
	var first_item = visible[0];
	var last_item = visible[visible.length-1];
	this.$elem.find('.owl-item').removeClass('first-item');
	this.$elem.find('.owl-item').removeClass('last-item');
	this.$elem.find('.owl-item').eq(first_item).addClass('first-item');
	this.$elem.find('.owl-item').eq(last_item).addClass('last-item');
}


/*CROPPIE*/
function initCroppie(){
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
        
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            loadCropImage(e);
        }
        reader.readAsDataURL(file);
    });
}

function loadCropImage(e){
    const width = 600;
    const height = 300;
    $('#my-image').attr('src', e.target.result);
    resize = new Croppie($('#my-image')[0], {
        viewport: { width, height},
        boundary: { width: width + 50, height: height + 50 },
        showZoomer: true, // hacer zoom a la foto
        enableResize: false,
        enableOrientation: true, // para q funcione girar la imagen 
        mouseWheelZoom: 'ctrl'
    });
}

$('.crop-rotate').on('click', function(ev) {
    resize.rotate(parseInt($(this).data('deg')));
});

//ACCION 
$("#cmbAccion").on("change", function(){
    showDetalleAccion($(this).val());
});

function showDetalleAccion(accion){
    $(".inputAccion").hide();
    if(accion == "URL" || accion == "FILTER"){
        $("#txt_accion_desc").show();
    }else if(accion == "PRODUCTO"){
        $("#cmbProductos").show();
    }else if(accion == "NOTICIA"){
        $("#cmbNoticias").show();
    }
}