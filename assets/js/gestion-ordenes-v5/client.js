$(function() {
    
});

function getUserById(user_id, orden_id, openTab){
    $("#clientModal").modal().data("ordenId", orden_id);

    fetch(`${ApiUrl}/usuarios/${user_id}`,{
            method: 'GET',
            headers: {
            'Api-Key':ApiKey
            }
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                let user = response.data;
                var template = Handlebars.compile($("#client-detail-template").html());
                $("#client-detail").html(template(user));

                $(".textRecordatorio").emojioneArea({
                    container: ".containerEmoji",
                    hideSource: false,
                });

                feather.replace();

                if(openTab){
                    $(".client-detail-options[data-target='"+openTab+"']").trigger("click");
                }
            }else{

            }
                //reject(response.mensaje);
        })
        .catch(error=>{
            console.log(error);
            //reject('Ocurrió un error al obtener información de la orden');
        });
}

$("body").on("click", ".client-detail-options", function(){
    let $item = $(this);

    $(".client-detail-options").removeClass("active");
    $item.addClass("active");

    $(".client-tab-details").hide();
    $("#"+$item.data("target")).show();
});