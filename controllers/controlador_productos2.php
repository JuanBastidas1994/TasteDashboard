<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos.php";
$Clproductos = new cl_productos();
$session = getSession();
$variaciones = null;

controller_create();

function crear(){
    global $Clproductos;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    /*$return['success'] = 1;
    $return['mensaje'] = "cEti ".count($cmbEtiquetas);
    return $return;*/


    $precio = floatval($txt_precio);
    $precio_no_tax = floatval($precio) / 1.12;
    $valor_iva = floatval($precio) - floatval($precio_no_tax);
    $precio_no_tax=($txt_precio_no_taxC);
    $valor_iva =($txt_ivaC);
    
    $desc_larga = editor_encode($desc_larga);
    $nameImg = 'product_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;
    
    $Clproductos->nombre = $txt_nombre;
    $Clproductos->desc_corta = $txt_descripcion_corta;
    $Clproductos->desc_larga = $desc_larga;
    $Clproductos->image_min = $nameImgMin;
    $Clproductos->image_max = $nameImg;
    $Clproductos->costo = formatFloat($txt_costo);
    $Clproductos->precio = formatFloat($txt_precio);
    $Clproductos->precio_anterior = formatFloat($txt_precio_anterior);
    $Clproductos->precio_no_tax = formatFloat4($precio_no_tax);
    $Clproductos->iva_valor = formatFloat4($valor_iva);
    $Clproductos->iva_porcentaje = 12;
    $Clproductos->cod_producto_padre = $cod_producto_padre;
    $Clproductos->categorias = $cmb_categoria;
    $Clproductos->peso = $txt_peso;
    $Clproductos->sku = $txt_sku;
    

    if(isset($_POST['chk_estado']))
        $Clproductos->estado = 'A';
    else
        $Clproductos->estado = 'I';

    if(isset($_POST['chk_detalle']))
        $Clproductos->open_detalle = 1;
    else
        $Clproductos->open_detalle = 0;

    if(isset($_POST['chk_base']))
        $Clproductos->cobra_iva = 1;
    else
        $Clproductos->cobra_iva = 0;
        
    if(isset($_POST['chk_combo']))
        $Clproductos->is_combo = 1;
    else
        $Clproductos->is_combo = 0;    

    if(isset($_POST['chk_fSinStock']))
        $Clproductos->facturar_sin_stock = 1;
    else
        $Clproductos->facturar_sin_stock = 0;    


    if(!isset($_POST['cod_producto'])){
        
        //VALIDAR AUXILIAR
        $aux = "";
        do{
            $alias = create_slug(sinTildes($txt_nombre.$aux));
            $aux = intval(rand(1,100)); 
        }while(!$Clproductos->aliasDisponible($alias));
        $Clproductos->alias = $alias;
        $idP=0;
        if($Clproductos->crear($idP)){
            $return['success'] = 1;
            $return['mensaje'] = "Producto creado correctamente";
            $return['id'] = $idP;
            $return['alias'] = $alias;
            $cod_producto=$idP;
            /*SUBIR IMAGEN*/
            if($txt_crop != "" && $txt_crop_min != ""){
                base64ToImage($txt_crop, $nameImg);
                base64ToImage($txt_crop_min, $nameImgMin);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                $img3 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImgMin;
                copy($img1, $img2);
                copy($img1, $img3);
            }

            //INSERT ETIQUETAS
            for ($i=0; $i < count($cmbEtiquetas); $i++) { 
                $Clproductos->setEtiquetas($cod_producto, $cmbEtiquetas[$i]);
            }

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el producto, por favor vuelva a intentarlo";
        }
    }else{
        $Clproductos->cod_producto = $cod_producto;
        if($Clproductos->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Producto editado correctamente";
            $return['id'] = $Clproductos->cod_producto;
            $idP = $Clproductos->cod_producto;

            //INSERT ETIQUETAS
            $Clproductos->delEtiquetas($idP);
            for ($i=0; $i < count($cmbEtiquetas); $i++) { 
                $Clproductos->setEtiquetas($idP, $cmbEtiquetas[$i]);
            }

            $data = NULL;
            if($Clproductos->getArray($cod_producto, $data)){
                $return['alias'] = $data['alias'];
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['image_max'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clproductos->setImage($nameImgMax, 'max', $idP);
                        if($cambio){
                            deleteFile($data['image_max']);
                        }
                    }
                }
                if($txt_crop_min != ""){
                    $nameImgMin = ($data['image_min']!=$data['image_max']) ? $data['image_min'] : 'min_'.$data['image_min'];
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMin = getNameImagejpg($nameImgMin, $cambio);
                    if(base64ToImage($txt_crop_min, $nameImgMin)){
                        $Clproductos->setImage($nameImgMin, 'min', $idP);
                        if($cambio){
                            deleteFile($data['image_min']);
                        }
                    }
                }
                $return['imagen'] = "editada";
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el producto";
        }
    }
    
    //DISPONIBILIDAD
    for($x=0; $x<count($id); $x++){
        $precioReplace=$precioR[$x]; 
        $cod_sucursal = $id[$x];
        $precio = $txt_precio_sucursal[$x];
        $precio_anterior = $txt_precio_anterior_sucursal[$x];
        if($select[$x]==1)
            $estado = 'A';
        else
            $estado = 'I';
            
        $Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado,$precioReplace);
    }
    
    return $return;
}

function formatFloat($float){
    return round($float,2);
}

function formatFloat4($float){
    return round($float,4);
}

function get(){
    global $Clproductos;
    if(!isset($_GET['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clproductos->getArray($cod_producto, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Producto encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
  global $Clproductos;
  if(!isset($_GET['cod_producto']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

  extract($_GET);

    $resp = $Clproductos->set_estado($cod_producto, $estado);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Producto editado correctamente";
      if($estado == "D")
        $return['mensaje'] = "Producto eliminado correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al editar el producto";
    }
    return $return;
}

function remove_categoria(){
  global $Clproductos;
  if(!isset($_GET['cod_producto']) || !isset($_GET['cod_categoria'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

  extract($_GET);

    $resp = $Clproductos->remove_categoria($cod_producto, $cod_categoria);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = html_entity_decode("Producto removido de la categor&iacute;a");
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al remover el producto de esta categoria, por favor intentelo m&aacute;s tarde";
    }
    return $return;
}

function upload_img(){
    global $Clproductos;
    global $session;
    if(!isset($_POST['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $nameImg = 'galery-'.$cod_producto.'-'.datetime_format().'.jpg';
    if($txt_crop_galeria != ""){
        /*CODIGO PARA GUARDAR*/
        
        base64ToImage($txt_crop_galeria, $nameImg);
        $id=0;
        if($Clproductos->add_img_product($cod_producto, $nameImg, $id)){
            $return['success'] = 1;
            $return['mensaje'] = "Imagen Subida con exito";

            $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
            $img = $files.$nameImg;
            $html =  '<div class="col-md-4 col-sm-4 col-xs-12">
                    <img src="'.$img.'" style="width: 100%;height: 120px;object-fit: cover;"/>
                    <span data-value="'.$id.'" class="deleteImg custom-file-container__image-multi-preview__single-image-clear">
                        <span class="custom-file-container__image-multi-preview__single-image-clear__icon" data-upload-token="fbjn5kugte6vr2cegadi4t">×</span>
                    </span>
                  </div>';
            $return['html'] = $html;      

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al agregar la imagen al producto, por favor intentelo nuevamente";
        }
        
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Debes recortar la imagen";
    }
    return $return;
}

function delete_img(){
    global $Clproductos;
    if(!isset($_GET['cod_imagen'])){
            $return['success'] = 0;
            $return['mensaje'] = "Falta informacion";
            return $return;
    }
    extract($_GET);

    $resp = $Clproductos->delete_imagen($cod_imagen);
    if(count($resp)>0){
      $return['success'] = 1;
      $return['mensaje'] = "Imagen eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al eliminar la imagen";
    }
    return $return;
}

function add_opcion(){
    global $Clproductos;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_POST);

    if(count($txt_nomItemDet) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Asegúrate de agregar las opciones correctamente";
        return $return;
    }

    $id=0;
    if($Clproductos->crear_opcion($cod_producto,$txt_opcion_titulo, $txt_opciones_cantidad, $txt_opciones_cantidad_max, $cmb_productos, $tipo_opcion, $cmb_isCheck, $id)){
        $return['success'] = 1;
        $return['mensaje'] = "Opcion creada correctamente";
        $return['id'] = $id;
        $return['html'] = '<tr>
                          <td>'.$txt_opcion_titulo.'</td>
                          <td>'.implode(", ", $txt_nomItemDet).'</td>
                          <td>'.$txt_opciones_cantidad.'</td>
                          <td>'.$txt_opciones_cantidad_max.'</td>
                          <td>
                            <a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEditarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="edit-2"></i></a>
                            <a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEliminarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                          </td>
                        </tr>';
        
        /*Insertar Items*/
        if ($tipo_opcion == 1){
            $txt_nomItemDet=$txt_codItemDet;
        }
        for($i=0; $i<count($txt_nomItemDet); $i++){
           $aumentarPrecio = $chk_is[$i];
            $Clproductos->crear_opcion_detalle($id, $txt_nomItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
        }
        
        
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al crear la opcion, por favor intentalo nuevamente";
    }
    return $return;
}

function add_combo(){
    global $Clproductos;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_POST);

    $y=0;
    $id=0;
    $tableC="";
    $Clproductos->delete_Combo($cod_producto);
    for($i=0; $i<count($txt_nomCombo); $i++){
           $aumentarPrecio = $chk_is[$i];
            $respC = $Clproductos->crear_opcion_combo($cod_producto, $txt_cod_hijo[$i], $txt_cantidadCombo[$i],$id);
            $tableC .='<tr class="trItem">
                          <td>
                                <input class="form-control txt_cod_producDetalle" name="txt_cod_producDetalle[]" value="'.$id.'" type="hidden">
                                <input class="form-control txt_cod_hijo" name="txt_cod_hijo[]" value="'.$txt_cod_hijo[$i].'" type="hidden">
                                <input class="form-control txt_peso_combo" name="txt_peso_combo[]" value="'.$txt_peso_combo[$i].'" type="hidden">
                                <input class="form-control txt_nomCombo" name="txt_nomCombo[]" value="'.$txt_nomCombo[$i].'"  readonly>
                            </td>
                          <td><input type="number" class="form-control txt_cantidadCombo" name="txt_cantidadCombo[]" data-peso="'.$txt_peso_combo[$i].'" min="1" placeholder="cantidad" value="'.$txt_cantidadCombo[$i].'" style="text-align: right;"></td>    
                          <td>
                            <a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEliminarCombo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                          </td>
                        </tr>';
            if($respC)
            $y++;
        }
    
    if($y == count($txt_nomCombo)) 
    {
        $return['success'] = 1;
        $return['mensaje'] = "Opcion creada correctamente";
        $return['html'] = $tableC;
    }
    else
    {
        $return['success'] = 0;
        $return['mensaje'] = "Ups, problemas al crear tu combo. Intentalo mas tarde...";
    }
        
    return $return;
}

/*--NUEVO--*/
function importar(){
    global $Clproductos;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_GET);

    $Info =$Clproductos->getProductoOpciones($cod_productoOpciones);
    $productos = "";
    if ($Info['productos'] != "" or $Info['productos'] != null)
    {
        $productos = json_decode($Info['productos']);
    }
    if($Clproductos->crear_opcion($cod_producto,$Info['titulo'], $Info['cantidad_min'], $Info['cantidad'],$productos , $Info['isDatabase'], $Info['isCheck'], $id)){
        $return['success'] = 1;
        $return['mensaje'] = "Opcion creada correctamente";
        $return['id'] = $id;
        
        $item = array();
        $aumentar_precio = array();
        $precio = array();
        $posicion = array();
        $detalles =$Clproductos->select_opciones($cod_productoOpciones);   
        foreach ($detalles as $l) {
            $item[]= $l['itemPrincipal'];
            $aumentar_precio[]= $l['aumentar_precio'];
            $precio[]= $l['precio'];
            $posicion[]= $l['posicion'];
        }

        for($i=0; $i<count($item); $i++){
            $Clproductos->crear_opcion_detalle($id, $item[$i], $aumentar_precio[$i], $precio[$i], $posicion[$i]);
        }
        
        
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al crear la opcion, por favor intentalo nuevamente";
    }
    return $return;
}
/*--NUEVO--*/

function select_opcion(){
    global $Clproductos;
    
    extract($_GET);
    $opcSelesct="";
    $html="";
    
    $resp = $Clproductos->select_opciones($cod_producto_opcion);
    if($resp){
        $itemP = array();
        foreach($resp as $r){
            $return['data'] = $r;
            
            $check = "";
            $isCheck = 0;
            $readonly = "readonly";
            if($r['aumentar_precio'] == 1){
                $check = "checked";
                $isCheck = 1;
                $readonly = "";
            }
            
            $html.='<tr class="trItem" data-id="'.$r['cod_producto_opciones_detalle'].'">
                        <td>
                            <input class="form-control txt_id_det" name="cod_detalle[]" value="'.$r['cod_producto_opciones_detalle'].'" style="display:none">
                            <input class="form-control txtnomDet" name="txt_nomItemDet[]" value="'.$r['item'].'">
                        </td>
                        <td style="text-align: center;">
                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                            <input class="form-control chk_is" name="chk_is[]" value="'.$isCheck.'" type="hidden">
                                  <input class="precioCheck" type="checkbox" name="precioCheck[]" '.$check.'/>
                                  <span class="slider round"></span>
                            </label>
                        </td>
                        <td><input type="number" class="form-control txt_precio" name="txt_precio[]" placeholder="precio" value="'.$r['precio'].'" style="text-align: right;" '.$readonly.'></td>
                        <td style="text-align: center;"><button type="button" class="btn btn-danger btnDelItem"><i data-feather="trash"></i></button></td>
                    </tr>';
                    $itemP[]=$r['item'];
          //  $opcSelesct.='<option value="'.$r['item'].'">'.$r['item'].'</option>';
        }
        $resp = $Clproductos->lista();
           if($resp){
               foreach($resp as $r){
                    $selected = "";
                    if(in_array($r['nombre'], $itemP))
                      $selected = 'selected="selected"';
                   $opcSelesct.='<option '.$selected.' value="'.$r['cod_producto'].'" data-peso="'.$r['peso'].'" data-precio="'.$r['precio'].'">'.$r['nombre'].'</option>';
               }
           }
        $return['html'] = $html;
        $return['opcSelesct'] = $opcSelesct;
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al obtener los datos";
    }
    return $return;
}

function edit_opcion(){
     global $Clproductos;
     
     extract($_POST);

    if(count($txt_nomItemDet) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Asegúrate de agregar las opciones correctamente";
        return $return;
    }
     
     //$res = $Clproductos->editar_opciones($cod_producto_opcion, $txt_opcion_titulo, $txt_opciones_cantidad, $txt_opciones_cantidad_max, $cmb_productos, $tipo_opcion, $cmb_isCheck);
     
     if($Clproductos->editar_opciones($cod_producto_opcion, htmlentities($txt_opcion_titulo), $txt_opciones_cantidad, $txt_opciones_cantidad_max, $tipo_opcion, $cmb_isCheck)){
       //  $return['query']=$Clproductos->Qeditar_opciones($cod_producto_opcion, $txt_opcion_titulo, $txt_opciones_cantidad, $txt_opciones_cantidad_max, $cmb_productos, $tipo_opcion, $cmb_isCheck);
         /*Insertar Items*/
        if ($tipo_opcion == 1){
            $txt_nomItemDet = $txt_codItemDet;
        }
        for($i=0; $i< count($txt_nomItemDet); $i++){
                $aumentarPrecio = $chk_is[$i];
                
            if($Clproductos->select_opcion_detalle($cod_detalle[$i])){
                $Clproductos->editar_opcion_detalle($cod_detalle[$i], $txt_nomItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
            }
            else{
                $Clproductos->crear_opcion_detalle($cod_producto_opcion, $txt_nomItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
            }
        }
        
        $return['success'] = 1;
        $return['mensaje'] = "Editado correctamente";
     }
     else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar";
     }
    return $return;
}

function delete_opcion(){
    global $Clproductos;
    if(!isset($_GET['cod_opcion'])){
            $return['success'] = 0;
            $return['mensaje'] = "Falta informacion";
            return $return;
    }
    extract($_GET);

    $resp = $Clproductos->delete_opcion($cod_opcion);
    if(($resp)){
        $Clproductos->delete_opciones_detalle($cod_opcion);
      $return['success'] = 1;
      $return['mensaje'] = "opcion eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al eliminar la opcion";
    }
    return $return;
}

function eliminarUnaOpcionDetalle(){
    global $Clproductos;
    extract($_GET);

    $row = $Clproductos->getOpcionCabecera($cod_opcion);

    if($row['cant_detalle'] > 1){
        if($Clproductos->delete_opcion_detalle($cod_opcion)){
            $return['success'] = 1;
            $return['mensaje'] = "Opción eliminada correctamente";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al eliminar la opción";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No se puede eliminar el último ítem, por favor elimine toda la opción o agregue nuevos ítems y luego elimine este ítem";
    }
    return $return;
}

function delete_opcionCombo(){
    global $Clproductos;
    if(!isset($_GET['cod_opcion'])){
            $return['success'] = 0;
            $return['mensaje'] = "Falta informacion";
            return $return;
    }
    extract($_GET);

    $resp = $Clproductos->delete_opcionCombo($cod_opcion);
    if(($resp)){
      $return['success'] = 1;
      $return['mensaje'] = "opcion eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al eliminar la opcion";
    }
    return $return;
}

function detectarColores(){
    $html = "";
    $variantes = $_POST['txt_opcion_titulo'];
    $opciones = $_POST['cmb_variante_productos'];
    $tipos = $_POST['cmb_variante_tipo'];
    foreach($tipos as $key=>$items){
        if($items == "color"){
            $colores = $opciones[$key];
            foreach($colores as $color){
                $html .= '<tr>
                        <td>'.$color.'</td>
                        <td>
                            <input type="color" value="" name="color-'.$color.'" class="form-control"/>
                        </td>
                    </tr>';
            }
        }
    }
    $return['success'] = 1;
    $return['mensaje'] = "Colores detectados";
    $return['html'] = $html;
    return $return;
}

function variantes(){
    //var_dump($_POST);
    global $variaciones;
    $variantes = $_POST['txt_opcion_titulo'];
    $opciones = $_POST['cmb_variante_productos'];
    $info = null;
    recursive($opciones, 0, $info);
    
    $display = "none;";
    if($_POST['tipo_empresa'] <> 1)
        $display = "initial;";
    
    $sku_padre = "";
    if(isset($_POST['sku_padre']))
        $sku_padre = $_POST['sku_padre'];

    $html = "";
    foreach ($variaciones as $items) {
        $json = json_encode($items);
        $base64 = base64_encode($json);
        $texto = implode("/", $items);
        $html .= '<tr>
            <td>'.$texto.'</td>
            <td>
                <input type="text" value="0.00" name="txt_precio_variante[]" class="form-control"/>
                <input type="hidden" value="'.$base64.'" name="txt_atributos_variante[]" class="form-control"/>
            </td>
            <td style="display: '.$display.'">
                <input type="text" name="txt_variante_sku[]" class="form-control" placeholder="SKU" value="'.$sku_padre.'"/>
            </td>
        </tr>';
    }
    $return['success'] = 1;
    $return['mensaje'] = "variante agregada";
    $return['html'] = $html;
    return $return;
}

function guardar_caracteristicas(){
    global $Clproductos;
    global $session;
    if(!isset($_POST['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    $html = "";
    $cod_producto = $_POST['cod_producto'];
    $caracteristicas = $_POST['txt_opcion_titulo'];
    $opciones = $_POST['cmb_variante_productos'];
    $tipos = $_POST['cmb_variante_tipo'];
    foreach($tipos as $key=>$tipo){
        
        $caracteristica = $caracteristicas[$key];
        
        $idCaracteristica = $Clproductos->setCaracteristica($cod_producto, $caracteristica, $tipo);
        if($idCaracteristica){
            //GUARDAR DETALLE
            $items = $opciones[$key];
            foreach($items as $item){
                $detalle2 = "";
                if($tipo == "color"){
                    $detalle2 = isset($_POST['color-'.$item]) ? $_POST['color-'.$item] : "";
                }
                $Clproductos->setCaracteristicaDetalle($idCaracteristica, $item, $detalle2);
            }
            
            
        }else{
            //NO PUDO GUARDAR CARACTERISTICA
        }
    }
    
    
    $return['success'] = 1;
    $return['mensaje'] = "Características guardadas correctamente";
    return $return;
}

function guardar_atributos_variante(){
    global $Clproductos;
    global $session;
    if(!isset($_POST['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
    $cod_producto = $_POST['cod_producto'];
    $opciones = $_POST['cmbAtributoVariante'];

    $resp = $Clproductos->set_variante_caracteristica($cod_producto, $opciones);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Atributos asignados correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al asignar los atributos";
    }
    return $return;
}

function guardar_variantes(){
    global $Clproductos;
    global $session;
    if(!isset($_POST['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    $html="";
    $cod_producto = $_POST['cod_producto'];
    $variantes = $_POST['txt_opcion_titulo'];
    $opciones = $_POST['cmb_variante_productos'];

    $items_precio = $_POST['txt_precio_variante'];
    $items_atributos = $_POST['txt_atributos_texto'];
    $items_codigo = $_POST['txt_atributos_codigo'];
    $items_sku = $_POST['txt_variante_sku'];

    $array = NULL;
    if($Clproductos->getArray($cod_producto, $array)){
        $producto = new cl_productos();
        $producto->desc_corta = $array['desc_corta'];
        $producto->desc_larga = $array['desc_larga'];
        $producto->open_detalle = $array['open_detalle'];
        $producto->costo = floatval($array['costo']);
        $producto->peso = ($array['peso']);
        $producto->sku = ($array['sku']);
        $producto->is_combo = ($array['is_combo']);
        $producto->cobra_iva = ($array['cobra_iva']);
        $producto->iva_porcentaje = 12;
        $producto->cod_producto_padre = $cod_producto;
        $producto->categorias = $Clproductos->get_categorias($cod_producto);
        $producto->facturar_sin_stock = $array['noStock'];
        $producto->estado = 'A';

        for($x=0; $x<count($items_atributos); $x++){
            $atributos = json_decode(base64_decode($items_atributos[$x]),true);
            $codigos = json_decode(base64_decode($items_codigo[$x]),true);
            
            //PRECIO
            $precio = number_format(floatval($items_precio[$x]),2);
            $precio_no_tax = number_format(($precio / 1.12),2);
            $valor_iva = $precio - $precio_no_tax;
            $producto->precio = $precio;
            $producto->precio_anterior = 0;
            $producto->precio_no_tax = $precio_no_tax;
            $producto->iva_valor = $valor_iva;

            //NOMBRE
            $nombre = $array['nombre']." ".implode("/",$atributos);
            $producto->nombre = $nombre;
            
            $aux = "";
            do{
                $alias = create_slug(sinTildes($nombre.$aux));
                $aux = intval(rand(1,100)); 
            }while(!$Clproductos->aliasDisponible($alias));

            $producto->alias = $alias;
            $producto->sku = $items_sku[$x];

            //IMAGEN
            $nameImg = 'product_'.datetime_format().'_'.$x.'.jpg';
            $producto->image_min = $nameImg;
            $producto->image_max = $nameImg;
            
            //DATA DISPONIBILIDAD
            $disponibilidad = $producto->getdisponibilidadByproduct($cod_producto);
            
            $id=0;
            if($producto->crear($id)){
                //COPIAR IMAGEN DEL PRODUCTO PADRE
                $img1 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$array['image_min'];
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                copy($img1, $img2);

                //AGREGAR PRODUCTO VARIANTE
                $producto->set_variante_caracteristica($id,$codigos);
                $producto->set_variantes($id,$atributos);
                
                //AGREGAR DISPONIBILIDAD AL PRODUCTO PADRE
                if($disponibilidad){
                     foreach ($disponibilidad as $d) {
                         $producto->setDisponibilidad($id,$d['cod_sucursal'], $d['precio'], $d['precio_anterior'], $d['estado'],$d['replacePrice']);   
                     }
                         
                }
                
                $nombreVariante = $nombre;
                $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
                $img = $files.$array['image_min'];
                $imagenVariante = $img;
                $htmlOpciones = implode("/", $atributos);
                $html .= '
                <tr>
                  <td class="text-center">
                      <span><img src="'.$imagenVariante.'" class="profile-img" alt="'.$nombreVariante.'"></span>
                  </td>
                  <td>'.$htmlOpciones.'</td>
                  <td>$'.$precio.'</td>
                  <td>
                    <a target="_blank" href="crear_productos.php?id='.$alias.'" data-value="'.$alias.'"  class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar Variante"><i data-feather="edit-2"></i></a>
                  </td>
                </tr>';
                                            
                
            }
        }
                
        $return['success'] = 1;
        $return['mensaje'] = "Variantes agregadas correctamente";
        $return['html'] = $html;
       
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function recursive($array, $posicion, &$data){
    global $variaciones;
    $num_items = 0;
    $opcion = $array[$posicion];
    foreach($opcion as $key=>$value){
        if(isset($array[$posicion+1])){
            $data[0][$posicion] = $value;
            recursive($array,$posicion+1,$data);
        }else{
            $data[0][$posicion] = $value;
            $variaciones[] = $data[0];
        }
    }
}

//DISPONIBILIDAD
function setDisponibilidad(){
    global $Clproductos;
    
    extract($_POST);

    for($x=0; $x<count($id); $x++){
        $cod_sucursal = $id[$x];
        $precio = $txt_precio_sucursal[$x];
        $precio_anterior = $txt_precio_anterior_sucursal[$x];
        if($select[$x]==1)
            $estado = 'A';
        else
            $estado = 'I';
        $Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado);
    }
    $return['success'] = 1;
    $return['mensaje'] = "Disponibilidad actualizada";
    return $return;
}

function getOpciones(){
   global $Clproductos; 
   
   $html = "";
   $resp = $Clproductos->lista();
   if($resp){
       foreach($resp as $r){
           $html.='<option value="'.$r['cod_producto'].'" data-peso="'.$r['peso'].'" data-precio="'.$r['precio'].'">'.$r['nombre'].'</option>';
       }
       $return['success'] = 1;
       $return['mensaje'] = "Lista obtenida";
       $return['html'] = $html;
   }
   else{
       $return['success'] = 0;
       $return['mensaje'] = "Error al obtener datos";
   }   
   return $return;
}

function getOpcionesCombo(){
   global $Clproductos; 
   extract($_GET);
   $html = "";
   
   $cod_producto = 0;
   if(isset($_GET['cod_producto']))
   $cod_producto=$_GET['cod_producto'];
   
   $listaProductos = $Clproductos->get_Combo($cod_producto);
   $listaP = $Clproductos->lista();
   if($listaP)
   {
        foreach ($listaP as $c) {
            $selected = "";
            if(in_array($c['cod_producto'], $listaProductos))
              $selected = 'selected="selected"';
          $html.='<option '.$selected.' value="'.$c['cod_producto'].'" data-peso="'.$c['peso'].'">'.$c['nombre'].'</option>';
        }
        $return['success'] = 1;
        $return['mensaje'] = "Lista obtenida";
        $return['html'] = $html;
   }
   else{
       $return['success'] = 0;
       $return['mensaje'] = "Error al obtener datos";
   }  
  /* $resp = $Clproductos->lista();
   if($resp){
       foreach($resp as $r){
           $html.='<option value="'.$r['cod_producto'].'" data-peso="'.$r['peso'].'">'.$r['nombre'].'</option>';
       }
       $return['success'] = 1;
       $return['mensaje'] = "Lista obtenida";
       $return['html'] = $html;
   }
   else{
       $return['success'] = 0;
       $return['mensaje'] = "Error al obtener datos";
   } */  
   return $return;
}

function actualizar(){
    global $Clproductos;

    extract($_POST);
    if($tipo == "opciones")
    {
        for ($i=0; $i < count($datos); $i++) { 
        $Clproductos->actPosicionOpciones($datos[$i], $i+1);
        }
    }
    if($tipo == "detalles")
    {
        for ($i=0; $i < count($datos); $i++) { 
        $Clproductos->actPosicionDetalles($datos[$i], $i+1);
        }
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function cambiarVarianteVisualizacion(){
    global $Clproductos;
    extract($_GET);

    if($Clproductos->cambiarVarianteVisualizacion($tipo, $cod_producto)){
        $return['success'] = 1;
        $return['mensaje'] = "Visualización de la variante editada";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar la visualización de la variante";
    }
    return $return;
}
?>