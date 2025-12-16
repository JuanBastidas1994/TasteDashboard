<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos.php";
require_once "../clases/cl_categorias.php";
$Clproductos = new cl_productos();
$Clcategorias = new cl_categorias();
$session = getSession();
$filesUP = url_upload.'/assets/empresas/'.$session['alias'].'/';
$files = '../assets/empresas/'.$session['alias'].'/';

controller_create();
error_reporting(E_ALL);

function importar(){
    global $session;
    global $filesUP;
    global $files;
    
    $accion=$_POST['cmbAccion'];
    $formato=explode(".",$_FILES["excel"]["name"]);
    $f = strtolower($formato[count($formato)-1]);
    $nombre = "importar-productos-".fechaSignos().".".$f;
    $dirLogo = $filesUP."/".$nombre;
    
    if($f=="xls" || $f=="xlsx") /* VALIDAR SI ES EXCEL */
    {
        if (move_uploaded_file($_FILES["excel"]["tmp_name"], $dirLogo))
        {
            /* INICIO EXTRAER EXCEL */
            require_once('class/PHPExcel.php');
            require_once('class/PHPExcel/Reader/Excel2007.php');
            
            // Cargando la hoja de excel
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load($files.$nombre);
            $objFecha = new PHPExcel_Shared_Date();
            // Asignamos la hoja de excel activa
            $objPHPExcel->setActiveSheetIndex(0);
            
            if($accion == "VERIFICAR"){
                $data = VerificarProductos($objPHPExcel);
            }else if($accion == "ACTUALIZAR"){
                $data = ActualizarProductos($objPHPExcel);
            }else if($accion == "INSERTAR"){
                $data = InsertarProductos($objPHPExcel);
            }
            
            /* FIN EXTRAER EXCEL */
            $return['success'] = 1;
            $return['accion'] = $accion;
            $return['mensaje'] = "Datos importados";
            $return['data'] = $data;

            return $return;
        }
        else
        {
            $return['success'] = 0;
            $return['mensaje'] = "El excel no cargo, por favor vuelva a escoger la archivo. ".$filesUP;
            return $return;
        }
    }   
    else{
        $return['success'] = 0;
        $return['mensaje'] = "El formato del archivo no es permitido"; 
        return $return;
    } 
}

function VerificarProductos($objPHPExcel){
    global $Clproductos;
    global $Clcategorias;
    global $session;
    $html = ""; 
    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    for ($x=2;$x<=$filas;$x++){
        $_DATOS_GROUP[$x]['nombre'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();             
        $_DATOS_GROUP[$x]['descripcion']= $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();    
        $_DATOS_GROUP[$x]['pvp'] = $objPHPExcel->getActiveSheet()->getCell('C'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['incluye_iva'] = $objPHPExcel->getActiveSheet()->getCell('D'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['grava_iva'] = $objPHPExcel->getActiveSheet()->getCell('E'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['sku'] = $objPHPExcel->getActiveSheet()->getCell('F'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['peso'] = $objPHPExcel->getActiveSheet()->getCell('G'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['categorias']= $objPHPExcel->getActiveSheet()->getCell('H'.$x)->getCalculatedValue();   

        $nombre = $_DATOS_GROUP[$x]['nombre'];
        $descripcion = $_DATOS_GROUP[$x]['descripcion'];
        $categorias = $_DATOS_GROUP[$x]['categorias'];
        $pvp = $_DATOS_GROUP[$x]['pvp'];
        $incluye_iva = $_DATOS_GROUP[$x]['incluye_iva'];
        $grava_iva = $_DATOS_GROUP[$x]['grava_iva'];
        $sku = $_DATOS_GROUP[$x]['sku'];
        $peso = $_DATOS_GROUP[$x]['peso'];


        if($nombre == "" || $sku == "" || $pvp == ""){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            if($Clproductos->getBySku($sku)){
                $_DATOS_GROUP[$x]['importado'] = true;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "EXISTE";
            }else{
                $_DATOS_GROUP[$x]['importado'] = false;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "NO EXISTE";
            }
        }
    }
    return $_DATOS_GROUP;
}

function InsertarProductos($objPHPExcel){
    global $Clproductos;
    global $Clcategorias;
    global $session;
    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    for ($x=2;$x<=$filas;$x++){
        $_DATOS_GROUP[$x]['nombre'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();             
        $_DATOS_GROUP[$x]['descripcion']= $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();    
        $_DATOS_GROUP[$x]['pvp'] = $objPHPExcel->getActiveSheet()->getCell('C'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['incluye_iva'] = $objPHPExcel->getActiveSheet()->getCell('D'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['grava_iva'] = $objPHPExcel->getActiveSheet()->getCell('E'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['sku'] = $objPHPExcel->getActiveSheet()->getCell('F'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['peso'] = $objPHPExcel->getActiveSheet()->getCell('G'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['categorias']= $objPHPExcel->getActiveSheet()->getCell('H'.$x)->getCalculatedValue();   

        $nombre = $_DATOS_GROUP[$x]['nombre'];
        $descripcion = $_DATOS_GROUP[$x]['descripcion'];
        $categorias = $_DATOS_GROUP[$x]['categorias'];
        $pvp = $_DATOS_GROUP[$x]['pvp'];
        $incluye_iva = $_DATOS_GROUP[$x]['incluye_iva'];
        $grava_iva = $_DATOS_GROUP[$x]['grava_iva'];
        $sku = $_DATOS_GROUP[$x]['sku'];
        $peso = $_DATOS_GROUP[$x]['peso'];

        $incluye_iva = $incluye_iva === 'true'? true:false;
        $grava_iva = $grava_iva === 'true'? true:false;
        $grava_iva = $grava_iva === true? 1:0;
        $peso = $peso === ""? 0:$peso;

        if($nombre == "" || $sku == "" || $pvp == "" ||  !is_numeric($peso)){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            if($Clproductos->getBySku($sku)){
                $_DATOS_GROUP[$x]['importado'] = false;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "YA EXISTE ESTE SKU";
            }else{
                $categoriasVerificadas = [];
                if(trim($categorias) <> ""){
                   $cats = explode("-", $categorias);
                   for($i=0; $i<count($cats); $i++){
                       $cat = trim($cats[$i]);
                        if(is_numeric($cat)){
                            if($Clcategorias->get($cat)){
                                $categoriasVerificadas[] = $cat;
                            }
                        }
                    }
                }
 
                if($incluye_iva){
                    $precio_no_tax = $pvp/1.12;
                    $iva = $precio_no_tax*0.12;
                }
                else{
                    $precio_no_tax = $pvp;
                    $iva = $precio_no_tax*0.12;
                    $pvp = $precio_no_tax + $iva;
                }
                
                
                $Clproductos->nombre = htmlentities($nombre);
                $Clproductos->desc_corta = htmlentities($descripcion);
                $Clproductos->precio = $pvp;
                $Clproductos->precio_no_tax = number_format($precio_no_tax, 4);
                $Clproductos->iva_valor = number_format($iva, 4);
                $Clproductos->iva_porcentaje = 12;
                $Clproductos->cobra_iva = $grava_iva;
                $Clproductos->peso = $peso;
                $Clproductos->sku = $sku;
                $Clproductos->categorias = $categoriasVerificadas;
                $aux = "";
                do{
                    $alias = create_slug(sinTildes($nombre.$aux));
                    $aux = intval(rand(1,100)); 
                }while(!$Clproductos->aliasDisponible($alias));
                $Clproductos->alias = $alias;
                
                //COPIAR IMAGEN
                $nameImg = 'product_'.datetime_format().'_'.$x.'_'.rand(1, 100).'.png';
                $Clproductos->image_min = 'min_'.$nameImg;
                $Clproductos->image_max = $nameImg;
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                $img3 = url_upload.'/assets/empresas/'.$session['alias'].'/min_'.$nameImg;
                copy($img1, $img2);
                copy($img1, $img3);
                //COPIAR IMAGEN
                
                if($Clproductos->crear_importados($id)){
                    $_DATOS_GROUP[$x]['importado'] = true;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "CREADO CORRECTAMENTE";
                }else{
                    $_DATOS_GROUP[$x]['importado'] = false;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "NO SE PUDO CREAR";
                }  
            }

        }
    }
    return $_DATOS_GROUP;
}

function ActualizarProductos($objPHPExcel){
    global $Clproductos;
    global $Clcategorias;
    global $session;
    $html = ""; 
    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    for ($x=2;$x<=$filas;$x++){
        $_DATOS_GROUP[$x]['nombre'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();             
        $_DATOS_GROUP[$x]['descripcion']= $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();    
        $_DATOS_GROUP[$x]['pvp'] = $objPHPExcel->getActiveSheet()->getCell('C'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['incluye_iva'] = $objPHPExcel->getActiveSheet()->getCell('D'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['grava_iva'] = $objPHPExcel->getActiveSheet()->getCell('E'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['sku'] = $objPHPExcel->getActiveSheet()->getCell('F'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['peso'] = $objPHPExcel->getActiveSheet()->getCell('G'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['categorias']= $objPHPExcel->getActiveSheet()->getCell('H'.$x)->getCalculatedValue();   

        $nombre = $_DATOS_GROUP[$x]['nombre'];
        $descripcion = $_DATOS_GROUP[$x]['descripcion'];
        $categorias = $_DATOS_GROUP[$x]['categorias'];
        $pvp = $_DATOS_GROUP[$x]['pvp'];
        $incluye_iva = $_DATOS_GROUP[$x]['incluye_iva'];
        $grava_iva = $_DATOS_GROUP[$x]['grava_iva'];
        $sku = $_DATOS_GROUP[$x]['sku'];
        $peso = $_DATOS_GROUP[$x]['peso'];

        $incluye_iva = $incluye_iva === 'true'? true:false;
        $grava_iva = $grava_iva === 'true'? true:false;
        $grava_iva = $grava_iva === true? 1:0;
        $peso = $peso === "" ? 0 : $peso;

        if($nombre == "" || $sku == "" || $pvp == "" ||  !is_numeric($peso)){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            $producto = $Clproductos->getBySku($sku);
            if(!$producto){
                $_DATOS_GROUP[$x]['importado'] = false;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "NO EXISTE";
                
            }else{
                $categoriasVerificadas = [];
                if(trim($categorias) !== ""){
                   $cats = explode("-", $categorias);
                   for($i=0; $i<count($cats); $i++){
                       $cat = trim($cats[$i]);
                        if(is_numeric($cat)){
                            if($Clcategorias->get($cat)){
                                $categoriasVerificadas[] = $cat;
                            }else
                                $_DATOS_GROUP[$x]['motivo_cat'] = "CATEGORIA $cat NO EXISTE";
                        }else{
                            $_DATOS_GROUP[$x]['motivo_cat'] = "CATEGORIA $cat NO ES ENTERO";
                        }
                    }
                }
    
                if($incluye_iva){
                    $precio_no_tax = $pvp/1.12;
                    $iva = $precio_no_tax*0.12;
                }
                else{
                    $precio_no_tax = $pvp;
                    $iva = $precio_no_tax*0.12;
                    $pvp = $precio_no_tax + $iva;
                }
                
                $Clproductos->cod_producto = $producto['cod_producto'];
                $Clproductos->precio = $pvp;
                $Clproductos->precio_no_tax = number_format($precio_no_tax, 4);
                $Clproductos->iva_valor = number_format($iva, 4);
                $Clproductos->iva_porcentaje = 12;
                $Clproductos->peso = $peso;
                $Clproductos->cobra_iva = $grava_iva;
                $Clproductos->categorias = $categoriasVerificadas;
                if($Clproductos->editar_importados($id)){
                    $_DATOS_GROUP[$x]['importado'] = true;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "ACTUALIZADO CORRECTAMENTE";
                    $_DATOS_GROUP[$x]['categorias'] = $categoriasVerificadas;
                }else{
                    $_DATOS_GROUP[$x]['importado'] = false;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "NO SE PUDO ACTUALIZAR";
                }    
            }

        }
    }
    return $_DATOS_GROUP;
}

?>