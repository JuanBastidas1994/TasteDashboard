<?php
require_once "funciones.php";
if(!isLogin()){
    header("location:login.php");
}
header("location:gestion_ordenes_v5.php");
?>