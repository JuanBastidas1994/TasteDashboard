<?php
require_once "../funciones.php";
require_once "../clases/cl_tarifas.php";

$Cltarifas = new cl_tarifas();
$session   = getSession();

controller_create();

/* ══════════════════════════════════════════════
 *  TARIFAS
 * ══════════════════════════════════════════════ */

/** GET  ?metodo=getTarifas&cod_sucursal=X
 *  Devuelve todas las tarifas de una sucursal con sus rangos anidados */
function getTarifas()
{
    global $Cltarifas;

    if (!isset($_GET['cod_sucursal'])) {
        return ['success' => 0, 'mensaje' => 'Falta cod_sucursal'];
    }

    $cod_sucursal = intval($_GET['cod_sucursal']);
    $tarifas      = $Cltarifas->getByOficina($cod_sucursal);

    // Adjuntar rangos a cada tarifa
    if ($tarifas) {
        foreach ($tarifas as &$tarifa) {
            $tarifa['rangos'] = $Cltarifas->getRangos($tarifa['cod_tarifa']) ?: [];
        }
    } else {
        $tarifas = [];
    }

    return ['success' => 1, 'mensaje' => 'Tarifas', 'data' => $tarifas];
}

/** POST  ?metodo=saveTarifa
 *  Body JSON: { cod_sucursal, nombre, peso_max_kg (opcional) }
 *  Crea o edita una tarifa (si viene cod_tarifa > 0 edita) */
function saveTarifa()
{
    global $Cltarifas;

    $POST = json_decode(file_get_contents('php://input'), true);
    if (!$POST || !isset($POST['cod_sucursal']) || !isset($POST['nombre'])) {
        return ['success' => 0, 'mensaje' => 'Faltan datos (cod_sucursal, nombre)'];
    }

    $Cltarifas->cod_sucursal = intval($POST['cod_sucursal']);
    $Cltarifas->nombre       = trim($POST['nombre']);

    $peso = isset($_POST['peso_max_kg']) ? floatval($_POST['peso_max_kg']) : null;
    $Cltarifas->peso_max_kg = ($peso !== null && $peso != 0) ? $peso : null;

    $cod_tarifa = isset($POST['cod_tarifa']) ? intval($POST['cod_tarifa']) : 0;

    if ($cod_tarifa > 0) {
        // EDITAR
        $Cltarifas->cod_tarifa = $cod_tarifa;
        if ($Cltarifas->editar()) {
            return ['success' => 1, 'mensaje' => 'Tarifa actualizada', 'cod_tarifa' => $cod_tarifa];
        }
        return ['success' => 0, 'mensaje' => 'Error al actualizar tarifa'];
    } else {
        // CREAR
        $id = 0;
        if ($Cltarifas->crear($id)) {
            return ['success' => 1, 'mensaje' => 'Tarifa creada', 'cod_tarifa' => $id];
        }
        return ['success' => 0, 'mensaje' => 'Error al crear tarifa'];
    }
}

/** GET  ?metodo=removeTarifa&cod_tarifa=X
 *  Elimina tarifa y sus rangos en cascada */
function removeTarifa()
{
    global $Cltarifas;

    if (!isset($_GET['cod_tarifa'])) {
        return ['success' => 0, 'mensaje' => 'Falta cod_tarifa'];
    }

    $resp = $Cltarifas->eliminar(intval($_GET['cod_tarifa']));
    if ($resp) {
        return ['success' => 1, 'mensaje' => 'Tarifa eliminada'];
    }
    return ['success' => 0, 'mensaje' => 'Error al eliminar tarifa'];
}

/* ══════════════════════════════════════════════
 *  RANGOS
 * ══════════════════════════════════════════════ */

/** POST  ?metodo=saveRangos
 *  Body JSON: { cod_tarifa, rangos: [{id, distancia_ini, distancia_fin, precio}] }
 *  Guarda/edita todos los rangos de una tarifa (mismo comportamiento que antes) */
function saveRangos()
{
    global $Cltarifas;

    $POST = json_decode(file_get_contents('php://input'), true);
    if (!$POST || !isset($POST['cod_tarifa']) || !isset($POST['rangos'])) {
        return ['success' => 0, 'mensaje' => 'Faltan datos (cod_tarifa, rangos)'];
    }

    $cod_tarifa = intval($POST['cod_tarifa']);

    if($cod_tarifa == 0){        
        return ['success' => 0, 'mensaje' => 'Tarifa aun no creada'];
    }

    $rangos     = $POST['rangos'];
    $total      = count($rangos);

    $Cltarifas->cod_tarifa = $cod_tarifa;

    foreach ($rangos as $key => $rango) {
        $distanciaIni = $rango['distancia_ini'];
        $distanciaFin = $rango['distancia_fin'];
        $precio       = $rango['precio'];

        // Primer rango siempre inicia en 0, último termina en 50
        if ($key === 0)          $distanciaIni = 0;
        if ($key === $total - 1) $distanciaFin = 50;

        $Cltarifas->id            = intval($rango['id']);
        $Cltarifas->distancia_ini = floatval($distanciaIni);
        $Cltarifas->distancia_fin = floatval($distanciaFin);
        $Cltarifas->precio        = floatval($precio);

        if ($Cltarifas->id === 0) {
            $Cltarifas->saveRango();
        } else {
            $Cltarifas->editRango();
        }
    }

    return ['success' => 1, 'mensaje' => 'Rangos guardados correctamente'];
}

/** GET  ?metodo=removeRango&id=X */
function removeRango()
{
    global $Cltarifas;

    if (!isset($_GET['id'])) {
        return ['success' => 0, 'mensaje' => 'Falta ID del rango'];
    }

    $resp = $Cltarifas->removeRango(intval($_GET['id']));
    if ($resp) {
        return ['success' => 1, 'mensaje' => 'Rango eliminado'];
    }
    return ['success' => 0, 'mensaje' => 'Error al eliminar rango'];
}
?>
