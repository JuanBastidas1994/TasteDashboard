<?php
require_once "../funciones.php";
require_once "../clases/cl_productos.php";
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_contifico.php";

$session = getSession();
$ClProductos = new cl_productos();
$ClEmpresas = new cl_empresas();
$ClContifico = new cl_contifico($session["cod_empresa"]);

controller_create();

function listaProductosContifico(){
    global $session;
    global $ClContifico;
    global $ClProductos;
    extract($_GET);

    $ClContifico->getCredentials();
    if(is_string($ClContifico->API)) {
        $respContifico = lista($ClContifico->API, $page);
        if($respContifico) {
            $ingredientes = $ClProductos->getIngredientes();
            if($ingredientes) {
                foreach ($respContifico["results"] as &$prodContifico) {
                    $prodContifico["page"] = $page;
                    $prodContifico["existe"] = false;
                    foreach ($ingredientes as $ingrediente) {
                        if($ingrediente["id_contifico"] == $prodContifico["id"]) {
                            $prodContifico["existe"] = true;
                        }
                    }
                }
            }
        }
        $return['success'] = 1;
        $return['mensaje'] = "Lista de productos contífico";
        $return['data'] = $respContifico;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "Contífico no está configurado";
    return $return;
}

function lista($api, $page) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.contifico.com/sistema/api/v2/producto/?page=' . $page,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $api
    ),
    ));

    $response = json_decode(curl_exec($curl), true);
    curl_close($curl);

    return $response;
}

function saveIngrediente() {
    global $ClProductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    if(!$ClProductos->getIngrediente($ingrediente["id"])) {
        if($ClProductos->saveIngrediente($ingrediente)) {
            $return['success'] = 1;
            $return['mensaje'] = "Ingrediente guardado correctamente";
            return $return;
        }
        $return['success'] = 0;
        $return['mensaje'] = "Error al guardar el ingrediente";
        return $return;
    }
    else {
        if($ClProductos->editIngrediente($ingrediente)) {
            $return['success'] = 1;
            $return['mensaje'] = "Ingrediente editado correctamente";
            return $return;
        }
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar el ingrediente";
        return $return;
    }
}

function editUnidadMedidaIngrediente() {
    global $ClProductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    if($ClProductos->getIngrediente($ingrediente["id"])) {
        if($ClProductos->editUnidadMedidaIngrediente($ingrediente)) {
            $return['success'] = 1;
            $return['mensaje'] = "Unidad de medida editada correctamente";
            return $return;
        }
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar la medida editada";
        return $return;
    }
    else {
        $return['success'] = 0;
        $return['mensaje'] = "El ingrediente no existe";
        return $return;
    }
}

function deleteIngrediente() {
    global $ClProductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    if($ClProductos->getIngrediente($id)) {
        if($ClProductos->deleteIngrediente($id)) {
            $return['success'] = 1;
            $return['mensaje'] = "Ingrediente eliminado correctamente";
            return $return;
        }
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el ingrediente";
        return $return;
    }
    else {
        $return['success'] = 0;
        $return['mensaje'] = "El ingrediente no existe";
        return $return;
    }
}

function listaUnidadesMedidas() {
    global $ClProductos;

    $unidades = $ClProductos->getUnidadesMedidas();
    if($unidades) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de unidades de medidas";
        $return['data'] = $unidades;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay unidades de medidas";
    return $return;
}

function getIngredientes() {
    global $ClProductos;

    $ingredientes = $ClProductos->getIngredientes();
    if($ingredientes) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de ingredientes";
        $return['data'] = $ingredientes;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay ingredientes";
    return $return;
}
?>