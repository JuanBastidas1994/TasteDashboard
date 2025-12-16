<?php
// Permitir que cualquier origen pueda acceder a la imagen (CORS)
header("Access-Control-Allow-Origin: *"); 

// Especificar el tipo de contenido (en este caso, es una imagen JPEG)
header("Content-Type: image/jpeg"); 

// Obtener la URL de la imagen pasada como parámetro en la URL del proxy
$imageUrl = $_GET['url'];  

// Obtener los datos de la imagen usando file_get_contents
$imageData = file_get_contents($imageUrl);  

// Enviar los datos de la imagen al cliente
echo $imageData;  
?>