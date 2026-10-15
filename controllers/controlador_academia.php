<?php
/*
 * MIGRACIÓN — ejecutar una vez en la BD:
 *
 * CREATE TABLE IF NOT EXISTS `tb_academia_tutoriales` (
 *   `cod_tutorial` INT AUTO_INCREMENT PRIMARY KEY,
 *   `titulo`       VARCHAR(200) NOT NULL,
 *   `descripcion`  TEXT,
 *   `youtube_id`   VARCHAR(50),
 *   `duracion`     VARCHAR(10),
 *   `fecha`        DATE,
 *   `categoria`    VARCHAR(60),
 *   `estado`       CHAR(1) DEFAULT 'A',
 *   `posicion`     INT DEFAULT 0
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 */

require_once "../funciones.php";
$session = getSession();

controller_create();

/* ---- listar todos (para el admin DataTable) ---- */
function lista() {
    $query = "SELECT * FROM tb_academia_tutoriales ORDER BY posicion ASC, cod_tutorial ASC";
    $resp   = Conexion::buscarVariosRegistro($query);
    return ['success' => 1, 'data' => $resp ?: []];
}

/* ---- listar activos (para academia.php público) ---- */
function listaActivos() {
    $query = "SELECT * FROM tb_academia_tutoriales WHERE estado = 'A' ORDER BY posicion ASC, cod_tutorial ASC";
    $resp   = Conexion::buscarVariosRegistro($query);
    return ['success' => 1, 'data' => $resp ?: []];
}

/* ---- obtener uno ---- */
function get() {
    if (!isset($_GET['cod_tutorial'])) return ['success' => 0, 'mensaje' => 'Falta información'];
    $id   = intval($_GET['cod_tutorial']);
    $resp = Conexion::buscarRegistro("SELECT * FROM tb_academia_tutoriales WHERE cod_tutorial = ?", [$id]);
    if ($resp) return ['success' => 1, 'data' => $resp];
    return ['success' => 0, 'mensaje' => 'Tutorial no encontrado'];
}

/* ---- crear ---- */
function crear() {
    if (empty($_POST)) return ['success' => 0, 'mensaje' => 'Falta información'];
    extract($_POST);

    $titulo      = htmlspecialchars(trim($titulo ?? ''));
    $descripcion = htmlspecialchars(trim($descripcion ?? ''));
    $youtube_id  = preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($youtube_id ?? ''));
    $duracion    = htmlspecialchars(trim($duracion ?? ''));
    $fecha       = $fecha ?? date('Y-m-d');
    $categoria   = htmlspecialchars(trim($categoria ?? ''));
    $estado      = ($estado ?? 'A') === 'A' ? 'A' : 'I';

    $posMax = Conexion::buscarRegistro("SELECT COALESCE(MAX(posicion),0)+1 AS pos FROM tb_academia_tutoriales");
    $posicion = $posMax ? intval($posMax['pos']) : 1;

    $query = "INSERT INTO tb_academia_tutoriales (titulo, descripcion, youtube_id, duracion, fecha, categoria, estado, posicion)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if (Conexion::ejecutar($query, [$titulo, $descripcion, $youtube_id, $duracion, $fecha, $categoria, $estado, $posicion])) {
        return ['success' => 1, 'mensaje' => 'Tutorial creado correctamente'];
    }
    return ['success' => 0, 'mensaje' => 'Error al crear el tutorial'];
}

/* ---- actualizar ---- */
function actualizar() {
    if (empty($_POST)) return ['success' => 0, 'mensaje' => 'Falta información'];
    extract($_POST);

    $cod_tutorial = intval($cod_tutorial ?? 0);
    $titulo       = htmlspecialchars(trim($titulo ?? ''));
    $descripcion  = htmlspecialchars(trim($descripcion ?? ''));
    $youtube_id   = preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($youtube_id ?? ''));
    $duracion     = htmlspecialchars(trim($duracion ?? ''));
    $fecha        = $fecha ?? date('Y-m-d');
    $categoria    = htmlspecialchars(trim($categoria ?? ''));
    $estado       = ($estado ?? 'A') === 'A' ? 'A' : 'I';

    $query = "UPDATE tb_academia_tutoriales SET titulo=?, descripcion=?, youtube_id=?, duracion=?, fecha=?, categoria=?, estado=? WHERE cod_tutorial=?";
    if (Conexion::ejecutar($query, [$titulo, $descripcion, $youtube_id, $duracion, $fecha, $categoria, $estado, $cod_tutorial])) {
        return ['success' => 1, 'mensaje' => 'Tutorial actualizado correctamente'];
    }
    return ['success' => 0, 'mensaje' => 'Error al actualizar el tutorial'];
}

/* ---- eliminar ---- */
function eliminar() {
    if (!isset($_GET['cod_tutorial'])) return ['success' => 0, 'mensaje' => 'Falta información'];
    $id = intval($_GET['cod_tutorial']);
    if (Conexion::ejecutar("DELETE FROM tb_academia_tutoriales WHERE cod_tutorial = ?", [$id])) {
        return ['success' => 1, 'mensaje' => 'Tutorial eliminado correctamente'];
    }
    return ['success' => 0, 'mensaje' => 'Error al eliminar el tutorial'];
}

/* ---- reordenar (drag & drop) ---- */
function reordenar() {
    if (!isset($_POST['orden'])) return ['success' => 0, 'mensaje' => 'Falta información'];
    $orden = json_decode($_POST['orden'], true);
    foreach ($orden as $posicion => $cod_tutorial) {
        Conexion::ejecutar(
            "UPDATE tb_academia_tutoriales SET posicion = ? WHERE cod_tutorial = ?",
            [intval($posicion) + 1, intval($cod_tutorial)]
        );
    }
    return ['success' => 1, 'mensaje' => 'Orden actualizado'];
}
