$(document).ready(function() {
    
    InfoGuia();
    InfoTracking();
    function InfoGuia()
    {
        var guia=$("#guia").val();
        var url=$("#url_laar").val();
        $.ajax({
          url: url+'guias/v2/'+guia,
          type:'GET',
          success: function(response){
              console.log("Guia..."+response['noGuia']);
              $(".info_estado").html(response['estadoActual']);
              $(".info_num").html(response['noGuia']);
              $(".info_producto").html(response['producto']);
              $(".info_envios").html(response['numeroEnvios']);
              $(".info_peso").html(response['pesoKilos']);
              $(".info_nomC").html(response['nombreCliente']);
              
              
          },
          error: function(data){
              notify("Error ..", "success", 2);
          }
        });
    }
    
    function InfoTracking()
    {
        var guia=$("#guia").val();
        var url=$("#url_laar").val();
        $.ajax({
          url: url+'guias/'+guia+'/tracking',
          type:'GET',
          success: function(response){
              var object = (response);
              var html_="";
              var x = 0;
              for (const property in object) {
                  console.log(`${property}: ${object[property]['nombre']}`);
                  var estado=`${object[property]['nombre']}`;
                  var d="--";
                  var hora ="--";
                  var fecha="0000-00-00";
                  if(object[property]['fecha'] != null )
                  {
                      d=new Date(object[property]['fecha']);
                      hora=d.toLocaleTimeString();
                      fecha=d.getFullYear() +"-"+ d.getMonth()+"-"+d.getDate();
                      
                  }
                  
                  html_ +='<li>'+
                  '<div class="modern-timeline-badge"></div>'+
                                        '<div class="modern-timeline-panel">'+
                                            '<div class="modern-timeline-preview"><img src="assets/img/tracking'+x+'.png" alt="timeline"></div>'+
                                            '<div class="modern-timeline-body">'+
                                                '<h4 class="mb-2">'+estado+'</h4> '+
                                                '<p class="t_recolectarF"><strong>Fecha: </strong> '+fecha+'</p>'+
                                                '<hr>'+
                                                '<p class="t_recolectarH"><strong>Hora: </strong> '+hora+'</p>'+
                                            '</div>'+
                                        '</div>'+
                                    '</li>';
                    x++;                
                }
                $("#info-timeline").html(html_); 
          },
          error: function(data){
              notify("Error ..", "success", 2);
          }
        });
    }

    if($("#mapa").length){
        var mapa = document.getElementById("mapa");
        var latitud = parseFloat(mapa.getAttribute("data-latitud"));
        var longitud = parseFloat(mapa.getAttribute("data-longitud"));
        pos = {lat: latitud, lng: longitud};
        var map = new google.maps.Map(mapa, {
          zoom: 14,
          center: pos
        });

        marker = new google.maps.Marker({
          position: pos,
          map: map,
          id: 15
        });
      }

    $("#btnBack").on("click",function(){
        var link = $(this).attr("data-module-back");
        if (typeof link === "undefined") {
          link = "index.php";
        }else{
            window.location.href = link;
        }
      }); 
      
    $(".btnNotificar").on("click",function(){
         var $this = $(this);
        var usuario = $this.data("usuario");
        var texto = $("#demo1").val();
        if(texto == "")
        {
            notify("Campo obligatorio", "success", 2);
            return;
        }
        //ENVIAR NOTIFICACION
        $.ajax({
          url: 'controllers/controlador_notificaciones.php?metodo=notificarUsuario&usuario='+usuario+'&texto='+texto,
          type:'GET',
          success: function(response){
              console.log(response);
              notify(response['mensaje'], "success", 2);
          },
          error: function(data){
              notify("Error al enviar la notificacion, intenta nuevamente", "success", 2);
          }
        });

        
      });   

});