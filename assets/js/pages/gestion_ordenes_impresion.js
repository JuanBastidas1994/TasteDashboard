var printer = {};
let impresoras = [];

/*CONFIG PUERTO IMPRESORA*/
if (JSON.parse(localStorage.getItem('printer')) === null) {
    printer.url = "http://localhost:8890";
    printer.puerto = "8890";
    printer.impresoras = [];
    localStorage.setItem('printer', JSON.stringify(printer));
}

function openImpresion(){
    $('#ImpresionModal').modal();

    printer = JSON.parse(localStorage.getItem('printer'));
    $("#txtPrintUrl").val(printer.url);
    $("#txtPrintPuerto").val(printer.puerto);
    loadPrintersServices();
    loadPrintersSave();
}

function loadPrintersServices(){
    printer = JSON.parse(localStorage.getItem('printer'));

    fetch(`${printer.url}/print/lista`)
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                impresoras = response.data;
                for(var x=0; x<impresoras.length; x++){
                    let nombre = impresoras[x]['nombre'];
                    $("#cmbLstImpresoras").append(`<option value="${nombre}">${nombre}</option>`);
                }
                $(".printImpresora").val($("#cmbLstImpresoras").val());
            }else{
                
            }
        })
        .catch(error=>{
            console.log(error);
        });
}

function loadPrintersSave(){
    var templatenodata = Handlebars.compile($("#no-impresoras").html());
    var templateListaImpresoras = Handlebars.compile($("#lista-impresoras").html());

    printer = JSON.parse(localStorage.getItem('printer'));
    if(printer.impresoras.length > 0){
        $(".LstImpresorasSave").html(templateListaImpresoras(printer.impresoras));
    }else{
        $(".LstImpresorasSave").html(templatenodata({mensaje: "No tienes impresoras configuradas"}));
    }
}

$("#cmbLstImpresoras").on("change", function(){
    $(".printImpresora").val($("#cmbLstImpresoras").val());
});

$(".btnAddImpresora").on("click", function(){
    if($(".printImpresora").val().trim().length === ""){
        messageDone('Debes ingresar la comunicación con la impresora','error');
    }

    let item = {
        nombre: $(".printImpresora").val(),
        paginas: $(".printPaginas").val(),
        tipo: $(".printTipo").val(),
        size: $(".printPapel").val(),
    }

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
    }
    loadPrintersSave();
});


function printOrdenV2(id){
    let printer = JSON.parse(localStorage.getItem('printer'));

    let info = {
        id: id,
        impresoras: printer.impresoras
    }
    $.ajax({
        url: 'controllers/controlador_gestion_impresion.php?metodo=getOrden',
        type: 'POST',
        data: info,
        success: function(response){
            console.log("getOrden", response);
            if( response['success'] == 1){
                let json = JSON.stringify(response.impresoras);
                console.log(json);
                console.log(`${printer.url}/print/v2`);
                $.ajax({
                    type: "POST",
                    url: `${printer.url}/print/v2`,
                    dataType: "json",
                    data: json,
                    success: function(response){
                        console.log("SUCCESS IMPRESION",response);
                        notify("Impresión correcta", "success", 2);
                    },error: function(data){
                        console.log("Servicio de impresion apagado");
                        notify("Error: Verifica el servicio de impresion", "error", 2);
                        console.log(data);
                    }
                });
            }
            else{
                notify(response['mensaje'], "error", 2);
            }
        },
        error: function(data){
          console.log("ERROR IMPRESION",data);
        }
    });
}

$("body").on("click", ".printOrdenV2", function(){
      printOrdenV2($(this).data("value"));
});