<?php
require_once "funciones.php";

$_SESSION[name_session] = NULL;
session_destroy();
setcookie("token", "", time() - 3600, '/');
//header("location:index.php");
header("location:login.php");
ob_end_flush();

?>