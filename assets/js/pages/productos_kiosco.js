$(function () {
    getProducts();
});

function getProducts() {
    OpenLoad("Cargando datos...");
    fetch(`controllers/controlador_productos.php?metodo=getProductsKiosco`, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let target = $("#style-3 tbody");
                let template = Handlebars.compile($("#productos-template").html());
                target.html(template(response.data));

                feather.replace();
                $('[data-toggle="tooltip"]').tooltip();
            }
            else {
            }
            CloseLoad();
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
        });
}

$("body").on("click", ".btnSaveProduct", function () {
    let data = $(this).data("producto");
    let ck = $(this).parents("tr").find(".visible");
    data.precio = $(this).parents("tr").find(".precioProducto").val();
    data.estado = "A";
    data.is_custom = 0;
    if (!ck.is(":checked"))
        data.estado = "I";

    console.log(data);
    swal.fire({
        title: 'Este cambio se aplicará a todas las sucursales',
        text: '¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            saveProducts(data);
        }
        else {
            notify("No se guardaron los cambios", "info", 2);
        }
    });
});


function saveProducts(data, element = null) {
    console.log(data);

    OpenLoad("Guardando cambios...");
    fetch(`controllers/controlador_productos.php?metodo=setProductsKiosco`, {
        method: 'POST',
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                notify(response.mensaje, "success", 2);
                if(element != null) {
                    element.trigger("click");
                }
            }
            else {
                notify(response.mensaje, "error", 2);
            }
            CloseLoad();
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
        });
}

$("body").on("click", ".custom", function () {
    let ck = $(this);
    let data = ck.data("producto");
    let precio = ck.parents("tr").find(".precioProducto").val();
    let btnModal = ck.parents("tr").find(".btnCustomProduct");
    let li = ck.parents("tr").find(".is-custom");

    let preciosCustom = true;
    let textSwal = "Se utilizarán precios personalizados por cada sucursal";
    if(!ck.is(":checked")) {
        preciosCustom = false;
        textSwal = "No se utilizarán precios personalizados, asegúrese de configurar los valores correspondientes";
    }

    swal.fire({
        title: textSwal,
        text: '¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            if(preciosCustom) {
                li.removeClass("d-none");
                data.is_custom = 1;
                data.precio = precio;
                saveProducts(data, btnModal); 
            }
            else{
                li.addClass("d-none");
                data.is_custom = 0;
                data.precio = precio;
                saveProducts(data); 
            }
        }
        else {
            resetCheck(ck);
            notify("No se aplicaron cambios", "info", 2);
        }
    });
});

function resetCheck(ck) {
    if(ck.is(":checked")) {
        ck.prop("checked", false);
    }
    else {
        ck.prop("checked", true);
    }
}

function getProductsOffices(producto) {
    OpenLoad("Cargando sucursales...");
    fetch(`controllers/controlador_productos.php?metodo=getProductsOffices&cod_producto=${producto.cod_producto}`, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                $("#modalProductCustom h5").text(producto.nombre);
                let target = $("#modalProductCustom .modal-body");
                let template = Handlebars.compile($("#modal-body-template").html());
                response.producto = producto;

                target.html(template(response));
                $("#modalProductCustom").modal();
            }
            else {
            }
            CloseLoad();
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
        });
}

$("body").on("click", ".btnCustomProduct", function () {
    let producto = $(this).data("producto");
    getProductsOffices(producto);
});


$("body").on("click", "#saveCustom", function () {
    let data = [];
    let cod_producto = $("#productCustomId").val().trim();
    if (cod_producto == "") {
        notify("Falta ID del producto", "error", 2);
        return;
    }

    $(".precioCustom").each(function () {
        let item = {};
        item.precio = $(this).val();
        item.cod_sucursal = $(this).data("office");
        item.cod_producto = cod_producto;
        item.estado = 'A';
        item.is_custom = 1;

        let ck = $(this).parents("tr").find(".estadoCustom");
        if (!ck.is(":checked"))
            item.estado = 'I';

        data.push(item);
    });

    OpenLoad("Guardando datos...");
    fetch(`controllers/controlador_productos.php?metodo=setProductsKioscoCustom`, {
        method: 'POST',
        body: JSON.stringify({ data })
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                $("#modalProductCustom").modal("hide");
                notify(response.mensaje, "success", 2);
            }
            else {
                notify(response.mensaje, "error", 2);
            }
            CloseLoad();
        })
        .catch(error => {
            CloseLoad();

            console.log(error);
        });

    console.log(data);
});