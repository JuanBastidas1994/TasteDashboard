<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos.php";
require_once "../clases/cl_empresas.php";
$Clproductos = new cl_productos();
$session = getSession();
require_once "../templates/producto_variantes.php";

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

    error_log('[DEBUG precio_especial] POST keys: ' . implode(', ', array_keys($_POST)));
    error_log('[DEBUG precio_especial] chk=' . ($_POST['chk_precio_especial'] ?? 'NOT SET') . ' precio=' . ($_POST['txt_precio_especial'] ?? 'NOT SET') . ' fin=' . ($_POST['fecha_especial_fin'] ?? 'NOT SET'));

    $minutos_preparacion = intval($txt_preparacion);
    if (isset($txt_preparacion_unidad) && $txt_preparacion_unidad === 'dias') {
        $minutos_preparacion = $minutos_preparacion * 1440;
    } elseif ($minutos_preparacion % 5 != 0) {
        return [ 'success' => 0, 'mensaje' => 'El tiempo de preparación debe ser múltiplo de 5' ];
    }

    $precio = floatval($txt_precio);
    $precio_no_tax = floatval($precio) / 1.15;
    $valor_iva = floatval($precio) - floatval($precio_no_tax);
    $precio_no_tax=($txt_precio_no_taxC);
    $valor_iva =($txt_ivaC);
    
    $desc_larga = editor_encode($desc_larga);
    
    $Clproductos->nombre = $txt_nombre;
    $Clproductos->desc_corta = $txt_descripcion_corta;
    $Clproductos->desc_larga = $desc_larga;
    $Clproductos->costo = formatFloat($txt_costo);
    $Clproductos->precio = formatFloat($txt_precio);
    $Clproductos->precio_anterior = formatFloat($txt_precio_anterior);
    $Clproductos->precio_no_tax = formatFloat4($precio_no_tax);
    $Clproductos->iva_valor = formatFloat4($valor_iva);
    $Clproductos->iva_porcentaje = 12;
    $Clproductos->cod_producto_padre = $cod_producto_padre;
    $Clproductos->categorias = $cmb_categoria;
    $Clproductos->peso = $txt_peso;
    $Clproductos->volumen = $txt_volumen;
    $Clproductos->sku = $txt_sku;
    $Clproductos->image_min = '';
    $Clproductos->image_max = '';
    $Clproductos->tiempo_preparacion = $minutos_preparacion;
    $Clproductos->estado = (isset($_POST['chk_estado'])) ? 'A' : 'I';
    $Clproductos->open_detalle = (isset($_POST['chk_detalle'])) ? 0 : 1;
    $Clproductos->cobra_iva = (isset($_POST['chk_base'])) ? 1 : 0;
    $Clproductos->is_combo = (isset($_POST['chk_combo'])) ? 1 : 0;
    $Clproductos->facturar_sin_stock = (isset($_POST['chk_fSinStock'])) ? 1 : 0;
    $Clproductos->venta_delivery = (isset($_POST['venta_delivery'])) ? 1 : 0;
    $Clproductos->venta_pickup   = (isset($_POST['venta_pickup']))   ? 1 : 0;
    $Clproductos->venta_mesa     = (isset($_POST['venta_mesa']))     ? 1 : 0;

    $Clproductos->precio_especial        = null;
    $Clproductos->precio_especial_inicio = null;
    $Clproductos->precio_especial_fin    = null;

    if (isset($_POST['chk_precio_especial'])) {
        $pe = floatval($_POST['txt_precio_especial'] ?? 0);
        if ($pe > 0) {
            $Clproductos->precio_especial        = $pe;
            $Clproductos->precio_especial_inicio = $_POST['fecha_especial_inicio'] ?: date('Y-m-d');
            $Clproductos->precio_especial_fin    = $_POST['fecha_especial_fin']    ?: null;
        }
    }
    
    $cod_producto = 0;

    $isLoadImage = hasCropImage('img_crop', 'txt_crop') && hasCropImage('img_crop_min', 'txt_crop_min');

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
            if($isLoadImage){
                $nameImg = 'product_'.datetime_format().'.jpg';
                $nameImgMin = 'min_'.$nameImg;

                if(saveCropImage('img_crop', 'txt_crop', $nameImg) && saveCropImage('img_crop_min', 'txt_crop_min', $nameImgMin)){
                    $Clproductos->setImages($nameImg, $nameImgMin, $cod_producto);
                }else{
                    $return['mensaje'] .= ", pero no se pudo guardar la imagen (revisa URL_UPLOAD)";
                }
            }

            //INSERT ETIQUETAS
            if (isset($cmbEtiqueta) && trim($cmbEtiqueta) !== '') {
                if(!is_numeric($cmbEtiqueta)){
                    $Clproductos->createAndSetEtiqueta($cod_producto, $cmbEtiqueta);
                }else{
                    $Clproductos->setEtiquetas($cod_producto, $cmbEtiqueta);
                }
            }else{
                $Clproductos->delEtiquetas($cod_producto);
            }
            
            //EMPAQUE
            if(isset($txt_unidades)){
                $txt_unidades = intval($txt_unidades);
                $alto = isset($txt_alto) ? intval($txt_alto) : 0;
                if($txt_unidades > 0){
                    $Clproductos->updateEmpaque($idP, $txt_unidades, $alto);
                }
            }

            //EVENTO
            if(isset($evento_section_rendered) && isset($chk_es_evento)){
                $anticipacion = intval($txt_evento_anticipacion);
                $maximo = intval($txt_evento_maximo);
                $Clproductos->updateEvento($idP, $anticipacion, $maximo, $txt_evento_titulo, $txt_evento_desc);
            }

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el producto, por favor vuelva a intentarlo";
        }
    }else{
        $cod_producto = $_POST['cod_producto'];
        $Clproductos->cod_producto = $cod_producto;
        
        $cant = $Clproductos->getCantOptionsAndVariants($cod_producto); //Si tiene opciones o variantes no debe poder agregar al carrito sin entrar al detalle
        if($cant > 0)
            $Clproductos->open_detalle = 1;
        
        if($Clproductos->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Producto editado correctamente ";
            $return['id'] = $Clproductos->cod_producto;
            // $return['dias'] = $cmbDias;
            $idP = $Clproductos->cod_producto;

            //INSERT ETIQUETAS
            $Clproductos->delEtiquetas($cod_producto);
            if (isset($cmbEtiqueta) && trim($cmbEtiqueta) !== '') {
                if(!is_numeric($cmbEtiqueta)){
                    $Clproductos->createAndSetEtiqueta($cod_producto, $cmbEtiqueta);
                }else{
                    $Clproductos->setEtiquetas($cod_producto, $cmbEtiqueta);
                }
            }
            
            //EMPAQUE
            if(isset($txt_unidades)){
                $txt_unidades = intval($txt_unidades);
                $alto = isset($txt_alto) ? intval($txt_alto) : 0;
                $Clproductos->deleteEmpaque($idP);
                if($txt_unidades > 0){
                    $Clproductos->updateEmpaque($idP, $txt_unidades, $alto);
                }
            }

            //EVENTO
            if(isset($evento_section_rendered)){
                $Clproductos->deleteEvento($idP);
                if(isset($chk_es_evento)){
                    $anticipacion = intval($txt_evento_anticipacion);
                    $maximo = intval($txt_evento_maximo);
                    $Clproductos->updateEvento($idP, $anticipacion, $maximo, $txt_evento_titulo, $txt_evento_desc);
                }
            }

            $data = NULL;
            if($Clproductos->getArray($cod_producto, $data)){
                $return['alias'] = $data['alias'];

                if($isLoadImage){
                    $nameImg = 'product_'.datetime_format().'.jpg';
                    $nameImgMin = 'min_'.$nameImg;

                    // Solo se reemplaza (y se borra la anterior) si la nueva imagen se guardó en disco
                    if(saveCropImage('img_crop', 'txt_crop', $nameImg) && saveCropImage('img_crop_min', 'txt_crop_min', $nameImgMin)){
                        $Clproductos->setImages($nameImg, $nameImgMin, $cod_producto);

                        if($data['image_max'] !== "")
                            deleteFile($data['image_max']);

                        if($data['image_min'] !== "")
                            deleteFile($data['image_min']);
                    }else{
                        $return['mensaje'] .= ", pero no se pudo guardar la imagen (revisa URL_UPLOAD)";
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
    
    //DIAS
    $Clproductos->deleteDays($cod_producto);
    if($rb_dias == 1){
        $dias_producto = $_POST['dias'] ?? [];
        $Clproductos->setDays($cod_producto, $dias_producto);
    }
    
    return $return;
}

function formatFloat($float) {
    if (is_numeric($float) && $float !== '') {
        return round((float)$float, 2); 
    }
    return 0;
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
    if(hasCropImage('img_crop_galeria', 'txt_crop_galeria')){
        /*CODIGO PARA GUARDAR*/
        $id=0;
        if(!saveCropImage('img_crop_galeria', 'txt_crop_galeria', $nameImg)){
            $return['success'] = 0;
            $return['mensaje'] = "No se pudo guardar la imagen (revisa URL_UPLOAD)";
        }else if($Clproductos->add_img_product($cod_producto, $nameImg, $id)){
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
    $cmb_productos = array_map('htmlentities', $cmb_productos);
    $txt_opcion_titulo = htmlentities($txt_opcion_titulo);
    $txt_opcion_descripcion = htmlentities($txt_opcion_descripcion);
    if($Clproductos->crear_opcion($cod_producto,$txt_opcion_titulo, $txt_opcion_descripcion, $txt_opciones_cantidad, $txt_opciones_cantidad_max, $cmb_productos, $tipo_opcion, $cmb_isCheck, $id)){
        $Clproductos->setOpenDetalleTrue($cod_producto);
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
            $Clproductos->crear_opcion_detalle($id, $txt_nomItemDet[$i], $txt_descItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
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
    if($Clproductos->crear_opcion($cod_producto,$Info['titulo'], '', $Info['cantidad_min'], $Info['cantidad'],$productos , $Info['isDatabase'], $Info['isCheck'], $id)){
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
            $Clproductos->crear_opcion_detalle($id, $item[$i], '', $aumentar_precio[$i], $precio[$i], $posicion[$i]);
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
    global $session;

    $cod_empresa = $session['cod_empresa'];
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
            
            $html .= '<tr class="trItem" data-id="'.$r['cod_producto_opciones_detalle'].'" data-empresa="'.$cod_empresa.'">
                    <td style="width: 60%;">
                        <input class="form-control txt_id_det" name="cod_detalle[]" value="'.$r['cod_producto_opciones_detalle'].'" type="hidden">
                        
                        <input class="form-control txtnomDet mb-1 fw-bold border-0 p-1" name="txt_nomItemDet[]" value="'.$r['item'].'" placeholder="Nombre del item">
                        
                        
                        <textarea  name="txt_descItemDet[]"
                            class="form-control form-control-sm text-muted border-0 p-1"
                            placeholder="Descripción (ej: Fría, sin cebolla)"
                        >'.($r['detalle'] ?? '').'</textarea>
                        
                        <input type="hidden" class="form-control" name="txt_codItemDet[]" value="'.$r['itemPrincipal'].'">
                    </td>
                    
                    <td style="width: 25%;">
                        <div class="d-flex align-items-center justify-content-end">
                            <input class="form-control chk_is" name="chk_is[]" value="'.$isCheck.'" type="hidden">
                            
                            <input class="precioCheck mr-1" type="checkbox" name="precioCheck[]" '.$check.' />
                            
                            <input type="number" class="form-control txt_precio" name="txt_precio[]" 
                                placeholder="0.00" value="'.$r['precio'].'" 
                                style="text-align: right;" '.$readonly.'>
                        </div>
                    </td>
                    
                    <td class="text-right" style="width: 15%; vertical-align: middle;">
                        <button type="button" class="p-0 border-0 bg-transparent btnDelItem mr-1">
                            <i data-feather="trash"></i>
                        </button>
                        <button type="button" class="p-0 border-0 bg-transparent btnModalIngredientes no-show-ingredients" 
                                onclick="getIngredientesEnOpciones('.$r['cod_producto_opciones_detalle'].')">
                            <i data-feather="coffee"></i>
                        </button>
                    </td>
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
     
     if($Clproductos->editar_opciones($cod_producto_opcion, htmlentities($txt_opcion_titulo), htmlentities($txt_opcion_descripcion), $txt_opciones_cantidad, $txt_opciones_cantidad_max, $tipo_opcion, $cmb_isCheck)){
       
         /*Insertar Items*/
        if ($tipo_opcion == 1){
            $txt_nomItemDet = $txt_codItemDet;
        }
        for($i=0; $i< count($txt_nomItemDet); $i++){
                $aumentarPrecio = $chk_is[$i];
                
            if($Clproductos->select_opcion_detalle($cod_detalle[$i])){
                $Clproductos->editar_opcion_detalle($cod_detalle[$i], $txt_nomItemDet[$i], $txt_descItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
            }
            else{
                $Clproductos->crear_opcion_detalle($cod_producto_opcion, $txt_nomItemDet[$i], $txt_descItemDet[$i], $aumentarPrecio, $txt_precio[$i], $i);
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

/* SUBIDA DIRECTA DE IMAGEN (subir_imagen_masivo.php): se guarda el archivo original sin recortar ni recomprimir */
function subir_imagen_directa(){
    global $Clproductos;
    global $session;
    $cod_producto = isset($_POST['cod_producto']) ? intval($_POST['cod_producto']) : 0;
    $producto = producto_de_empresa($cod_producto);
    if(!$producto){
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe";
        return $return;
    }

    $file = isset($_FILES['imagen']) ? $_FILES['imagen'] : null;
    if(!$file || $file['error'] !== UPLOAD_ERR_OK){
        $return['success'] = 0;
        $return['mensaje'] = "No se recibió la imagen (código ".($file ? $file['error'] : '-')."). Revisa upload_max_filesize en PHP";
        return $return;
    }

    $info = @getimagesize($file['tmp_name']);
    $extensiones = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if(!$info || !isset($extensiones[$info[2]])){
        $return['success'] = 0;
        $return['mensaje'] = "Formato no permitido, solo JPG, PNG o WEBP";
        return $return;
    }

    $ruta = url_upload.'/assets/empresas/'.$session['alias'].'/';
    if(!is_dir($ruta) && !@mkdir($ruta, 0755, true)){
        error_log("[subir_imagen_directa] No se pudo crear la carpeta $ruta");
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo crear la carpeta de imágenes (revisa URL_UPLOAD)";
        return $return;
    }

    $nameImg = 'product_'.datetime_format().'_'.$cod_producto.'.'.$extensiones[$info[2]];
    $nameImgMin = 'min_'.$nameImg;
    if(!@move_uploaded_file($file['tmp_name'], $ruta.$nameImg)){
        error_log("[subir_imagen_directa] No se pudo escribir ".$ruta.$nameImg);
        $return['success'] = 0;
        $return['mensaje'] = "No se pudo guardar la imagen (revisa URL_UPLOAD y permisos)";
        return $return;
    }

    // Miniatura con el ancho mínimo configurado para la empresa (tb_size_crop), por defecto 400
    $Clempresas = new cl_empresas(NULL);
    $sizeCrop = $Clempresas->getSizeCrop($session['cod_empresa']);
    $anchoMin = ($sizeCrop && intval($sizeCrop['size_min_width']) > 0) ? intval($sizeCrop['size_min_width']) : 400;
    if(!crear_miniatura($ruta.$nameImg, $ruta.$nameImgMin, $info[2], $anchoMin))
        @copy($ruta.$nameImg, $ruta.$nameImgMin);

    if(!$Clproductos->setImages($nameImg, $nameImgMin, $cod_producto)){
        @unlink($ruta.$nameImg);
        @unlink($ruta.$nameImgMin);
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar el producto";
        return $return;
    }

    // Borrar las anteriores solo si ningún otro producto (ej. una variante) las usa
    foreach(array_unique([$producto['image_max'], $producto['image_min']]) as $anterior){
        if($anterior != "" && !$Clproductos->imagenEnUso($anterior, $cod_producto))
            @unlink($ruta.$anterior);
    }

    $return['success'] = 1;
    $return['mensaje'] = "Imagen actualizada";
    $return['imagen'] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$nameImgMin;
    $return['detalle'] = $info[0].'x'.$info[1].' · '.round($file['size'] / 1024).' KB';
    return $return;
}

function crear_miniatura($origen, $destino, $tipo, $ancho){
    if(!extension_loaded('gd'))
        return false;
    switch($tipo){
        case IMAGETYPE_JPEG: $img = @imagecreatefromjpeg($origen); break;
        case IMAGETYPE_PNG:  $img = @imagecreatefrompng($origen); break;
        case IMAGETYPE_WEBP: $img = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($origen) : false; break;
        default: $img = false;
    }
    if(!$img)
        return false;

    $w = imagesx($img);
    $h = imagesy($img);
    if($w <= $ancho){
        imagedestroy($img);
        return @copy($origen, $destino);
    }
    $alto = intval(round($h * $ancho / $w));
    $min = imagecreatetruecolor($ancho, $alto);
    if($tipo != IMAGETYPE_JPEG){
        imagealphablending($min, false);
        imagesavealpha($min, true);
    }
    imagecopyresampled($min, $img, 0, 0, 0, 0, $ancho, $alto, $w, $h);

    if($tipo == IMAGETYPE_JPEG)
        $ok = imagejpeg($min, $destino, 85);
    else if($tipo == IMAGETYPE_PNG)
        $ok = imagepng($min, $destino, 6);
    else
        $ok = imagewebp($min, $destino, 85);

    imagedestroy($img);
    imagedestroy($min);
    return $ok;
}

/* CARACTERÍSTICAS Y VARIANTES */
function producto_de_empresa($cod_producto){
    global $Clproductos, $session;
    $array = NULL;
    if(intval($cod_producto) <= 0 || !$Clproductos->getArray(intval($cod_producto), $array))
        return false;
    if($array['cod_empresa'] != $session['cod_empresa'])
        return false;
    return $array;
}

/* Quita vacíos y repetidos (sin distinguir mayúsculas) y los que ya existen */
function limpiar_valores($valores, $existentes = []){
    $vistos = [];
    foreach($existentes as $e)
        $vistos[mb_strtolower(trim($e), 'UTF-8')] = true;
    $limpios = [];
    foreach(($valores ?: []) as $valor){
        $valor = trim($valor);
        $clave = mb_strtolower($valor, 'UTF-8');
        if($valor == "" || isset($vistos[$clave]))
            continue;
        $vistos[$clave] = true;
        $limpios[] = $valor;
    }
    return $limpios;
}

function guardar_caracteristicas(){
    global $Clproductos;
    $cod_producto = isset($_POST['cod_producto']) ? intval($_POST['cod_producto']) : 0;
    if(!producto_de_empresa($cod_producto)){
        $return['success'] = 0;
        $return['mensaje'] = "Debe guardar primero el producto para guardar sus características";
        return $return;
    }
    if($Clproductos->lista_variantes($cod_producto)){
        $return['success'] = 0;
        $return['mensaje'] = "El producto ya tiene variantes, solo puedes agregar valores a las características existentes";
        return $return;
    }

    $titulos = isset($_POST['txt_opcion_titulo']) ? $_POST['txt_opcion_titulo'] : [];
    $opciones = isset($_POST['cmb_variante_productos']) ? $_POST['cmb_variante_productos'] : [];
    $tipos = isset($_POST['cmb_variante_tipo']) ? $_POST['cmb_variante_tipo'] : [];
    $colores = isset($_POST['colores']) ? $_POST['colores'] : [];

    $nombres = array_column($Clproductos->getCaracteristicas($cod_producto) ?: [], 'caracteristica');

    $nuevas = [];
    foreach($titulos as $key => $titulo){
        $titulo = trim($titulo);
        $valores = limpiar_valores(isset($opciones[$key]) ? $opciones[$key] : []);
        if($titulo == "" || count($valores) == 0){
            $return['success'] = 0;
            $return['mensaje'] = "Cada característica debe tener nombre y al menos un valor";
            return $return;
        }
        if(count(limpiar_valores([$titulo], $nombres)) == 0){
            $return['success'] = 0;
            $return['mensaje'] = "La característica '$titulo' está repetida";
            return $return;
        }
        $nombres[] = $titulo;
        $tipo = (isset($tipos[$key]) && $tipos[$key] == "color") ? "color" : "texto";
        $nuevas[] = ['titulo' => $titulo, 'tipo' => $tipo, 'valores' => $valores];
    }

    foreach($nuevas as $nueva){
        $idCaracteristica = $Clproductos->setCaracteristica($cod_producto, $nueva['titulo'], $nueva['tipo']);
        if(!$idCaracteristica){
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la característica ".$nueva['titulo'];
            return $return;
        }
        foreach($nueva['valores'] as $valor){
            $color = ($nueva['tipo'] == "color" && isset($colores[$valor])) ? $colores[$valor] : "";
            $Clproductos->setCaracteristicaDetalle($idCaracteristica, $valor, $color);
        }
    }

    $return['success'] = 1;
    $return['mensaje'] = "Características guardadas correctamente";
    return $return;
}

function agregar_valores_caracteristica(){
    global $Clproductos;
    $cod_caracteristica = isset($_POST['cod_caracteristica']) ? intval($_POST['cod_caracteristica']) : 0;
    $caracteristica = $Clproductos->getCaracteristica($cod_caracteristica);
    if(!$caracteristica){
        $return['success'] = 0;
        $return['mensaje'] = "Característica no existe";
        return $return;
    }

    $existentes = array_column($caracteristica['detalle'] ?: [], 'detalle');
    $valores = limpiar_valores(isset($_POST['valores']) ? $_POST['valores'] : [], $existentes);
    if(count($valores) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Escribe al menos un valor nuevo (que no exista ya)";
        return $return;
    }

    $color = isset($_POST['color']) ? $_POST['color'] : "";
    foreach($valores as $valor){
        $Clproductos->setCaracteristicaDetalle($cod_caracteristica, $valor, $color);
    }

    $return['success'] = 1;
    $return['mensaje'] = count($valores) == 1 ? "Valor agregado" : "Valores agregados";
    return $return;
}

function guardar_atributos_variante(){
    global $Clproductos;
    if(!isset($_POST['cod_producto']) || !isset($_POST['cmbAtributoVariante'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    $cod_producto = intval($_POST['cod_producto']);
    $opciones = array_map('intval', $_POST['cmbAtributoVariante']);

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
    $cod_producto = isset($_POST['cod_producto']) ? intval($_POST['cod_producto']) : 0;
    $array = producto_de_empresa($cod_producto);
    if(!$array){
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe, por favor intentelo nuevamente";
        return $return;
    }

    $items_precio = isset($_POST['txt_precio_variante']) ? $_POST['txt_precio_variante'] : [];
    $items_codigo = isset($_POST['txt_atributos_codigo']) ? $_POST['txt_atributos_codigo'] : [];
    $items_sku = isset($_POST['txt_variante_sku']) ? $_POST['txt_variante_sku'] : [];

    // Solo se aceptan combinaciones pendientes de este producto; los textos salen de la BD, no del cliente
    $caracteristicas = $Clproductos->getCaracteristicas($cod_producto);
    $pendientes = [];
    foreach(variantes_pendientes($Clproductos, $cod_producto, $caracteristicas) as $combinacion)
        $pendientes[variantes_clave_codigos($combinacion['codigos'])] = $combinacion;

    $ivaPorcentaje = floatval($array['iva_porcentaje']) > 0 ? floatval($array['iva_porcentaje']) : 12;
    $disponibilidad = $Clproductos->getdisponibilidadByproduct($cod_producto);
    $rutaEmpresa = url_upload.'/assets/empresas/'.$session['alias'].'/';

    $creadas = 0;
    $errores = [];
    for($x=0; $x<count($items_codigo); $x++){
        $codigos = json_decode($items_codigo[$x], true);
        $clave = is_array($codigos) ? variantes_clave_codigos($codigos) : "";
        if(!isset($pendientes[$clave])){
            $errores[] = "Fila ".($x+1)." (códigos: ".$items_codigo[$x]."): la combinación ya existe o no pertenece a las características del producto";
            continue;
        }
        $combinacion = $pendientes[$clave];
        unset($pendientes[$clave]);
        $atributos = $combinacion['textos'];

        $producto = new cl_productos();
        $producto->desc_corta = $array['desc_corta'];
        $producto->desc_larga = $array['desc_larga'];
        $producto->open_detalle = $array['open_detalle'];
        $producto->costo = floatval($array['costo']);
        $producto->peso = $array['peso'];
        $producto->volumen = 0;
        $producto->tiempo_preparacion = $array['tiempo_preparacion'];
        $producto->is_combo = $array['is_combo'];
        $producto->cobra_iva = $array['cobra_iva'];
        $producto->iva_porcentaje = $ivaPorcentaje;
        $producto->cod_producto_padre = $cod_producto;
        $producto->categorias = $Clproductos->get_categorias($cod_producto);
        $producto->facturar_sin_stock = $array['noStock'];
        $producto->venta_delivery = $array['venta_delivery'];
        $producto->venta_pickup = $array['venta_pickup'];
        $producto->venta_mesa = $array['venta_mesa'];
        $producto->estado = 'A';

        //PRECIO
        $precio = round(floatval(isset($items_precio[$x]) ? $items_precio[$x] : 0), 2);
        $precio_no_tax = round($precio / (1 + $ivaPorcentaje / 100), 2);
        $producto->precio = $precio;
        $producto->precio_anterior = 0;
        $producto->precio_no_tax = $precio_no_tax;
        $producto->iva_valor = round($precio - $precio_no_tax, 2);

        //NOMBRE
        $nombre = $array['nombre']." ".implode("/", $atributos);
        $producto->nombre = $nombre;
        $aux = "";
        do{
            $alias = create_slug(sinTildes($nombre.$aux));
            $aux = intval(rand(1,100));
        }while(!$Clproductos->aliasDisponible($alias));
        $producto->alias = $alias;
        $producto->sku = isset($items_sku[$x]) ? trim($items_sku[$x]) : "";

        //IMAGEN: copia de la del producto padre
        $nameImg = 'product_'.datetime_format().'_'.$x.'.jpg';
        if($array['image_min'] != "" && file_exists($rutaEmpresa.$array['image_min']) && @copy($rutaEmpresa.$array['image_min'], $rutaEmpresa.$nameImg)){
            $producto->image_min = $nameImg;
            $producto->image_max = $nameImg;
        }else{
            $producto->image_min = $array['image_min'];
            $producto->image_max = $array['image_max'];
        }

        $id=0;
        if($producto->crear($id)){
            $producto->set_variante_caracteristica($id, $combinacion['codigos']);
            $producto->set_variantes($id, $atributos);

            //DISPONIBILIDAD DEL PRODUCTO PADRE
            foreach(($disponibilidad ?: []) as $d){
                $producto->setDisponibilidad($id, $d['cod_sucursal'], $d['precio'], $d['precio_anterior'], $d['estado'], $d['replacePrice']);
            }
            $creadas++;
        }else{
            $errores[] = implode("/", $atributos).": error al insertar el producto en la BD (detalle SQL en errores_sql.log)";
        }
    }

    foreach($errores as $error)
        error_log("[guardar_variantes] producto $cod_producto - $error");

    if($creadas == 0){
        $return['success'] = 0;
        $return['mensaje'] = "No se creó ninguna variante. ".(count($errores) ? $errores[0] : "No se recibieron variantes para crear");
        $return['errores'] = $errores;
        return $return;
    }

    $Clproductos->setOpenDetalleTrue($cod_producto);
    $return['success'] = 1;
    $return['mensaje'] = $creadas == 1 ? "1 variante creada correctamente" : "$creadas variantes creadas correctamente";
    if(count($errores)){
        $return['mensaje'] .= ". No se crearon ".count($errores).": ".$errores[0];
        $return['errores'] = $errores;
    }
    return $return;
}

/* HTML de las secciones Características y Variantes para refrescarlas sin recargar la página */
function html_variantes(){
    global $Clproductos;
    global $session;
    $cod_producto = isset($_GET['cod_producto']) ? intval($_GET['cod_producto']) : 0;
    $array = producto_de_empresa($cod_producto);
    if(!$array){
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe";
        return $return;
    }
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    $return['success'] = 1;
    $return['caracteristicas'] = html_producto_caracteristicas($Clproductos, $cod_producto);
    $return['variantes'] = html_producto_variantes($Clproductos, $cod_producto, $array['sku'], $files);
    return $return;
}

function cambiarVarianteVisualizacion(){
    global $Clproductos;
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : "";
    $cod_producto = isset($_GET['cod_producto']) ? intval($_GET['cod_producto']) : 0;
    if(!producto_de_empresa($cod_producto) || !preg_match('/^\w+$/', $tipo)){
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar la visualización de la variante";
        return $return;
    }

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

function getProdSucursal(){ //OBTENER PRODUCTOS ASIGNADOS A LA SUCURSAL
    global $Clproductos;
    extract($_GET);
    $files = url_sistema."assets/empresas/".$alias."/";
    $cantAsignados = 0;
    $cantNoAsignados = 0;
    $htmlAsignados = "";
    $htmlNoAsignados = "";
    $estado = "Activo";
    $badge = "shadow-none badge badge-primary";
    $gravaIva = "check-circle";
    $gravaIvaColor = "text-success";

    $asignados = $Clproductos->getProductosAsignadosSucursal($cod_empresa, $cod_sucursal);
    if($asignados){
        $cantAsignados = count($asignados);
        foreach ($asignados as $producto) {
            
            if("I" == $producto['estado']){
                $estado = "Inactivo";
                $badge = "shadow-none badge badge-danger";
            }
            if(0 == $producto['cobra_iva']){
                $gravaIva = "x-circle";
                $gravaIvaColor = "text-danger";
            }
            $htmlAsignados.= '  <tr>
                                    <td>'.$producto['cod_producto'].'</td>
                                    <td><img src="'.$files.$producto['image_min'].'" height="30"></td>
                                    <td>'.$producto['nombre'].'</td>
                                    <td>$'.number_format($producto['precio_no_tax'], 2).'</td>
                                    <td>$'.number_format($producto['iva_valor'], 2).'</td>
                                    <td>$'.number_format($producto['precio'], 2).'</td>
                                    <td>$'.number_format($producto['precio_anterior'], 2).'</td>
                                    <td class="text-center"><i class="'.$gravaIvaColor.'" data-feather="'.$gravaIva.'"></i></td>
                                    <td class="text-center"><span class="'.$badge.'">'.$estado.'</span></td>
                                </tr>';
        }
    }
    $noAsignados = $Clproductos->getProductosNoAsignadosSucursal($cod_empresa, $cod_sucursal);
    if($noAsignados){
        $cantNoAsignados = count($noAsignados);
        foreach ($noAsignados as $producto) {
            
            if("I" == $producto['estado']){
                $estado = "Inactivo";
                $badge = "shadow-none badge badge-danger";
            }
            if(0 == $producto['cobra_iva']){
                $gravaIva = "x-circle";
                $gravaIvaColor = "text-danger";
            }
            $htmlNoAsignados.= '  <tr>
                                    <td>'.$producto['cod_producto'].'</td>
                                    <td><img src="'.$files.$producto['image_min'].'" height="30"></td>
                                    <td>'.$producto['nombre'].'</td>
                                    <td>$'.number_format($producto['precio_no_tax'], 2).'</td>
                                    <td>$'.number_format($producto['iva_valor'], 2).'</td>
                                    <td>$'.number_format($producto['precio'], 2).'</td>
                                    <td>$'.number_format($producto['precio_anterior'], 2).'</td>
                                    <td class="text-center"><i class="'.$gravaIvaColor.'" data-feather="'.$gravaIva.'"></i></td>
                                    <td class="text-center"><span class="'.$badge.'">'.$estado.'</span></td>
                                </tr>';
        }
    }

    $return['success'] = 1;
    $return['mensaje'] = "Datos obtenidos";
    $return['cantAsignados'] = $cantAsignados;
    $return['cantNoAsignados'] = $cantNoAsignados;
    $return['htmlAsignados'] = $htmlAsignados;
    $return['htmlNoAsignados'] = $htmlNoAsignados;
    return $return;
}

// INGREDIENTES
function getIngredientes() {
    global $Clproductos;
    extract($_GET);

    $ingredientes = $Clproductos->getIngredientes();
    if($ingredientes) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de ingredientes";
        $return['data'] = $ingredientes;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay ingredientes";
    return $return;
}

function getProductosIngredientes() {
    global $Clproductos;
    extract($_GET);
    $productoIngredientes = $Clproductos->getProductoIngredientes($cod_producto);
    if($productoIngredientes) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingredientes asignados al producto";
        $return['data'] = $productoIngredientes;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "El producto no tiene ingredientes asignados";
    return $return;
}

function addProductosIngredientes() {
    global $Clproductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $Clproductos->cod_producto = $cod_producto;
    $Clproductos->cod_ingrediente = $cod_ingrediente;
    $Clproductos->valor = $valor;
    
    if($Clproductos->addProductosIngredientes()){
        $return['success'] = 1;
        $return['mensaje'] = "Ingredientes agregados al producto";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al agregar ingredientes al producto";
    return $return;
}

function editProductosIngredientes() {
    global $Clproductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $Clproductos->cod_producto_ingrediente = $cod_producto_ingrediente;
    $Clproductos->valor = $valor;

    if($Clproductos->editProductosIngredientes()) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente del producto editado correctamente";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al editar el ingrediente del producto";
    return $return;
}

function deleteProductosIngredientes() {
    global $Clproductos;
    extract($_GET);

    $Clproductos->cod_producto_ingrediente = $cod_producto_ingrediente;

    if($Clproductos->deleteProductosIngredientes()) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente del producto eliminado correctamente";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al eliminar el ingrediente del producto";
    return $return;
}

function getProductosOpcionesIngredientes() {
    global $Clproductos;
    extract($_GET);
    $productoOpcionIngredientes = $Clproductos->getProductoOpcionesIngredientes($cod_producto_opcion);
    if($productoOpcionIngredientes) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingredientes asignados la opción";
        $return['data'] = $productoOpcionIngredientes;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "La opción no tiene ingredientes asignados";
    return $return;
}

function addProductosOpcionesIngredientes() {
    global $Clproductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $Clproductos->cod_producto_opcion = $cod_producto_opcion;
    $Clproductos->cod_ingrediente = $cod_ingrediente;
    $Clproductos->valor = $valor;
    
    if($Clproductos->addProductosOpcionesIngredientes($principal)) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingredientes agregados a la opción";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al agregar ingredientes a la opción";
    return $return;
}

function editProductosOpcionesIngredientes() {
    global $Clproductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $Clproductos->cod_producto_opcion_ingrediente = $cod_producto_opcion_ingrediente;
    $Clproductos->valor = $valor;

    if($Clproductos->editProductosOpcionesIngredientes($principal)) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente de la opción editado correctamente";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al editar el ingrediente de la opción";
    return $return;
}

function deleteProductosOpcionesIngredientes() {
    global $Clproductos;
    extract($_GET);

    $Clproductos->cod_producto_opcion_ingrediente = $cod_producto_opcion_ingrediente;

    if($Clproductos->deleteProductosOpcionesIngredientes()) {
        $return['success'] = 1;
        $return['mensaje'] = "Ingrediente de la opción eliminado correctamente";
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "Error al eliminar el ingrediente de la opción";
    return $return;
}
// INGREDIENTES

//Activar Inventario

function activateOpcionInventario(){
    global $Clproductos;
    
    $input = json_decode(file_get_contents('php://input'), true);
    extract($input);
    
    $isInventario = 0;
    $msg = "Opción inactivada para inventario";
    if($estado){
        $isInventario = 1;
        $msg = "Opción activada para inventario";
    }
    
    if($Clproductos->setInventarioOpcionDetalle($id, $isInventario)){
        $return['success'] = 1;
        $return['mensaje'] = $msg;
        $return['estado'] = $estado;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al realizar la acción, por favor intentelo nuevamente";
    }
    return $return;
}

#region Kiosco
// PRODUCTOS KIOSCO
function getProductsKiosco() {
    global $Clproductos;
    extract($_GET);

    $productos = $Clproductos->getProductosKiosco();
    if($productos) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de productos kiosco";
        $return['data'] = $productos;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay productos";
    return $return;
}

function setProductsKiosco() {
    global $Clproductos;

    require_once '../clases/cl_sucursales.php';
    $ClSucursales = new cl_sucursales();

    $sucursales = $ClSucursales->lista();
    if(!$sucursales) {
        $return['success'] = 0;
        $return['mensaje'] = "No hay sucursales";
        return $return;
    }

    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    foreach ($sucursales as $sucursal) {
        $cod_sucursal = $sucursal["cod_sucursal"];

        $Clproductos->cod_producto = $cod_producto;
        $Clproductos->precio = $precio;
        $Clproductos->cod_sucursal = $cod_sucursal;
        $Clproductos->estado = $estado;
        $Clproductos->is_custom = $is_custom;
        if($Clproductos->setProductoKiosco()) {
            $success[] = "Guardado sucursal: $cod_sucursal";
        }
        else {
            $error[] = "Error guardar en sucursal: $cod_sucursal";
        }
    }

    if(count($error) > 0) {
        $return['success'] = -1;
        $return['mensaje'] = "Error al guardar en una o varias sucursales";
        $return['error'] = $error;
        $return['correcto'] = $success;
        return $return;
    }

    $return['success'] = 1;
    $return['mensaje'] = "Guardado correctamente";
    $return['error'] = $error;
    $return['correcto'] = $success;
    return $return;
}

function getProductsOffices() {
    global $Clproductos;
    extract($_GET);

    if(!isset($cod_producto)) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta identificador ID del producto";
        return $return;
    }

    $productos = $Clproductos->getProductsOffices($cod_producto);
    if($productos) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de productos kiosco, sucursales";
        $return['data'] = $productos;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay productos";
    return $return;
}

function setProductsKioscoCustom() {
    global $Clproductos;
    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    if(count($data) > 0) {
        $success = [];
        $error = [];
        foreach ($data as $producto) {
            $Clproductos->cod_producto = $producto["cod_producto"];
            $Clproductos->precio = $producto["precio"];
            $Clproductos->cod_sucursal = $producto["cod_sucursal"];
            $Clproductos->estado = $producto["estado"];
            $Clproductos->is_custom = $producto["is_custom"];
            if($Clproductos->setProductoKiosco()) {
                $success[] = $producto;
            }
            else {
                $error[] = $producto;
            }
        }
        
        if(count($error) > 0) {
            $return['success'] = -1;
            $return['mensaje'] = "Error al guardar uno o varios productos";
            $return['error'] = $error;
            $return['correcto'] = $success;
            return $return;
        }
        
        $return['success'] = 1;
        $return['mensaje'] = "Guardado correctamente";
        $return['error'] = $error;
        $return['correcto'] = $success;
        return $return;
    }
    
    $return['success'] = 0;
    $return['mensaje'] = "No hay datos que actualizar";
    return $return;
}
// FIN PRODUCTOS KIOSCO
#endregion
?>