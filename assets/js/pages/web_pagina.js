var templateCategorias, templateSeccionSlider, templateSeccionLista, templateBloqueRight, templateVideo, templateHtml, templateBlog;
loadTemplate('templates/home-categorias.html').then(resp => {templateCategorias = resp;});
loadTemplate('templates/home-seccion-slider.html').then(resp => {templateSeccionSlider = resp;});
loadTemplate('templates/home-seccion-lista.html').then(resp => {templateSeccionLista = resp;});
loadTemplate('templates/home-bloque-right.html').then(resp => {templateBloqueRight = resp;});
loadTemplate('templates/home-video.html').then(resp => {templateVideo = resp;});
loadTemplate('templates/home-html.html').then(resp => {templateHtml = resp;});
loadTemplate('templates/home-blog.html').then(resp => {templateBlog = resp;});

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
});

function ordenarItems(data){
    console.log(data);

    var parametros = {
        "codigos": data,
      }
     
    $.ajax({
        url:'controllers/controlador_web_pagina.php?metodo=actualizarPosicion',
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
    $(".div-formas").hide();
    $(".div-html").hide();
    if(valor=="ordenar" || valor=="anuncios" || valor=="blog" || valor=="categorias"){
        $(".div-formas").show();

        $("#cmbModulos").hide();
        $("#cmbAnuncios").hide();
        $("#cmbBlog").hide();
        if(valor=="ordenar"){
            $("#cmbModulos").show();
        }
        if(valor=="anuncios"){
            $("#cmbAnuncios").show();
        }
        if(valor=="blog"){
            $("#cmbBlog").show();
        }
    }
    if(valor=="youtube" || valor=="video" || valor=="script" || valor=="html"){
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

/*GUARDAR PAGINA */
$("#btnGuardarPage").on("click", function(){
    let formData = new FormData($("#frmSavePage")[0]);
    $.ajax({
        beforeSend: function(){
            OpenLoad("Buscando informacion, por favor espere...");
        },
        url:'controllers/controlador_web_pagina.php?metodo=crearPage',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
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
            url:'controllers/controlador_web_pagina.php?metodo=changeHome',
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

    let EndPoint = 'controllers/controlador_web_pagina.php?metodo=guardar';
    let idEsquema = $("#idEsquema").val();
    if(idEsquema !== ""){
        EndPoint = 'controllers/controlador_web_pagina.php?metodo=editar';
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

$("body").on("click", "#btnActualizarEsquema", function(){
    let id = $("#idEsquema").val();
    let formData = new FormData($("#frmEdit")[0]);
    $.ajax({
        beforeSend: function(){
            OpenLoad("Actualizando informacion, por favor espere...");
        },
        url:'controllers/controlador_web_pagina.php?metodo=editar',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
       success: function(response){
          console.log(response);
          if(response['success']==1){
            messageDone(response['mensaje'], "success");
            $("#editarEsquemaModal").modal("hide");
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
});


/* EDITAR SECCION */
$("body").on("click", ".btnEditarSeccion", function(){
    let id = $(this).data("value");
    let parametros = {
        "id": id
        }
    $.ajax({
        beforeSend: function(){
            OpenLoad("Buscando informacion, por favor espere...");
        },
       url:'controllers/controlador_web_pagina.php?metodo=get',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
              let tipo = response['datos']['cod_tipo'];
            $("#idEsquema").val(response['datos']['cod_front_pagina_detalle']);
            $("#txt_titulo").val(response['datos']['titulo']);
            $("#cmbTipo").val(response['datos']['cod_tipo']);
            $("#cmbTipo").trigger("change");
            $("#cmbTipo").attr("readonly","readonly");
            $("#cmbForma").val(response['datos']['forma']);
            $("#cmbNumColumnas").val(response['datos']['num_columnas']);
            $("#cmbMD").val(response['datos']['md']);
            $("#cmbSM").val(response['datos']['sm']);
            $("#txt_classname").val(response['datos']['classname']);
            //$("#txt_detalle").val(response['datos']['detalle']);
            //$("#txt_detalle2").val(response['datos']['detalle2']);
            //$("#cmbFondoImagen").val(response['datos']['fondo_imagen']);

            

            $("#txt_detalle").val(response['datos']['html']);

            if(tipo == "html"){
                if (CKEDITOR.instances.htmlEditor) {
                    CKEDITOR.instances.htmlEditor.destroy();
                }
                $("#htmlEditor").val(response['datos']['html']);
                CKEDITOR.replace("htmlEditor");
            }

            $("#crearSeccionModal").modal();
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
            url:'controllers/controlador_web_pagina.php?metodo=eliminar',
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

/*LISTA DE SECCIONES POR PAGINA*/
function ListSections(){
    let api_key = $("#api_key").val();
    let aliasPagina = $("#aliasPagina").val();
    fetch(`https://api.mie-commerce.com/v10/app/pagina/${aliasPagina}`,{
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
                if(item.tipo == "ordenar" || item.tipo == "anuncios"){
                    if(item.forma=="slide_4")
                        $('.appSection').append(templateSeccionSlider(item));
                    if(item.forma=="lista_4")
                        $('.appSection').append(templateSeccionLista(item));
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