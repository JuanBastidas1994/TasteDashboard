$(document).ready(function() {
    getSucursales();
    getCotizaciones();
});

function getSucursales() {
    fetch(`controllers/controlador_cotizacion_precio.php?metodo=getSucursales`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            let template = Handlebars.compile($("#sucursales-template").html());
            $("#cmbSucursal").append(template(response.data));
        }
    })
    .catch(error => {
        console.log(error);
    });
}

function getCotizaciones() {
    let sucursal = $("#cmbSucursal").val();

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_cotizacion_precio.php?metodo=getCotizaciones&sucursal=${sucursal}`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            let template = Handlebars.compile($("#cotizaciones-template").html());
            pintarFilas(template(response.data));
        }
        else{
            pintarFilas("");
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error => {
        pintarFilas("");
        notify("Error al realizar la petición", "error", 2);
        CloseLoad();
        console.log(error);
    });
}

// Devuelve la instancia de DataTables, inicializándola una sola vez. Pasar opciones de nuevo
// a una tabla ya inicializada tira error, por eso siempre se re-obtiene sin argumentos.
function getDataTable() {
    if($.fn.DataTable.isDataTable('#style-3')){
        return $('#style-3').DataTable();
    }
    let tabla = $('#style-3').DataTable({
        dom: 'rtip',
        stripeClasses: [],
        lengthMenu: [7, 10, 20, 50],
        pageLength: 20,
        order: []
    });
    tabla.on('draw.dt', function(){
        feather.replace();
        $('[data-toggle="tooltip"]').tooltip();
    });
    return tabla;
}

// Mezclar manipulación directa del DOM con la API de DataTables rompe la tabla (ver
// facturas_pendientes.js), por eso se usa clear() + rows.add() + draw().
function pintarFilas(filasHtml) {
    let tabla = getDataTable();
    tabla.clear();
    let $filas = $('<table>').html(filasHtml).find('tr');
    if($filas.length > 0){
        tabla.rows.add($filas);
    }
    tabla.draw();
}

$("body").on("click", ".btnCopiar", function(){
    let valor = $(this).data("copy");
    navigator.clipboard.writeText(valor).then(function(){
        notify("Copiado al portapapeles", "success", 1);
    }).catch(function(){
        notify("No se pudo copiar", "error", 2);
    });
});
