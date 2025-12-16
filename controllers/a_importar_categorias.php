<?php
require_once "../funciones.php";

error_reporting(E_ALL);

$cod_empresa = 135;
$data = importar();
header("Content-Type: application/json");
echo json_encode($data);

function importar(){
    
    /* INICIO EXTRAER EXCEL */
    require_once('class/PHPExcel.php');
    require_once('class/PHPExcel/Reader/Excel2007.php');
    
    // Cargando la hoja de excel
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load("a_categorias2.xlsx");
    $objFecha = new PHPExcel_Shared_Date();
    // Asignamos la hoja de excel activa
    
    //$objPHPExcel->setActiveSheetIndex(0);
    
    $data = [];
    $data = InsertarSubCategorias($objPHPExcel);
    
    /* FIN EXTRAER EXCEL */
    $return['success'] = 1;
    $return['accion'] = $accion;
    $return['mensaje'] = "Datos importados";
    $return['data'] = $data;

    return $return;
}


function InsertarSubCategorias($objPHPExcel){
    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    
    $inicio = 201;
    $fin = 300;
    if($fin>$filas)
        $fin = $filas;
        
    for ($x=$inicio;$x<=$fin;$x++){    
        $_DATOS_GROUP[$x]['categoria_id'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['categoria_nombre'] = $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['subcategoria_nombre'] = $objPHPExcel->getActiveSheet()->getCell('C'.$x)->getCalculatedValue();

        extract($_DATOS_GROUP[$x]);
        echo '<br/>'.$categoria_id.' - '.$categoria_nombre.' - ' .$subcategoria_nombre;
        
        if(trim($categoria_id) == "" || trim($categoria_nombre) == "" || trim($subcategoria_nombre) == ""){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            $subcategoria = findCategory($subcategoria_nombre);
            if($subcategoria){
                if(addCategoryChild($categoria_id, $subcategoria['cod_categoria'])){
                    $_DATOS_GROUP[$x]['importado'] = false;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "CATEGORIA ASIGNADA";   
                }else{
                    $_DATOS_GROUP[$x]['importado'] = false;
                    $_DATOS_GROUP[$x]['fila'] = $x;
                    $_DATOS_GROUP[$x]['motivo'] = "YA ESTABA GUARDADA";
                }
            }else{
                if(createCategory($_DATOS_GROUP[$x])){
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

function findCategory($name){
    global $cod_empresa;
    $query = "SELECT * FROM tb_categorias WHERE categoria = '$name' AND cod_empresa = $cod_empresa AND estado IN ('A', 'I');";
    return Conexion::buscarRegistro($query);
}

function createCategory($data){
    global $cod_empresa;
    extract($data);
    
    $aux = "";
    do{
        $alias = create_slug(sinTildes($subcategoria_nombre.$aux));
        $aux = intval(rand(1,100));
    }while(AliasExist($alias));
    
    $nameImg = 'categoria_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;
    $query = "INSERT INTO tb_categorias VALUES(NULL, $cod_empresa, $categoria_id, '$alias', '$subcategoria_nombre', '', '', '$nameImgMin', '$nameImg', 0, NOW(), 'A', '')";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp){
        $id = Conexion::lastId();
        addCategoryChild($categoria_id, $id);
        
        echo '- ID:'.$id;
        
        //Imagenes
        $url_upload = "/home1/digitalmind/dashboard.mie-commerce.com/assets";
        $noImg = $url_upload.'/img/200x200.jpg';
        copy($noImg, $url_upload.'/empresas/megaproductos86/'.$nameImg);
        copy($noImg, $url_upload.'/empresas/megaproductos86/'.$nameImgMin);
        
        return true;
    }else{
        return false;
    }
}

function addCategoryChild($padre, $hijo){
    $query = "SELECT * FROM tb_categorias_dependientes WHERE cod_categoria = $hijo AND cod_categoria_padre = $padre";
    if(Conexion::buscarRegistro($query)){
        return false;
    }else{
        $query = "INSERT INTO tb_categorias_dependientes(cod_categoria, cod_categoria_padre) VALUES($hijo, $padre)";
        Conexion::ejecutar($query, null);
        return true;
    }
    
}

function AliasExist($alias){
    global $cod_empresa;
	$query = "SELECT * FROM tb_categorias WHERE alias = '$alias' AND cod_empresa = $cod_empresa AND estado IN ('A','I')";
	return Conexion::buscarRegistro($query, NULL);
}
?>