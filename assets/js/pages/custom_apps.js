$(document).ready(function(){

    feather.replace();

    /*INFORMACION - TIPO DE MENU (GRID / LISTA)*/
    $(".menu-type-option").on("click", function(){
        $(".menu-type-option").removeClass("active");
        $(this).addClass("active");
        $("#hd_menu_type").val($(this).data("value"));
    });

    /*INFORMACION - URLs ANDROID / IOS*/
    $("#btnGuardarInfoApp").on("click", function(){
        var cod_empresa = parseInt($("#id").val());
        var parametros = {
            "cod_empresa": cod_empresa,
            "url_android": $("#txt_url_android").val(),
            "url_ios": $("#txt_url_ios").val(),
            "menu_type": $("#hd_menu_type").val()
        }
        $.ajax({
            url: 'controllers/controlador_empresa.php?metodo=updateCustomAppInfo',
            data: parametros,
            type: "POST",
            beforeSend: function(){
                OpenLoad("Actualizando información, por favor espere...");
            },
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
                console.log(data);
            },
            complete: function(){
                CloseLoad();
            },
        });
    });

    /*MENU - ORDEN DE TABS MOVIBLES*/
    $(".connectedTabsSortable").sortable({
        connectWith: ".connectedTabsSortable",
        placeholder: "tab-order-item",
    }).disableSelection();

    $("#btnGuardarTabsOrder").on("click", function(){
        var cod_empresa = parseInt($("#id").val());
        var seleccionados = [];
        $("#lstTabsSeleccionados li").each(function(){
            seleccionados.push($(this).data("id"));
        });

        var parametros = {
            "cod_empresa": cod_empresa,
            "tabs_order": seleccionados.join(",")
        }
        $.ajax({
            url: 'controllers/controlador_empresa.php?metodo=updateTabsOrder',
            data: parametros,
            type: "POST",
            beforeSend: function(){
                OpenLoad("Actualizando orden, por favor espere...");
            },
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
                console.log(data);
            },
            complete: function(){
                CloseLoad();
            },
        });
    });

    /*LOGOS*/
    $(".flLogos").change(function(){
        let inputfile = this.files[0];
        let imgSubida = $(this).data("titulo");
        let nomImage = $(this).data("name");
        let formData = new FormData($("#frmLogos")[0]);
        formData.append("nomImage", nomImage);
        formData.append("inputFile", inputfile);

        $.ajax({
            url: 'controllers/controlador_empresa.php?metodo=subirLogos',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    notify(imgSubida + " subida con éxito", "success");
                }
                else{
                    notify(response['mensaje'], "error");
                }
            },
            error: function(data){
            },
            complete: function(){
            },
        });
    });

    $(".btnActLogosPagina").on("click", function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Los cambios podrían ser irreversibles',
            text: '¿Continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function(result){
            if (result.value) {
                actualizarLogos();
            }
        });
    });

    function actualizarLogos() {
        let cod_empresa = $("#id").val();
        let url = "/home1/digitalmind/" + $("#urlFolder").val();
        let parametros = {
            "id": cod_empresa,
            "url": url
        }
        $.ajax({
            url: 'https://dashboard.mie-commerce.com/replicador/iconos.php',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
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

});
