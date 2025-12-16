let idTimeline = "";
$(function(){
    let urlParams = new URLSearchParams(window.location.search);
    idTimeline = urlParams.get("prd");
    if(idTimeline == null)
        window.location.href = "index.php";
    obtenerTimelines();
});

function obtenerTimelines(){
    let parametros = {
        alias: idTimeline
    }
    $.ajax({
       url:'controllers/controlador_timeline.php?metodo=obtenerLista',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
            let lstTimeline = '';
            let data = response.data;
            for (let i = 0; i < data.length; i++) {
                let estado = "Activo";
                let badge = "primary";
                if(data[i].estado != "A"){
                    estado = "Inactivo";
                    badge = "danger";
                }
                lstTimeline+= ` <tr>
                                    <td>${data[i].nombre}</td>
                                    <td class="text-center">
                                        <span class="shadow-none badge badge-${badge}">${estado}</span>
                                    </td>
                                    <td>
                                        <ul class="table-controls text-center">
                                            <li>
                                                <a href="crear_timeline.php?id=${data[i].cod_timeline}&alias=${idTimeline}" target="_blank">
                                                    <i data-feather="edit-2"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="btnEliminar" data-id="${data[i].cod_timeline}">
                                                    <i data-feather="trash-2"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>`;
            }
            $("#tbody").html(lstTimeline);
            feather.replace();
          }
          else{
            messageDone(response.mensaje, "error");
          }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
}

$("body").on("click", ".btnEliminar", function(){
    let id = $(this).data("id");
    
    Swal.fire({
        title: '¿Está seguro?',
        text: 'No podrá revertir esto',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function(result){
        if (result.value) {
            eliminarTimeline(id);
        }
    }); 
});

function eliminarTimeline(id){
    let parametros = {
        id: id
    }
    $.ajax({
       url:'controllers/controlador_timeline.php?metodo=eliminar',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
            messageDone(response.mensaje, "success");
          }
          else{
            messageDone(response.mensaje, "error");
          }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
}