let map;
let poligonos = [];
let polygonSelected = null;
let officeSelected = null;
let isActiveClickNewPolygon = false;

$(function() {
    initMap();
    // getSucursales();
});

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: { lat: -2.1753280, lng: -79.90624 },
        mapTypeControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        //disableDefaultUI: true,
        gestureHandling: "greedy", //Permite hacer zoom con el scroll
    });

    map.addListener("click", function(event){
        createPolygonClick(event.latLng);
    });
}

//Crear un poligono mediante click
function createPolygonClick(position){
    if(!isActiveClickNewPolygon) return;
    disableNewPolygon();

    let lat = position.lat();
    let lng = position.lng();

    const coordenadas = [
        { lat: lat - 0.00200, lng: lng - 0.00200 }, //Izquierda abajo
        { lat: lat - 0.00200, lng: lng + 0.00200 }, //Izquierda arriba
        { lat: lat + 0.00200, lng: lng + 0.00200 }, //Derecha arriba
        { lat: lat + 0.00200, lng: lng - 0.00200 }, //Derecha abajo
        { lat: lat - 0.00200, lng: lng - 0.00200 }, //Izquierda abajo
    ];
    addPolygon(coordenadas, officeSelected);
}

//Funcion de test para conseguir todas las coordenadas
function getCoordenadas(){
    let info = [];
    poligonos.map((polygon) => {
        let vertices = getPolygon(polygon);
        info.push({
            office_id: polygon.office_id,
            vertices
        })
    });
    console.log(info);
    return info;
}

//Click en un polígono
function onGetPolygon(){
    polygonSelected = this;
    console.log(polygonSelected);
    
    let vertices = getPolygon(polygonSelected);
    polygonSelected.setOptions({
        strokeColor: "red"
    });

    $("#polygonDetailModal .vertices").html("");
    vertices.map((vertice) => {
        $("#polygonDetailModal .vertices").append(`<li>${vertice}</li>`);
    })

    $("#polygonDetailModal .title").html(polygonSelected.office_name);
    $("#polygonDetailModal").modal();
}

//Click al eliminar polígon
function onDeletePolygon(){
    if(polygonSelected !== null){
        deletePolygon(polygonSelected);
        $("#polygonDetailModal").modal('hide');
        polygonSelected = null;
    }else{
        alert("No hay poligono seleccionado");
    }
}

function activateNewPolygon(office_id){
    $(".alert-box-poligono").show();
    isActiveClickNewPolygon = true;

    officeSelected = getOfficeById(office_id);
    console.log(officeSelected);
}

function disableNewPolygon(){
    $(".alert-box-poligono").hide();
    isActiveClickNewPolygon = false;
}

$('#polygonDetailModal').on('hidden.bs.modal', function (e) {
    console.log("SE CERRO POLIGONO DETALLE");
    if(polygonSelected !== null){
        polygonSelected.setOptions({
            strokeColor: polygonSelected.fillColor
          });
        polygonSelected = null;
    }
})

function activateDrag(checkbox){
    console.log(checkbox);
    console.log(checkbox.checked);
    
    permitDraggable = checkbox.checked;
    poligonos.map((polygon) => {
        polygon.setOptions({
            draggable: checkbox.checked
        });
    });
}

function changeModeZoom(){
    var elem = document.getElementById("bloqueZoom");
    
    if(document.fullscreenElement === null){ //ENTRAR A MODO ZOOM
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        }
    }else{                                  //SALIR DE MODO ZOOM
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

document.addEventListener("fullscreenchange", function() {
    let $btnZoom = $("#btnZoom");
    console.log(document.fullscreenElement);
    if(document.fullscreenElement !== null){
        console.log("ENTRO A MODO ZOOM");
        $btnZoom.html(`<i data-feather="minimize"></i> Minimizar`);
        feather.replace();
    }else{
        console.log("SALIO DEL MODO ZOOM");
        $btnZoom.html(`<i data-feather="maximize"></i> Maximixar`);
        feather.replace();
    }
});