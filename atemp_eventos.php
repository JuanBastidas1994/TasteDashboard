<?php
    $fecha = "2022-02-14";
    $eventos = [];
    $item = [];
    $item['title'] = "Titulo";
    $item['category'] = "Titulo";
    $item['image'] = "Titulo";
    $item['date'] = $fecha;
    $item['month'] = date('m', strtotime($fecha));
    $item['year'] = date('y', strtotime($fecha));
    $item['day'] = date('d', strtotime($fecha));
    $item['start_time'] = "10:00:00";
    $item['end_time'] = "12:00:00";
    $item['trainer'] = "Davis C.";
    $item['color'] = "1";
    $item['content'] = "Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum ";
    
    $eventos['items'][] = $item;
    $eventos['success'] = 1;
    $eventos['mensaje'] = "datos obtenidos";

    header('Content-Type: application/json');
    echo json_encode($eventos);
?>