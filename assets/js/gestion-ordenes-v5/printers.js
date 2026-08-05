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

function getEstacionId(){
    printer = JSON.parse(localStorage.getItem('printer'));
    if(!printer.estacion_id){
        printer.estacion_id = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : ('est-' + Date.now() + '-' + Math.random().toString(16).slice(2));
        localStorage.setItem('printer', JSON.stringify(printer));
    }
    return printer.estacion_id;
}

function syncImpresorasFromApi(){
    if(!sucursal_id || sucursal_id == 0) return;

    let estacionId = getEstacionId();
    fetch(`${ApiUrl}/impresoras/${sucursal_id}/${estacionId}`, {
        headers: {'Api-Key': ApiKey}
    })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1 && response.data.length > 0){
                aplicarImpresorasDesdeApi(response.data);
            }else if(codRol > 2){
                //No hay nada guardado en la BD todavía para esta estación: migrar lo que haya en localStorage (una sola vez)
                //Solo para sesiones reales de cajero (sucursal fija) - un admin probando no debe escribir nada
                let printerLocal = JSON.parse(localStorage.getItem('printer'));
                if(printerLocal.impresoras && printerLocal.impresoras.length > 0){
                    migrarImpresorasLocales(printerLocal.impresoras, estacionId);
                }
            }
        })
        .catch(error => console.log(error));
}

function aplicarImpresorasDesdeApi(data){
    printer = JSON.parse(localStorage.getItem('printer'));
    //Solo las ya asignadas (CAJA/COCINA) le sirven al cajero para imprimir; las "sin asignar" son solo para el panel admin
    printer.impresoras = data
        .filter(function(item){ return item.tipo == 'CAJA' || item.tipo == 'COCINA'; })
        .map(function(item){
            return {id: item.cod_impresora, nombre: item.nombre, paginas: item.paginas, tipo: item.tipo, size: item.size};
        });
    localStorage.setItem('printer', JSON.stringify(printer));
    loadPrintersSave();
}

function migrarImpresorasLocales(impresorasLocales, estacionId){
    let pendientes = impresorasLocales.length;
    let migradas = [];
    impresorasLocales.forEach(function(item){
        fetch(`${ApiUrl}/impresoras`, {
            method: 'POST',
            headers: {'Api-Key': ApiKey},
            body: JSON.stringify({
                cod_sucursal: sucursal_id,
                estacion_id: estacionId,
                nombre: item.nombre,
                tipo: item.tipo,
                size: item.size,
                paginas: item.paginas
            })
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                migradas.push({id: response.cod_impresora, nombre: item.nombre, tipo: item.tipo, size: item.size, paginas: item.paginas});
            }
        })
        .catch(()=>{})
        .finally(()=>{
            pendientes--;
            if(pendientes === 0 && migradas.length > 0){
                printer = JSON.parse(localStorage.getItem('printer'));
                printer.impresoras = migradas;
                localStorage.setItem('printer', JSON.stringify(printer));
                loadPrintersSave();
            }
        });
    });
}

function reportarImpresorasDetectadas(nombres){
    if(codRol <= 2 || !sucursal_id || sucursal_id == 0 || nombres.length === 0) return;

    fetch(`${ApiUrl}/impresoras/reportar`, {
            method: 'POST',
            headers: {'Api-Key': ApiKey},
            body: JSON.stringify({
                cod_sucursal: sucursal_id,
                estacion_id: getEstacionId(),
                nombres: nombres
            })
        })
        .catch(error => console.log(error));
}

function loadPrintersServices(){
    printer = JSON.parse(localStorage.getItem('printer'));
    // console.log(`${printer.url}/print/lista`);

    //Lista de impresoras
    loadPrintersSave();
    syncImpresorasFromApi();

    //Verificar si el servicio esta activo
    fetch(`${printer.url}/print/lista`)
        .then(res => res.json())
        .then(response => {
            // console.log(response);
            if(response.success == 1){
                impresoras = response.data;
                var template = Handlebars.compile($("#printers-form-template").html());
                $("#printer-form").html("");
                $("#printer-form").append(template(impresoras));
                updateIcons($("#iconPrinterStatus"), "check-circle", "text-success");
                setPrinterServiceStatus(true);
                reportarImpresorasDetectadas(impresoras.map(function(p){ return p.nombre; }));
            }else{
                console.log("No hay impresoras");
                updateIcons($("#iconPrinterStatus"), "x-circle", "text-danger");
                setPrinterServiceStatus(false);
            }
        })
        .catch(error=>{
            console.log(error);
            $("#printer-form").html('<div class="text-center"><h1>Servicio de impresión apagado</h1></div>');
            $("#iconPrinterStatus").removeClass();
            updateIcons($("#iconPrinterStatus"), "x-circle", "text-danger");
            setPrinterServiceStatus(false);
        });
}

function setPrinterServiceStatus(activo){
    if(activo){
        $("#printerStatusText").text("Activo");
        $("#printerTabActive").removeClass("d-none");
        $("#printerTabInactive").addClass("d-none");
        $("#printerHintInactive").addClass("d-none");
        $("#btnVerImpresion").text("Ver impresoras");
    }else{
        $("#printerStatusText").text("Apagado — abre el programa si ya lo tienes instalado");
        $("#printerTabActive").addClass("d-none");
        $("#printerTabInactive").removeClass("d-none");
        $("#printerHintInactive").removeClass("d-none");
        $("#btnVerImpresion").text("Instalar");
    }
}

function loadPrintersSave(){
    printer = JSON.parse(localStorage.getItem('printer'));
    var templateListaImpresoras = Handlebars.compile($("#printers-lista-template").html());
    if($("#officePrintersList").length){
        if(printer.impresoras && printer.impresoras.length > 0){
            $("#officePrintersList").html(templateListaImpresoras(printer));
            $("#officePrintersSection").removeClass("d-none");
        }else{
            $("#officePrintersList").html("");
            $("#officePrintersSection").addClass("d-none");
        }
    }
    if($("#txtPrintUrl").length){
        $("#txtPrintUrl").val(printer.url);
    }
    feather.replace();
}

function updateUrl(){
    let url = $("#txtPrintUrl").val();
    fetch(`${url}/print`)
        .then(res => res.json())
        .then(response => {
            // console.log(response);
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

function addPrinter(nombre, tipo){
    if(codRol <= 2){
        messageDone('Debes estar en una sesión de cajero (sucursal fija) para asignar impresoras, no en el modo de vista previa de administrador', 'error');
        return;
    }
    let estacionId = getEstacionId();
    OpenLoad("Guardando impresora...");
    fetch(`${ApiUrl}/impresoras`, {
            method: 'POST',
            headers: {'Api-Key': ApiKey},
            body: JSON.stringify({
                cod_sucursal: sucursal_id,
                estacion_id: estacionId,
                nombre: nombre,
                tipo: tipo,
                size: "80",
                paginas: 1
            })
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if(response.success == 1){
                printer = JSON.parse(localStorage.getItem('printer'));
                printer.impresoras.push({id: response.cod_impresora, nombre: nombre, paginas: 1, tipo: tipo, size: "80"});
                localStorage.setItem('printer', JSON.stringify(printer));
                notify("Impresora guardada", 'success', 2);
                loadPrintersSave();
            }else{
                messageDone(response.mensaje, 'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('No se pudo guardar la impresora', 'error');
        });
}

function testAndAssignPrinter(nombre){
    let printerInfo = [{
        id: 'test',
        nombre: nombre,
        paginas: 1,
        tipo: "CAJA",
        size: "80",
        detalle: [
            {texto: "IMPRESION TEST", tipo: "CENTER"},
            {texto: "impresion de prueba", tipo: "LEFT"},
            {texto: `Impresora: ${nombre}`, tipo: "LEFT"}
        ]
    }];

    fetch(`${printer.url}/print/v2`, {
            method: 'POST',
            body: JSON.stringify(printerInfo)
        })
        .then(res => res.json())
        .then(()=>{
            Swal.fire({
                title: '¿Imprimió correctamente?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                padding: '2em'
            }).then(function(result){
                if(result.value){
                    askPrinterType(nombre);
                }
            });
        })
        .catch(error=>{
            notify("Error: Verifica el servicio de impresion", "error", 10);
            console.log(error);
        });
}

function askPrinterType(nombre){
    Swal.fire({
        title: '¿Para qué es esta impresora?',
        input: 'select',
        inputOptions: {CAJA: 'CAJA', COCINA: 'COCINA'},
        inputPlaceholder: 'Selecciona una opción',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function(result){
        if(result.value){
            addPrinter(nombre, result.value);
        }
    });
}


function printOrder(order_id){
    let config = getConfigGestionOrdenes();
    if(!config.permisos.includes('DESKTOP_IMPRESION')){
        notify("La empresa no tiene activado el servicio de impresión, para más información contáctase con su asesor comercial", "error", 10);
        return false;
    }
    
    printer = JSON.parse(localStorage.getItem('printer'));
    let info = {
        cod_orden: order_id,
        impresoras: printer.impresoras
    }
    
    if(printer.impresoras.length === 0){
        notify("No tienes impresoras configuradas", "error", 10);
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
            // console.log(response);
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
                        // console.log(response);
                    })
                    .catch(error=>{
                        notify("Error: Verifica el servicio de impresion", "error", 10);
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

$("body").on("click", ".btnTestPrinter", function(){
    let btn = $(this).data();
    printer = JSON.parse(localStorage.getItem('printer'));

    let impresora = printer.impresoras.find(function(imp){
        return imp.id == btn.id;
    });

    if(!impresora){
        notify("No se encontró la configuración de la impresora", "error", 5);
        return;
    }

    let printerInfo = [{
        id: impresora.id,
        nombre: impresora.nombre,
        paginas: impresora.paginas,
        tipo: impresora.tipo,
        size: impresora.size,
        detalle: [
            {texto: "IMPRESION TEST", tipo: "CENTER"},
            {texto: "impresion de prueba", tipo: "LEFT"},
            {texto: `Impresora: ${impresora.nombre}`, tipo: "LEFT"},
            {texto: `Tipo: ${impresora.tipo}`, tipo: "LEFT"}
        ]
    }];

    //Enviar información al servicio de impresión
    fetch(`${printer.url}/print/v2`,{
            method: 'POST',
            body: JSON.stringify(printerInfo)
        })
        .then(res => res.json())
        .then(response => {
            notify("Impresión correcta", "success", 2);
            // console.log(response);
        })
        .catch(error=>{
            notify("Error: Verifica el servicio de impresion", "error", 10);
            console.log(error);
        });
});

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
    if(codRol <= 2){
        messageDone('Debes estar en una sesión de cajero (sucursal fija) para eliminar impresoras, no en el modo de vista previa de administrador', 'error');
        return;
    }
    fetch(`${ApiUrl}/impresoras/eliminar`, {
            method: 'POST',
            headers: {'Api-Key': ApiKey},
            body: JSON.stringify({cod_impresora: id})
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                let printers = JSON.parse(localStorage.getItem("printer"));
                printers.impresoras = $.grep(printers.impresoras, function(p) {
                    if(p.id != undefined)
                        return p.id != id;
                    else
                        return p.nombre != id;
                });
                localStorage.setItem('printer', JSON.stringify(printers));
                loadPrintersSave();
            }else{
                messageDone(response.mensaje, 'error');
            }
        })
        .catch(error=>{
            console.log(error);
            messageDone('No se pudo eliminar la impresora', 'error');
        });
}

