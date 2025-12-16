<?php

//Campo pos de pruebas: c47b15bc-a027-43f7-9dd4-35d3ef5a670f
//id producto Pruebas -> GEjb2g6OpfzydVNW

/*
CLAVES PRODUCCION CONTIFICO
API KEY
9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4

API TOKEN (CAMPO POS)
dc323986-9aa2-4eab-93a5-31b45e60bf09

ID PRODUCTO -> oLqdPML26IKZEel4
*/
error_reporting(0);
include("webServices/conexion.php");
$con = conectar();

function GetToken($facturacion,$emisor)
{
    if($facturacion == "pruebas")
    {
    	$pos = "c47b15bc-a027-43f7-9dd4-35d3ef5a670f";
    }
    else
    {
    	$categoria = "RYWb44Z7UB6KbZ1m";
    	if($emisor=="001")		//URDESA
		return "dc323986-9aa2-4eab-93a5-31b45e60bf09";
	   else if($emisor == "002")	//SAMBO
		  return "a9895323-f987-4892-9123-b2325e533da6"; 	
	   else				//OTRA
		  return "dc323986-9aa2-4eab-93a5-31b45e60bf09";
    }
}

function LstProductos()
{
    $ch = curl_init("https://contifico.com/sistema/api/v1/producto/");
    $json = NULL;
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.'9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4'; // key here
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function LstCategoria()
{
    $ch = curl_init("https://contifico.com/sistema/api/v1/categoria/");
    $json = NULL;
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.'9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4'; // key here
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function LstDocumento($ID)
{
echo "https://contifico.com/sistema/api/v1/documento/".$ID;
    $ch = curl_init("https://contifico.com/sistema/api/v1/documento/".$ID);
    $json = NULL;
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    //$headers[] = 'Authorization: '.'9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4'; // key here 
$headers[] = 'Authorization: '.'FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U';	
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function CreateProducto($iva, $minimo, $pvp, $nombre, $id)
{
    $facturacion = "produccion"; //produccion o pruebas son los valores permitidos
    if($facturacion == "pruebas")
    {
    	$pos = "c47b15bc-a027-43f7-9dd4-35d3ef5a670f";
    	$api = "FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U";
    	$categoria = "P1mBdJ1GxHEMb0J6";
    }
    else
    {
    	$pos = "dc323986-9aa2-4eab-93a5-31b45e60bf09";
    	$api = "9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4";
    	$categoria = "RYWb44Z7UB6KbZ1m";
    }

    $producto['codigo_barra'] = NULL;
    $producto['porcentaje_iva'] = $iva;
    $producto['categoria_id'] = $categoria;
    $producto['minimo'] = $pvp;
    $producto['pvp2'] = $pvp;
    $producto['pvp3'] = $pvp;
    $producto['pvp1'] = $pvp;
    $producto['pvp_manual'] = false;
    $producto['descripcion'] = "Obra de Teatro - ".$nombre;
    $producto['nombre'] = $nombre;
    $producto['codigo'] = $id;
    $producto['estado'] = "A";
    
    	
    $ch = curl_init("https://contifico.com/sistema/api/v1/producto/");
    $json = json_encode($producto);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$api;
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function LstFactura($id)
{
    $ch = curl_init("https://contifico.com/sistema/api/v1/documento/".$id);
    $json = NULL;
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    //$headers[] = 'Authorization: '.'9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4'; // key here
    $headers[] = 'Authorization: '.'FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U'; //<-- Key Prueba Contifico.
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function SendFactura($idFactura, $con)
{
    $facturacion = "produccion"; //produccion o pruebas son los valores permitidos
    if($facturacion == "pruebas")
    {
    	$pos = "c47b15bc-a027-43f7-9dd4-35d3ef5a670f";
    	$api = "FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U";
    	$producto = "GEjb2g6OpfzydVNW";
    }
    else
    {
    	$pos = "dc323986-9aa2-4eab-93a5-31b45e60bf09";
    	$api = "9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4";
    	$producto = "oLqdPML26IKZEel4";
    }
    
    $ch = curl_init("https://contifico.com/sistema/api/v1/documento/");
    
    $query = "select * from facturas_cabecera where ID_FACTURA = $idFactura";
    $resp = mysqli_query($con, $query);
    if(mysqli_num_rows($resp)>0)
    {
    	$info = mysqli_fetch_array($resp);
    	
    	//$newDate = $info['FECHA_FACTURA'];
	$newDate = date("d/m/Y", strtotime($info['FECHA_FACTURA']));
    	
    	$pos = GetToken($facturacion,$info['EMISOR']);
    	$contifico['pos'] = $pos;
    	$contifico['fecha_emision'] = $newDate;
    	$contifico['tipo_documento'] = "FAC";
    	$contifico['documento'] = $info['EMISOR']."-".$info['PTO_EMISION']."-".$info['NUM_FACTURA'];
    	$contifico['estado'] = "P";
    	$contifico['autorizacion'] = "123456789"; // -> PONER CUALQUIER NUMERO, CONTIFICO CAMBIA LA AUTORIZACION
    	$contifico['caja_id'] = NULL;
    	$contifico['electronico'] = true;
    	$iva = $info['PORCENTAJE_IVA'] * 100;
    	
    	$query = "select * from clientes where id=".$info['ID_CLIENTE'];
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoCliente = mysqli_fetch_array($resp);
    		$cliente['ruc'] = $infoCliente['num_documento'];
    		$cliente['cedula'] = $infoCliente['num_documento'];
    		$cliente['razon_social'] = $infoCliente['nombre'];
    		$cliente['telefonos'] = $infoCliente['telefono'];
    		$cliente['direccion'] = $infoCliente['domicilio'];
    		$cliente['tipo'] = "N";
    		$cliente['email'] = $infoCliente['correo'];
    		if($infoCliente['extranjero'] == 0)
    			$cliente['es_extranjero'] = true;
    		else
    			$cliente['es_extranjero'] = false;
    	}
    	$contifico['cliente'] = $cliente;
    	
    	$query = "select * from usuarios where codigo_usuario ='".$info['USUARIO_CREACION']."'";
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoVendedor = mysqli_fetch_array($resp);
    		$vendedor['ruc'] = $infoVendedor['cedula'];
    		$vendedor['cedula'] = $infoVendedor['cedula'];
    		$vendedor['razon_social'] = $infoVendedor['nombre'];
    		$vendedor['telefonos'] = "0999999999";
    		$vendedor['direccion'] = $infoVendedor['direccion'];
    		$vendedor['tipo'] = "N";
    		$vendedor['email'] = $infoVendedor['correo'];
    		$vendedor['es_extranjero'] = false;
    	}
    	$contifico['vendedor'] = $vendedor;
    	
    	$acuSubtotal = 0;
    	$acuIva = 0;
    	
    	$query = "SELECT d.*, f.id_contifico
		FROM facturas_detalle d, funciones f
		WHERE d.ID_ARTICULO = f.id_funcion
		and d.ID_FACTURA = $idFactura";
	$resp = mysqli_query($con, $query);
	$x=0;
	while($row = mysqli_fetch_array($resp))
	{
		$precio = number_format($row['SUBTOTAL'], 2, '.', '');
		
		$detalles[$x]['producto_id'] = $row['id_contifico'];
		$detalles[$x]['cantidad'] = 1; 
		$detalles[$x]['precio'] = $row['PRECIO']; 
		$detalles[$x]['porcentaje_iva'] = $iva; 
		$detalles[$x]['porcentaje_descuento'] = ($row['PORCENTAJE_DESC']*100); 
		$detalles[$x]['base_cero'] = 0; 
		$detalles[$x]['base_gravable'] = $precio; 
		$detalles[$x]['base_no_gravable'] = 0; 
		
		$acuSubtotal = $acuSubtotal + $precio; 
		$x++;
	}
	
	$acuSubtotal = number_format($acuSubtotal, 2, '.', '');
	$acuIva = $acuSubtotal * $info['PORCENTAJE_IVA'];
	$acuIva = number_format($acuIva, 2, '.', '');
	
	$contifico['descripcion'] = "FACTURA ".$info['NUM_FACTURA'];
	$contifico['subtotal_0'] = 0;
	$contifico['subtotal_12'] = $acuSubtotal;
	$contifico['iva'] = $acuIva;
	$contifico['servicio'] = 0;
	$contifico['total'] = ($acuSubtotal + $acuIva);
	$contifico['adicional1'] = "";
	$contifico['adicional2'] = "";	
	$contifico['detalles'] = $detalles;	
	
	
	$query = "SELECT DISTINCT *
		FROM facturas_formasPago
		WHERE ID_FACTURA = $idFactura
		order by FORMA_PAGO desc";
	$resp = mysqli_query($con, $query);
	$x=0;
	$numPagos = mysqli_num_rows($resp);
	$auxPagoTarjeta = 0;
	while($row = mysqli_fetch_array($resp))
	{
		$forma_cobro = "EF";
		switch($row['FORMA_PAGO'])
		{
			case 'E': $forma_cobro = "EF"; break;
			case 'T': $forma_cobro = "TC"; break;
			case 'SE': $forma_cobro = "EF"; break;
			case 'ST': $forma_cobro = "TC"; break;
		}
		
		if($numPagos==1)
		{
			$monto = $contifico['total'];
		}
		else
		{
			if($row['FORMA_PAGO'] == "E")
			{
				$monto = $contifico['total'] - $auxPagoTarjeta;
			}
			else
			{
				$auxPagoTarjeta = $row['MONTO'];
				$monto = $row['MONTO'];
			}	
		}
		
		$cobro[$x]['forma_cobro'] = $forma_cobro; 
		$cobro[$x]['monto'] = $monto; 
		$cobro[$x]['numero_cheque'] = NULL; 
		$cobro[$x]['tipo_ping'] = "D"; 
		$x++;
	}
	$contifico['cobros'] = $cobro;
    }
    
    $json = json_encode($contifico);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$api; //<-- Key Contifico.
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    
    $query = "insert into log_facturas (id_factura, send_json, resp_contifico, tipo, pos, api, ambiente) ";
    $query.= "values($idFactura, '$json', '$response', 'ENVIAR', '".$pos."', '$api', '$facturacion')";
    $resp = mysqli_query($con, $query);
    
    return $response;
}

//AUMENTAR CAMPO GUARDO CONTIFICO, SI O NO.
//AUMENTAR CAMPO ES FACTURA, DNA, ANULACION.
function AnulaFactura($idFactura, $con)
{
    $facturacion = "produccion"; //produccion o pruebas son los valores permitidos
    if($facturacion == "pruebas")
    {
    	$pos = "c47b15bc-a027-43f7-9dd4-35d3ef5a670f";
    	$api = "FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U";
    	$producto = "GEjb2g6OpfzydVNW";
    }
    else
    {
    	$pos = "dc323986-9aa2-4eab-93a5-31b45e60bf09";
    	$api = "9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4";
    	$producto = "oLqdPML26IKZEel4";
    }	

    $ch = curl_init("https://contifico.com/sistema/api/v1/documento/");
    
    $query = "select * from facturas_cabecera where ID_FACTURA = $idFactura";
    //echo $query;
    $resp = mysqli_query($con, $query);
    if(mysqli_num_rows($resp)>0)
    {
    	$info = mysqli_fetch_array($resp);
    	
    	//$newDate = $info['FECHA_FACTURA'];
	$newDate = date("d/m/Y", strtotime($info['FECHA_FACTURA']));
    	
    	$pos = GetToken($facturacion,$info['EMISOR']);
    	$contifico['pos'] = $pos;
	$contifico['id'] = $info['ID_CONTIFICO'];
	$contifico['anulado'] = true;
    	$contifico['fecha_emision'] = $newDate;
    	$contifico['tipo_documento'] = $info['TIPO_DOCUMENTO'];;
    	$contifico['documento'] = $info['EMISOR']."-".$info['PTO_EMISION']."-".$info['NUM_FACTURA'];
    	$contifico['estado'] = "P";
    	$contifico['autorizacion'] = "123456789"; // -> PONER CUALQUIER NUMERO, CONTIFICO CAMBIA LA AUTORIZACION
    	$contifico['caja_id'] = NULL;
    	$contifico['electronico'] = true;
    	$iva = $info['PORCENTAJE_IVA'] * 100;
    	
    	$query = "select * from clientes where id=".$info['ID_CLIENTE'];
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoCliente = mysqli_fetch_array($resp);
    		$cliente['ruc'] = $infoCliente['num_documento'];
    		$cliente['cedula'] = $infoCliente['num_documento'];
    		$cliente['razon_social'] = $infoCliente['nombre'];
    		$cliente['telefonos'] = $infoCliente['telefono'];
    		$cliente['direccion'] = $infoCliente['domicilio'];
    		$cliente['tipo'] = "N";
    		$cliente['email'] = $infoCliente['correo'];
    		if($infoCliente['extranjero'] == 0)
    			$cliente['es_extranjero'] = true;
    		else
    			$cliente['es_extranjero'] = false;
    	}
    	$contifico['cliente'] = $cliente;
    	
    	$query = "select * from usuarios where codigo_usuario ='".$info['USUARIO_CREACION']."'";
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoVendedor = mysqli_fetch_array($resp);
    		$vendedor['ruc'] = $infoVendedor['cedula'];
    		$vendedor['cedula'] = $infoVendedor['cedula'];
    		$vendedor['razon_social'] = $infoVendedor['nombre'];
    		$vendedor['telefonos'] = "0999999999";
    		$vendedor['direccion'] = $infoVendedor['direccion'];
    		$vendedor['tipo'] = "N";
    		$vendedor['email'] = $infoVendedor['correo'];
    		$vendedor['es_extranjero'] = false;
    	}
    	$contifico['vendedor'] = $vendedor;
    	
    	$acuSubtotal = 0;
    	$acuIva = 0;
    	
    	$query = "SELECT d.*, f.id_contifico
		FROM facturas_detalle d, funciones f
		WHERE d.ID_ARTICULO = f.id_funcion
		and d.ID_FACTURA = $idFactura";
	$resp = mysqli_query($con, $query);
	$x=0;
	while($row = mysqli_fetch_array($resp))
	{
		$precio = number_format($row['SUBTOTAL'], 2, '.', '');
		
		$detalles[$x]['producto_id'] = $row['id_contifico'];
		$detalles[$x]['cantidad'] = 1; 
		$detalles[$x]['precio'] = $row['PRECIO']; 
		$detalles[$x]['porcentaje_iva'] = $iva; 
		$detalles[$x]['porcentaje_descuento'] = ($row['PORCENTAJE_DESC']*100); 
		$detalles[$x]['base_cero'] = 0; 
		$detalles[$x]['base_gravable'] = $precio; 
		$detalles[$x]['base_no_gravable'] = 0; 
		
		$acuSubtotal = $acuSubtotal + $precio; 
		$x++;
	}
	
	$acuSubtotal = number_format($acuSubtotal, 2, '.', '');
	$acuIva = $acuSubtotal * $info['PORCENTAJE_IVA'];
	$acuIva = number_format($acuIva, 2, '.', '');
	
	$contifico['descripcion'] = "FACTURA ".$info['NUM_FACTURA'];
	$contifico['subtotal_0'] = 0;
	$contifico['subtotal_12'] = $acuSubtotal;
	$contifico['iva'] = $acuIva;
	$contifico['servicio'] = 0;
	$contifico['total'] = ($acuSubtotal + $acuIva);
	$contifico['adicional1'] = "";
	$contifico['adicional2'] = "";	
	$contifico['detalles'] = $detalles;	
	
	
	$query = "SELECT DISTINCT *
		FROM facturas_formasPago
		WHERE ID_FACTURA = $idFactura
		order by FORMA_PAGO desc";
	$resp = mysqli_query($con, $query);
	$x=0;
	$numPagos = mysqli_num_rows($resp);
	$auxPagoTarjeta = 0;
	while($row = mysqli_fetch_array($resp))
	{
		$forma_cobro = "EF";
		switch($row['FORMA_PAGO'])
		{
			case 'E': $forma_cobro = "EF"; break;
			case 'T': $forma_cobro = "TC"; break;
			case 'SE': $forma_cobro = "EF"; break;
			case 'ST': $forma_cobro = "TC"; break;
		}
		
		if($numPagos==1)
		{
			$monto = $contifico['total'];
		}
		else
		{
			if($row['FORMA_PAGO'] == "E")
			{
				$monto = $contifico['total'] - $auxPagoTarjeta;
			}
			else
			{
				$auxPagoTarjeta = $row['MONTO'];
				$monto = $row['MONTO'];
			}	
		}
		
		$cobro[$x]['forma_cobro'] = $forma_cobro; 
		$cobro[$x]['monto'] = $monto; 
		$cobro[$x]['numero_cheque'] = NULL; 
		$cobro[$x]['tipo_ping'] = "D"; 
		$x++;
	}
	$contifico['cobros'] = $cobro;
    }
    
    $json = json_encode($contifico);
    //echo $json;	
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$api; //<-- Key Prueba Contifico.
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    
    $query = "insert into log_facturas (id_factura, send_json, resp_contifico, tipo, pos, api, ambiente) ";
    $query.= "values($idFactura, '$json', '$response', 'ENVIAR', '".$pos."', '$api', '$facturacion')";
    $resp = mysqli_query($con, $query);
    
    return $response;
}


function SendDNA($idFactura, $con)
{
    $facturacion = "produccion"; //produccion o pruebas son los valores permitidos
    if($facturacion == "pruebas")
    {
    	$pos = "c47b15bc-a027-43f7-9dd4-35d3ef5a670f";
    	$api = "FrguR1kDpFHaXHLQwplZ2CwTX3p8p9XHVTnukL98V5U";
    	$producto = "GEjb2g6OpfzydVNW";
    }
    else
    {
    	$pos = "dc323986-9aa2-4eab-93a5-31b45e60bf09";
    	$api = "9KPugrsgM14ZhiscobpjeiJcoVvHALlVNOX7mYYp5E4";
    	$producto = "oLqdPML26IKZEel4";
    }
    
    $ch = curl_init("https://contifico.com/sistema/api/v1/documento/");
    
    $query = "select * from facturas_cabecera where ID_FACTURA = $idFactura";
    //echo $query;
    $resp = mysqli_query($con, $query);
    if(mysqli_num_rows($resp)>0)
    {
    	$info = mysqli_fetch_array($resp);
    	
    	//$newDate = $info['FECHA_FACTURA'];
	$newDate = date("d/m/Y", strtotime($info['FECHA_FACTURA']));
    	
    	$pos = GetToken($facturacion,$info['EMISOR']);
    	$contifico['pos'] = $pos;
    	$contifico['fecha_emision'] = $newDate;
    	$contifico['tipo_documento'] = "DNA";
    	$contifico['documento'] = $info['EMISOR']."-".$info['PTO_EMISION']."-".$info['NUM_FACTURA'];
    	$contifico['estado'] = "P";
    	$contifico['autorizacion'] = "123456789"; // -> PONER CUALQUIER NUMERO, CONTIFICO CAMBIA LA AUTORIZACION
    	$contifico['caja_id'] = NULL;
    	$contifico['electronico'] = true;
    	$iva = $info['PORCENTAJE_IVA'] * 100;
    	
    	$query = "select * from clientes where id=".$info['ID_CLIENTE'];
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoCliente = mysqli_fetch_array($resp);
    		$cliente['ruc'] = $infoCliente['num_documento'];
    		$cliente['cedula'] = $infoCliente['num_documento'];
    		$cliente['razon_social'] = $infoCliente['nombre'];
    		$cliente['telefonos'] = $infoCliente['telefono'];
    		$cliente['direccion'] = $infoCliente['domicilio'];
    		$cliente['tipo'] = "N";
    		$cliente['email'] = $infoCliente['correo'];
    		if($infoCliente['extranjero'] == 0)
    			$cliente['es_extranjero'] = true;
    		else
    			$cliente['es_extranjero'] = false;
    	}
    	$contifico['cliente'] = $cliente;
    	
    	$query = "select * from usuarios where codigo_usuario ='".$info['USUARIO_CREACION']."'";
    	$resp = mysqli_query($con, $query);
    	if(mysqli_num_rows($resp)>0)
    	{
    		$infoVendedor = mysqli_fetch_array($resp);
    		$vendedor['ruc'] = $infoVendedor['cedula'];
    		$vendedor['cedula'] = $infoVendedor['cedula'];
    		$vendedor['razon_social'] = $infoVendedor['nombre'];
    		$vendedor['telefonos'] = "0999999999";
    		$vendedor['direccion'] = $infoVendedor['direccion'];
    		$vendedor['tipo'] = "N";
    		$vendedor['email'] = $infoVendedor['correo'];
    		$vendedor['es_extranjero'] = false;
    	}
    	$contifico['vendedor'] = $vendedor;
    	
    	$acuSubtotal = 0;
    	$acuIva = 0;
    	
    	$query = "SELECT d.*, f.id_contifico
		FROM facturas_detalle d, funciones f
		WHERE d.ID_ARTICULO = f.id_funcion
		and d.ID_FACTURA = $idFactura";
	$resp = mysqli_query($con, $query);
	$x=0;
	while($row = mysqli_fetch_array($resp))
	{
		$precio = number_format($row['TOTAL'], 2, '.', '');
		
		$detalles[$x]['producto_id'] = $row['id_contifico'];	//PRODUCCION
		$detalles[$x]['cantidad'] = 1; 
		$detalles[$x]['precio'] = $row['TOTAL']; 
		$detalles[$x]['porcentaje_iva'] = 0; 
		$detalles[$x]['porcentaje_descuento'] = ($row['PORCENTAJE_DESC']*100); 
		$detalles[$x]['base_cero'] = 0; 
		$detalles[$x]['base_gravable'] = 0; 
		$detalles[$x]['base_no_gravable'] = $precio; 
		
		$acuSubtotal = $acuSubtotal + $precio; 
		$x++;
	}
	
	$acuSubtotal = number_format($acuSubtotal, 2, '.', '');
	$acuIva = $acuSubtotal * $info['PORCENTAJE_IVA'];
	$acuIva = number_format($acuIva, 2, '.', '');
	
	$contifico['descripcion'] = "DNA ".$info['NUM_FACTURA'];
	$contifico['subtotal_0'] = 0;
	$contifico['subtotal_12'] = $acuSubtotal;
	$contifico['iva'] = 0;
	$contifico['servicio'] = 0;
	$contifico['total'] = ($acuSubtotal);
	$contifico['adicional1'] = "";
	$contifico['adicional2'] = "";	
	$contifico['detalles'] = $detalles;	
	
	
	$query = "SELECT DISTINCT *
		FROM facturas_formasPago
		WHERE ID_FACTURA = $idFactura
		order by FORMA_PAGO desc";
	$resp = mysqli_query($con, $query);
	$x=0;
	$numPagos = mysqli_num_rows($resp);
	$auxPagoTarjeta = 0;
	while($row = mysqli_fetch_array($resp))
	{
		$forma_cobro = "EF";
		switch($row['FORMA_PAGO'])
		{
			case 'E': $forma_cobro = "EF"; break;
			case 'T': $forma_cobro = "TC"; break;
			case 'SE': $forma_cobro = "EF"; break;
			case 'ST': $forma_cobro = "TC"; break;
		}
		
		if($numPagos==1)
		{
			$monto = $contifico['total'];
		}
		else
		{
			if($row['FORMA_PAGO'] == "E")
			{
				$monto = $contifico['total'] - $auxPagoTarjeta;
			}
			else
			{
				$auxPagoTarjeta = $row['MONTO'];
				$monto = $row['MONTO'];
			}	
		}
		
		$cobro[$x]['forma_cobro'] = $forma_cobro; 
		$cobro[$x]['monto'] = $monto; 
		$cobro[$x]['numero_cheque'] = NULL; 
		$cobro[$x]['tipo_ping'] = "D"; 
		$x++;
	}
	$contifico['cobros'] = $cobro;
    }
    
    $json = json_encode($contifico);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: '.$api;
	
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    curl_close($ch);
    
    $query = "insert into log_facturas (id_factura, send_json, resp_contifico, tipo, pos, api, ambiente) ";
    $query.= "values($idFactura, '$json', '$response', 'ENVIAR', '".$pos."', '$api', '$facturacion')";
    $resp = mysqli_query($con, $query);
    
    return $response;
}



?>
