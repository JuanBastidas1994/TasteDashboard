let ApiUrl = "https://api.mie-commerce.com/taste/v1/";
let ApiKey = "";
let product_id = 0;

let opcionDetalleTipoSelected = "PRODUCTO";
let opcionDetalleIdSelected = 0;

$(function() {
    ApiKey = $("#alias").val();
    product_id = $("#productId").val();
    getAllIngredientes();
    getIngredientesByProduct();
});

function getIngredientesByProduct(){
    fetch(`${ApiUrl}/productos/ingredientes/${product_id}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        console.log("Get ingredientes",response);
        let target = $("#contentOpciones");
        if(response.success == 1){
            let template1 = Handlebars.compile($("#product-ingredientes-template").html());
            $("#productInformation").html(template1(response));

            let template = Handlebars.compile($("#product-data-template").html());
            target.html(template(response));
            feather.replace();
        }
        else{
            target.html("<p>No se pudo obtener la información, por favor intentalo nuevamente</p>");
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function getAllIngredientes(){
    fetch(`${ApiUrl}/productos/ingredientes`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        console.log("All ingredientes",response);
        if(response.success == 1){
            let template = Handlebars.compile($("#lista-ingredientes-template").html());
            $("#cmbIngredientes").html(template(response.ingredientes));
            feather.replace();
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function openModalIngredientes(id, tipo){
    $("#modalIngredientes").modal();
    console.log(id, tipo);
    opcionDetalleIdSelected = id;
    opcionDetalleTipoSelected = tipo;
    $("#cantidadIngrediente").val(1);
}

$("body").on("change", "#cmbIngredientes", function(){
    let data = $(this).find("option:selected").data();
    $("#unidadMedidaOpciones").text(data.unidad_medida);
});

/*AGREGAR INGREDIENTES*/
function setIngredienteToOpcion() {
    let cod_ingrediente = $("#cmbIngredientes").val();
    let valor = $("#cantidadIngrediente").val();

    let exists = false;
    /*
    $(".lstProductosOpcionesIngredientes .row").each(function() {
        let xcod_ingrediente = $(this).data("ingrediente");
        if(cod_ingrediente == xcod_ingrediente)
            exists = true;
    });*/

    if(exists) {
        messageDone("Este ingrediente ya fue añadido", "error");
        return;
    }

    if(opcionDetalleIdSelected == 0) {
        messageDone("Primero debe guardar la opción", "error");
        return;
    }
    if(cod_ingrediente == "") {
        messageDone("Error al elegir ingrediente", "error");
        return;
    }
    if(!validarCantidad(valor)){
        return;
    }

    let info = {
        cod_producto_opcion: opcionDetalleIdSelected,
        cod_ingrediente,
        valor
    };
    let Endpoint = `controllers/controlador_productos.php?metodo=addProductosOpcionesIngredientes`;
    if(opcionDetalleTipoSelected == "PRODUCTO"){
        Endpoint = `controllers/controlador_productos.php?metodo=addProductosIngredientes`;
        info = {
            cod_producto: opcionDetalleIdSelected,
            cod_ingrediente,
            valor
        };
    }

    OpenLoad("Guardando información...");
    fetch(Endpoint, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
            getIngredientesByProduct();
        }
        else {
            messageDone(response.mensaje, "error");
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

/*EDITAR*/
$("body").on("click", ".btnEditarProductoIngrediente", function(){
    let data = $(this).data();
    let $inputCantidad = $(this).parents('.item-ingrediente').find("input");
    console.log(data, $inputCantidad.val());

    if(validarCantidad($inputCantidad.val())){
        editProductoIngrediente(data.id, data.type, $inputCantidad.val());
    }
});

function editProductoIngrediente(id, type, valor) {
    let info = {
        cod_producto_opcion_ingrediente: id,
        valor
    };
    let Endpoint = `controllers/controlador_productos.php?metodo=editProductosOpcionesIngredientes`;
    if(type == "PRODUCTO"){
        Endpoint = `controllers/controlador_productos.php?metodo=editProductosIngredientes`;
        info = {
            cod_producto_ingrediente: id,
            valor
        };
    }

    fetch(Endpoint, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}


/*ELIMINAR*/
$("body").on("click", ".btnDeleteProductoIngrediente", function(){
    let data = $(this).data();
    swal.fire({
       title: ' Se eliminará este ingrediente del producto',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
           deleteProductoIngrediente(data.id, data.type);
       }
    }); 
});

function deleteProductoIngrediente(id, type) {
    let Endpoint = `controllers/controlador_productos.php?metodo=deleteProductosOpcionesIngredientes&cod_producto_opcion_ingrediente=${id}`;
    if(type == "PRODUCTO"){
        Endpoint = `controllers/controlador_productos.php?metodo=deleteProductosIngredientes&cod_producto_ingrediente=${id}`;
    }

    fetch(Endpoint, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            getIngredientesByProduct();
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

/*ACTIVAR INVENTARIO*/
$("body").on("change", ".chkIsInventario", function(){    //Activar/Desactivar Inventario
    let data = $(this).data();
     let info = {
         id: data.id,
         estado: $(this).is(':checked')
     };
     console.log(info);
    fetch(`controllers/controlador_productos.php?metodo=activateOpcionInventario`,{
             method: 'POST',
             body: JSON.stringify(info)
         })
         .then(res => res.json())
         .then(response => {
             if(response.success == 1){
                 console.log("Activate Inventario",response);
                 notify(response.mensaje,'success',2);
             }else{
                 messageDone(response.mensaje,'error');
             }
         })
         .catch(error=>{
             messageDone(error,'error');
         });
 });

//Funciones Adicionales
function validarCantidad(valor){
    if(valor == "") {
        messageDone("Ingrese un valor en la cantidad", "error");
        return false;
    }
    if(isNaN(valor)) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return false;
    }else if(valor <= 0) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return false;
    }
    return true;
}