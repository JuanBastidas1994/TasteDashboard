/*
=========================================
|                                       |
|           Scroll To Top               |
|                                       |
=========================================
*/ 
$('.scrollTop').click(function() {
    $("html, body").animate({scrollTop: 0});
});


$('.navbar .dropdown.notification-dropdown > .dropdown-menu, .navbar .dropdown.message-dropdown > .dropdown-menu ').click(function(e) {
    e.stopPropagation();
});

/*
=========================================
|                                       |
|       Multi-Check checkbox            |
|                                       |
=========================================
*/

function checkall(clickchk, relChkbox) {

    var checker = $('#' + clickchk);
    var multichk = $('.' + relChkbox);


    checker.click(function () {
        multichk.prop('checked', $(this).prop('checked'));
    });    
}


/*
=========================================
|                                       |
|           MultiCheck                  |
|                                       |
=========================================
*/

/*
    This MultiCheck Function is recommanded for datatable
*/

function multiCheck(tb_var) {
    tb_var.on("change", ".chk-parent", function() {
        var e=$(this).closest("table").find("td:first-child .child-chk"), a=$(this).is(":checked");
        $(e).each(function() {
            a?($(this).prop("checked", !0), $(this).closest("tr").addClass("active")): ($(this).prop("checked", !1), $(this).closest("tr").removeClass("active"))
        })
    }),
    tb_var.on("change", "tbody tr .new-control", function() {
        $(this).parents("tr").toggleClass("active")
    })
}

/*
=========================================
|                                       |
|           MultiCheck                  |
|                                       |
=========================================
*/

function checkall(clickchk, relChkbox) {

    var checker = $('#' + clickchk);
    var multichk = $('.' + relChkbox);


    checker.click(function () {
        multichk.prop('checked', $(this).prop('checked'));
    });    
}

/*
=========================================
|                                       |
|               Tooltips                |
|                                       |
=========================================
*/

$('.bs-tooltip').tooltip();

/*
=========================================
|                                       |
|               Popovers                |
|                                       |
=========================================
*/

$('.bs-popover').popover();


/*
================================================
|                                              |
|               Rounded Tooltip                |
|                                              |
================================================
*/

$('.t-dot').tooltip({
    template: '<div class="tooltip status rounded-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
})


/*
================================================
|            IE VERSION Dector                 |
================================================
*/

function GetIEVersion() {
  var sAgent = window.navigator.userAgent;
  var Idx = sAgent.indexOf("MSIE");

  // If IE, return version number.
  if (Idx > 0) 
    return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));

  // If IE 11 then look for Updated user agent string.
  else if (!!navigator.userAgent.match(/Trident\/7\./)) 
    return 11;

  else
    return 0; //It is not IE
}

function tableEstado(estado){
  var textEstado = 'Activo';
  var badge = 'primary';
  if(estado == 'I'){
    badge = 'danger';
    textEstado = 'Inactivo';
  }

  return '<span class="shadow-none badge badge-'+badge+'">'+textEstado+'</span>';
}

function tableAcciones(codigo){
  return '<ul class="table-controls"><li><a href="javascript:void(0);" data-value="'+codigo+'" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li><li><a href="javascript:void(0);" data-value="'+codigo+'"  class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li></ul>';
}

function OpenLoad(texto)
{
  $.blockUI({
        message: '<span class="text-semibold"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin position-left"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg></i>&nbsp; '+texto+'</span>',
        fadeIn: 800, 
        //timeout: 2000, //unblock after 2 second
        overlayCSS: {
            backgroundColor: '#1b2024',
            opacity: 0.8,
            zIndex: 1200,
            cursor: 'wait'
        },
        css: {
            border: 0,
            color: '#fff',
            zIndex: 1201,
            padding: 0,
            backgroundColor: 'transparent'
        }
    });
}

function CloseLoad()
{
  $.unblockUI();
}

function messageDone(texto, tipo){
    var title = "Buen trabajo!";
    if(tipo == "error")
      title = "Ocurrió un problema!";
    Swal.fire({
      title: title,
      text: texto,
      icon: tipo,
      type: tipo,
      padding: '2em'
    })
}

function messageConfirm(titulo, desc, tipo){
	var title = "¿Estás seguro?";
	if(titulo !== "")
		title = titulo;

	var promesa = new Promise(function(resolve, reject){
		Swal.fire({
	      title: title,
	      text: desc,
	      icon: tipo,
	      type: tipo,
	      showCancelButton: true,
	      confirmButtonText: 'Aceptar',
	      cancelButtonText: 'Cancelar',
	      padding: '2em'
	    }).then(function(result) {
	      if (result.value) {
	        resolve(true);
	      }
	      resolve(false);
	    });
	});
	return promesa;
}

function SwalText(title, desc, placeholder, defaultValue = ''){
	var promesa = new Promise(function(resolve, reject){
    Swal.fire({
      title: title,
      text: desc,
      input: 'text',
      inputValue: defaultValue,
      inputPlaceholder: placeholder,
      inputAttributes: {
        autocapitalize: 'off'
      },
      showCancelButton: true,
      confirmButtonText: 'Generar Link',
      cancelButtonText: 'Cancelar',
      showLoaderOnConfirm: true,
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.value) {
        resolve(result.value);
      }
      resolve(false);
    });
    //Fin Swal
	});
	return promesa;
}

function SwalInput(title, desc="", placeholder="Deja aquí tus comentarios"){
    document.activeElement.blur();
	var promesa = new Promise(function(resolve, reject){
    Swal.fire({
      title: title,
      text: desc,
      input: 'textarea',
      inputPlaceholder: placeholder,
      inputAttributes: {
        autocapitalize: 'off'
      },
      showCancelButton: true,
      confirmButtonText: 'Aplicar',
      cancelButtonText: 'Cancelar',
      showLoaderOnConfirm: true,
      allowOutsideClick: () => !Swal.isLoading(),
      didOpen: () => {
        setTimeout(() => {
            document.querySelector("#swal2-textarea").focus();
        }, 100); // Agregamos un pequeño delay por seguridad
      }
    }).then((result) => {
      if (result.value) {
        resolve(result.value);
      }
      resolve(false);
    });
    //Fin Swal
	});
	return promesa;
}

function SwalSelect(title, options){
	var promesa = new Promise(function(resolve, reject){
    Swal.fire({
      title: title,
      input: 'select',
      inputPlaceholder: 'Selecciona un motorizado',
      inputOptions: options,
      showCancelButton: true,
      confirmButtonText: 'Aplicar',
      cancelButtonText: 'Cancelar',
      showLoaderOnConfirm: true,
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.value) {
        resolve(result.value);
      }
      resolve(false);
    });
    //Fin Swal
	});
	return promesa;
}

function notify(texto, tipo, tiempo, position = "bottom-left"){
  var time = tiempo * 1000;
  var background = '#3b3f5c';
  var textColor = '#fff';

  if(tipo == 'success'){
    textColor = '#fff';
    background = '#8dbf42';
  }else if(tipo == 'error'){
    textColor = '#fff';
    background = '#e7515a';
  }else if(tipo == 'warning'){
    textColor = '#fff';
    background = '#e2a03f';
  }else if(tipo == 'info'){
    textColor = '#fff';
    background = '#2196f3';
  }else if(tipo == 'primary'){
    textColor = '#fff';
    background = '#1b55e2';
  }


  Snackbar.show({
      text: texto,
      actionTextColor: textColor,
      backgroundColor: background,
      duration: time,
      showAction: false,
      pos: position
  });
}

function toast(text, tipo, tiempo, options = null, nameFunc){
  var time = tiempo * 1000;
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-left",    
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": 0,
    "extendedTimeOut": 0,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    "data": options,
    "onclick": nameFunc
  }

  if(tipo == 'success'){
    toastr.success(text);
  }else if(tipo == 'error'){
    toastr.error(text);
  }else if(tipo == 'warning'){
    toastr.warning(text);
  }else if(tipo == 'info'){
    toastr.info(text);
  }else if(tipo == 'primary'){
    toastr.info(text);
  }else if(tipo == 'loading'){
    toastr.loading(text);
  }
  return toastr;
}

$("#btnDescartar").on("click",function(){
    var link = $(this).attr("data-module-back");
    if (typeof link === "undefined") {
      link = "index.php";
    }
    swal.fire({
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

$(".exitApplication").on("click",function(){
    const alias = Cookies.get('alias');
    console.log(alias);
    swal.fire({
          title: '¿Estas seguro de cerrar sesion?',
          text: "¡Dejaras de recibir notificaciones!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Salir',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {

            $.ajax({
                url:'controllers/controlador_usuario.php?metodo=logout',
                type: "POST",
                success: function(response){
                    console.log(response);
                    if(response['success']==1){
                        messageDone(response['mensaje'],'success');
                        unSubscribeTokenToTopic("general");
                        unSubscribeTokenToTopic(response['alias']);
                        unSubscribeTokenToTopic("usuario"+response['id']);
                        let token = localStorage.getItem('token');
                        deleteLoginFirebase(token);
                        setTimeout(function () {
                          window.location.href = './login.php';
                        }, 1500);
                    }else{
                        messageDone(response['mensaje'],'error');
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
