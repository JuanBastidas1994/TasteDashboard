<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_cards_fidelizacion.php";
$Clcards = new cl_cards();
$session = getSession();
$filesUP = url_upload.'/assets/empresas/'.$session['alias'].'/';
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

controller_create();
error_reporting(E_ALL);

function importar(){
    global $filesUP;
    global $files;
    
    $accion=$_POST['cmbAccion'];
    $formato=explode(".",$_FILES["excel"]["name"]);
    $f = strtolower($formato[count($formato)-1]);
    $nombre = "importar-codigos-".fechaSignos().".".$f;
    $dirLogo = $filesUP."/".$nombre;
    
    if($f=="xls" || $f=="xlsx"){ /* VALIDAR SI ES EXCEL */
        if (move_uploaded_file($_FILES["excel"]["tmp_name"], $dirLogo)){
            
            /* INICIO EXTRAER EXCEL */
            require_once('class/PHPExcel.php');
            require_once('class/PHPExcel/Reader/Excel2007.php');
            
            // Cargando la hoja de excel
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load($dirLogo);
            $objFecha = new PHPExcel_Shared_Date();
            // Asignamos la hoja de excel activa
            $objPHPExcel->setActiveSheetIndex(0);
            
            $data = [];
            echo $accion;
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
        else{
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



function InsertarProductos($objPHPExcel){
    global $Clcards;
    global $session;
    $cod_empresa = $session['cod_empresa'];
    
    echo 'INSERTAR CARDS';


    $filas = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
    for ($x=2;$x<=$filas;$x++){
        $_DATOS_GROUP[$x]['codigo'] = $objPHPExcel->getActiveSheet()->getCell('A'.$x)->getCalculatedValue();             

        $codigo = $_DATOS_GROUP[$x]['codigo'];
        
        if($codigo == ""){
            $_DATOS_GROUP[$x]['importado'] = false;
            $_DATOS_GROUP[$x]['fila'] = $x;
            $_DATOS_GROUP[$x]['motivo'] = "FALTAN CAMPOS OBLIGATORIOS";
        }
        else{
            if($Clcards->get($cod_empresa, $codigo)){
                $_DATOS_GROUP[$x]['importado'] = false;
                $_DATOS_GROUP[$x]['fila'] = $x;
                $_DATOS_GROUP[$x]['motivo'] = "YA EXISTE ESTE CÓDIGO";
            }else{                
                $Clcards->cod_cliente = 0;
                $Clcards->codigo = $codigo;
                $Clcards->estado = "I";
                
                if($Clcards->crear($cod_empresa)){
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

function vaciarTarjeta(){
    global $Clcards;
    extract($_GET);
    if($Clcards->vaciarTarjeta($cod_tarjeta)){
        $return['success'] = 1;
        $return['mensaje'] = "Editado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al editar";
    }
    return $return;
}
?>