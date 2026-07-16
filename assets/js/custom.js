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

function SwalCancelMotivo() {
    document.activeElement.blur();

    var motivos = [
        { value: 'ERROR_PEDIDO',    color: '#FF8C42', label: 'Error en el pedido',                 desc: 'El cliente pidió algo incorrecto',         svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' },
        { value: 'CLIENTE_CANCELO', color: '#4A9EE0', label: 'Cliente lo canceló',                 desc: 'El cliente ya no desea el pedido',          svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>' },
        { value: 'SIN_STOCK',       color: '#2ECC71', label: 'Producto no disponible',             desc: 'El producto no estaba disponible',          svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>' },
        { value: 'TIEMPO_ESPERA',   color: '#F39C12', label: 'Tiempos de espera',                  desc: 'El tiempo de entrega es muy largo',         svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' },
        { value: 'OTRO',            color: '#95A5A6', label: 'Otro motivo',                        desc: 'Especifica otro motivo',                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>' }
    ];

    var items = motivos.map(function(m) {
        return '<label class="swal-motivo-row" data-value="' + m.value + '" style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;cursor:pointer;margin-bottom:7px;border:1.5px solid #edf0f5;background:#fff;transition:all .15s;user-select:none;">'
             + '<div class="swal-radio-dot" style="width:18px;height:18px;border-radius:50%;border:2px solid #cbd5e0;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;"></div>'
             + '<div style="width:38px;height:38px;border-radius:10px;background:' + m.color + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">' + m.svg + '</div>'
             + '<div style="flex:1;text-align:left;">'
             +   '<div style="font-size:14px;font-weight:600;color:#2d3748;line-height:1.2;">' + m.label + '</div>'
             +   '<div style="font-size:12px;color:#718096;margin-top:2px;">' + m.desc + '</div>'
             + '</div>'
             + '<input type="radio" name="motivo_cancel" value="' + m.value + '" style="display:none;">'
             + '</label>';
    }).join('');

    var html = '<p style="font-size:13px;font-weight:600;color:#2d3748;text-align:left;margin:0 0 10px;">Selecciona el motivo de cancelación</p>'
             + '<div id="swal_motivos_wrap">' + items + '</div>'
             + '<div id="swal_otro_wrap" style="margin-top:12px;position:relative;display:none;">'
             +   '<textarea id="swal_comentario" maxlength="120" placeholder="Describe el motivo..." rows="3"'
             +   ' style="width:100%;padding:10px 12px 24px;border:1.5px solid #edf0f5;border-radius:12px;font-size:13px;resize:none;color:#2d3748;box-sizing:border-box;outline:none;font-family:inherit;"></textarea>'
             +   '<span id="swal_char_counter" style="position:absolute;bottom:8px;right:12px;font-size:11px;color:#a0aec0;">0/120</span>'
             + '</div>';

    var promesa = new Promise(function(resolve) {
        Swal.fire({
            iconHtml: '<div style="width:56px;height:56px;background:#fff0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">'
                    + '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e53e3e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
                    + '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>'
                    + '</svg></div>',
            title: '¿Estás seguro de Cancelar la orden?',
            html: html,
            showCancelButton: true,
            confirmButtonText: 'Cancelar Orden',
            cancelButtonText: 'Volver',
            confirmButtonColor: '#e53e3e',
            width: 460,
            focusConfirm: false,
            didOpen: function() {
                var btnVolver = document.querySelector('.swal2-cancel');
                if (btnVolver) {
                    btnVolver.style.cssText += 'background:#fff!important;color:#4a5568!important;border:1.5px solid #e2e8f0!important;font-weight:500;box-shadow:none!important;';
                }
                var icon = document.querySelector('.swal2-icon');
                if (icon) { icon.style.border = 'none'; icon.style.margin = '0 auto 8px'; }

                document.querySelectorAll('.swal-motivo-row').forEach(function(row) {
                    row.addEventListener('click', function() {
                        document.querySelectorAll('.swal-motivo-row').forEach(function(r) {
                            r.style.borderColor = '#edf0f5';
                            r.style.background  = '#fff';
                            var dot = r.querySelector('.swal-radio-dot');
                            dot.style.borderColor = '#cbd5e0';
                            dot.style.background  = '';
                            dot.innerHTML = '';
                        });
                        this.style.borderColor = '#e53e3e';
                        this.style.background  = '#fff8f8';
                        var dot = this.querySelector('.swal-radio-dot');
                        dot.style.borderColor = '#e53e3e';
                        dot.style.background  = '#e53e3e';
                        dot.innerHTML = '<div style="width:6px;height:6px;border-radius:50%;background:#fff;"></div>';
                        var radio = this.querySelector('input[type="radio"]');
                        radio.checked = true;

                        // Mostrar textarea solo para "Otro motivo"
                        var wrap = document.getElementById('swal_otro_wrap');
                        if (radio.value === 'OTRO') {
                            wrap.style.display = 'block';
                            document.getElementById('swal_comentario').focus();
                        } else {
                            wrap.style.display = 'none';
                            document.getElementById('swal_comentario').value = '';
                            document.getElementById('swal_char_counter').textContent = '0/120';
                        }
                    });
                });

                var ta = document.getElementById('swal_comentario');
                var counter = document.getElementById('swal_char_counter');
                if (ta) {
                    ta.addEventListener('input', function() {
                        counter.textContent = this.value.length + '/120';
                    });
                }
            },
            preConfirm: function() {
                var selected = document.querySelector('input[name="motivo_cancel"]:checked');
                if (!selected) {
                    Swal.showValidationMessage('Selecciona un motivo de cancelación');
                    return false;
                }
                var comentario = (document.getElementById('swal_comentario').value || '').trim();
                if (selected.value === 'OTRO' && !comentario) {
                    Swal.showValidationMessage('Describe el motivo en el comentario');
                    return false;
                }
                var labelEl = selected.closest('.swal-motivo-row').querySelector('div:last-of-type > div:first-child');
                var labelText = labelEl ? labelEl.textContent : selected.value;
                return comentario ? labelText + ' - ' + comentario : labelText;
            }
        }).then(function(result) {
            resolve(result.value || false);
        });
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
