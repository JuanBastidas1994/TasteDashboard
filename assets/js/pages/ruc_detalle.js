//Ready
let ApiContifico = "v1"; //v1 - v2
let $modalProductos = $("#modalProductosContifico");
let typeImportContificoProduct = "PRODUCTO";
let productSelected = 0;
let ingredienteSelected = 0;
let officeSelected = 0;
let $tableOffices = null;
let $tableTalonarios = null;
let $tableIngredientes = null;
let $tableRecipientes = null;
let $trContificoConfirm = null;
let rucId = 0;
$(document).ready(function () {
    rucId = $.urlParam('id');
   loadMisProductos();
   loadOffices();
   loadTalonarios();
   loadIngredientes();
   loadRecipientes();
   
   //Carga desde contifico
   loadProductosContifico();
   loadBodegas();


   loadCategoriasContifico();
});

//Mis productos
function loadMisProductos(){
    fetch(`controllers/controlador_ruc.php?metodo=getAllProducts&id=${rucId}`,{
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

//Producto contifico
function loadProductosContifico(){
    console.log("LOAD PRODUCTOS FROM CONTIFICO");
    getProductsContifico()
        .then(productos => {
            console.log("GET PRODUCTS",productos);
            
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
        })
        .catch(error => {
            messageDone(error,'error');
        });
}


function getProductsContifico(){
    var promesa = new Promise(function(resolve, reject){
        fetch(`controllers/controlador_contifico.php?metodo=lstProductsByRuc&id=${rucId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Productos contifico",response);
                resolve(response.productos);
            }else{
                reject(response.mensaje); 
            }
        })
        .catch(error=>{
            reject('Ocurrió un error al obtener los productos de contífico');
        });
    });
    return promesa;
}

/*Bodegas*/
function loadOffices(){
    fetch(`controllers/controlador_ruc.php?metodo=getAllOffices&id=${rucId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            console.log("Mis Bodegas",response);
            if(response.success == 1){
                var template = Handlebars.compile($("#office-template").html());
                $("#body-office").html(template(response.offices));
                $tableOffices = $("#table-office").DataTable({
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

function loadBodegas(){
    fetch(`controllers/controlador_contifico.php?metodo=lstBodegasByRuc&id=${rucId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                let bodegas = response.bodegas;
                console.log("BODEGAS",bodegas);
            
                var template = Handlebars.compile($("#bodega-contifico-template").html());
                $("#body-bodega").html(template(bodegas));
                $("#table-bodega").DataTable({
                    "pageLength": 25
                });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone('Ocurrió un error al obtener las bodegas de contífico','error');
        });
}


//Talonarios
function loadTalonarios(){
    fetch(`controllers/controlador_ruc.php?metodo=getAllPostokens&id=${rucId}`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            console.log("Mis Talonarios",response);
            if(response.success == 1){
                var template = Handlebars.compile($("#talonario-template").html());
                $("#body-talonario").html(template(response.postokens));
                $tableTalonarios = $("#table-talonarios").DataTable({
                    "pageLength": 25
                });
                feather.replace();
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
    fetch(`controllers/controlador_ruc.php?metodo=getAllIngredientes&id=${rucId}`,{
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
    fetch(`controllers/controlador_ruc.php?metodo=getAllRecipientes&id=${rucId}`,{
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


/*----------------Productos-----------------------*/
$("body").on("click", ".btnAsignarProducto", function(){
   let data = $(this).data();
   console.log(data);
   productSelected = data.id;
   
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Ligar producto");
   $(".btnSetProducto").show();
   
   $modalProductos.modal();
   
   typeImportContificoProduct = "PRODUCTO";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico");
});

$("body").on("click", ".btnSetProducto", function(){
    let data = $(this).data();
    let info = {
        "ruc_id": rucId,
        "product_id": productSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_ruc.php?metodo=setProductToContifico`,{
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
                $modalProductos.modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});

/*--------------Bodegas----------------*/
$("body").on("click", ".btnAsignarBodega", function(){
   let data = $(this).data();
   console.log(data);
   officeSelected = data.id;
   $("#modalBodegasContifico").modal();
   
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico");
});

$("body").on("click", ".btnSetBodega", function(){
    let data = $(this).data();
    let info = {
        "ruc_id": rucId,
        "office_id": officeSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    };
    console.log(info);
   fetch(`controllers/controlador_ruc.php?metodo=setOfficesToContifico`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                $tableOffices.destroy();
                loadOffices();
                notify(response.mensaje,'success',2);
                $("#modalBodegasContifico").modal('hide');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
});

/*---------------Talonario---------------------*/
function openNewTalonario(){
    fetch(`controllers/controlador_ruc.php?metodo=getOfficesNoConfig`,{
            method: 'GET',
        })
        .then(res => res.json())
        .then(response => {
            console.log("Offices",response);
            if(response.success == 1){
                var template = Handlebars.compile($("#offices-check-template").html());
                $("#officesCheck").html(template(response.offices));
                //feather.replace();
                $("#modalCrearTalonario").modal();
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

function saveNewTalonario(){
    let offices = [];
    $(".chkOffices").each(function( index ) {
        if($(this).prop("checked")){
            offices.push($(this).val());
        }
    });

    if(offices.length == 0){
        messageDone("Debes asignar como mínimo una sucursal",'error');
        return;
    }
        
    let info = {
        ruc_id: rucId,
        api_token: $("#txt_api_token").val(),
        emisor: $("#txt_emisor").val(),
        emision: $("#txt_emision").val(),
        sec_fac: $("#txt_sec_fac").val(),
        sec_dna: $("#txt_sec_dna").val(),
        offices: offices
    }
    console.log(info);
    fetch(`controllers/controlador_ruc.php?metodo=savePosToken`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                
                $tableTalonarios.destroy();
                loadTalonarios();
                notify(response.mensaje,'success',2);
                $("#modalCrearTalonario").modal('hide');
                
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}



/*----------------Ingredientes-----------------------*/
$("body").on("click", ".btnAsignarIngrediente", function(){
   let data = $(this).data();
   console.log(data);
   ingredienteSelected = data.id;
   
   //Ocultar boton no funcional
   hideButtonsProducts();
   $("#titleProductosModal").html("Ligar Ingrediente");
   $(".btnSetIngrediente").show();
   
   $modalProductos.modal();
   
   typeImportContificoProduct = "INGREDIENTE";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico-ingredientes");
});


$("body").on("click", ".btnSetIngrediente", function(){
    let data = $(this).data();
    let info = {
        "ruc_id": rucId,
        "ingrediente_id": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_ruc.php?metodo=setIngredienteToContifico`,{
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
                $modalProductos.modal('hide');
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
   
   $modalProductos.modal();
   typeImportContificoProduct = "IMPORTAR";
});

//Importar ingredientes
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
            "ruc_id": rucId,
            "contifico_id": data.id,
            "contifico_name": data.name,
            "unidad": unidad,
            "precio": data.precio
        };
        console.log(info);
        fetch(`controllers/controlador_ruc.php?metodo=importIngredienteFromContifico`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Ingredientes",response);
                notify(response.mensaje,'success',2);
                $modalProductos.modal('hide');
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
   
   $modalProductos.modal();
   
   typeImportContificoProduct = "RECIPIENTE";
   $trContificoConfirm = $(this).parents("tr").find(".info-contifico-recipientes");
});


$("body").on("click", ".btnSetRecipiente", function(){
    let data = $(this).data();
    let info = {
        "ruc_id": rucId,
        "recipiente_id": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_ruc.php?metodo=setRecipienteToContifico`,{
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
                $modalProductos.modal('hide');
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
   $modalProductos.modal();
   typeImportContificoProduct = "IMPORTAR_RECIPIENTES";
});

//Guardar Recipiente
$("body").on("click", ".btnSaveRecipiente", function(){
    let data = $(this).data();
    messageConfirm("¿Estás seguro de importar el recipiente "+data.name+"?", "", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "ruc_id": rucId,
                    "contifico_id": data.id,
                    "contifico_name": data.name,
                    "precio": data.precio
                };
                fetch(`controllers/controlador_ruc.php?metodo=importRecipientesFromContifico`,{
                        method: 'POST',
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        if(response.success == 1){
                            console.log("Set Recipientes",response);
                            notify(response.mensaje,'success',2);
                            $modalProductos.modal('hide');
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
   
   $modalProductos.modal();
   
});

$("body").on("click", ".btnSetDomiciliouAdicionales", function(){
    let data = $(this).data();
    let info = {
        "ruc_id": rucId,
        "tipo": ingredienteSelected,
        "contifico_id": data.id,
        "contifico_name": data.name
    }   
    console.log(info);
   fetch(`controllers/controlador_ruc.php?metodo=setDomicilioAdicionalesToContifico`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("Set Domicilio o adicional",response);
                notify(response.mensaje,'success',2);
                $modalProductos.modal('hide');
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
   fetch(`controllers/controlador_ruc.php?metodo=activateTalonario`,{
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
   fetch(`controllers/controlador_ruc.php?metodo=activateInventario`,{
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


//CONTIFICO v2

//Producto contifico
let $tableProductos = null;
function loadCategoriasContifico(){
    fetch(`controllers/controlador_contifico.php?metodo=lstCategorias&id=${rucId}`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log("Mis Categorias",response);
        if(response.success == 1){
            $("#modalCategoriasContifico").modal();
            var template = Handlebars.compile($("#categorias-contifico-template").html());
            $("#LstCategorias").html(template(response.categorias));
        }else{
            messageDone(response.mensaje,'error');
        }
    })
    .catch(error=>{
        console.log("MIS CATEGORIAS", error);
        messageDone(error,'error');
    });
}

function loadProductosByCategoryContifico(category_id, page){
    $("#table-products2").html("Cargando...");
    
    if($tableProductos !== null){
        $tableProductos.clear().draw();
        $tableProductos.destroy();
        $tableProductos = null;
    }

    fetch(`controllers/controlador_contifico.php?metodo=lstProductosByCategory&id=${rucId}&category_id=${category_id}&page=${page}`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log("Mis Productos By categoria",response);
        if(response.success == 1){    
            $("#numRows").html(response.numRows);        
            var template = Handlebars.compile($("#product-contifico-template").html());
            $("#table-products2").html(template(response.productos));
            $tableProductos = $("#table-contifico2").DataTable({
                "pageLength": 100,
                "drawCallback": function( settings ) {
                    //console.log( 'DataTables has redrawn the table', settings, typeImportContificoProduct );
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

$("body").on("change","#cmbVersionContifico", function(){
    ApiContifico = $(this).val();
    if(ApiContifico == "v1")
        $modalProductos = $("#modalProductosContifico");
    else
        $modalProductos = $("#modalCategoriasContifico");
});