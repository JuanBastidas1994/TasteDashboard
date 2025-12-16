$(document).ready(function () {

    const fillTemplate = (descripciones) => {
        const template = Handlebars.compile(document.getElementById('listaDescripcion').innerHTML)
        const filled = template(descripciones)
        console.log(descripciones)

        document.getElementById('outPut').innerHTML = filled
    }

    CKEDITOR.replace("editor1");
    $('#buttonSave').on('click', function (e) {
        const formData = new FormData()
        const ckeditorDescription = CKEDITOR.instances.editor1.getData()
        formData.append('cod_producto', $('#txt_codProducto').val())
        formData.append('titulo', $('#txt_titulo').val())
        formData.append('descripcion', ckeditorDescription)
        formData.append('cod_producto_descripcion', $('#txt_codProductoDescripcion').val())
        $.ajax({
            beforeSend: function () {
                OpenLoad('Guardando datos...')
            },
            url: 'controllers/controlador_productos_descripcion.php?metodo=crear',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response)
                if (response.success) {
                    fillTemplate(response.descripciones)
                    limpiarFormulario()
                }
                feather.replace()
            },
            error: function (data) {
                console.log(data)
            },
            complete: function (data) {
                CloseLoad();
            }
        })
    })

    $('body').on('click', '.delete-description', function (e) {
        Swal.fire({
            title: '¿Desea Eliminar esta descripción?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            denyButtonText: `No Eliminar`,
        }).then((result) => {
            console.log(result)
            if (result.value) {
                const codProducto = $('#txt_codProducto').val();
                const codProductoDescripcion = $(this).data('descriptionid')
                $.ajax({
                    beforeSend: function () {
                        OpenLoad('Guardando datos...')
                    },
                    url: `controllers/controlador_productos_descripcion.php?metodo=delete&cod_producto_descripciones=${codProductoDescripcion}&cod_producto=${codProducto}`,
                    success: function (response) {
                        console.log(response)
                        if (response.success) {
                            fillTemplate(response.descripciones)
                        }
                        feather.replace()
                        Swal.fire('¡Eliminado correctamente!', '', 'success')
                    },
                    error: function (response) {
                        console.log(response)
                    },
                    complete: function () {
                        CloseLoad()
                    }
                })
            }
        })

    })

    $('body').on('click', '.update-description', function (e) {
        const codDescription = $(this).data('descriptionid')
        $.ajax({
            beforeSend: function () {
                OpenLoad('...')
            },
            url: `controllers/controlador_productos_descripcion.php?metodo=get&cod_producto_descripciones=${codDescription}`,
            success: function (response) {
                if (response.success) {
                    $('#txt_codProductoDescripcion').val(codDescription)
                    cargarInfoInput(response.data)
                    console.log(response)
                }
            },
            error: function (response) {
                console.log(response)
            },
            complete: function () {
                CloseLoad()
            }
        })
    })
    $('#nuevaDescription').on('click', function (e) {
        limpiarFormulario()
    })
    const cargarInfoInput = description => {
        $('#txt_titulo').val(description.titulo)
        $('#txtCodProducto').val()
        if (CKEDITOR.instances['editor1']) {
            CKEDITOR.instances['editor1'].setData(description.descripcion)
        }
    }
    const limpiarFormulario = () => {
        $('#txt_titulo').val('')
        $('#txt_codProductoDescripcion').val(0)
        if (CKEDITOR.instances['editor1']) {
            CKEDITOR.instances['editor1'].setData('')
        }

    }

    const descriptionsItems = document.getElementById('outPut')
    const sortable = Sortable.create(descriptionsItems, {
        store: {
            set: function (sortable) {
                const codProducto = $('#txt_codProducto').val();
                let order = sortable.toArray()
                const formData = new FormData()
                formData.append('cod_producto', codProducto)
                formData.append('orderItems', order)
                $.ajax({
                    url: `controllers/controlador_productos_descripcion.php?metodo=orderDescriptions`,
                    type: 'POST',
                    beforeSend: () => {
                        OpenLoad('...')
                    },
                    success: function (response) {
                        console.log(response)
                    },
                    complete: function () {
                        CloseLoad()
                    },
                    contentType: false,
                    processData: false,
                    data: formData
                })
            }
        }
    })


});