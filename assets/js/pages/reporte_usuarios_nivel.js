$(document).ready(function(){
    //$("#cmb_niveles").trigger("change");
});

$("body").on("change", "#cmb_niveles2", function(){
    let cod_nivel = $(this).val();

    var myTable = $('#style-3').DataTable();
    myTable.destroy();
    $("#bloqueClientes tr").remove();

    var parametros = {
        "cod_nivel": cod_nivel
    }
    $.ajax({
        beforeSend: function(){
            OpenLoad("Obteniendo datos...");
        },
        url:'controllers/controlador_reporte_usuarios_nivel.php?metodo=getDatos',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#bloqueClientes").html(response['html']);
            }
            else{
                messageDone(response['mensaje'], "error");
            }
            initDT($('#style-3'));
        },
        error: function(data){
        },
        complete: function(){
            CloseLoad();
        },
    });
});

function initDT(elemento){
    elemento.DataTable( {
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
            "sInfoEmpty": "Mostrando pag. 1",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Buscar...",
           "sLengthMenu": "Resultados :  _MENU_",
           "sEmptyTable": "No se encontraron resultados",
           "sZeroRecords": "No se encontraron resultados",
           "buttons": {
                "copy": "Copiar",
                "csv": "CSV",
                "excel": "Excel",
                "pdf": "PDF",
                "print": "Imprimir",
                "create": "Crear",
                "edit": "Editar",
                "remove": "Remover",
                "upload": "Subir"
            }
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 10
    } );
}

$("body").on("change", "#cmb_niveles", function(){
    let cod_nivel = $(this).val();

    $('#style-3').dataTable().fnClearTable();
    $('#style-3').dataTable().fnDestroy();

    var tb = $('#style-3').dataTable({
        processing: true,
        serverSide: true,
        dom: 'Bfrtip',
        pageLength: 1000,
        ordering: true,
        order: [[3, "desc"]],
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        columns: [
            null,
            null,
            null,
            {type: 'numeric'},
            {type: 'numeric'},
            {type: 'numeric'},
            null
        ],
        ajax: {
            url:'controllers/controlador_reporte_usuarios_nivel.php?metodo=getDatosDatatables',
            type:'GET',
            data: {cod_nivel},
            error: function(e){
                console.log(e);
            },
            complete: function(){
                feather.replace();
            }
        }
    });
});