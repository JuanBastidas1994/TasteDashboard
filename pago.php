<?php
require_once "funciones.php";
require_once "clases/cl_payphone.php";
//require_once "clases/cl_contifico.php";

/*PAYPHONE*/
$payphone = new cl_payphone(NULL);


echo $payphone->lst_regiones();


$mensaje = "";
$payphone->cod_usuario = 2;
$payphone->cedula = "0952423606";
$payphone->titular = "Juan Bastidas";
$payphone->phone = "0979393146";
$payphone->email = "juankbastidasjuve@gmail.com";
$payphone->cardNumber = "5144400023309006";
$payphone->mes = "03";
$payphone->year = "23";
$payphone->CVV = "123";
$payphone->impuestos = 0.12;
$payphone->subtotal = 0.88;
$payphone->total = 1;
if($payphone->PayCart($mensaje)){
	echo 'Cobro correctamente, Mensaje: '.$mensaje;
}else{
	echo 'Error al cobrar, Mensaje: '.$mensaje;
}


/*CONTIFICO*/
/*
echo '<br>CONTIFICO<br>';
$contifico = new cl_contifico(NULL);
$lstProductos = $contifico->LstProductos();
var_dump($lstProductos);
*/
?>