<?php
require_once "../funciones.php";
//Claseso
require_once "../clases/cl_categorias.php";
$Clcategorias = new cl_categorias();
$session = getSession();

controller_create();

function crear(){
    global $Clcategorias;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    //VALIDAR AUXILIAR
    $aux = "";
    do{
        $alias = create_slug(sinTildes($txt_nombre.$aux));
        $aux = intval(rand(1,100));
    }while(!$Clcategorias->aliasDisponible($alias));

    $desc_larga = editor_encode($desc_larga);
    $nameImg = 'categoria_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;
    $Clcategorias->alias = $alias;
    $Clcategorias->nombre = $txt_nombre;
    $Clcategorias->desc_corta = $txt_descripcion_corta;
    $Clcategorias->desc_larga = $desc_larga;
    $Clcategorias->image_min = $nameImgMin;
    $Clcategorias->image_max = $nameImg;

    if(count($cmb_categoria) > 0)
        $Clcategorias->cod_categoria_padre = $cmb_categoria[0];
    else
        $Clcategorias->cod_categoria_padre = 0;

    if(isset($_POST['chk_estado']))
        $Clcategorias->estado = 'A';
    else
        $Clcategorias->estado = 'I';
      
    if(!isset($_POST['cod_producto'])){
        $id=0;
        if($Clcategorias->crear2($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Categoría creada correctamente";
            $return['id'] = $id;
            $return['alias'] = $alias;

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

            /*Guardar Categorias*/
            for ($i=0; $i < count($cmb_categoria); $i++) { 
                $Clcategorias->guardarVariasCategorias($id, $cmb_categoria[$i]);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la categoria, por favor vuelva a intentarlo";
        }
    }else{
        $Clcategorias->cod_categoria = $cod_producto;
        if($Clcategorias->editar2()){
            $return['success'] = 1;
            $return['mensaje'] = "Categoria editada correctamente";
            $return['id'] = $Clcategorias->cod_categoria;

            $data = NULL;
            if($Clcategorias->getArray($cod_producto, $data)){
                $return['alias'] = $data['alias'];
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['image_max'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clcategorias->setImage($nameImgMax, 'max', $cod_producto);
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
                        $Clcategorias->setImage($nameImgMin, 'min', $cod_producto);
                        if($cambio){
                            deleteFile($data['image_min']);
                        }
                    }
                }
                $return['imagen'] = "editada";
            }

            /*Guardar Categorias*/
            $Clcategorias->eliminarVariasCategorias($cod_producto);
            for ($i=0; $i < count($cmb_categoria); $i++) { 
                $Clcategorias->editarVariasCategorias($cod_producto, $cmb_categoria[$i]);
            }

            /*SUBIR IMAGEN*/
            //uploadFile($_FILES["img_product"], $nameImg);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la categoria";
        }
    }
    return $return;
}

function get(){
    global $Clcategorias;
    if(!isset($_GET['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clcategorias->getArray($cod_producto, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Usuario encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Usuario no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
  global $Clcategorias;
  if(!isset($_GET['cod_categoria']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

  extract($_GET);

    $resp = $Clcategorias->set_estado($cod_categoria, $estado);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Categoria editado correctamente";
      if($estado == "D")
            $return['mensaje'] = "Categoria eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al editar el usuario";
    }
    return $return;
}

function actualizar(){
   global $Clcategorias;

    if(!isset($_POST['codigos'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($codigos); $i++) { 
        $Clcategorias->moverProductosCategorias($codigos[$i], $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function actualizarCategorias(){
   global $Clcategorias;

    if(!isset($_POST['codigos'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($codigos); $i++) { 
        $Clcategorias->moverCategorias($codigos[$i], $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function getVariasCategorias(){
    global $Clcategorias;
    extract($_GET);
    $cod_categorias = [];

    $categoriasPadres = $Clcategorias->getVariasCategorias($cod_categoria);
    if($categoriasPadres){
        foreach ($categoriasPadres as $categoriaPadre) {
            $cod_categorias[] = $categoriaPadre['cod_categoria_padre'];
        }
        $return['categorias'] = $cod_categorias;
        $return['success'] = 1;
        $return['mensaje'] = "Categorías obtenidas";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al traer las categorias";
    }
    return $return;
}
?>