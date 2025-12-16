$(function () {
    getProductosContifico();
    getIngredientes();
    getUnidadesMedidas();
});

function getProductosContifico(page = 1) {
    $(".btnPrev").attr("disabled", true);
    $(".btnNext").attr("disabled", true);

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_ingredientes.php?metodo=listaProductosContifico&page=${page}`, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let target = $("#lstProductoContifico");
                let template = Handlebars.compile($("#lista-productos-contifico-template").html());
                target.html(template(response.data));

                if (response.data.previous != null)
                    $(".btnPrev").removeAttr("disabled");
                if (response.data.next != null)
                    $(".btnNext").removeAttr("disabled");
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

function getIngredientes() {
    fetch(`controllers/controlador_ingredientes.php?metodo=getIngredientes`, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let target = $("#lstIngredientes");
                let template = Handlebars.compile($("#lista-ingredientes-template").html());
                target.html(template(response.data));
                feather.replace();
            }
            else {
            }
        })
        .catch(error => {
            console.log(error);
        });
}

function getUnidadesMedidas() {
    fetch(`controllers/controlador_ingredientes.php?metodo=listaUnidadesMedidas`, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let target = $("#cmbUnidadMedida");
                let html = "";
                for (i = 0; i < response.data.length; i++) {
                    let cod_unidad_medida = response.data[i].cod_unidad_medida;
                    let nombre = response.data[i].nombre;
                    html += `<option value="${cod_unidad_medida}">${nombre}</option>`
                }
                target.html(html);
            }
            else {
                notify(response.mensaje, "error", 2);
            }
        })
        .catch(error => {
            console.log(error);
        });
}

$("body").on("click", ".btnLoadMore", function () {
    let currentPage = $(".currentpage").html();
    currentPage = parseInt(currentPage) + 1;
    $(".currentpage").html(currentPage);
    getProductosContifico(currentPage);
});

$("body").on("click", ".btnNext", function () {
    let currentPage = $(".currentpage").html();
    currentPage = parseInt(currentPage) + 1;
    $(".currentpage").html(currentPage);
    getProductosContifico(currentPage);
});

$("body").on("click", ".btnPrev", function () {
    let currentPage = $(".currentpage").html();
    currentPage = parseInt(currentPage) - 1;
    $(".currentpage").html(currentPage);
    getProductosContifico(currentPage);
});

function openUnidadMedidas() {
    $("#modalUnidades").modal();
}

$("body").on("click", ".btnImportar", function () {
    let btn = $(this);
    saveIngredients(btn.data("ingredient"));
});

$("body").on("click", ".btnEliminar", function () {
    let data = JSON.parse($(this).attr("data-ingredient"));
    Swal.fire({
        title: 'Se eliminará el ingrediente',
        text: '¿Continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            deleteIngredients(data);
        }
    });
});

function saveIngredients(ingrediente) {

    OpenLoad("Guardando...");
    fetch(`controllers/controlador_ingredientes.php?metodo=saveIngrediente`, {
        method: 'POST',
        body: JSON.stringify({ ingrediente })
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                notify(response.mensaje, "success", 2);
                getIngredientes();
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

function editUnidadMedidaIngrediente(ingrediente) {
    ingrediente.cod_unidad_medida = $("#cmbUnidadMedida").val();
    OpenLoad("Guardando...");
    fetch(`controllers/controlador_ingredientes.php?metodo=editUnidadMedidaIngrediente`, {
        method: 'POST',
        body: JSON.stringify({ingrediente})
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                notify(response.mensaje, "success", 2);
                getIngredientes();
                $("#modalUnidades").modal("hide");
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

function deleteIngredients(ingrediente) {

    OpenLoad("Guardando...");
    fetch(`controllers/controlador_ingredientes.php?metodo=deleteIngrediente`, {
        method: 'POST',
        body: JSON.stringify({ id: ingrediente.id })
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                notify(response.mensaje, "success", 2);
                getIngredientes();
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

$("body").on("click", ".btnEditUnidadMedida", function(){
    let data = $(this).attr("data-ingredient");
    $("#btnGuardarUnidad").attr("data-ingredient", data);
    data = JSON.parse(data);
    console.log(data);
    $("#cmbUnidadMedida").val(data.cod_unidad_medida);

    $("#modalUnidades").modal();
});

$("#btnGuardarUnidad").on("click", function(){
    let data = JSON.parse($(this).attr("data-ingredient"));
    editUnidadMedidaIngrediente(data);
});

$("#resetSearch").on("click", function(){
    $("#search").val("");
    $(".results").show();
});

$("#search").on("focus", function(){
    $(this).select();
});

$("#search").on("keyup", function(){
    let search = $(this).val().toString().toLowerCase();
    let results = $(".results");
    if(search == "") {
        results.show();
        return;
    }

    results.each(function() {
        let filter = $(this).data("filter").toString().toLowerCase();
        if(filter.includes(search))
            $(this).show();
        else
            $(this).hide();
    });
});

$("#resetSearch2").on("click", function(){
    $("#search2").val("");
    $(".results2").show();
});

$("#search2").on("focus", function(){
    $(this).select();
});

$("#search2").on("keyup", function(){
    let search = $(this).val().toString().toLowerCase();
    let results = $(".results2");
    if(search == "") {
        results.show();
        return;
    }

    results.each(function() {
        let filter = $(this).data("filter").toString().toLowerCase();
        if(filter.includes(search))
            $(this).show();
        else
            $(this).hide();
    });
});