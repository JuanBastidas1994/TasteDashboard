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

function getAllProducts(){
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
    
    $productos = getProducts($id, $cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de productos";
    $return['productos'] = $productos;
    $return['ruc'] = $ruc;
    return $return;
}

function setProductToContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    if(setProduct($ruc_id, $product_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Producto ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el producto";
    }
    return $return;
}

function getAllOffices(){
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
    
    $offices = getOffices($id, $cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de sucursales";
    $return['offices'] = $offices;
    $return['ruc'] = $ruc;
    return $return;
}

function setOfficesToContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    if(setBodega($ruc_id, $office_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Bodega ligada correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar la bodega";
    }
    return $return;
}

function getAllPostokens(){
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
    
    $postokens = getPosTokens($id, $cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de Talonarios";
    $return['postokens'] = $postokens;
    $return['ruc'] = $ruc;
    return $return;
}

function getOfficesNoConfig(){
    global $Clcontifico;
    global $cod_empresa;
    
    $offices = getOfficesNoConfiguration($cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de Offices";
    $return['offices'] = $offices;
    return $return;
}

function savePosToken(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    if(storePosToken($ruc_id, $cod_empresa, $api_token, $emisor, $emision, $sec_fac, $sec_dna)){
        $cod_postoken = Conexion::lastId();
        foreach($offices as $office){
            setPostokenToOffice($ruc_id, $office, $cod_postoken);
        }
        $return['success'] = 1;
        $return['mensaje'] = "Talonario creado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al guardar el talonario";
    }
    return $return;
}

function getAllIngredientes(){
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
    
    $ingredientes = getIngredientes($id, $cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de ingredientes";
    $return['ingredientes'] = $ingredientes;
    $return['ruc'] = $ruc;
    return $return;
}

function setIngredienteToContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    if(setIngrediente($ruc_id, $ingrediente_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el ingrediente";
    }
    return $return;
}

function importIngredienteFromContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $precio = ($precio === "") ? $precio = 0 : $precio = $precio;
    if(saveIngrediente($ruc_id, $cod_empresa, $unidad, $precio, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el ingrediente";
    }
    return $return;
}

/*----------------- RECIPIENTES -----------------------*/
function getAllRecipientes(){
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
    
    $recipientes = getRecipientes($id, $cod_empresa);
    
    $return['success'] = 1;
    $return['mensaje'] = "Lista de recipientes";
    $return['recipientes'] = $recipientes;
    $return['ruc'] = $ruc;
    return $return;
}

function setRecipienteToContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    if(setRecipiente($ruc_id, $recipiente_id, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Recipiente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el recipiente";
    }
    return $return;
}

function importRecipientesFromContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $precio = ($precio === "") ? $precio = 0 : $precio = $precio;
    if(saveRecipiente($ruc_id, $cod_empresa, $precio, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = "Recipiente ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar el recipiente";
    }
    return $return;
}

//Productos
function getProducts($id, $cod_empresa){
    global $session;
    $dir = url_sistema.'assets/empresas/'.$session['alias'].'/';
    $query = "SELECT p.cod_producto, p.nombre, p.precio, p.image_min, p.cod_producto_padre, pf.id, pf.name_in_contifico, pf.cod_sistema_facturacion 
            FROM tb_productos p
            LEFT JOIN tb_productos_facturacion pf ON p.cod_producto = pf.cod_producto AND pf.cod_contifico_empresa = $id
            WHERE p.cod_empresa = $cod_empresa
            AND p.estado IN ('A', 'I')";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach($resp as $key => $item){
        $resp[$key]['image_min'] = $dir.$item['image_min'];
    }
    return $resp;
}

function setProduct($ruc_id, $cod_producto, $id_contifico, $contifico_name){
    $query = "DELETE FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_contifico_empresa = $ruc_id";
    Conexion::ejecutar($query,NULL);	
    
    $query = "INSERT INTO tb_productos_facturacion(id, cod_producto, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
		        VALUES('$id_contifico', $cod_producto, '$contifico_name', 1, $ruc_id)";
	return Conexion::ejecutar($query,NULL);	
}

//Bodegas
function getOffices($id, $cod_empresa){
    $query = "SELECT s.cod_sucursal, s.nombre, cs.cod_contifico_empresa, cs.cod_postoken, cs.id_bodega, cs.name_bodega, cs.inventario, cs.cod_contifico_sucursal
            FROM tb_sucursales s
            LEFT JOIN tb_contifico_sucursal cs ON s.cod_sucursal = cs.cod_sucursal AND cs.cod_contifico_empresa = $id
            WHERE s.cod_empresa = $cod_empresa";
    return Conexion::buscarVariosRegistro($query);
}

function setBodega($ruc_id, $cod_sucursal, $id_contifico, $contifico_name){
    $query = "SELECT * FROM tb_contifico_sucursal WHERE cod_sucursal = $cod_sucursal AND cod_contifico_empresa = $ruc_id";
    $bodega = Conexion::buscarRegistro($query);	
    if($bodega){
        $query = "UPDATE tb_contifico_sucursal SET id_bodega = '$id_contifico', name_bodega = '$contifico_name'
               WHERE cod_sucursal = $cod_sucursal AND cod_contifico_empresa = $ruc_id";
	    return Conexion::ejecutar($query,NULL);	
    }else{
        $query = "INSERT INTO tb_contifico_sucursal(id_bodega, cod_sucursal, name_bodega, cod_postoken, cod_contifico_empresa)
		        VALUES('$id_contifico', $cod_sucursal, '$contifico_name', 0, $ruc_id)";
	    return Conexion::ejecutar($query,NULL);	
    }
}

//Postokens
function getPosTokens($id, $cod_empresa){
    $query = "SELECT cod_postoken, pos, emisor, ptoemision, secuencial, secuencial_dna, facturar
                FROM tb_contifico_empresa_postokens
                WHERE cod_empresa = $cod_empresa
                AND cod_contifico_empresa = $id";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach($resp as $key => $item){
        $offices = getOfficesByPostoken($item['cod_postoken']);
        $officesNames = array();
        foreach ($offices as $it) {
            $officesNames[] = $it['nombre'];
        }
        $resp[$key]['sucursalesText'] = implode(',', $officesNames);
        $resp[$key]['sucursales'] = getOfficesByPostoken($item['cod_postoken']);
        $resp[$key]['secuencial_fac'] = $item['emisor'].'-'.$item['ptoemision'].'-'.str_pad($item['secuencial'], 10, "0", STR_PAD_LEFT);;
    }
    return $resp;
}

function getOfficesByPostoken($cod_postoken){
    $query = "SELECT s.cod_sucursal, s.nombre 
                FROM tb_sucursales s
                INNER JOIN tb_contifico_sucursal cs ON cs.cod_sucursal = s.cod_sucursal AND cs.cod_postoken = $cod_postoken";
    return Conexion::buscarVariosRegistro($query);            
}

function getOfficesNoConfiguration($cod_empresa){
    $query = "SELECT s.cod_sucursal, s.nombre, cs.cod_postoken
            FROM tb_sucursales s
            LEFT JOIN tb_contifico_sucursal cs ON s.cod_sucursal = cs.cod_sucursal
            WHERE s.estado IN ('A', 'I')
            AND s.cod_empresa = $cod_empresa";
    $resp = Conexion::buscarVariosRegistro($query);
    foreach($resp as $key => $item){
        $resp[$key]['disable'] = true; 
        $postoken = intval($item['cod_postoken']);
        if($postoken !== null && $postoken !== 0){
            $resp[$key]['disable'] = false;    
        }
    }
    return $resp;
}

function storePosToken($ruc_id, $cod_empresa, $pos, $emisor, $ptoemision, $secuencial_fac, $secuencial_dna){
    $query = "INSERT INTO tb_contifico_empresa_postokens(cod_contifico_empresa, cod_empresa, pos, emisor, ptoemision, secuencial, secuencial_dna, facturar)
		        VALUES($ruc_id, $cod_empresa, '$pos', '$emisor', '$ptoemision', '$secuencial_fac', '$secuencial_dna', 0)";
    return Conexion::ejecutar($query,NULL);
}

function setPostokenToOffice($ruc_id, $cod_sucursal, $cod_postoken){
    $query = "SELECT * FROM tb_contifico_sucursal WHERE cod_sucursal = $cod_sucursal AND cod_contifico_empresa = $ruc_id";
    $bodega = Conexion::buscarRegistro($query);	
    if($bodega){
        $query = "UPDATE tb_contifico_sucursal SET cod_postoken = $cod_postoken
               WHERE cod_sucursal = $cod_sucursal AND cod_contifico_empresa = $ruc_id";
	    return Conexion::ejecutar($query,NULL);	
    }else{
        $query = "INSERT INTO tb_contifico_sucursal(id_bodega, cod_sucursal, name_bodega, cod_postoken, cod_contifico_empresa)
		        VALUES('', $cod_sucursal, '', $cod_postoken, $ruc_id)";
	    return Conexion::ejecutar($query,NULL);	
    }
}

//Ingredientes
function getIngredientes($id, $cod_empresa){
    $query = "SELECT i.cod_ingrediente, i.ingrediente as nombre, i.precio, i.cod_unidad_medida, igf.id, igf.name_in_contifico, igf.cod_sistema_facturacion 
            FROM tb_ingredientes i
            LEFT JOIN tb_ingredientes_facturacion igf ON i.cod_ingrediente = igf.cod_ingrediente AND igf.cod_contifico_empresa = $id
            WHERE i.cod_empresa = $cod_empresa
            AND i.estado IN ('A', 'I')";
    return Conexion::buscarVariosRegistro($query);
}

function setIngrediente($ruc_id, $cod_ingrediente, $id_contifico, $contifico_name){
    $query = "DELETE FROM tb_ingredientes_facturacion WHERE cod_ingrediente = $cod_ingrediente AND cod_contifico_empresa = $ruc_id";
    Conexion::ejecutar($query,NULL);	
    
    $query = "INSERT INTO tb_ingredientes_facturacion(id, cod_ingrediente, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
		        VALUES('$id_contifico', $cod_ingrediente, '$contifico_name', 1, $ruc_id)";
	return Conexion::ejecutar($query,NULL);	
}

function saveIngrediente($ruc_id, $cod_empresa, $unidad, $precio, $id_contifico, $contifico_name){
    $query = "INSERT INTO tb_ingredientes(cod_empresa, cod_unidad_medida, ingrediente, precio, estado)
                VALUES($cod_empresa, '$unidad', '$contifico_name', $precio, 'A')";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp){
        $cod_ingrediente = Conexion::lastId();
        setIngrediente($ruc_id, $cod_ingrediente, $id_contifico, $contifico_name);
    }
    return $resp;
}

//Recipientes
function getRecipientes($id, $cod_empresa){
    $query = "SELECT i.cod_recipiente, i.nombre, i.precio, igf.id, igf.name_in_contifico, igf.cod_sistema_facturacion 
            FROM tb_recipientes i
            LEFT JOIN tb_recipientes_facturacion igf ON i.cod_recipiente = igf.cod_recipiente AND igf.cod_contifico_empresa = $id
            WHERE i.cod_empresa = $cod_empresa
            AND i.estado IN ('A', 'I')";
    return Conexion::buscarVariosRegistro($query);
}

function setRecipiente($ruc_id, $cod_recipiente, $id_contifico, $contifico_name){
    $query = "DELETE FROM tb_recipientes_facturacion WHERE cod_recipiente = $cod_recipiente AND cod_contifico_empresa = $ruc_id";
    Conexion::ejecutar($query,NULL);	
    
    $query = "INSERT INTO tb_recipientes_facturacion(id, cod_recipiente, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
		        VALUES('$id_contifico', $cod_recipiente, '$contifico_name', 1, $ruc_id)";
	return Conexion::ejecutar($query,NULL);	
}

function saveRecipiente($ruc_id, $cod_empresa, $precio, $id_contifico, $contifico_name){
    $query = "INSERT INTO tb_recipientes(cod_empresa, nombre, precio, estado)
                VALUES($cod_empresa, '$contifico_name', $precio, 'A')";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp){
        $cod_recipiente = Conexion::lastId();
        setRecipiente($ruc_id, $cod_recipiente, $id_contifico, $contifico_name);
    }
    return $resp;
}

//DOMICILIO Y ADICIONALES
function setDomicilioAdicionalesToContifico(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $ruc = $Clcontifico->getRuc($ruc_id,$cod_empresa);
    if(!$ruc){
        $return['success'] = 0;
        $return['mensaje'] = "Ruc no existente";
        return $return;
    }
    
    $alias = "ADICIONALES";
    $tipoText = "Productos Adicionales";
    if($tipo == "DOMICILIO"){
        $alias = "ENVIO_DOMICILIO";  
        $tipoText = "Servicio a Domicilio";
    }
    
    if(setDomicilioAdicional($ruc_id, $alias, $cod_empresa, $contifico_id, $contifico_name)){
        $return['success'] = 1;
        $return['mensaje'] = $tipoText." ligado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al ligar ".$tipoText;
    }
    return $return;
}

function setDomicilioAdicional($ruc_id, $alias, $cod_empresa, $id_contifico, $contifico_name){
    $query = "DELETE FROM tb_productos_envio_facturacion WHERE alias = '$alias' AND cod_contifico_empresa = $ruc_id AND cod_empresa = $cod_empresa";
    Conexion::ejecutar($query,NULL);	
    
    $query = "INSERT INTO tb_productos_envio_facturacion(id, alias, cod_empresa, name_in_contifico, cod_sistema_facturacion, cod_contifico_empresa)
		        VALUES('$id_contifico', '$alias', $cod_empresa, '$contifico_name', 1, $ruc_id)";
	return Conexion::ejecutar($query,NULL);	
}

//Activar Talonarios
function activateTalonario(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $isFacturar = 0;
    $msg = "Talonario inactivado correctamente";
    if($estado){
        $isFacturar = 1;
        $msg = "Talonario activado correctamente";
    }
    
    $query = "UPDATE tb_contifico_empresa_postokens SET facturar = $isFacturar WHERE cod_postoken = $id";
    if(Conexion::ejecutar($query,NULL)){
        $return['success'] = 1;
        $return['mensaje'] = $msg;
        $return['estado'] = $estado;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al realizar la acción, por favor intentelo nuevamente";
    }
    return $return;
}

//Activar Inventario
function activateInventario(){
    global $Clcontifico;
    global $cod_empresa;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $isInventario = 0;
    $msg = "Bodega inactivado para inventario correctamente";
    if($estado){
        $isInventario = 1;
        $msg = "Bodega activado para inventario correctamente";
    }
    
    $query = "UPDATE tb_contifico_sucursal SET inventario = $isInventario WHERE cod_contifico_sucursal = $id";
    if(Conexion::ejecutar($query,NULL)){
        $return['success'] = 1;
        $return['mensaje'] = $msg;
        $return['estado'] = $estado;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al realizar la acción, por favor intentelo nuevamente";
    }
    return $return;
}
?>