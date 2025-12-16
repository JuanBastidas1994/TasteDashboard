<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_contifico.php";
require_once "../clases/cl_productos.php";
$session = getSession();
$cod_empresa = $session['cod_empresa'];
$Clcontifico = new cl_contifico($cod_empresa);
$Clproductos = new cl_productos(NULL);
define('cod_sistema_facturacion',1);

controller_create();

function crear_producto(){
    global $Clcontifico;
    global $Clproductos;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    $info = $Clproductos->get($id);
    if($info){
        $nombre = $info['nombre'];
        $pvp = $info['precio'];
        $sku = $info['sku'];
        $iva = 12;
        if($info['cobra_iva'] == 0)
            $iva = 0;
   
        $producto = $Clcontifico->CreateProducto($iva, $pvp, $nombre, $id);
        if(!isset($producto['mensaje'])){
            $return['success'] = 1;
            $return['mensaje'] = "Creado correctamente en contifico";
            $return['producto'] = $producto;
    
            $id_contifico = $producto['id'];
            if(!setProductoById($id_contifico, $id)){
                $return['mensaje'] = "Creado correctamente en Contifico, pero no se pudo ligar con nuestro producto, por favor realizarlo manualmente";
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el producto en el Sistema Contable, Error: ".$producto['mensaje'];
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "El producto no existe, por favor verificar la informacion";
    }
    return $return;
}

function set_id_producto(){
    global $Clcontifico;

    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $producto = $Clcontifico->GetProducto($idFact);

    if(!isset($producto['mensaje'])){
        $return['success'] = 1;
        $return['mensaje'] = "Asignado correctamente";
        
        if(!setProductoById($idFact, $id)){
            $return['mensaje'] = "No se pudo ligar con nuestro producto, por favor realizarlo manualmente";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Producto no encontrado en sistema contable, Error: ".$producto['mensaje'];
    }

    return $return;
}


function setProductoById($id, $cod_producto){
    $query = "INSERT INTO tb_productos_facturacion(id, cod_producto, cod_sistema_facturacion) 
            VALUES('$id',$cod_producto,'".cod_sistema_facturacion."')";
    $resp = Conexion::ejecutar($query, NULL);
    return $resp;
}

function getProductoById($cod_producto){
    $query = "SELECT * FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_sistema_facturacion = ".cod_sistema_facturacion;
    $resp = Conexion::buscarRegistro($query);
    return $resp;
}

/*FUNCIONES DE CREACION DE FACTURA*/
function crearFactura(){
    global $Clcontifico;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_GET);
    
    if(ExistFacturaToOrden($id)){
        $return['success'] = 0;
        $return['mensaje'] = "La orden $id ya tiene una factura creada";
        return $return;
    }
    
    $cod_sucursal = 0;
    $msgError = "";
    $factura = armarFactura($id, false, $cod_sucursal, $msgError);
    if(!$factura){
        $return['success'] = 0;
        $return['mensaje'] = $msgError;
        return $return;
    }
    $return['infoEnviada'] = $factura;
    $return['classContifico'] = $Clcontifico;
    
    $respFactura = $Clcontifico->CreateFactura($factura);
    $idContifico = isset($respFactura['id']) ? $respFactura['id'] : 0;
    if($idContifico !== 0){
        $return['success'] = 1;
        $return['mensaje'] = "Factura creada correctamente";
        $Clcontifico->incrementSecuencial($cod_sucursal);
        saveOrdenFactura($id, $respFactura['id'], $respFactura['documento']);

    }else{
        if(isset($respFactura['mensaje'])){
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la factura. Detalle: ".$respFactura['mensaje'];
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo crear la factura ".$Clcontifico->msgError;
        }
        
    }
    $return['respFactura'] = $respFactura;
    return $return;
}

function anularFactura(){
    global $Clcontifico;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_GET);
    
    if(!ExistFacturaToOrden($id)){
        $return['success'] = 0;
        $return['mensaje'] = "La orden $id no tiene una factura creada";
        return $return;
    }
    
    $cod_sucursal = 0;
    $msgError = "";
    $factura = armarFactura($id, true, $cod_sucursal, $msgError);
    if(!$factura){
        $return['success'] = 0;
        $return['mensaje'] = $msgError;
        return $return;
    }
    
    
    $respFactura = $Clcontifico->CreateFactura($factura);
    if(isset($respFactura['id'])){
        $return['success'] = 1;
        $return['mensaje'] = "Factura Anulada correctamente";
        AnularOrdenFactura($id);
        //$Clcontifico->incrementSecuencial($cod_sucursal);
        //saveOrdenFactura($id, $respFactura['id'], $respFactura['documento']);

    }else{
        if(isset($respFactura['mensaje'])){
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la factura. Detalle: ".$respFactura['mensaje'];
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo crear la factura ".$Clcontifico->msgError;
        }
        
    }

    $return['infoEnviada'] = $factura;
    $return['classContifico'] = $Clcontifico;
    $return['respFactura'] = $respFactura;
    return $return;
}

function ExistFacturaToOrden($cod_orden){
    $query = "SELECT * FROM tb_orden_factura_electronica WHERE cod_orden = $cod_orden AND estado = 'CREADA'";
    $resp = Conexion::buscarRegistro($query);
    return $resp;
}

function saveOrdenFactura($pcod_orden, $pclaveAcceso, $pnumFactura){
    $query = "INSERT INTO tb_orden_factura_electronica(cod_orden, num_factura, clave_acceso, estado, cod_sistema_facturacion) 
            VALUES('$pcod_orden','$pnumFactura','$pclaveAcceso','CREADA','".cod_sistema_facturacion."')";
    $resp = Conexion::ejecutar($query, NULL);
    return $resp;
}

function AnularOrdenFactura($pcod_orden){
    $query = "UPDATE tb_orden_factura_electronica SET estado = 'ANULADA' WHERE cod_orden = $pcod_orden";
    $resp = Conexion::ejecutar($query, NULL);
    return $resp;
}

function armarFactura($cod_orden, $anular, &$codSucursal, &$mensaje){
    global $Clcontifico;
    global $Clproductos;
    global $cod_empresa;
    require_once "../clases/cl_ordenes.php";
    $ClOrdenes = new cl_ordenes();
    $orden = $ClOrdenes->get_orden_array($cod_orden);
    $porcentaje_iva = 12;
    
    if($orden){
            $codSucursal = $orden['cod_sucursal'];
            $posSucursal = $Clcontifico->getPoscode($orden['cod_sucursal']);
            if(!$posSucursal){
                $mensaje = "La sucursal no tiene configurado un pto de emisión";
                return false;
            } 

		 	$contifico['pos'] = $posSucursal['pos'];
		 	if($anular){
                $factElectronica = ExistFacturaToOrden($cod_orden);
                if(!$factElectronica){
                    $mensaje = "La orden $cod_orden no tiene una factura creada";
                    return false;
                }
				$contifico['anulado'] = true;
				$contifico['estado'] = 'A'; //-> A de anulado
                $contifico['documento'] = $factElectronica['num_factura']; //buscar numero de factura a anular
		 	}else{
                $contifico['documento'] = $posSucursal['emisor']."-".$posSucursal['ptoemision']."-".str_pad($posSucursal['secuencial'], 9, "0", STR_PAD_LEFT);
                $contifico['estado'] = "P";
            }
		 	$newDate = date("d/m/Y", strtotime($orden['fecha']));
	    	$contifico['fecha_emision'] = $newDate;
	    	$contifico['tipo_documento'] = "FAC";
	    	$contifico['autorizacion'] = "123456789"; // -> PONER CUALQUIER NUMERO, CONTIFICO CAMBIA LA AUTORIZACION
	    	$contifico['caja_id'] = NULL;
	    	$contifico['electronico'] = true;
	    	
	    	/*CLIENTE*/
	    	require_once "../clases/cl_usuarios.php";
	    	$Clusuarios=new cl_usuarios();
	    	$usuario = $Clusuarios->get($orden['cod_usuario']);
	    	if($usuario){
	    	    
                if(strlen($usuario['num_documento']) == 13){
                    $cliente['ruc'] = $usuario['num_documento'];
                    $cliente['cedula'] = substr($usuario['num_documento'],0,10);
                }
                else{
                    $cliente['cedula'] = $usuario['num_documento'];  
                    $cliente['ruc'] = $usuario['num_documento']."001";
                }
	    		    
	    		$cliente['razon_social'] = $usuario['nombre']." ".$usuario['apellido'];
	    		$cliente['telefonos'] = $usuario['telefono'];
	    		$cliente['direccion'] = $usuario['direccion'];
	    		$cliente['tipo'] = "N";
	    		$cliente['email'] = $usuario['correo'];
	    		$cliente['es_extranjero'] = false;
	    	    $contifico['cliente'] = $cliente;
	    	}
	    	
	    	/*VENDEDOR*/
	    	$vendedor['ruc'] = "0952423606001";
    		$vendedor['cedula'] = "0952423606";
    		$vendedor['razon_social'] = "Vendedor";
    		$vendedor['telefonos'] = "0999999999";
    		$vendedor['direccion'] = "Juan montalvo";
    		$vendedor['tipo'] = "N";
    		$vendedor['email'] = "juankbastidasjuve@gmail.com";
    		$vendedor['es_extranjero'] = false;
    		$contifico['vendedor'] = $vendedor;
    		
    		/*DETALLE DE LA FACTURA*/
            $detalle=[];
            $x=0;
            foreach($orden['detalle'] as $item){
                $resp = getProductoById($item['cod_producto']);
                if($resp){
					$base0 = 0;
					$base12 = 0;
					if($item['cobra_iva']==1)
					    $base12 = $item['precio'];
					else
					    $base0 = $item['precio'];
					
					$detalle[$x]['producto_id'] = $resp['id'];
					$detalle[$x]['cantidad'] = $item['cantidad']; 
					$detalle[$x]['precio'] = $item['precio']; 
					$detalle[$x]['porcentaje_iva'] = $porcentaje_iva; 
					$detalle[$x]['porcentaje_descuento'] = 0; 
					$detalle[$x]['base_cero'] = 0;
					$detalle[$x]['base_gravable'] = $base12;
					$detalle[$x]['base_no_gravable'] = $base0;
					$x++;
                }else{
                    $info = $Clproductos->get($item['cod_producto']);
                    if($info){
                        $sku = ($info['sku'] != "") ? $info['sku']: $info['cod_producto'];
                        $producto = $Clcontifico->CreateProducto(12, $info['precio'], $info['nombre'], $sku);
                        if(isset($producto['id'])){
                            if(setProductoById($producto['id'], $info['cod_producto'])){
                                $base0 = 0;
            					$base12 = 0;
            					if($item['cobra_iva']==1)
            					    $base12 = $item['precio'];
            					else
            					    $base0 = $item['precio'];
            					
            					$detalle[$x]['producto_id'] = $producto['id'];
            					$detalle[$x]['cantidad'] = $item['cantidad']; 
            					$detalle[$x]['precio'] = $item['precio']; 
            					$detalle[$x]['porcentaje_iva'] = $porcentaje_iva; 
            					$detalle[$x]['porcentaje_descuento'] = 0; 
            					$detalle[$x]['base_cero'] = 0;
            					$detalle[$x]['base_gravable'] = $base12;
            					$detalle[$x]['base_no_gravable'] = $base0;
            					$x++;
                            }else{
                                $mensaje = "No se puede crear la factura porque no se pudo crear en Contifico el producto ".$item['nombre'];
                                return false;
                            }
                        }else{
                            //$return['respContifico'] = $producto;
                            $mensaje = "Error al crear producto ".$item['nombre']." en Contifico - Error: ".$producto['message'];
                            return false;
                        }
                    }else{
                        $mensaje = "No existe el producto ".$item['nombre']." en el sistema, por favor verificar ";
                        return false;
                    }
                }
            }
            
            /*AUMENTAR EL ENVIO COMO PRODUCTO*/
            if($orden['envio']>0){
                $iva = 0;
                $envioBase0 = 0;
                $envioBase12 = 0;
                $aliasEnvio='ENVIO_BASE_0';
                $gravaIva = empresaGravaIva($cod_empresa);
                if($gravaIva == 1){
                    $iva = 12;
                    $envioBase12 = $orden['envio'];
                    $aliasEnvio='ENVIO_BASE_12';
                }else{
                    $envioBase0 = $orden['envio'];
                }
                $resp = getEnvioByAlias($aliasEnvio, $cod_empresa); 
                if(!$resp){
                    $producto = $Clcontifico->CreateProducto($iva, 2.50, 'Servicio a domicilio', $aliasEnvio);
                    if(isset($producto['id'])){
                        if(setEnvioByAlias($producto['id'], $aliasEnvio, $cod_empresa)){
                            $detalle[$x]['producto_id'] = $producto['id'];
                            $detalle[$x]['cantidad'] = 1; 
                            $detalle[$x]['precio'] = $orden['envio']; 
                            $detalle[$x]['porcentaje_iva'] = $iva; 
                            $detalle[$x]['porcentaje_descuento'] = 0; 
                            $detalle[$x]['base_cero'] = 0;
                            $detalle[$x]['base_gravable'] = $envioBase12;
                            $detalle[$x]['base_no_gravable'] = $envioBase0;
                        }else{
                            $return['success'] = 0;
                            $return['mensaje'] = "No se puede crear la factura porque no se pudo crear el producto -Servicio a domicilio- en Contifico";
                            return $return;
                        }
                    }else{
                        $return['success'] = 0;
                        $return['mensaje'] = "No se puede crear la factura porque no se pudo crear el producto -Servicio a domicilio- en Contifico";
                        $return['respCreateEnvio'] = $producto;
                        return $return;
                    }
                }else{
                    $detalle[$x]['producto_id'] = $resp['id'];
                    $detalle[$x]['cantidad'] = 1; 
                    $detalle[$x]['precio'] = $orden['envio']; 
                    $detalle[$x]['porcentaje_iva'] = $iva; 
                    $detalle[$x]['porcentaje_descuento'] = 0; 
                    $detalle[$x]['base_cero'] = 0;
                    $detalle[$x]['base_gravable'] = $envioBase12;
                    $detalle[$x]['base_no_gravable'] = $envioBase0;
                }

            }
            
            /*INFO DE LA FACTURA*/
            $contifico['detalles'] = $detalle;
            $contifico['descripcion'] = "N. Orden ".$orden['cod_orden'];
			$contifico['subtotal_0'] = $orden['subtotal0'];
			$contifico['subtotal_12'] = $orden['subtotal12'];
			$contifico['iva'] = $orden['iva'];
			$contifico['servicio'] = 0;
			$contifico['total'] = $orden['total'];
			$contifico['adicional1'] = "";
			$contifico['adicional2'] = "";
			
			/*FORMA DE PAGO*/
			$pagos=[];
            $x=0;
            foreach($orden['pagos'] as $item){
                $pagos[$x]['forma_cobro'] = getFormaPago($item['forma_pago']);
                $pagos[$x]['monto'] = $item['monto'];
                $pagos[$x]['numero_cheque'] = NULL;
                $pagos[$x]['tipo_ping'] = "D";
                $x++;
            }
             $contifico['cobros'] = $pagos;
			
			return $contifico;
                
    }else{
        
    }
}

function empresaGravaIva($cod_empresa){
    $query = "SELECT * FROM tb_empresas WHERE cod_empresa = $cod_empresa";
    $resp = Conexion::buscarRegistro($query);
    if($resp){
        return $resp['envio_grava_iva'];
    }
    return 0;
}

function getEnvioByAlias($alias, $cod_empresa){
    $query = "SELECT * FROM tb_productos_envio_facturacion WHERE alias = '$alias' AND cod_empresa = $cod_empresa AND cod_sistema_facturacion = ".cod_sistema_facturacion;
    $resp = Conexion::buscarRegistro($query);
    return $resp;
}

function setEnvioByAlias($id, $alias, $cod_empresa){
    $query = "INSERT INTO tb_productos_envio_facturacion(id, alias, cod_empresa, cod_sistema_facturacion) 
            VALUES('$id','$alias',$cod_empresa,'".cod_sistema_facturacion."')";
    $resp = Conexion::ejecutar($query, NULL);
    return $resp;
}

function getFormaPago($forma){
    if($forma == "E")
        return "EF";
    if($forma == "T")
        return "TC";  
    if($forma == "P")
        return "EF";    
}

function addRuc(){
    global $Clcontifico;
    global $cod_empresa;
    
   $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);
    
    $Clcontifico->API = $apitoken;
    
    $categoriesContifico = $Clcontifico->LstCategories();
    
    $return = null;
    if(isset($categoriesContifico['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Cont��fico Error: ".$categoriesContifico['mensaje'];
        return $return;
    }
    
    
    if($Clcontifico->addRuc($razon_social, $ruc, 'development', $apitoken, $cod_empresa)){
        $id_ruc = Conexion::lastId();
        $return['success'] = 1;
        $return['mensaje'] = "Ruc creado correctamente";
        $return['ruc_id'] = $id_ruc;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo guardar el ruc, por favor intentelo nuevamente";
    }
    return $return;
    
}

function lista_productos(){
    global $Clcontifico;
    
    $productos = $Clcontifico->LstProductos();
    if(isset($productos['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Contífico Error: ".$productos['mensaje'];
        return $return;
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de productos";
    $return['productos'] = $productos;
    return $return;
}

function lstProductsByRuc(){
    global $Clcontifico;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $ruc = $Clcontifico->getRuc($id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $Clcontifico->API = $ruc['api'];
    //$productos = $Clcontifico->LstProductosV2();
    $productos = $Clcontifico->LstProductos();
    if(isset($productos['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Contífico Error: ".$productos['mensaje'];
        return $return;
    }
    
    $x=0;
    $productsResp = null;
    foreach($productos as $key => $item){
        $productsResp[$x]['id'] = $item['id'];
        $productsResp[$x]['nombre'] = $item['nombre'];
        $productsResp[$x]['pvp1'] = $item['pvp1'];
        $productsResp[$x]['tipo'] = $item['tipo'];
        $productsResp[$x]['codigo'] = $item['codigo'];
        $productsResp[$x]['cuenta_venta_id'] = $item['cuenta_venta_id'];
        $productsResp[$x]['stock'] = $item['cantidad_stock'];
        $x++;
    }
        
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de productos";
    $return['productos'] = $productsResp;
    $return['ruc'] = $ruc;
    return $return;
}

function lstBodegasByRuc(){
    global $Clcontifico;
    global $cod_empresa;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $ruc = $Clcontifico->getRuc($id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $Clcontifico->API = $ruc['api'];
    $bodegas = $Clcontifico->LstBodegas();
    if(isset($bodegas['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Contífico Error: ".$bodegas['mensaje'];
        return $return;
    }

    $return['success'] = 1;
    $return['mensaje'] = "Lista de bodegas";
    $return['bodegas'] = $bodegas;
    $return['ruc'] = $ruc;
    return $return;
}

function lstCategorias(){
    global $Clcontifico;
    global $cod_empresa;

    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $ruc = $Clcontifico->getRuc($id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $Clcontifico->API = $ruc['api'];
    $productos = $Clcontifico->LstCategories();
    if(isset($productos['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Contífico Error: ".$productos['mensaje'];
        return $return;
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de categorias";
    $return['categorias'] = $productos;
    return $return;
}

function lstProductosByCategory(){
    global $Clcontifico;
    global $cod_empresa;
    
    if(!isset($_GET['id']) || !isset($_GET['category_id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);
    
    $ruc = $Clcontifico->getRuc($id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $numRows = 0;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $Clcontifico->API = $ruc['api'];
    $productos = $Clcontifico->LstProductosByCategory($category_id, $page, $numRows);
    if(isset($productos['mensaje'])){
        $return['success'] = 0;
        $return['mensaje'] = "Contífico Error: ".$productos['mensaje'];
        return $return;
    }
    
    $x=0;
    $productsResp = null;
    foreach($productos as $key => $item){
        $productsResp[$x]['id'] = $item['id'];
        $productsResp[$x]['nombre'] = $item['nombre'];
        $productsResp[$x]['pvp1'] = $item['pvp1'];
        $productsResp[$x]['tipo'] = $item['tipo'];
        $productsResp[$x]['codigo'] = $item['codigo'];
        $productsResp[$x]['cuenta_venta_id'] = $item['cuenta_venta_id'];
        $productsResp[$x]['stock'] = $item['cantidad_stock'];
        $x++;
    }
        
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de productos";
    $return['numRows'] = $numRows;
    $return['productos'] = $productsResp;
    $return['ruc'] = $ruc;
    return $return;
}
?>