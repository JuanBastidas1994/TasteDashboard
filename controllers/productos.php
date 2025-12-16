<?php
/*
  AUTOR: JUANK BASTIDAS
  FECHA: 03-03-2020
*/
require_once "../conexion.php";
require_once "../funciones.php";
require_once "../clases/cl_productos.php";
$Clproductos = new cl_productos(NULL);

$session = getSession();

$cod_usuario = $session['cod_usuario'];
$cod_empresa = $session['cod_empresa'];
$cod_rol = $session['cod_rol'];
$metodo = $_GET['metodo'];

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if($metodo == "S")
{
    $id = $_GET['id'];
    $query = "SELECT * 
                FROM tb_productos 
                WHERE cod_producto = ".$id;
    $resp = mysqli_query($con, $query); 
    				                        
    if(mysqli_num_rows($resp)>0)
    {
        $row = mysqli_fetch_array($resp);

        $queryStock = "SELECT *
                        FROM tb_inventario
                        WHERE cod_producto";
        $respStock = mysqli_query($con, $queryStock);
        $rowStock = mysqli_fetch_assoc($respStock);

    	$return['success'] = 1;
    	$return['informacion'] = array_map('html_entity_decode', $row);	
    	$return['informacion']['desc_larga'] = html_entity_decode(htmlspecialchars_decode($row['desc_larga']));
    	$return['informacion']['ruta_logo'] = $dir2 ."/".$row['image_min'];
        $return['informacion']['stock'] = $rowStock['cantidad'];

        /////////////
        //Obtener categorias
        $query_ck = "SELECT *
                    FROM tb_productos_categoria
                    WHERE cod_producto = $id";
        $resp_ck = mysqli_query($con, $query_ck);
        $f = 0;
        while($row_ck = mysqli_fetch_assoc($resp_ck))
        {
            $checks[$f] = $row_ck['cod_categoria'];   
            $f++;         
        }
        $return['checks'] = $checks;
        /////////////

        ////////////
        //OBTENER ESPECIFICACIONES
        $queryEsp = "SELECT *
                    FROM tb_productos_especificacion
                    WHERE cod_producto = $id";
        $respEsp = mysqli_query($con, $queryEsp);
        while($rowEsp = mysqli_fetch_assoc($respEsp))
        {
            $rowEsp = array_map('html_entity_decode', $rowEsp);
            $esp.= '<div class="col-md-12 col-sm-12 col-xs-12 ubl_esp">
                      <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
                        <input type="text" name="txt_esp2[]" class="form-control uesp" readonly="readonly" value="'.$rowEsp['nombre'].'">
                      </div>
                      <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 20px;">
                        <textarea name="txa_det_esp[]" class="form-control udet_esp" readonly="readonly">'.$rowEsp['especificacion'].'</textarea>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
                        <button class="btn btn-danger ubtn_quitar_esp" onclick="uquitarEsp(event);">Eliminar</button>
                      </div>
                    </div>';
        }
        $return['esp'] = $esp;
        ////////////
                                $query2="SELECT pi.*
                                            FROM tb_productos_imagenes pi, tb_productos p
                                            WHERE pi.cod_producto = p.cod_producto
                                            AND pi.cod_producto = ".$id."
                                            AND p.cod_empresa= ".$cod_empresa;
                                  $resp2=mysqli_query($con,$query2);
                                  if(mysqli_num_rows($resp2)>0)
                                  {
                                    while($row2=mysqli_fetch_assoc($resp2)) 
                                    {
                                      $img .= '<!-- INICIO ZONA DE EDITAR IMAGENES -->
                                          <form>
                                            <div class="col-md-12 col-sm-12 col-xs-12 x_panel" id="itemImagen'.$row2['cod_imagen'].'">
                                              <div class="col-md-6 col-sm-6 col-xs-12">
                                                <a href="'.$dir2.'/'.$row2['nombre_img'].'" target="_blank"><img style="width: 50px;" src="'.$dir2.'/'.$row2['nombre_img'].'"></a>
                                              </div>
                                              <div class="col-md-6 col-sm-6 col-xs-12">
                                                <button onclick="eliminarImagen('.$row2['cod_imagen'].',event)" class="btn btn-danger">Eliminar&nbsp;&nbsp;<i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                              </div> 
                                            </div>
                                          </form>
                                                <!-- FIN ZONA DE EDITAR IMAGENES-->'; 
                                    }                                    
                                  }
                                else
                                  {
                                    $img = "No hay imagenes";
                                  }
                                  $return['img'] = $img;
                                  $query3="SELECT pa.* 
                                            FROM tb_productos_archivos pa, tb_productos p
                                            WHERE pa.cod_producto = p.cod_producto
                                            AND pa.cod_producto = ".$id."
                                            AND p.cod_empresa =".$cod_empresa;
                                  $resp3=mysqli_query($con,$query3);
                                  if(mysqli_num_rows($resp3)>0)
                                  {
                                     $file .= '<label>Lista de Archivos</label>';
                                    while($row3=mysqli_fetch_assoc($resp3))
                                    {
                                      $file .= '<!-- INICIO ZONA DE EDITAR ARCHIVOS -->
                                      <div class="col-md-12 col-sm-12 col-xs-12 x_panel" id="itemArchivo'.$row3['cod_archivo'].'">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                          <a href="'.$dir2.'/'.$row3['nombre_archivo'].'" target="_blank"><img style="width: 50px;" src="'.$dir3.'/pdf.png"></a>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                          <button onclick="eliminarArchivo('.$row3['cod_archivo'].',event)" class="btn btn-danger">Eliminar&nbsp;&nbsp;<i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                        </div> 
                                      </div>
                                          <!-- FIN ZONA DE EDITAR ARCHIVOS-->'; 
                                    }
                                  }
                                  else
                                  {
                                    $file = "No hay archivos";
                                  }
                                  $return['file'] = $file;
    }
    else
    {
    	$return['success'] = 0;
    	$return['mensaje'] = "No hay items";	
    }
}

if($metodo == "I"){
    foreach ($_POST as $key => $value) {
        $$key = $value;
    }
    //var_dump($_POST);
    $edit = false;
    if(isset($_POST['cod_producto'])){
        $cod_producto = $_POST['cod_producto'];
        if($cod_producto > 0)
            $edit = true;
    }
    
    if($edit){
        $query = "UPDATE tb_jugadores SET nombre='$txt_nombre',cedula='$txt_cedula',correo='$txt_correo',telefono='$txt_telefono',direccion='$txt_direccion',
                fecha_nacimiento='$fecha_nacimiento',cod_nacionalidad='$cmbNacionalidad',club_actual='$txt_club_actual',club_proveniente='$txt_club_ant',observacion='$txt_observacion',estado='A'
                WHERE cod_jugador = $cod_jugador";
        $resp = mysqli_query($con, $query);
        if($resp){
            $return['success'] = 1;
            $return['mensaje'] = "Editado correctamente";
            $return['id'] = $cod_jugador;
            $return['query'] = $query; 
            
            $query = "DELETE FROM tb_jugador_posicion WHERE cod_jugador = $cod_jugador";    
            $resp = mysqli_query($con,$query);
            for($i=0; $i<count($posicion); $i++){
                $pos = $posicion[$i];
                $query = "INSERT INTO tb_jugador_posicion(cod_jugador, posicion) VALUES($cod_jugador, '$pos')";    
                $resp = mysqli_query($con,$query);
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el jugador, por favor intentelo nuevamente";
        }
    }
    else{
        /*
        do{
            $alias = create_slug(sinTildes2($txt_nombre));
        }while($Clproductos->aliasDisponible($alias));*/

        //codigo momentaneo
        $alias = create_slug(sinTildes($txt_nombre));
        $a = $Clproductos->aliasDisponible($alias);
        if(!$Clproductos->aliasDisponible($alias)){
            $alias = "no-disponible";
        }
        $logo = "img_".datetime_format().".png"; 

        $Clproductos->alias = $alias;
        $Clproductos->nombre = $txt_nombre;
        $Clproductos->desc_corta = $txt_descripcion_corta;
        $Clproductos->desc_larga = $alias;
        $Clproductos->image_min = $alias;
        $Clproductos->image_max = $alias;
        $Clproductos->precio = $txt_precio;
        $Clproductos->cod_producto_padre = $cmb_categoria;
        if($Clproductos->crear()){
            //$cod_jugador = mysqli_insert_id($con);
            $return['success'] = 1;
            $return['mensaje'] = "Guardado correctamente";
            $return['respAlias'] = $a;
            //$return['id'] = $cod_jugador;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el producto, por favor intentelo mas tarde ";
        }

    }
}

if($metodo == "ERROR")
{
    $return['success'] = 0;
    $return['mensaje'] = $mensaje;
}


header('Content-Type: application/json');
echo json_encode($return);
?>