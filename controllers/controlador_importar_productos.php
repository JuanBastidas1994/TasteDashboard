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

//error_reporting(E_ALL);
controller_create();

function importar(){
    global $session;
    global $filesUP;
    global $files;
    
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
            $html = ReadExcelProductos($objPHPExcel);
            /* FIN EXTRAER EXCEL */
            $return['success'] = 1;
            $return['mensaje'] = "Datos importados";
            $return['html'] = $html;
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

function ReadExcelProductos($objPHPExcel){
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
        $peso = $peso === ""? 0:$peso;

        if($nombre == "" || $pvp == "" ||  !is_numeric($peso)){   
            $html.="<tr>
                        <td>No importado fila #: ".$x."</td>
                        <td>No importado</td>
                    </tr>";
        }
        else{
            $hayCategorias = false;
            if(trim($categorias) <> ""){
               $cats = explode("-", $categorias); 
               for($i=0; $i<count($cats); $i++){
                    if(!is_numeric(trim($cats[$i]))){
                        $cats[$i] = 0;
                    }
                    else
                        $hayCategorias = true;
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
            //
            
            if($Clproductos->crear_importados($id)){
                if($hayCategorias){
                    for($j=0; $j<count($cats); $j++){
                        $cod_categoria = $cats[$j];
                        if($Clcategorias->getArray($cod_categoria, $array)){
                            $Clproductos->impotarCategoriaAProducto($id, $cod_categoria);
                        }
                    }
                }
                $html.="<tr>
                            <td>".$nombre."</td>
                            <td>Importado</td>
                        </tr>";
            }
            else{
                $html.="<tr>
                            <td>".$nombre."</td>
                            <td>No importado</td>
                        </tr>";
            }
        }
    }
    return $html;
}

?>