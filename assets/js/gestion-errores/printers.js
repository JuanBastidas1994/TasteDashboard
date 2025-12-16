var printer = {};
let impresoras = [];

/*CONFIG PUERTO IMPRESORA*/
if (JSON.parse(localStorage.getItem('printer')) === null) {
    printer.url = "http://localhost:8890";
    printer.puerto = "8890";
    printer.impresoras = [];
    localStorage.setItem('printer', JSON.stringify(printer));
}

/* $(function(){
    loadPrintersSave();
}); */

function loadPrintersServices(){
    printer = JSON.parse(localStorage.getItem('printer'));
    console.log(`${printer.url}/print/lista`);

    //Lista de impresoras
    loadPrintersSave();

    //Verificar si el servicio esta activo
    fetch(`${printer.url}/print/lista`)
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                impresoras = response.data;
                var template = Handlebars.compile($("#printers-form-template").html());
                $("#printer-form").html("");
                $("#printer-form").append(template(impresoras));
                updateIcons($("#iconPrinterStatus"), "check-circle", "text-success");
            }else{
                console.log("No hay impresoras");
                updateIcons($("#iconPrinterStatus"), "x-circle", "text-danger");
            }
        })
        .catch(error=>{
            console.log(error);
            $("#printer-form").append('<div class="text-center"><h1>Servicio de impresión apagado</h1></div>');
            $("#iconPrinterStatus").removeClass();
            updateIcons($("#iconPrinterStatus"), "x-circle", "text-danger");
        });
}

function loadPrintersSave(){
    printer = JSON.parse(localStorage.getItem('printer'));
    var templateListaImpresoras = Handlebars.compile($("#printers-lista-template").html());
    $("#printers-lista").html(templateListaImpresoras(printer));
    feather.replace();
}

function updateUrl(){
    let url = $("#txtPrintUrl").val();
    fetch(`${url}/print`)
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                let printer = JSON.parse(localStorage.getItem('printer'));
                printer.url = $("#txtPrintUrl").val();
                localStorage.setItem('printer', JSON.stringify(printer));
                loadPrintersServices();
                notify('Servicio configurado correctamente','success', 2);
            }else{
                console.log("error");
                messageDone('Ocurrió un error, por favor vuelve a intentarlo','error');
            }
        })
        .catch(error=>{
            console.log(error);
            messageDone('Servicio apagado o mal configurado, revisa la url','error');
        });
    
}

function addPrinter(){
    if($("#cmbLstImpresoras").val().trim().length === ""){
        messageDone('Debes ingresar la comunicación con la impresora','error');
        return;
    }

    let d = new Date;
    let item = {
        id: d.getTime(),
        nombre: $("#cmbLstImpresoras").val(),
        paginas: $(".printPaginas").val(),
        tipo: $(".printTipo").val(),
        size: $(".printPapel").val(),
    }

    /*
    printer = JSON.parse(localStorage.getItem('printer'));
    var find = printer.impresoras.some(function (prin) {
	    return prin.tipo === item.tipo;
	});

    if(!find){
        printer.impresoras.push(item);
        localStorage.setItem('printer', JSON.stringify(printer));
    }else{
        for (var i = 0; i < printer.impresoras.length; i++) {
            if (printer.impresoras[i].tipo === item.tipo) {
                printer.impresoras[i] = item;
                localStorage.setItem('printer', JSON.stringify(printer));
            }
        }
    }*/
    printer.impresoras.push(item);
    localStorage.setItem('printer', JSON.stringify(printer));
    
    notify("Impresora actualizada",'success',2);
    loadPrintersSave();
}


function printOrder(order_id){
    let config = getConfigGestionOrdenes();
    if(!config.permisos.includes('DESKTOP_IMPRESION')){
        messageDone("La empresa no tiene activado el servicio de impresión, para más información contáctase con su asesor comercial",'error');
        return false;
    }
    
    printer = JSON.parse(localStorage.getItem('printer'));
    let info = {
        cod_orden: order_id,
        impresoras: printer.impresoras
    }
    
    if(printer.impresoras.length === 0){
        messageDone("No tienes impresoras configuradas",'error');
        return false;
    }
    OpenLoad("Imprimiendo...");
    fetch(`${ApiUrl}/printer`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            console.log(response);
            if(response.success == 1){
                let printerInfo = response.impresoras;

                //Enviar información al servicio de impresión
                fetch(`${printer.url}/print/v2`,{
                        method: 'POST',
                        body: JSON.stringify(printerInfo)
                    })
                    .then(res => res.json())
                    .then(response => {
                        notify("Impresión correcta", "success", 2);
                        console.log(response);
                    })
                    .catch(error=>{
                        notify("Error: Verifica el servicio de impresion", "error", 2);
                        console.log(error);
                    });
            }else{
                notify(response.mensaje, "error", 2);
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            notify('Ocurrió un error', "error", 2);
        });
}

$("body").on("click", ".btnDeletePrinter", function(){
    let printerId = $(this).data("id");
    if(printerId == "") {
        printerId = $(this).data("name");
    }
    Swal.fire({
       title: 'Eliminar impresora',
       text: '¿Continuar?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
            deletePrinter(printerId);
       }
    }); 
});

function deletePrinter(id) {
    let printers = localStorage.getItem("printer");
    if(printers != null) {
        printers = JSON.parse(printers);
        if(printers != null) {
            printers.impresoras = $.grep(printers.impresoras, function(p) {
                if(p.id != undefined)
                    return p.id != id;
                else
                    return p.nombre != id;
            });
            localStorage.setItem('printer', JSON.stringify(printers));
            loadPrintersSave();
        }
    }
}

$("body").on("click", ".btnResetPrinters", function(){
    Swal.fire({
       title: 'Se eliminarán todas las impresoras configuradas',
       text: '¿Continuar?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
            resetPrinters();
       }
    }); 
});

function resetPrinters() {
    let printers = localStorage.getItem("printer");
    if(printers != null) {
        printers = JSON.parse(printers);
        if(printers != null) {
            printers.impresoras = [];
            localStorage.setItem('printer', JSON.stringify(printers));
            loadPrintersSave();
        }
    }
}