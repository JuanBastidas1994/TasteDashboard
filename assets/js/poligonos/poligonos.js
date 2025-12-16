let nextId = 0;
let permitDraggable = false;

function addPolygon(coordenadas, office){
    nextId = nextId + 1;
    /* Construir un poligono */
    const polygon = new google.maps.Polygon({
        id: nextId,
        office_id: parseInt(office.cod_sucursal),
        office_name: office.nombre,
        paths: coordenadas,
        strokeColor: office.color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: office.color,
        fillOpacity: 0.50,
        editable: true,
        draggable: permitDraggable,
        geodesic: true,
    });
    
    polygon.setMap(map);
    polygon.addListener("click", onGetPolygon);

    poligonos.push(polygon);
}

function getPolygon(polygon){
    //console.log(polygon);
    const positions = [];
    const vertices = polygon.getPath();
    for (let i = 0; i < vertices.getLength(); i++) {
        const xy = vertices.getAt(i);
        positions.push(`${xy.lat()} ${xy.lng()}`);
    }

    //Verificar si la ultima posición cierra el polígono, si no lo hace, corregir
    if(positions[0] !== positions[positions.length - 1])
        positions.push(positions[0]);
    
    return positions;
}

function deletePolygon(polygon){
    let id = polygon.id;
    poligonos = poligonos.filter((polygon) => polygon.id !== id);
    polygon.setMap(null);
}


/*
SELECT o.*, ST_AsGeoJSON(o.zone) as vertices FROM tb_sucursal_cobertura o;
*/