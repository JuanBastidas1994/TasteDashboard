<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_web_modulos.php";
require_once "../clases/cl_web_anuncios.php";
$ClWebModulos = new cl_web_modulos();
$ClWebAnuncios = new cl_web_anuncios();
$session = getSession();
$variaciones = null;

controller_create();

function lista(){
    global $ClWebAnuncios;
    global $session;
    if(!isset($_GET['cod_modulo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $htmlAgotados = "";
    $respAgotados = $ClWebAnuncios->listaByModulo($cod_modulo);
    if(!$respAgotados)
        $htmlAgotados = '<tr><td colspan="4">No hay registros</td></tr>';
    foreach ($respAgotados as $anuncios) {
        $imagen = $files.$anuncios['imagen'];
        $badge='primary';
        if($anuncios['estado'] == 'I')
            $badge='danger';
        $htmlAgotados .= '<tr data-id="'.$anuncios['cod_anuncio_detalle'].'" id="tr'.$anuncios['cod_anuncio_detalle'].'">
            <td class="text-center">
                <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
            </td>
            <td>'.$anuncios['titulo'].'</td>
            <td>'.$anuncios['subtitulo'].'</td>
            <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($anuncios['estado']).'</span></td>
            <td class="text-center">
                <ul class="table-controls">
                    <li><a href="javascript:void(0);" data-value="'.$anuncios['cod_anuncio_detalle'].'" class="bs-tooltip btnEditarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                    <li><a href="javascript:void(0);" data-value="'.$anuncios['cod_anuncio_detalle'].'"  class="bs-tooltip btnEliminarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                </ul>
            </td>
        </tr>';
    }
    
    $width = 500;
    $height = 500;
    $info= $ClWebAnuncios->listabyId($cod_modulo);
    if($info)
    {
        if($info['width'] != 0)
        {        
        $width=$info['width'];
        $height=$info['height'];
        }
    }

    /*OBTENER CATEGORIAS*/
    $listaCat = ''; 
    $galeria = $ClWebAnuncios->listaCategorias($cod_modulo);
    if($galeria){
        $arrayCats = [];
        foreach ($galeria as $gale){
            $cats = explode(",", $gale['categorias']);
            for($i=0; $i<count($cats); $i++){
                if(!in_array($cats[$i], $arrayCats)){
                    $arrayCats[] = $cats[$i];
                }
            }
        }                 
   
        for($i=0; $i<count($arrayCats); $i++){
            $listaCat.= '<option value="'.$arrayCats[$i].'">'.ucwords($arrayCats[$i]).'</option>';
        }
    }

    $return['categorias'] = $listaCat;
    $return['success'] = 1;
    $return['mensaje'] = "Info";
    $return['agotados'] = $htmlAgotados;
    $return['width'] = $width;
    $return['height'] = $height;
    
    return $return;
}

function actualizar(){
    global $ClWebAnuncios;
    global $session;
    if(!isset($_POST['cod_modulo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    for ($i=0; $i < count($productos); $i++) { 
        $ClWebAnuncios->actPosicion($productos[$i], $i+1);
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function crear(){
    global $ClWebAnuncios;
    global $session;
    /*if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }*/

    extract($_POST);
    
    $nameImg = 'anuncio_'.datetime_format().'.jpg';
    
    if(!isset($_POST['cmbAccion'])){
        $accion = "URL";
    }else{
        $accion = $cmbAccion;
        if($accion == "URL" || $accion == "FILTER"){
            $txt_url_boton = $txt_accion_desc;
        }else if($accion == "PRODUCTO"){
            $txt_url_boton = $cmbProductos;
        }else if($accion == "NOTICIA"){
            $txt_url_boton = $cmbNoticias;
        }else if($accion == "INFO"){
            $txt_url_boton = "";
        }
    }
    
    $ClWebAnuncios->titulo = $txt_titulo;
    $ClWebAnuncios->subtitulo = $txt_subtitulo;
    $ClWebAnuncios->descripcion = $txt_descripcion_corta;
    $ClWebAnuncios->text_boton = $txt_texto_boton;
    $ClWebAnuncios->url_boton = $txt_url_boton;
    $ClWebAnuncios->accion = $accion;
    $ClWebAnuncios->cod_cabecera = $_POST['cod_anuncio_cabecera'];
    $ClWebAnuncios->imagen = $nameImg;
    $ClWebAnuncios->categoriaAnuncio = strtolower(implode(",", $cmbCat));
    //$ClWebAnuncios->imagen = $nameImg;
    //$nameImg = $nombre_img;
    
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    $imagen = $files.$nameImg;
    
    $htmlNewtr = "";
    
    if(isset($_POST['chk_estado'])){
        $ClWebAnuncios->estado = 'A';
        $badge='primary';
        $text_estado = "Activo";
    }
    else{
        $ClWebAnuncios->estado = 'I';
        $badge='danger';
        $text_estado = "Inactivo";
    }
        
    $imagen = $files.$nameImg;
    
    if($id == ""){
        $contador = $ClWebAnuncios->contadorModulos($_POST['cod_anuncio_cabecera']);
        $return['contador'] = $contador;
        $posicion = $contador+1;
        
        $ClWebAnuncios->posicion = $posicion;
          
        if(!isset($_POST['cod_anuncio_detalle'])){
            $id=0;
            if($ClWebAnuncios->crear($id)){
                $return['id'] = $id;
                $return['img'] = $nameImg;
                $return['ruta_img'] = $imagen;
    
                /*SUBIR IMAGEN*/
                if($txt_crop != ""){
                    base64ToImage($txt_crop, $nameImg);
                }else{
                    $img1 = url_upload.'/assets/img/200x200.jpg';
                    $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                    copy($img1, $img2);
                }

                $htmlNewtr .= '<tr data-id="'.$id.'" id="tr'.$id.'">
                                    <td class="text-center">
                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                    </td>
                                    <td>'.$txt_titulo.'</td>
                                    <td>'.$txt_subtitulo.'</td>
                                    <td class="text-center"><span class="shadow-none badge badge-primary">Activo</span></td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li><a href="javascript:void(0);" data-value="'.$id.'" class="bs-tooltip btnEditarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                            <li><a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEliminarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>';
                
                $return['success'] = 1;
                $return['mensaje'] = "Promocion creada correctamente";
                $return['html'] = $htmlNewtr;
                
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al crear la promocion, por favor vuelva a intentarlo";
            }
        }
    }
    else{
        if($txt_crop != ""){
            base64ToImage($txt_crop, $nameImg);
            $data = $ClWebAnuncios->getDetalleAnuncio($id);
            $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
            $nameImgMax = getNameImagejpg($data['imagen'], $cambio);
            if(base64ToImage($txt_crop, $nameImgMax)){
                $ClWebAnuncios->setImage($nameImgMax, 'max', $id);
                if($cambio){
                    deleteFile($data['imagen']);
                }
            }
        }
        $imagen = $files.$nameImgMax;
        
        if($ClWebAnuncios->editar($id)){
            $htmlNewtr .= '<td class="text-center">
                                <span><img src="'.$imagen."?v=".fechaSignos().'" class="profile-img" alt="Imagen"></span>
                                </td>
                                <td>'.$txt_titulo.'</td>
                                <td>'.$txt_subtitulo.'</td>
                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.$text_estado.'</span></td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li><a href="javascript:void(0);" data-value="'.$id.'" class="bs-tooltip btnEditarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                        <li><a href="javascript:void(0);" data-value="'.$id.'"  class="bs-tooltip btnEliminarAnuncio" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                    </ul>
                            </td>';
           
            $return['success'] = 1;
            $return['mensaje'] = "Anuncio editado correctamente";
            $return['id'] = $id;
            $return['img'] = $nameImg;
            $return['html'] = $htmlNewtr;
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la promocion, por favor vuelva a intentarlo";
        }
    }
    return $return;
}

function cargar(){
    global $ClWebAnuncios;
    global $session;
     $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    
    extract($_GET);
    $row = $ClWebAnuncios->getDetalleAnuncio($cod_anuncio_detalle);
    if($row){
        $catsModulo = [];
        $listaCat = "";
        if($row['categorias'] <> "" && $row['categorias'] <> null){
            $catsModulo = explode(",", $row['categorias']);
            
            /*OBTENER CATEGORIAS*/
            $listaCat = ''; 
            $galeria = $ClWebAnuncios->listaCategorias($row['cod_anuncio_cabecera']);
            if($galeria){
                $arrayCats = [];
                foreach ($galeria as $gale){
                    $cats = explode(",", $gale['categorias']);
                    for($i=0; $i<count($cats); $i++){
                        if(!in_array($cats[$i], $arrayCats)){
                            $arrayCats[] = $cats[$i];
                        }
                    }
                }                 
        
                for($i=0; $i<count($arrayCats); $i++){
                    $selected = "";
                        if(in_array($arrayCats[$i], $catsModulo))
                            $selected = "selected";
                    $listaCat.= '<option value="'.$arrayCats[$i].'" '.$selected.'>'.ucwords($arrayCats[$i]).'</option>';
                }
            }
        }
        
        $return['categorias'] = $listaCat;
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['data'] = $row;
        $return['ruta_img'] = $files.$row['imagen'];
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    return $return;
}

function eliminar(){
    global $ClWebAnuncios;
    global $session;
    
    extract($_GET);
    
    if($ClWebAnuncios->eliminar($cod_anuncio_detalle)){
        $return['success'] = 1;
        $return['mensaje'] = "Anuncio eliminado";
        $return['id'] = $cod_anuncio_detalle;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar";
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

function setAgotado(){
    global $Clproductos;
    
    extract($_POST);

    if(!$Clproductos->getdisponibilidad($cod_producto, $cod_sucursal)){
        $val=null;
        $pro = $Clproductos->getArray($cod_producto, $val);
        if($pro){
            if(!$Clproductos->setDisponibilidad($cod_producto, $cod_sucursal, $pro['precio'], $pro['precio_anterior'], 'A')){
                $return['success'] = 0;
                $return['mensaje'] = "Este producto no esta asigando para esta sucursal, por favor consultar con el Super administrador";
                return $return;
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Este producto no esta asigando para esta sucursal, por favor consultar con el Super administrador";
            return $return;
        }
        
    }

    $fecha = fecha();
    $fecha_fin = strtotime('+'.$minutos.' minute',strtotime($fecha));
    $fecha_fin = date("Y-m-d H:i:s", $fecha_fin);

    $resp = $Clproductos->setAgotado($cod_producto, $cod_sucursal, $fecha, $fecha_fin);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Producto fuera de venta durante $minutos minutos";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al pausar la venta del producto, por favor vuelta a intentarlo";
    }
    return $return;
}


?>