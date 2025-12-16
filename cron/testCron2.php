<?php

/* GUARDAR LOGS */
$folder = "logs";
if (!file_exists($folder)) {
    mkdir($folder, 0777);
}
$file = $folder."/test.log";
$fecha = date("Y-m-d H:i:s");
$log = "[$fecha] Se ejecutó el cronjob 2 de pruebas";
file_put_contents($file, PHP_EOL . $log, FILE_APPEND);
?>