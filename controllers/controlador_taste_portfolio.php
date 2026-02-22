<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_taste_portfolio.php";

$Clportfolio = new cl_taste_portfolio();

controller_create();

function getListRestaurants() {
    global $Clportfolio;

    $resp = $Clportfolio->getPortfolio();

    if (!$resp) {
        $return["success"] = 0;
        $return["mensaje"] = "No hay portfolios registrados";
        return $return;
    }

    foreach ($resp as $key => $value) {
        $resp[$key]['categories'] = json_decode($value['categories'], true);
        $resp[$key]['cities'] = json_decode($value['cities'], true);
    }

    $return["success"] = 1;
    $return["mensaje"] = "Portfolio Lista";
    $return["data"] = $resp;

    return $return;
}

function getRestaurant() {
    global $Clportfolio;
    extract($_GET);

    if (!isset($cod_taste_portfolio)) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    $Clportfolio->cod_taste_portfolio = $cod_taste_portfolio;
    $resp = $Clportfolio->getPortfolioById();

    if (!$resp) {
        $return["success"] = 0;
        $return["mensaje"] = "No hay detalle del portfolio";
        return $return;
    }

    $resp['categories'] = json_decode($resp['categories'], true);
    $resp['cities'] = json_decode($resp['cities'], true);

    $return["success"] = 1;
    $return["mensaje"] = "Portfolio detalle";
    $return["data"] = $resp;

    return $return;
}

function saveRestaurant() {
    global $Clportfolio;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    if (
        !isset($cod_empresa) || 
        !isset($name) || 
        !isset($path) || 
        !isset($categories) || 
        !isset($cities) || 
        !isset($cod_taste_portfolio)
    ) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    if (isset($_FILES['image'])) {
        $nombre = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        // $newPath = $nombre . "?no_cache= " . uniqid(); // Genera un nombre único para evitar colisiones  

        // move_uploaded_file($tmp, "uploads/" . $nombre); TODO: Cambiar ruta de subida
    }

    $Clportfolio->cod_empresa = $cod_empresa;
    $Clportfolio->path = $newPath ? $newPath : $path;
    $Clportfolio->categories = $categories;
    $Clportfolio->cities = $cities;
    $Clportfolio->cod_taste_portfolio = $cod_taste_portfolio;

    if ($cod_taste_portfolio == 0)
        $Clportfolio->createRestaurant();
    else
        $Clportfolio->updateRestaurant();

    $return["success"] = 1;
    $return["mensaje"] = "Restaurant actualizado";

    return $return;
}


function getRestaurantWithoutPortfolio() {
    global $Clportfolio;

    $resp = $Clportfolio->getRestauranWithoutPortfolio();

    if (!$resp) {
        $return["success"] = 0;
        $return["mensaje"] = "No hay restaurantes disponibles para agregar al portfolio";
        return $return;
    }

    $return["success"] = 1;
    $return["mensaje"] = "Restaurantes sin portfolio";
    $return["data"] = $resp;

    return $return;
}