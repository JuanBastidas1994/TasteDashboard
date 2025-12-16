$(function () {
    CKEDITOR.replace("editor1");

    const f2 = flatpickr(document.getElementById('timeFlatpickr2'), {
        enableTime: true,
        noCalendar: true,
       
        defaultDate: "00:00",
        time_24hr: true,


    });
    f2.setDate(time, true)
    $('#btnGuardar').on('click', function (e) {
        e.preventDefault()
        const form = $('#frmSave')
        form.validate()
        console.log(form)
        const isForm = form.valid()

        if (isForm == false) {
            alert('Falta llenar informacion')
            return false;
        }
        const formData = new FormData($('#frmSave')[0])
        const data = CKEDITOR.instances.editor1.getData();
        formData.append('desc_larga', data);
        formData.append('txt_crop', $("#txt_crop").val());
        formData.append('txt_crop_min', $("#txt_crop_min").val());
        const id = parseInt($('#id').val())

        if (id > 0)
            formData.append('cod_producto', id)

        //PRECIOS
        const poData = $('#frmPrecios').serializeArray()
        for (let i = 0; i < poData.length; i++)
            formData.append(poData[i].name, poData[i].value)

        //DISPONIBILIDAD
        const disData = $('#frmDisponibilidad').serializeArray()
        for (let i = 0; i < disData.length; i++)
            formData.append(disData[i].name, disData[i].value)

        //ETIQUETAS
        const etiData = $('#frmEtiquetas').serializeArray()
        for (let i = 0; i <  etiData.length;  i++)
            formData.append(etiData[i].name, etiData[i].value);   

        console.log(formData)
        $.ajax({
            beforeSend: function () {
                console.log('Guardando datos, por favor espere...')
            },
            url: 'controllers/controlador_servicios.php?metodo=crear',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: response => {
                console.log(response)
            },
            error: err => {
                console.log(err)
            }
        })
    })



    //DROPIFY PERFIL
    var resize = null;
    var drEvent = $('#dropifyPerfil').dropify({
        messages: {
            'default': 'Click para subir o arrastra',
            'remove': 'X',
            'replace': 'Sube o Arrastra y suelta'
        },
        error: {
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
    drEvent.on('dropify.beforeImagePreview', function (event, element) {
        if (resize != null)
            resize.destroy();

        selectDropify = "PERFIL";

        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    });

    //DROPIFY GALERIA 
    var resize = null;
    var drGaleria = $('#dropifyGaleria').dropify({
        messages: {
            'default': 'Click para subir o arrastra',
            'remove': 'X',
            'replace': 'Sube o Arrastra y suelta'
        },
        error: {
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
    drGaleria.on('dropify.beforeImagePreview', function (event, element) {
        if (resize != null)
            resize.destroy();

        selectDropify = "GALERIA";

        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    });

    $('#modalCroppie').on('shown.bs.modal', function () {
        let tipoRecorte = $("#tipoRecorte").val();
        let width1 = 400;
        let height1 = 400;
        let width2 = 500;
        let height2 = 500;
        let width3 = 150;
        let height3 = 150;
        if ("rectangle" == tipoRecorte) {
            width1 = 400;
            height1 = 600;
            width2 = 333.3333;
            height2 = 500;
            width3 = 99.9999;
            height3 = 500;
        }

        if (selectDropify == "PERFIL")
            var aux = $(".dropify").get(0);
        else
            var aux = $(".dropify").get(1);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#my-image').attr('src', e.target.result);

            resize = new Croppie($('#my-image')[0], {
                enableExif: true,
                viewport: { width: width1, height: height1 }, //tamaño de la foto que se va a obtener
                boundary: { width: width2, height: height2 }, //la imagen total
                showZoomer: true, // hacer zoom a la foto
                enableResize: false,
                enableOrientation: true // para q funcione girar la imagen 
            });
            $('#crop-get').on('click', function () { // boton recortar
                resize.result({ type: 'base64', size: 'viewport', format: 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF' }).then(function (dataImg) {
                    var InsertImgBase64 = dataImg;
                    console.log("BASE 64");
                    console.log(InsertImgBase64);
                    if (selectDropify == "PERFIL") {
                        $("#txt_crop").val(InsertImgBase64);
                        var imagen = $(".dropify-render img")[0];
                        $(imagen).attr("src", InsertImgBase64);
                    } else {
                        $("#txt_crop_galeria").val(InsertImgBase64);
                        var imagen = $(".dropify-render img")[1];
                        $(imagen).attr("src", InsertImgBase64);
                    }
                    /*MINIATURA*/
                    resize.result({ type: 'base64', size: { width: width3, height: height3 }, format: 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF' }).then(function (dataImg) {
                        console.log("IMAGEN MINIATURAAAAA");
                        console.log(dataImg);
                        $("#txt_crop_min").val(dataImg);
                    });
                    $("#modalCroppie").modal('hide');
                });



            });
            $('.crop-rotate').on('click', function (ev) {
                resize.rotate(parseInt($(this).data('deg')));
            });


        }
        reader.readAsDataURL(file);
    });
})