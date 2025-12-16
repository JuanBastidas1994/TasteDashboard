<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_servicios.php";
$Clservicios = new cl_servicios(NULL);
$session = getSession();

controller_create();

function crear(){
    global $Clservicios;
    global $session;
    // if(count($_POST)==0){
    //     $return['success'] = 0;
    //     $return['mensaje'] = "Falta informacion";
    //     return $return;
    // }

    extract($_POST);

    $precio = floatval($txt_precio);
    $precio_no_tax = floatval($precio) / 1.12;
    $valor_iva = floatval($precio) - floatval($precio_no_tax);
    $precio_no_tax=($txt_precio_no_taxC);
    $valor_iva =($txt_ivaC);

    $desc_larga = editor_encode($desc_larga);
    $nameImg = 'product_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;

    $Clservicios->nombre = $txt_nombre;
    $Clservicios->desc_corta = $txt_descripcion_corta;
    $Clservicios->desc_larga = $desc_larga;
    $Clservicios->image_min = $nameImgMin;
    $Clservicios->image_max = $nameImg;


    $Clservicios->costo = formatFloat($txt_costo);

    $Clservicios->precio = formatFloat($txt_precio);
    $Clservicios->precio_anterior = formatFloat($txt_precio_anterior);
    $Clservicios->precio_no_tax = formatFloat4($precio_no_tax);
    $Clservicios->iva_valor = formatFloat4($valor_iva);
    $Clservicios->iva_porcentaje = 12;
    $Clservicios->cod_producto_padre = $cod_producto_padre;
    $Clservicios->categorias = $cmb_categoria;
    $Clservicios->peso = 0;
    $Clservicios->sku = 0;
    $Clservicios->intervalo = $intervalo;
    if(isset($_POST['chk_estado']))
    $Clservicios->estado = 'A';
    else
        $Clservicios->estado = 'I';

    if(isset($_POST['chk_detalle']))
        $Clservicios->open_detalle = 1;
    else
        $Clservicios->open_detalle = 0;

    if(isset($_POST['chk_base']))
        $Clservicios->cobra_iva = 1;
    else
        $Clservicios->cobra_iva = 0;
        
    if(isset($_POST['chk_combo']))
        $Clservicios->is_combo = 1;
    else
        $Clservicios->is_combo = 0;    

    if(isset($_POST['chk_fSinStock']))
        $Clservicios->facturar_sin_stock = 1;
    else
        $Clservicios->facturar_sin_stock = 0;    


    if(!isset($_POST['cod_producto'])){
        $aux = "";

        do{
            $alias = create_slug(sinTildes($txt_nombre.$aux));
            $aux = intval(rand(1,100)); 
        }while(!$Clservicios->aliasDisponible($alias));

        $Clservicios->alias = $alias;

        $idP=0;

        if($Clservicios->crear($idP)){
            $return['success'] = 1;
            $return['mensaje'] = "Servicio creado correctamente";
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
                $Clservicios->setEtiquetas($cod_producto, $cmbEtiquetas[$i]);
            }

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el servicio, por favor vuelva a intentarlo";
        }
    }else{
        $Clservicios->cod_producto = $cod_producto;
        if($Clservicios->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Producto editado correctamente";
            $return['id'] = $Clservicios->cod_producto;
            $idP = $Clservicios->cod_producto;

            //INSERT ETIQUETAS
            $Clservicios->delEtiquetas($idP);
            for ($i=0; $i < count($cmbEtiquetas); $i++) { 
                $Clservicios->setEtiquetas($idP, $cmbEtiquetas[$i]);
            }

            $data = NULL;
            if($Clservicios->getArray($cod_producto, $data)){
                $return['alias'] = $data['alias'];
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['image_max'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clservicios->setImage($nameImgMax, 'max', $idP);
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
                        $Clservicios->setImage($nameImgMin, 'min', $idP);
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

    return $return;
 
}


function formatFloat($float){
    return round($float,2);
}

function formatFloat4($float){
    return round($float,4);
}



?>