<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_fact_movil.php";
require_once "../clases/cl_productos.php";
$session = getSession();
$cod_empresa = $session['cod_empresa'];

$Clfact = new cl_fact_movil($cod_empresa);
$Clproductos = new cl_productos(NULL);
define('cod_sistema_facturacion',2);

controller_create();

/*ENVIO*/
function lista_productos(){
    global $Clfact;
    $resp = $Clfact->LstProductos();
    if(!$resp){
        echo $Clfact->msgError;
        echo $Clfact->API;
    }
    return $resp;
}

function crear_producto(){
    global $Clfact;
    global $Clproductos;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    //VERIFICAR SI TIENE FACT MOVIL
    if($Clfact->errorToken){
        $return['success'] = 0;
        $return['mensaje'] = $Clfact->errorToken;
        return $return;
    }

    extract($_GET);
    
    $info = $Clproductos->get($id);
    if($info){
        $nombre = $info['nombre'];
        $pvp = $info['precio'];
        $sku = $info['sku'];
        $iva = 2; // [{"id":1,"nombre":"0%","tarifa":0},{"id":2,"nombre":"12%","tarifa":12},{"id":3,"nombre":"No Objeto de Impuesto","tarifa":0},{"id":4,"nombre":"Exento de IVA","tarifa":0}]
        if($info['cobra_iva'] == 0)
            $iva = 1;
        
        $tipo = 1; //[{"id":1,"nombre":"Bien"},{"id":2,"nombre":"Servicio"}]
        if($info['bien'] == "Servicio")
            $tipo = 2;
        $producto = $Clfact->CreateProducto($iva, $pvp, $nombre, $sku, $tipo, $id);
        if(isset($producto['id'])){
            $return['success'] = 1;
            $return['mensaje'] = "Creado correctamente en Facturero Movil";
            $return['producto'] = $producto;
    
            $idFact = $producto['id'];
            if(!setProductoById($idFact, $id)){
                $return['mensaje'] = "Creado correctamente en Facturacion Movil, pero no se pudo ligar con nuestro producto, por favor realizarlo manualmente";
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el producto en el Sistema Contable, Error: ".$producto['message'];
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "El producto no existe, por favor verificar la informacion";
    }
    
    return $return;
}

function crear_cliente($cedula, $nombre, $email){
    global $Clfact;
}

function set_id_producto(){
    global $Clfact;

    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $producto = $Clfact->GetProducto($idFact);

    if(isset($producto['id'])){
        $return['success'] = 1;
        $return['mensaje'] = "Asignado correctamente";

        $cod_sistema_facturacion = cod_sistema_facturacion;
        $query = "INSERT INTO tb_productos_facturacion(id,cod_producto,cod_sistema_facturacion) VALUES('$idFact', $id, $cod_sistema_facturacion)";
        if(!Conexion::ejecutar($query,NULL)){
            $return['mensaje'] = "No se pudo ligar con nuestro producto, por favor intentarlo nuevamente";
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Producto no encontrado en sistema contable, Error: ".$producto['message'];
    }

    return $return;
}

function getDocument(){
    global $Clfact;
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_GET);
    
    $resp = $Clfact->GetFacturaByAutorizacion($id);
    return $resp;
}

function crearFactura(){
    global $Clfact;
    global $Clproductos;
    global $cod_empresa;


    //VERIFICAR SI TIENE FACT MOVIL
    if($Clfact->errorToken){
        $return['success'] = 0;
        $return['mensaje'] = $Clfact->errorToken;
        return $return;
    }
    
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
    
    require_once "../clases/cl_ordenes.php";
    $ClOrdenes = new cl_ordenes();
    $orden = $ClOrdenes->get_orden_array($id);
    if($orden){
            $resp = getClienteById($orden['cod_usuario']);
            if(!$resp){
                $cliente = $Clfact->CreateCliente($orden['num_documento'],$orden['nombre'].' '.$orden['apellido'],$orden['correo']);
                if(isset($cliente['id'])){
                    $idFact = $cliente['id'];
                    $query = "INSERT INTO tb_cliente_facturacion(id,cod_usuario,cod_sistema_facturacion) VALUES('$idFact', ".$orden['cod_usuario'].", ".cod_sistema_facturacion.")";
                    if(Conexion::ejecutar($query,NULL)){
                        $resp['id'] = Conexion::lastId();
                    }else{
                        $return['success'] = 0;
                        $return['mensaje'] = "No se puede crear la factura porque no existe en Fact. Movil el Cliente: ".$orden['nombre'].' '.$orden['apellido'];
                        return $return;
                    }
                }else{
                    $return['success'] = 0;
                    $return['mensaje'] = "Error al crear el cliente en Fact. Movil - Cliente: ".$orden['nombre'].' '.$orden['apellido']." - ".$Clfact->msgError;
                    $return['respFactMovil'] = $resp;
                    return $return;
                }    
            }
            $factura['fechaEmision'] = $orden['fecha'];
            $factura['cliente'] = $resp['id'];
            
            /*DETALLE DE LA FACTURA*/
            $detalle=[];
            $x=0;
            foreach($orden['detalle'] as $item){
                $resp = getProductoById($item['cod_producto']);
                if($resp){
                    $detalle[$x]['producto'] = $resp['id'];
                    $detalle[$x]['cantidad'] = $item['cantidad'];
                    $detalle[$x]['descuento'] = $item['descuento'];
                    $detalle[$x]['precioUnitario'] = $item['precio'];
                }else{
                    $info = $Clproductos->get($item['cod_producto']);
                    if($info){
                        $iva = ($info['cobra_iva'] == 0) ? 1 : 2;
                        $tipo = ($info['bien'] == "Servicio") ? 2: 1;
                        $sku = ($info['sku'] != "") ? $info['sku']: $info['cod_producto'];
                        $producto = $Clfact->CreateProducto($iva, $info['precio'], $info['nombre'], $sku, $tipo, $id);
                        if(isset($producto['id'])){
                            if(setProductoById($producto['id'], $info['cod_producto'])){
                                $detalle[$x]['producto'] = $producto['id'];
                                $detalle[$x]['cantidad'] = $item['cantidad'];
                                $detalle[$x]['descuento'] = $item['descuento'];
                                $detalle[$x]['precioUnitario'] = $item['precio'];
                            }else{
                                $return['success'] = 0;
                                $return['mensaje'] = "No se puede crear la factura porque no se pudo crear en Fact. Movil el producto ".$item['nombre'];
                                return $return;
                            }
                        }else{
                            $return['success'] = 0;
                            $return['mensaje'] = "Error al crear producto ".$item['nombre']."-".$sku." en Fact. Movil - Error: ".$producto['message'];
                            $return['respFactMovil'] = $producto;
                            $return['info_producto'] = $info;
                            return $return;
                        }
                    }else{
                        $return['success'] = 0;
                        $return['mensaje'] = "No existe el producto ".$item['nombre']." en el sistema, por favor verificar ";
                        return $return;
                    }
                }
                $x++;
            }
            
            /*AUMENTAR EL ENVIO COMO PRODUCTO*/
            if($orden['envio'] > 0){
                $iva = 1;
                $aliasEnvio='ENVIO_BASE_0';
                $gravaIva = empresaGravaIva($cod_empresa);
                if($gravaIva == 1){
                    $iva = 2;
                    $aliasEnvio='ENVIO_BASE_12';
                }
                  
                $resp = getEnvioByAlias($aliasEnvio, $cod_empresa); 
                if(!$resp){
                    $producto = $Clfact->CreateProducto($iva, 2.50, 'Servicio a domicilio', $aliasEnvio, 2, 0);
                    if(isset($producto['id'])){
                        if(setEnvioByAlias($producto['id'], $aliasEnvio, $cod_empresa)){
                            $detalle[$x]['producto'] = $producto['id'];
                            $detalle[$x]['cantidad'] = 1;
                            $detalle[$x]['descuento'] = 0;
                            $detalle[$x]['precioUnitario'] = $orden['envio'];
                        }else{
                            $return['success'] = 0;
                            $return['mensaje'] = "Se creo el producto servicio a domicilio en Fact. Movil pero no se pudo asignar a nuestro sistema";
                            return $return;
                        }
                    }else{
                        $return['success'] = 0;
                        $return['mensaje'] = "No se puede crear la factura porque no se pudo crear el producto -Servicio a domicilio- en Fact. Movil";
                        $return['respFactMovil'] = $producto;
                        return $return;
                    }
                }else{
                    $detalle[$x]['producto'] = $resp['id'];
                    $detalle[$x]['cantidad'] = 1;
                    $detalle[$x]['descuento'] = 0;
                    $detalle[$x]['precioUnitario'] = $orden['envio'];
                }
            }
            
            $pagos=[];
            $x=0;
            foreach($orden['pagos'] as $item){
                $pagos[$x]['formaPagoSri'] = getFormaPago($item['forma_pago']);
                $pagos[$x]['total'] = $item['monto'];
                $pagos[$x]['plazo'] = 0;
                $pagos[$x]['unidadTiempo'] = "Dias";
                $x++;
            }
            
            $factura['infoFactura']['detallesFactura'] = $detalle;
            $factura['pagos']= $pagos;
            
            $respFactura = $Clfact->CreateFactura($factura);
            if(isset($respFactura['claveAcceso'])){
                $return['success'] = 1;
                $return['mensaje'] = "Factura creada correctamente";
                saveOrdenFactura($orden['cod_orden'], $respFactura['claveAcceso'], $respFactura['numeroDocumento']);
            }else{
                $adicional = "";
                if(isset($respFactura['message']))
                    $adicional = $respFactura['message'];
                else
                    $adicional = $Clfact->msgError;    
                
                $return['success'] = 0;
                $return['mensaje'] = "No se pudo crear la factura. ".$adicional;
            }
            
            $return['factura'] = $factura;
            $return['respFactura'] = $respFactura;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Orden no existente, por favor verifica la informacion";
    }
    return $return;
    
}

function anularFactura(){
    global $Clfact;
    global $Clproductos;
    global $cod_empresa;

    //VERIFICAR SI TIENE FACT MOVIL
    if($Clfact->errorToken){
        $return['success'] = 0;
        $return['mensaje'] = $Clfact->errorToken;
        return $return;
    }
    
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    extract($_GET);
    
    $factElectronica = ExistFacturaToOrden($id);
    if(!$factElectronica){
        $return['success'] = 0;
        $return['mensaje'] = "La orden $id no tiene una factura creada";
        return $return;
    }

    $doc = $Clfact->GetFacturaByAutorizacion($factElectronica['clave_acceso']);
    if(!isset($doc['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "La orden $id no se encontro en facturero móvil, por favor intentarlo nuevamente.";
        return $return;
    }

    require_once "../clases/cl_ordenes.php";
    $ClOrdenes = new cl_ordenes();
    $orden = $ClOrdenes->get_orden_array($id);

    if(!$orden){
    	$return['success'] = 0;
        $return['mensaje'] = "Orden no existente, por favor verifica la informacion";
        return $return;
    }

    $usuario = getClienteById($orden['cod_usuario']);
    if(!$usuario){
    	$return['success'] = 0;
        $return['mensaje'] = "Cliente no creado en facturero movil, por favor verificar";
        return $return;
    }

    /*DETALLE DE LA FACTURA*/
    $detalle=[];
    $x=0;
    foreach($orden['detalle'] as $item){
    	$resp = getProductoById($item['cod_producto']);
        if(!$resp){
        	$return['success'] = 0;
            $return['mensaje'] = "No existe el producto ".$item['nombre']." en fact. Movil, por favor verificar ";
            return $return;
        }

        $detalle[$x]['producto'] = $resp['id'];
        $detalle[$x]['cantidad'] = $item['cantidad'];
        $detalle[$x]['descuento'] = $item['descuento'];
        $detalle[$x]['precioUnitario'] = $item['precio'];
        $x++;
    }

    /*AUMENTAR EL ENVIO COMO PRODUCTO*/
    if($orden['envio'] > 0){
        $aliasEnvio='ENVIO_BASE_0';
        $gravaIva = empresaGravaIva($cod_empresa);
        if($gravaIva == 1){
            $aliasEnvio='ENVIO_BASE_12';
        }
            
        $resp = getEnvioByAlias($aliasEnvio, $cod_empresa); 
        if(!$resp){
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo agregar el costo de envio a la factura";
            return $return;
        }
        $detalle[$x]['producto'] = $resp['id'];
        $detalle[$x]['cantidad'] = 1;
        $detalle[$x]['descuento'] = 0;
        $detalle[$x]['precioUnitario'] = $orden['envio'];
    }

    $documento['fechaEmisionDocSustento'] = $orden['fecha'];
    //$documento['numDocModificado'] = $doc['id'];
    $documento['numDocModificado'] = $factElectronica['num_factura'];
    $documento['motivo'] = 'Error al ingresar la información';
    $documento['detallesNotaCredito'] = $detalle;

    $factura['fechaEmision'] = fecha_only();
    $factura['cliente'] = $usuario['id'];
    $factura['infoNotaCredito'] = $documento;


    $respFactura = $Clfact->AnularFactura($factura);
    if(isset($respFactura['claveAcceso'])){
        $return['success'] = 1;
        $return['mensaje'] = "Anulacion creada correctamente";
        $return['respFactura'] = $respFactura;
        $return['infoEnviada'] = $factura;
        AnularOrdenFactura($orden['cod_orden']);
    }else{
        $message = isset($respFactura['message']) ? $respFactura['message'] : "";
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo anular la factura, $message. ".$Clfact->msgError;
    }
    return $return;

}

function empresaGravaIva($cod_empresa){
    $query = "SELECT * FROM tb_empresas WHERE cod_empresa = $cod_empresa";
    $resp = Conexion::buscarRegistro($query);
    if($resp){
        return $resp['envio_grava_iva'];
    }
    return 0;
}

function getClienteById($cod_usuario){
    $query = "SELECT * FROM tb_cliente_facturacion WHERE cod_usuario = $cod_usuario AND cod_sistema_facturacion = ".cod_sistema_facturacion;
    $resp = Conexion::buscarRegistro($query);
    return $resp;
}

function getProductoById($cod_producto){
    $query = "SELECT * FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_sistema_facturacion = ".cod_sistema_facturacion;
    $resp = Conexion::buscarRegistro($query);
    return $resp;
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

function setProductoById($id, $cod_producto){
    $query = "INSERT INTO tb_productos_facturacion(id, cod_producto, cod_sistema_facturacion) 
            VALUES('$id',$cod_producto,'".cod_sistema_facturacion."')";
    $resp = Conexion::ejecutar($query, NULL);
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

function ExistFacturaToOrden($cod_orden){
    $query = "SELECT * FROM tb_orden_factura_electronica WHERE cod_orden = $cod_orden AND estado = 'CREADA'";
    //$query = "SELECT * FROM tb_orden_factura_electronica WHERE cod_orden = $cod_orden";
    $resp = Conexion::buscarRegistro($query);
    return $resp;
}

function getFormaPago($forma){
    if($forma == "E")
        return 1;
    if($forma == "T")
        return 19;  
    if($forma == "P")
        return 18;    
}
?>