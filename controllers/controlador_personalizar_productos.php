<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos.php";
$Clproductos = new cl_productos();
$session = getSession();
$variaciones = null;

controller_create();

function guardar_opciones(){
    global $Clproductos;
    extract($_POST);
    
    $cod_producto = $nombreProducto;
    $Clproductos->cod_producto = $cod_producto;
    $Clproductos->nomOpcion = $txt_nombre_opc;
    $Clproductos->precio_min = $txt_min;
    $Clproductos->precio_max = $txt_max;
    
    if(isset($_POST['ck_isCheck']))
        $Clproductos->isCheck = 1;
    else
        $Clproductos->isCheck = 0;
        
    if(isset($_POST['ck_isDB']))
         $Clproductos->isDatabase = 1;
    else
         $Clproductos->isDatabase = 0;
    
    if($Clproductos->insert_opc($id)){
        $return['id'] = $id;
        $return['success'] = 1;
        $return['mensaje'] = "Opción guardada";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al guardar opción";
    }
    return $return;
}

function guardar_items(){
    global $Clproductos;
    extract($_POST);
    
    for($i=0; $i<count($txt_nomItemDet); $i++){
        $Clproductos->cod_producto_opcion = $idOpcion;
         
        $Clproductos->nomDetalle = $txt_nomItemDet[$i];
        $Clproductos->precioDet = $txt_precioItemDet[$i];
        $Clproductos->aumentar_precio = $txt_check[$i];
        
        if($Clproductos->insert_items()){
            $return['success'] = 1;
            $return['mensaje'] = "Item guardado";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar Item";
        }
    }
    return $return;
}

function guardar_extra(){
  global $Clproductos;
  $nombre_categoria=$_POST['nombre_categoria'];
  $cod_producto=$_POST['cod_producto'];
  $cantidad=$_POST['cantidad'];
  

    $resp=$Clproductos->coincidencia_extras($cod_producto,$nombre_categoria);
    if(!$resp)
    {
      if($Clproductos->crear_extra($cod_producto,$nombre_categoria, $cantidad,$id))
      {
         $return['success'] = 1;
         $return['id'] = $id;
      }
      else
      {$return['success'] = 0;$return['mensaje'] = "Error al crear el extra del producto";}
    }
    return $return;
}

function eliminar_extra(){
  global $Clproductos;
  $nombre_categoria=$_POST['nombre_categoria'];
  $cod_producto=$_POST['cod_producto'];
  $cantidad=$_POST['cantidad'];
  
      $codigo_padre=$_POST['codigo_padre'];
      if($Clproductos->delete_category_extra($codigo_padre))
      {
         $return['success'] = 1;
         $return['id'] = 0;
      }
      else
      {$return['success'] = 0;$return['mensaje'] = "Error al eliminar el extra del producto";}
    
    return $return;
}

function ver_extra_detalle(){
  global $Clproductos;
  global $session;

  $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
  $cod_producto_extra=$_POST['cod_product_extra'];
  $cod_categoria=$_POST['codigo_categoria'];
  $html="";
  $contenido="";
  $boton="";
  $botonAgg="";

  if($cod_categoria!=0)
  {$respUno= $Clproductos->listaByCategoria($cod_categoria);}else{$respUno= $Clproductos->lista_extras();}
    if($respUno)
    {  
      foreach ($respUno as $producto) 
      {
        $respDos= $Clproductos->verificar_registro($producto['cod_producto'],$cod_producto_extra);
        if(!$respDos)
        {
           $contenido.='
            <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                <label>'.$producto['nombre'].'</label>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-4 col-4">
               <input class="checkInsert" type="checkbox" id="" cod_producto="'.$producto['cod_producto'].'" codigo_padre="'.$cod_producto_extra.'" categoria="'.$cod_categoria.'">
            </div>';
        }
       
      }

      $botonAgg.=' <button type="button" class="btn btn-outline-primary btnAgg" categoria="'.$cod_categoria.'" codigo_padre="'.$cod_producto_extra.'">Seleccionar todos los productos</button>';
      $return['contenido'] = $contenido;
      $return['botonAgg'] = $botonAgg;
    }






  $resp=$Clproductos->lista_productos_extras_detalle($cod_producto_extra);
  if($resp)
  {
     
     foreach ($resp as $item) {
      $html.='<tr>
                <td>'.$item['nombre'].'</td>
                <td style="text-align: center;"><a class="btnEliminarProducto" codigo="'.$item['cod_producto_extra_detalle'].'" categoria="'.$cod_categoria.'" codigo_padre="'.$cod_producto_extra.'"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></a></td>
              </tr>';
     }

     $boton.='<button type="button" class="btn btn-outline-primary btnEliminar" codigo="'.$cod_producto_extra.'" categoria="'.$cod_categoria.'" codigo_padre="'.$cod_producto_extra.'">Quitar toda la seleccion</button>';

     $return['success'] = 1;
     $return['html'] = $html;
     $return['boton'] = $boton;
  }
  else
  {
    $return['success'] = 0;
    $html.='<p>No hay productos seleccionados</p>';
    $return['html'] = $html;
  }
    
 return $return;
}

function eliminar_todos(){
  global $Clproductos;
  $codigo=$_POST['codigo'];
  $respUno= $Clproductos->delete_productos_extra($codigo);
  if($respUno)
  {  
     $return['success'] = 1;
     $return['mensaje'] ="Productos eliminados correctamente";
  }
  else
  {
     $return['success'] = 0;
     $return['mensaje'] ="Error al eliminar los productos";
  }
  return $return;
}

function eliminar_item(){
  global $Clproductos;
  $codigo=$_POST['codigo'];
  $respUno= $Clproductos->delete_item($codigo);
  if($respUno)
  {  
     $return['success'] = 1;
     $return['mensaje'] ="Producto eliminado correctamente";
  }
  else
  {
     $return['success'] = 0;
     $return['mensaje'] ="Error al eliminar el producto";
  }
  return $return;
}

function update_cantidad(){
  global $Clproductos;
  $codigo=$_POST['codigo'];
  $cantidad=$_POST['cantidad'];
  
  $respUno= $Clproductos->update_cantidad($codigo,$cantidad);
  if($respUno)
  {  
     $return['success'] = 1;
     $return['mensaje'] ="Cantidad editada correctamente";
  }
  else
  {
     $return['success'] = 0;
     $return['mensaje'] ="Error al editar la cantidad";
  }
  return $return;
}

function insertar_item(){
  global $Clproductos;
  $cod_producto_extra=$_POST['cod_producto_extra'];
  $cod_producto=$_POST['cod_producto'];

  $respUno= $Clproductos->insert_item_extra($cod_producto_extra,$cod_producto);
  if($respUno)
  {  
     $return['success'] = 1;
     $return['mensaje'] ="Producto seleccionado correctamente";
  }
  else
  {
     $return['success'] = 0;
     $return['mensaje'] ="Error al selecionar el producto";
  }
  return $return;
}

function insertar_productos(){
  global $Clproductos;
  $cod_producto_extra=$_POST['cod_producto_extra'];
  $categoria=$_POST['categoria'];
  
  if($categoria!=0){$respUno= $Clproductos->insert_productos_extra($categoria,$cod_producto_extra);}
  else{$respUno= $Clproductos->insert_todos_extra($cod_producto_extra);}

    if($respUno)
    {  
       $return['success'] = 1;
       $return['mensaje'] ="Productos seleccionados correctamente";
    }
    else
    {
       $return['success'] = 0;
       $return['mensaje'] ="Error al selecionar los productos";
    }
  return $return;
}

function actualizar(){
   global $Clproductos;

    if(!isset($_POST['productos'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($productos); $i++) { 
        $Clproductos->moverCategorias($productos[$i],$codigo, $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}