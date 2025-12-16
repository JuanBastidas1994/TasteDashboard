<?php
require_once "../funciones.php";

error_reporting(E_ALL);

$data = importar();
header("Content-Type: application/json");
echo json_encode($data);

function importar(){
    
    /* INICIO EXTRAER EXCEL */
    require_once('class/PHPExcel.php');
    require_once('class/PHPExcel/Reader/Excel2007.php');
    
    // Cargando la hoja de excel
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load("ciudadesInlog.xlsx");
    $objFecha = new PHPExcel_Shared_Date();
    // Asignamos la hoja de excel activa
    $objPHPExcel->setActiveSheetIndex(0);
    
    $data = [];
    $data = InsertarCiudades($objPHPExcel);
    
    /* FIN EXTRAER EXCEL */
    $return['success'] = 1;
    $return['accion'] = $accion;
    $return['mensaje'] = "Datos importados";
    $return['data'] = $data;

    return $return;
}


function InsertarCiudades($objPHPExcel){
    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    for ($x=2;$x<=$filas;$x++){
        $_DATOS_GROUP[$x]['codigo'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['ciudad'] = $objPHPExcel->getActiveSheet()->getCell('B'.$x)->getCalculatedValue();
        $_DATOS_GROUP[$x]['provincia'] = $objPHPExcel->getActiveSheet()->getCell('C'.$x)->getCalculatedValue();

        extract($_DATOS_GROUP[$x]);
        
        if(trim($codigo) == "" || trim($ciudad) == "" || trim($provincia) == ""){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            if(getCiudad($codigo)){
                $_DATOS_GROUP[$x]['importado'] = false;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "YA EXISTE ESTE CÓDIGO";
            }else{
                if(crearCiudad($codigo, $ciudad, $provincia)){
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

function getCiudad($codigo){
    $query = "SELECT * FROM tb_ciudades WHERE codigo='$codigo'";
    return Conexion::buscarRegistro($query);
}

function crearCiudad($codigo, $ciudad, $provincia){
    $query = "INSERT INTO tb_ciudades VALUES(NULL, 4, '$ciudad', '$codigo', '$provincia', '', 'A')";
    return Conexion::ejecutar($query,NULL);
}

?>