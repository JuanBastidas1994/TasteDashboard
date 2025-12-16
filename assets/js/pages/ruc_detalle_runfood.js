//Ready
let runfoodApi = "controllers/controlador_runfood.php";
let typeImportContificoProduct = "PRODUCTO";
let productSelected = 0;
let ingredienteSelected = 0;
let officeSelected = 0;
let $tableOffices = null;
let $tableTalonarios = null;
let $tableIngredientes = null;
let $tableRecipientes = null;
let $trContificoConfirm = null;
let OfficeId = 0;
$(document).ready(function () {
    OfficeId = $.urlParam('id');
    loadMisProductos();
    loadIngredientes();
    loadRecipientes();
    loadFormasPago();
   
    //Carga desde Runfood
    loadProductosRunfood();
    loadFormasPagoRunfood();
});

//Mis productos
function loadMisProductos(){
    fetch(`controllers/controlador_runfood.php?metodo=getAllProducts&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Mis Productos",response);
                var template = Handlebars.compile($("#my-product-template").html());
                $("#body-my-products").html(template(response.productos));
                $("#table-my-products").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

//Ingredientes
function loadIngredientes(){
    fetch(`controllers/controlador_runfood.php?metodo=getAllIngredientes&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Mis Ingredientes",response);
                var template = Handlebars.compile($("#ingredientes-template").html());
                $("#body-ingredientes").html(template(response.ingredientes));
                $tableIngredientes = $("#table-ingredientes").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

//Recipientes
function loadRecipientes(){
    fetch(`controllers/controlador_runfood.php?metodo=getAllRecipientes&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Mis Recipientes",response);
                var template = Handlebars.compile($("#recipientes-template").html());
                $("#body-recipientes").html(template(response.recipientes));
                $tableRecipientes = $("#table-recipientes").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

//Formas Pago
function loadFormasPago(){
    fetch(`controllers/controlador_runfood.php?metodo=getAllFormasPago&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Mis Formas de Pago",response);
                var template = Handlebars.compile($("#formaspago-template").html());
                $("#body-formaspago").html(template(response.formaspago));
                $tableRecipientes = $("#table-formaspago").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

//Productos Runfood
function loadProductosRunfood(){
    fetch(`${runfoodApi}?metodo=lstProducts&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Productos Runfood",response);
                let productos = response.productos;
                var template = Handlebars.compile($("#product-contifico-template").html());
                $("#table-products").html(template(productos));
                $("#table-contifico").DataTable({
                    "pageLength": 25,
                    "drawCallback": function( settings ) {
                        console.log( 'DataTables has redrawn the table', settings, typeImportContificoProduct );
                        if(typeImportContificoProduct == "PRODUCTO"){
                            hideButtonsProducts();
                            $(".btnSetProducto").show();
                        }else if(typeImportContificoProduct == "INGREDIENTE"){
                            hideButtonsProducts();
                            $(".btnSetIngrediente").show();
                        }else if(typeImportContificoProduct == "IMPORTAR"){
                            hideButtonsProducts();
                            $(".btnImportar").show();
                        }else if(typeImportContificoProduct == "IMPORTAR_RECIPIENTES"){
                            hideButtonsProducts();
                            $(".btnSaveRecipiente").show();
                        }else if(typeImportContificoProduct == "RECIPIENTE"){
                            hideButtonsProducts();
                            $(".btnSetRecipiente").show();
                        }else if(typeImportContificoProduct == "DOMICILIO"){
                            hideButtonsProducts();
                            $(".btnSetDomiciliouAdicionales").show();
                        }    
                    }
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

function loadFormasPagoRunfood(){
    // fetch(`${runfoodApi}/formaspago`,{
    fetch(`${runfoodApi}?metodo=lstFormasPago&id=${OfficeId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Formas de Pago Runfood",response);
                let formaspago = response.formaspago;
                var template = Handlebars.compile($("#formaspago-contifico-template").html());
                $("#body-formaspago-contifico").html(template(formaspago));
                $("#table-formaspago-contifico").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}


/*----------------Productos-----------------------*/
$("body").on("click", ".btnAsignarProducto", function(){
   let data = $(this).data();
   console.log(data);
   productSelected = data.id;
   
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Ligar producto");
   $(".btnSetProducto").show();
   
   $("#modalProductosContifico").modal();
   
   typeImportContificoProduct = "PRODUCTO";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico");
});

$("body").on("click", ".btnSetProducto", function(){
    let data = $(this).data();
    let info = {
        "office_id": OfficeId,
        "product_id": productSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=setProduct`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Mis Productos",response);
                
                $trContificoConfirm.html(`${data.name}
                                                    <dl>
                                                      <dd>${data.id}</dd>
                                                    </dl>`);
                notify(response.mensaje,'success',2);
                $("#modalProductosContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});


/*----------------Ingredientes-----------------------*/
$("body").on("click", ".btnAsignarIngrediente", function(){
   let data = $(this).data();
   console.log(data);
   ingredienteSelected = data.id;
   
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Ligar Ingrediente");
   $(".btnSetIngrediente").show();
   
   $("#modalProductosContifico").modal();
   
   typeImportContificoProduct = "INGREDIENTE";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico-ingredientes");
});


$("body").on("click", ".btnSetIngrediente", function(){
    let data = $(this).data();
    let info = {
        "office_id": OfficeId,
        "ingrediente_id": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=setIngrediente`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Ingredientes",response);
                
                $trContificoConfirm.html(`${data.name}
                                                    <dl>
                                                      <dd>${data.id}</dd>
                                                    </dl>`);
                notify(response.mensaje,'success',2);
                $("#modalProductosContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});


$("body").on("click", ".btnImportarIngredientes", function(){
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Importar ingrediente");
    $(".btnImportar").show();
   
   $("#modalProductosContifico").modal();
   typeImportContificoProduct = "IMPORTAR";
});

$("body").on("click", ".btnImportar", function(){
    let data = $(this).data();
    Swal.fire({
      title: 'Importar ingrediente '+data.name,
      text: 'Escoge una unidad de medida para este ingrediente',
      input: 'select',
      inputOptions: {
        'und': 'Unidad',
        'lb': 'Libras',
        'g': 'Gramos',
        'kg': 'Kilogramos',
        'l': 'Litros'
      },
      inputPlaceholder: 'Selecciona una unidad',
      showCancelButton: true,
      inputValidator: function (value) {
        return new Promise(function (resolve, reject) {
          if (value !== '') {
            resolve();
          } else {
            reject('Debes escoger una unidad de medida');
          }
        });
      }
    }).then(function (result) {
        let unidad = result.value;
        let info = {
            "office_id": OfficeId,
            "contifico_id": data.id,
            "contifico_name": data.name,
            "unidad": unidad,
            "precio": data.precio
        };
        console.log(info);
        fetch(`controllers/controlador_runfood.php?metodo=importIngrediente`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Ingredientes",response);
                notify(response.mensaje,'success',2);
                $("#modalProductosContifico").modal('hide');
                $tableIngredientes.destroy();
                loadIngredientes();
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
        
    });
});


/*----------------Recipientes-----------------------*/
$("body").on("click", ".btnAsignarRecipiente", function(){
   let data = $(this).data();
   console.log(data);
   ingredienteSelected = data.id;
   
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Ligar Recipientes");
   $(".btnSetRecipiente").show();
   
   $("#modalProductosContifico").modal();
   
   typeImportContificoProduct = "RECIPIENTE";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico-recipientes");
});


$("body").on("click", ".btnSetRecipiente", function(){
    let data = $(this).data();
    let info = {
        "office_id": OfficeId,
        "recipiente_id": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=setRecipiente`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Recipiente",response);
                
                $trContificoConfirm.html(`${data.name}
                                                    <dl>
                                                      <dd>${data.id}</dd>
                                                    </dl>`);
                notify(response.mensaje,'success',2);
                $("#modalProductosContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});


$("body").on("click", ".btnImportarRecipientes", function(){
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Importar recipiente");
    $(".btnSaveRecipiente").show();
   $("#modalProductosContifico").modal();
   typeImportContificoProduct = "IMPORTAR_RECIPIENTES";
});

//Guardar Recipiente
$("body").on("click", ".btnSaveRecipiente", function(){
    let data = $(this).data();
    messageConfirm("¿Estás seguro de importar el recipiente "+data.name+"?", "", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "office_id": OfficeId,
                    "contifico_id": data.id,
                    "contifico_name": data.name,
                    "precio": data.precio
                };
                fetch(`controllers/controlador_runfood.php?metodo=importRecipientes`,{
                        method: 'POST',
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        if(response.success == 1){
                            console.log("Set Recipientes",response);
                            notify(response.mensaje,'success',2);
                            $("#modalProductosContifico").modal('hide');
                            $tableRecipientes.destroy();
                            loadRecipientes();
                        }else{
                            messageDone(response.mensaje,'error');
                        }
                    })
                    .catch(error=>{
                        messageDone(error,'error');
                    });
            }
        });
});


/*----------------Formas de Pago----------------------*/
$("body").on("click", ".btnAsignarFormaPago", function(){
   let data = $(this).data();
   ingredienteSelected = data.id;
   
   $("#modalFormasPagoContifico").modal();
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico-formaspago");
});

$("body").on("click", ".btnSetFormaPago", function(){
    let data = $(this).data();
    let info = {
        "office_id": OfficeId,
        "formapago_id": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=setFormaPago`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Forma de Pago",response);
                
                $trContificoConfirm.html(`${data.name}
                                                    <dl>
                                                      <dd>${data.id}</dd>
                                                    </dl>`);
                notify(response.mensaje,'success',2);
                $("#modalFormasPagoContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});




/*ASGINAR DOMICILIO O ADICIONALES*/
$("body").on("click", ".btnAsignarDomiciliouAdicionales", function(){
   let data = $(this).data();
   console.log(data);
   
    if(data.tipo === "DOMICILIO")
        $("#titleProductosModal").html("Escoger producto para Servicio a Domicilio");
    else
        $("#titleProductosModal").html("Escoger producto para productos adicionales");
    
   
   ingredienteSelected = data.tipo;
   
   //Ocultar boton no funcional
   typeImportContificoProduct = "DOMICILIO";
   hideButtonsProducts();
   $(".btnSetDomiciliouAdicionales").show();
   
   $("#modalProductosContifico").modal();
   
});

$("body").on("click", ".btnSetDomiciliouAdicionales", function(){
    let data = $(this).data();
    let info = {
        "office_id": OfficeId,
        "tipo": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=setDomicilioAdicionales`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Domicilio o adicional",response);
                notify(response.mensaje,'success',2);
                $("#modalProductosContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});

$("body").on("change", ".chkTalonario", function(){    //Activar/Desactivar Talonario
   let data = $(this).data();
    let info = {
        id: data.id,
        estado: $(this).is(':checked')
    };
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=activateTalonario`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Activate Talonario",response);
                notify(response.mensaje,'success',2);
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});

$("body").on("change", ".chkInventario", function(){    //Activar/Desactivar Inventario
   let data = $(this).data();
    let info = {
        id: data.id,
        estado: $(this).is(':checked')
    };
    console.log(info);
   fetch(`controllers/controlador_runfood.php?metodo=activateInventario`,{
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



function hideButtonsProducts(){
    $(".btnSetProducto").hide();
    $(".btnImportar").hide();
    $(".btnSetIngrediente").hide();
    $(".btnSaveRecipiente").hide();
    $(".btnSetRecipiente").hide();
    $(".btnSetDomiciliouAdicionales").hide();
}

$.urlParam = function (name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);

  if (results == null) {
    return null;
  }

  return decodeURI(results[1]) || 0;
};