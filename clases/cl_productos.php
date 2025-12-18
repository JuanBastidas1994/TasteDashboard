<?php

class cl_productos
{
		var $session;
		var $cod_producto, $cod_producto_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $fecha_create, $user_create, $estado, $codigo, $categorias, $open_detalle, $peso, $volumen, $sku,$cobra_iva,$is_combo, $facturar_sin_stock, $tiempo_preparacion;
		var $precio, $precio_no_tax, $iva_valor, $iva_porcentaje, $precio_anterior, $costo;
		var $nomOpcion, $precio_min, $precio_max, $isCheck, $isDatabase, $cod_producto_opcion, $nomDetalle, $precioDet, $aumentar_precio, $posicion;
		var $cod_producto_ingrediente, $cod_ingrediente, $valor, $cod_producto_opcion_ingrediente;

		public function __construct($pcod_producto=null)
		{
			if($pcod_producto != null)
				$this->cod_producto = $pcod_producto;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function lista(){
			$query = "SELECT p.*
				FROM tb_productos p
				WHERE p.estado IN('A','I') 
				AND p.cod_producto_padre = 0
				AND p.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaActivos(){
			$query = "SELECT p.*
				FROM tb_productos p
				WHERE p.estado = 'A'
				AND p.cod_producto_padre = 0
				AND p.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaOld(){
			$query = "SELECT p.*
				FROM tb_productos p
				WHERE p.estado IN('A','I') 
				AND p.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaByCategoria($cod_categoria){		
			$query = "SELECT p.*
					FROM tb_productos p, tb_productos_categorias pc
					WHERE p.cod_producto = pc.cod_producto
					AND p.estado IN ('A','I')
					AND p.cod_producto_padre = 0
					AND pc.cod_categoria = $cod_categoria
					AND p.cod_empresa =".$this->session['cod_empresa']."
					ORDER BY p.posicion";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaBySucursal($cod_sucursal){
			$query = "SELECT * FROM vw_producto_sucursal WHERE estado ='A' AND cod_sucursal IN(0,$cod_sucursal) AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaProductBySucursal($cod_sucursal){
		    if($cod_sucursal == 0){
		        $query = "SELECT * FROM tb_productos WHERE estado ='A' AND cod_empresa = ".$this->session['cod_empresa'];   
		    }
		    else{
		        $query = "SELECT * FROM tb_productos p left join tb_productos_sucursal ps on p.cod_producto = ps.cod_producto where p.estado = 'A' and p.cod_empresa=".$this->session['cod_empresa']." and ps.cod_sucursal =".$cod_sucursal;
		    }
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaNoTax(){
			$query = "SELECT p.*
				FROM tb_productos p
				WHERE p.estado IN('A','I') 
				AND p.cobra_iva = 0
				AND p.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function get($cod_producto)
		{
			$query = "SELECT * FROM tb_productos where cod_producto = $cod_producto AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		
		public function getBySku($sku){
		    $empresa = $this->session['cod_empresa'];
		    $query = "SELECT * FROM tb_productos where cod_empresa = $empresa AND sku = '$sku' AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		
		public function GetProductosbyEmpresa()
		{
		    $empresa = $this->session['cod_empresa'];
		    $query = "select * from tb_productos where cod_empresa = ".$empresa." and estado in ('A','I')";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function GetProductosbyEmpresaOrder()
		{
		    $empresa = $this->session['cod_empresa'];
		    $query = "select p.*, c.* 
                    from tb_productos p, tb_productos_categorias pc, tb_categorias c
                    where p.cod_producto = pc.cod_producto
                    AND pc.cod_categoria = c.cod_categoria
                    AND p.cod_empresa = $empresa 
                    AND p.estado in ('A','I')
                    ORDER BY c.categoria ASC";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function GetProductosbyEmpresaFormat($empresa)
		{
		    $query = "select * from tb_productos where cod_empresa = ".$empresa." and estado in ('A','I')";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}

		public function aliasDisponible($alias){
			$empresa = $this->session['cod_empresa'];
			$query = "SELECT * FROM tb_productos WHERE alias = '$alias' AND estado IN ('A','I') AND cod_empresa = $empresa";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}

		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_productos(cod_producto_padre, cod_empresa, alias, nombre, desc_corta, desc_larga, image_min, image_max, fecha_create, user_create, estado, precio, precio_no_tax, iva_valor, iva_porcentaje, codigo, precio_anterior, costo, open_detalle, peso, volumen ,sku,is_combo,cobra_iva, noStock, tiempo_preparacion) ";
        	$query.= "VALUES($this->cod_producto_padre, $empresa, '$this->alias', '$this->nombre', '$this->desc_corta', '$this->desc_larga', '$this->image_min', '$this->image_max', NOW(), $usuario, 'A', $this->precio, $this->precio_no_tax, $this->iva_valor, $this->iva_porcentaje, '$this->codigo', $this->precio_anterior, $this->costo, $this->open_detalle, '$this->peso', '$this->volumen', '$this->sku', $this->is_combo, $this->cobra_iva, $this->facturar_sin_stock, $this->tiempo_preparacion)";

        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		$this->set_categorias($id);
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function crear_importados(&$id){
		    $usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$fecha = fecha();
            $query = "INSERT INTO tb_productos(cod_producto_padre, nombre, alias, desc_corta, precio_no_tax, precio, iva_valor, iva_porcentaje, cobra_iva, open_detalle, estado, user_create, cod_empresa, image_min, image_max, peso, sku, fecha_create) ";
            $query.= "VALUES('0', '$this->nombre', '$this->alias', '$this->desc_corta', '$this->precio_no_tax', '$this->precio', '$this->iva_valor', '$this->iva_porcentaje', '$this->cobra_iva', '1', 'A', '$usuario', '$empresa', '$this->image_min', '$this->image_max', '$this->peso', '$this->sku', '$fecha')";
            if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		$this->set_categorias($id);
        		return true;
        	}else{
        		return false;
        	}
        }

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
        	$query= "UPDATE tb_productos SET cod_producto_padre=$this->cod_producto_padre, nombre='$this->nombre', desc_corta='$this->desc_corta', desc_larga='$this->desc_larga', estado='$this->estado', precio=$this->precio, precio_no_tax=$this->precio_no_tax, iva_valor=$this->iva_valor, iva_porcentaje=$this->iva_porcentaje, codigo='$this->codigo',precio_anterior=$this->precio_anterior, costo= $this->costo , open_detalle= $this->open_detalle, peso = '$this->peso', volumen = '$this->volumen', sku='$this->sku',is_combo='$this->is_combo',cobra_iva='$this->cobra_iva', noStock='$this->facturar_sin_stock', tiempo_preparacion=$this->tiempo_preparacion, fecha_modificacion=NOW() WHERE cod_producto = $this->cod_producto";
        	if(Conexion::ejecutar($query,NULL)){
        		$this->set_categorias($this->cod_producto);
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function editar_importados(){
		    $usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
        	$query= "UPDATE tb_productos SET precio=$this->precio, precio_no_tax=$this->precio_no_tax, iva_valor=$this->iva_valor, iva_porcentaje=$this->iva_porcentaje, cobra_iva='$this->cobra_iva', peso = '$this->peso',fecha_modificacion=NOW() WHERE cod_producto = $this->cod_producto AND cod_empresa = $empresa";
        	if(Conexion::ejecutar($query,NULL)){
        		$this->set_categorias($this->cod_producto);
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function setImage($name, $scale, $cod_producto){
		    $option = "image_max='$name'";
		    if($scale=="min")
		        $option = "image_min='$name'";
		    $query = "UPDATE tb_productos SET $option WHERE cod_producto = $cod_producto";
		    return Conexion::ejecutar($query,NULL);
		}

		public function setImages($image_max, $image_min, $cod_producto){
		    $query = "UPDATE tb_productos 
						SET image_max='$image_max', image_min='$image_min' 
						WHERE cod_producto = $cod_producto";
		    return Conexion::ejecutar($query,NULL);
		}
		
		public function set_categorias($cod_producto){
			$query = "DELETE FROM tb_productos_categorias WHERE cod_producto = $cod_producto";
			Conexion::ejecutar($query,NULL);

			$categorias = $this->categorias;
			foreach ($categorias as $cat) {
				$query = "INSERT INTO tb_productos_categorias(cod_producto, cod_categoria) VALUES($cod_producto, $cat)";
				if(!Conexion::ejecutar($query,NULL)){
	        		return false;
	        	}
			}
		}

		public function get_categorias($cod_producto = null){
			if($cod_producto == null){
				$cod_producto = $this->cod_producto;
			}
			$return = null;
			$query = "SELECT cod_categoria FROM tb_productos_categorias WHERE cod_producto = $cod_producto";
            $resp = Conexion::buscarVariosRegistro($query);
            foreach ($resp as $data) {
            	$return[] = $data['cod_categoria'];
            }
            return $return;
		}

		public function set_estado($cod_producto, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_productos SET estado='$estado' WHERE cod_producto = $cod_producto";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function remove_categoria($cod_producto, $cod_categoria){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "DELETE FROM tb_productos_categorias WHERE cod_producto = $cod_producto AND cod_categoria = $cod_categoria";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getArray($cod_producto, &$array)
		{
			$query = "SELECT * FROM tb_productos where cod_producto = $cod_producto AND estado IN('A','I')";
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

		public function getArrayByAlias($alias, &$array)
		{
			$empresa = $this->cod_empresa;
			$query = "SELECT * FROM tb_productos WHERE alias = '$alias' AND cod_empresa = $empresa AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$array = $row;
				return true;
			}
			else
			{
				return false;
			}
		}

		/*VARIANTES DE LOS PRODUCTOS*/
		public function set_variantes($cod_producto,$atributos){
			$query = "DELETE FROM tb_productos_variante WHERE cod_producto = $cod_producto";
			Conexion::ejecutar($query,NULL);

			foreach ($atributos as $value) {
				$query = "INSERT INTO tb_productos_variante(cod_producto, atributo) VALUES($cod_producto, '$value')";
				if(!Conexion::ejecutar($query,NULL)){
	        		return false;
	        	}
			}
			return true;
		}
		
		public function set_variante_caracteristica($cod_producto, $detalles){
		    $query = "DELETE FROM tb_variante_caracteristica WHERE cod_producto = $cod_producto";
			Conexion::ejecutar($query,NULL);

			foreach ($detalles as $value) {
				$query = "INSERT INTO tb_variante_caracteristica(cod_producto, cod_caracteristica_detalle) VALUES($cod_producto, $value)";
				if(!Conexion::ejecutar($query,NULL)){
	        		return false;
	        	}
			}
			return true;
		}

		public function get_atributos_variante($cod_producto){
			$query = "SELECT pc.detalle
					FROM tb_variante_caracteristica vc, tb_producto_caracteristica_detalle pc
					WHERE vc.cod_caracteristica_detalle = pc.cod_producto_caracteristica_detalle
					AND vc.cod_producto = $cod_producto";
			return Conexion::buscarVariosRegistro($query);
		}

		public function lista_variantes($cod_producto){
			$lista = null;
			$query = "SELECT * FROM tb_productos p WHERE p.cod_producto_padre = $cod_producto AND estado IN('A','I')";
            $resp = Conexion::buscarVariosRegistro($query);
            foreach ($resp as $item) {
            	$cod_variante = $item['cod_producto'];
            	$query = "SELECT * FROM tb_productos_variante WHERE cod_producto = $cod_variante";
            	$variantes = Conexion::buscarVariosRegistro($query);
            	$atributos = null;
            	foreach ($variantes as $aux) {
            		$atributos[] = $aux['atributo'];
            	}
            	$item['atributos'] = $atributos;
            	$lista[] = $item;
            }
            return $lista;
		}


		/*IMAGENES DE LOS PRODUCTOS*/
		public function lista_imagenes($cod_producto){
			$query = "SELECT * FROM tb_productos_imagenes WHERE cod_producto = $cod_producto";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function add_img_product($cod_producto,$nombre,&$id){
			$query = "INSERT INTO tb_productos_imagenes(cod_producto, nombre_img, posicion) ";
        	$query.= "VALUES($cod_producto, '$nombre', 1)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function delete_imagen($cod_imagen){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;

			$query = "SELECT * FROM tb_productos_imagenes WHERE cod_imagen = $cod_imagen";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$query = "DELETE FROM tb_productos_imagenes WHERE cod_imagen = $cod_imagen";
	        	if(Conexion::ejecutar($query,NULL)){
					$files = url_upload.'/assets/empresas/'.$this->session['alias'].'/';
	        		$urlImg = $files.$row['nombre_img'];
	        		unlink($urlImg);
	        		return true;
	        	}else{
	        		return false;
	        	}
			}else
				return false;
		}


		/*OPCIONES DE LOS PRODUCTOS*/
		public function opciones($cod_producto){
			$query = "SELECT * FROM tb_productos_opciones WHERE cod_producto = $cod_producto AND productos <> '' order by posicion ASC";
			$row = Conexion::buscarVariosRegistro($query);
			if(count($row)>0)
			{
				for ($x=0; $x < count($row); $x++){
					$data = json_decode($row[$x]['productos']);
					if($row[$x]['isDatabase'] == 1){
					    for($i=0; $i<count($data); $i++){
					        $data[$i] = $this->getNombreProducto($data[$i]);
					    }
					}
					$row[$x]['items'] = $data;
				}
				
			}
			return $row;
		}
		
		/*--NUEVO--*/
		public function getProductoOpciones($cod_productoOpcion){
			$query = "SELECT * FROM tb_productos_opciones WHERE cod_producto_opcion = $cod_productoOpcion";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		/*--NUEVO--*/
		
		public function detalles($cod_opcion,$isDatabase){
		    if ($isDatabase==0)
		    {
		        $query = "SELECT * FROM tb_productos_opciones_detalle WHERE cod_producto_opcion = $cod_opcion order by posicion ASC";    
		        $row = Conexion::buscarVariosRegistro($query);
    			foreach ($row as $i) {
    			    $return[] = $i['item'];
    			}
		    }
		    else
		    {
		        $query = "SELECT * FROM tb_productos_opciones_detalle pd,tb_productos p WHERE p.cod_producto= pd.item and pd.cod_producto_opcion = $cod_opcion order by pd.posicion ASC";    
		        $row = Conexion::buscarVariosRegistro($query);
    			foreach ($row as $i) {
    			    $return[] = $i['nombre'];
    			}
		    }
			
			return $return;
		}

		public function crear_opcion($cod_producto, $titulo, $cantidad, $cantidad_max, $productos, $tipo_opcion, $cmb_isCheck, &$id){
			$json = json_encode($productos);
			$query = "INSERT INTO tb_productos_opciones(cod_producto, titulo, cantidad_min, cantidad, productos, isDatabase, isCheck) ";
        	$query.= "VALUES($cod_producto, '$titulo', $cantidad, $cantidad_max, '$json', $tipo_opcion, $cmb_isCheck)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function crear_opcion_combo($cod_producto, $cod_productoHijo, $cantidad,&$id){
			$query = "INSERT INTO tb_productos_detalle(cod_producto_padre, cod_producto_hijo, cantidad) ";
        	$query.= "VALUES($cod_producto, $cod_productoHijo, $cantidad)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function get_Combo($cod_producto){
		    $return= null;
		    $query = "select * from tb_productos_detalle d, tb_productos p where p.cod_producto=d.cod_producto_hijo and d.cod_producto_padre = $cod_producto";
			 $row = Conexion::buscarVariosRegistro($query);
			foreach ($row as $data) {
            	$return[] = $data['cod_producto_hijo'];
            }
            return $return;
        }
        
        public function lista_Combo($cod_producto){
		    $query = "select * from tb_productos_detalle d, tb_productos p where p.cod_producto=d.cod_producto_hijo and d.cod_producto_padre = $cod_producto";
			$row = Conexion::buscarVariosRegistro($query);
            return $row;
        }

		public function delete_opcion($cod_opcion){
			$query = "DELETE FROM tb_productos_opciones WHERE cod_producto_opcion = $cod_opcion";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function delete_Combo($cod_producto){
			$query = "DELETE FROM tb_productos_detalle WHERE cod_producto_padre = $cod_producto";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function delete_opcionCombo($cod_opcion){
			$query = "DELETE FROM tb_productos_detalle WHERE cod_producto_detalle = $cod_opcion";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

        public function select_opciones($id){
            $query = "SELECT po.*, pod.cod_producto_opciones_detalle,if(po.isDatabase=0,pod.item,(select p.nombre from tb_productos p where p.cod_producto = pod.item))as item,pod.item as itemPrincipal, pod.aumentar_precio, pod.precio, pod.posicion
                        FROM tb_productos_opciones po, tb_productos_opciones_detalle pod
                        WHERE po.cod_producto_opcion = pod.cod_producto_opcion
                        AND po.cod_producto_opcion = $id
                        ORDER BY pod.posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            $x =0;
            foreach ($resp as $r) {
                $resp[$x]['titulo']=html_entity_decode($r['titulo']);
                $x++;
            }
            
			return $resp;
        }
        
        public function editar_opciones($id_det_cab, $txt_opcion_titulo, $txt_opciones_cantidad, $txt_opciones_cantidad_max, $tipo_opcion, $cmb_isCheck){
           // $cmb_productos = array_map('htmlentities', $cmb_productos);
		   // $txt_opcion_titulo = htmlentities($txt_opcion_titulo);
		//	$json = json_encode($cmb_productos);
            //$query = "UPDATE tb_productos_opciones SET titulo = '$txt_opcion_titulo', cantidad_min = $txt_opciones_cantidad, cantidad = $txt_opciones_cantidad_max, productos = '$json', isDatabase = $tipo_opcion, isCheck = $cmb_isCheck WHERE cod_producto_opcion = $id_det_cab";
            $query = "UPDATE tb_productos_opciones SET titulo = '$txt_opcion_titulo', cantidad_min = $txt_opciones_cantidad, cantidad = $txt_opciones_cantidad_max, isDatabase = $tipo_opcion, isCheck = $cmb_isCheck WHERE cod_producto_opcion = $id_det_cab";
            if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        	//return $query;
        }
        
        public function crear_opcion_detalle($id, $txt_nomItemDet, $aumentarPrecio, $txt_precio, $posicion){
            $query = "INSERT INTO tb_productos_opciones_detalle(cod_producto_opcion, item, aumentar_precio, precio, posicion) VALUES($id, '$txt_nomItemDet', $aumentarPrecio, '$txt_precio', $posicion)";
            if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        }
        
        public function select_opcion_detalle($id){
            $query = "SELECT * FROM tb_productos_opciones_detalle WHERE cod_producto_opciones_detalle = $id";
            $row = Conexion::buscarRegistro($query);
			if($row)
			    return true;
			else
			    return false;
        }
        
        public function editar_opcion_detalle($id, $txt_nomItemDet, $aumentarPrecio, $txt_precio, $posicion){
            $query = "UPDATE tb_productos_opciones_detalle SET item = '$txt_nomItemDet', aumentar_precio = $aumentarPrecio, precio = $txt_precio, posicion = $posicion WHERE cod_producto_opciones_detalle = $id";
            if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        	//return $query;
        }
        
        public function delete_opciones_detalle($cod_opcion){
            $query = "DELETE FROM tb_productos_opciones_detalle WHERE cod_producto_opcion = $cod_opcion";
            if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        }
        
        public function delete_opcion_detalle($cod_opcion_detalle){
            $query = "DELETE FROM tb_productos_opciones_detalle WHERE cod_producto_opciones_detalle = $cod_opcion_detalle";
            if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        }

		public function getOpcionCabecera($id){
            $query = "SELECT * 
						FROM tb_productos_opciones_detalle 
						WHERE cod_producto_opciones_detalle = $id";
            $row = Conexion::buscarRegistro($query);
			if($row){
				$query = "SELECT COUNT(*) as cant_detalle
							FROM tb_productos_opciones_detalle
							WHERE cod_producto_opcion = ".$row['cod_producto_opcion'];
				return Conexion::buscarRegistro($query);
			}
        }
        
		//DISPONIBILIDAD DE LOS PRODUCTOS
		public function getdisponibilidad($cod_producto, $cod_sucursal){
			$query = "SELECT * FROM tb_productos_sucursal WHERE cod_producto = $cod_producto AND cod_sucursal = $cod_sucursal";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}
		
		public function getdisponibilidadByproduct($cod_producto){
			$query = "SELECT * FROM tb_productos_sucursal WHERE cod_producto = $cod_producto AND estado = 'A' ";
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}

		public function setDisponibilidad($cod_producto, $cod_sucursal, $precio, $precio_anterior, $estado,$precioReplace){
		    
			$precio = floatval($precio);
    		$precio_no_tax = number_format(($precio / 1.12),2);
    		$iva_valor = $precio - $precio_no_tax;
			if($this->getdisponibilidad($cod_producto, $cod_sucursal)){
			    if($precioReplace == 1){
				$query = "UPDATE tb_productos_sucursal SET precio=$precio, precio_anterior=$precio_anterior, precio_no_tax=$precio_no_tax, iva_valor=$iva_valor, estado='$estado',replacePrice=$precioReplace WHERE cod_producto = $cod_producto AND cod_sucursal = $cod_sucursal";
			    }
			    else
			    {
                $query = "UPDATE tb_productos_sucursal SET precio=NULL, precio_anterior=NULL, precio_no_tax=NULL, iva_valor=NULL, estado='$estado',replacePrice=$precioReplace WHERE cod_producto = $cod_producto AND cod_sucursal = $cod_sucursal";			        
			    }
			}else{
				if($precioReplace == 1){
				$query = "INSERT INTO tb_productos_sucursal(cod_producto, cod_sucursal, precio, precio_anterior, precio_no_tax, iva_valor, estado,replacePrice) 
						VALUES($cod_producto, $cod_sucursal, $precio, $precio_anterior, $precio_no_tax, $iva_valor, '$estado',$precioReplace)";
				}else
			    {
				$query = "INSERT INTO tb_productos_sucursal(cod_producto, cod_sucursal, precio, precio_anterior, precio_no_tax, iva_valor, estado,replacePrice) 
						VALUES($cod_producto, $cod_sucursal, NULL, NULL, NULL, NULL, '$estado',$precioReplace)";
			    }		
					
			}
			if(Conexion::ejecutar($query,NULL)){
        		return true;
        		
        	}else{
        		return false;
        	}
        	
		}
		
		public function setProductSucursal($cod_producto,$cod_sucursal)
		{
		    $query = "INSERT INTO tb_productos_sucursal(cod_producto, cod_sucursal, precio, precio_anterior, precio_no_tax, iva_valor, estado,replacePrice) 
						VALUES($cod_producto, $cod_sucursal, NULL, NULL, NULL, NULL, 'A',0)";
			if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}			
		}

		public function setAgotado($cod_producto, $cod_sucursal, $fecha_inicio, $fecha_fin){
			$query = "UPDATE tb_productos_sucursal SET agotado_inicio='$fecha_inicio', agotado_fin = '$fecha_fin'
					WHERE cod_producto = $cod_producto AND cod_sucursal = $cod_sucursal";
			if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function addAgotadoHistorial($cod_producto, $cod_sucursal, $cod_usuario, $estado, $minutos=0, $fecha_inicio='', $fecha_fin=''){
		    $query = "INSERT INTO tb_producto_agotado_historial (cod_producto, cod_sucursal, cod_usuario, estado, minutos, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            return Conexion::ejecutar($query, [
                $cod_producto, $cod_sucursal, $cod_usuario, $estado, $minutos, $fecha_inicio, $fecha_fin
            ]);
		}

		public function lista_agotados($cod_sucursal, $fecha){
			$query = "SELECT p.cod_producto, p.nombre, p.precio, p.image_min, p.image_max, s.agotado_inicio, s.agotado_fin, TIMESTAMPDIFF(MINUTE,'$fecha',agotado_fin) as tiempo_restante
						FROM tb_productos p, tb_productos_sucursal s
						WHERE p.cod_producto = s.cod_producto
						AND s.cod_sucursal = $cod_sucursal
						AND s.agotado_inicio <= '$fecha'
						AND s.agotado_fin >= '$fecha'
						AND p.estado = 'A'
						AND s.estado = 'A'";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;			
		}
		
		/*INICIO NUEVO MODULO*/
		public function lista_productos_extras_detalle($cod_producto_extra){
			$query= "SELECT ped.*, p.nombre
                    FROM tb_producto_extras_detalle ped, tb_productos p
                    WHERE ped.cod_producto=p.cod_producto 
                     AND p.estado IN ('A','I')
                    AND ped.cod_producto_extra=".$cod_producto_extra;
		    $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function verificar_registro($cod_producto,$cod_producto_extra){
			$query= "SELECT * FROM tb_producto_extras_detalle 
					 WHERE cod_producto_extra=$cod_producto_extra
					 AND cod_producto=".$cod_producto;
					// echo $query;
		    $resp = Conexion::buscarRegistro($query);
            return $resp;
		}

		public function delete_productos_extra($codigo){
			$query = "DELETE FROM  tb_producto_extras_detalle WHERE cod_producto_extra  = $codigo";
        	if(Conexion::ejecutar($query,NULL))
        	{
	        	return true;
        	}else{
        		return false;
        	}
		}

		public function insert_item_extra($cod_producto_extra,$cod_producto){
			$queryInsert = "INSERT INTO tb_producto_extras_detalle(cod_producto_extra,cod_producto) VALUES($cod_producto_extra, $cod_producto)";
			if(Conexion::ejecutar($queryInsert,NULL)){
				return true;
        	}
        	else
        	{
        		return false;
        	}
		       
		}

		public function insert_productos_extra($categoria,$producto_padre){
			$query = "SELECT p.*
					FROM tb_productos p, tb_productos_categorias pc
					WHERE p.cod_producto = pc.cod_producto
					AND p.estado IN ('A','I')
					AND pc.cod_categoria = $categoria
					AND p.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            if($resp)
            {
              foreach ($resp as $pro) {
            	$queryBuscar= "SELECT * FROM tb_producto_extras_detalle WHERE cod_producto_extra = $producto_padre AND cod_producto=".$pro['cod_producto'];
				$row = Conexion::buscarRegistro($queryBuscar);
				if(!$row)
				{
					$queryInsert = "INSERT INTO tb_producto_extras_detalle(cod_producto_extra,cod_producto) VALUES($producto_padre, ".$pro['cod_producto'].")";
					if(!Conexion::ejecutar($queryInsert,NULL)){
		        		return false;
		        	}
		        }
		      }
            	return true;
            }
            else{return false;}
		}

		public function verificar_extra($cod_producto){
			$query= "SELECT * FROM tb_producto_extras
					 WHERE titulo='Extra'
					 AND cod_producto=".$cod_producto;
					 //echo $query;
		    $resp = Conexion::buscarRegistro($query);
            return $resp;
		}

		public function lista_productos_extras($cod_producto){
			$query= "SELECT * FROM tb_producto_extras
					 WHERE cod_producto=".$cod_producto."
					 ORDER BY posicion";
					 //echo $query;
		    $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function insert_todos_extra($producto_padre){
			$query = "SELECT DISTINCT p.* FROM tb_productos_categorias pc, tb_productos p
					WHERE pc.cod_producto=p.cod_producto
					AND pc.cod_categoria BETWEEN 44 AND 48";
            $resp = Conexion::buscarVariosRegistro($query);
            if($resp)
            {
              foreach ($resp as $pro) {
            	$queryBuscar= "SELECT * FROM tb_producto_extras_detalle WHERE cod_producto_extra = $producto_padre AND cod_producto=".$pro['cod_producto'];
				$row = Conexion::buscarRegistro($queryBuscar);
				if(!$row)
				{
					$queryInsert = "INSERT INTO tb_producto_extras_detalle(cod_producto_extra,cod_producto) VALUES($producto_padre, ".$pro['cod_producto'].")";
					if(!Conexion::ejecutar($queryInsert,NULL)){
		        		return false;
		        	}
		        }
		      }
            	return true;
            }
            else{return false;}
		}

		public function moverCategorias( $nombres,$cod_producto, $posicion){
		   
		 	$query = "UPDATE  tb_producto_extras SET posicion=$posicion WHERE titulo ='".$nombres."' AND cod_producto=".$cod_producto;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}
		
		public function coincidencia_extras($cod_producto,$nombre){
			$query = "SELECT *FROM tb_producto_extras
					WHERE titulo='$nombre'
					AND cod_producto=".$cod_producto;
            $resp = Conexion::buscarRegistro($query);
            //echo $query;
            return $resp;
		}
		
		public function crear_extra($cod_producto,$nombre, $cantidad,&$id){
        	  $query= "INSERT INTO tb_producto_extras(cod_producto,titulo,cantidad, posicion) VALUES($cod_producto, '$nombre',$cantidad,1000)";
				if(Conexion::ejecutar($query,NULL))
				{
				 $id = Conexion::lastId();
        		//$this->set_categorias_extras($id,$productos);
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function delete_category_extra($codigo){
			$query = "DELETE FROM  tb_producto_extras WHERE cod_producto_extra  = $codigo";
        	if(Conexion::ejecutar($query,NULL))
        	{
	        		$queryuno = "DELETE FROM  tb_producto_extras_detalle WHERE cod_producto_extra  = $codigo";
	        	    if(Conexion::ejecutar($queryuno,NULL)){
	        		return true;
	        		}
	        		else{return false;}
        	}else{
        		return false;
        	}
		}
		
		public function delete_item($codigo){
			$query = "DELETE FROM  tb_producto_extras_detalle WHERE cod_producto_extra_detalle = $codigo";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function lista_extras(){
			$query = "SELECT DISTINCT p.* FROM tb_productos_categorias pc, tb_productos p
					WHERE pc.cod_producto=p.cod_producto
					AND pc.cod_categoria BETWEEN 44 AND 48";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function update_cantidad($codigo,$cantidad){
			$query = "UPDATE  tb_producto_extras SET cantidad=$cantidad WHERE cod_producto_extra = $codigo";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		/*FIN NUEVO MODULO*/
		
		public function getNombreProducto($cod_producto){
		    $query = "SELECT nombre FROM tb_productos WHERE cod_producto = $cod_producto";
		    $resp = Conexion::buscarRegistro($query);
            //echo $query;
            return $resp['nombre'];
		}
		
		public function insert_opc(&$id){
		    $query = "INSERT INTO tb_productos_opciones(cod_producto, titulo, cantidad, cantidad_min, isCheck, isDatabase) VALUES('$this->cod_producto', '$this->nomOpcion', '$this->precio_min', '$this->precio_max', '$this->isCheck', '$this->isDatabase')";
		    if(Conexion::ejecutar($query,NULL)){
		        $id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function insert_items(){
		    $query = "INSERT INTO tb_productos_opciones_detalle(cod_producto_opcion, item, aumentar_precio, precio) VALUES('$this->cod_producto_opcion', '$this->nomDetalle', '$this->aumentar_precio', '$this->precioDet')";
		    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function actPosicionOpciones($cod_opcion, $posicion){
    	    $query = "UPDATE tb_productos_opciones SET posicion = $posicion WHERE cod_producto_opcion = $cod_opcion";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function actPosicionDetalles($cod_detalle, $posicion){
    	    $query = "UPDATE tb_productos_opciones_detalle SET posicion = $posicion WHERE cod_producto_opciones_detalle = $cod_detalle";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function impotarCategoriaAProducto($cod_producto, $cod_categoria){
	        $query = "INSERT INTO tb_productos_categorias(cod_producto, cod_categoria) VALUES($cod_producto, $cod_categoria)";
				if(Conexion::ejecutar($query,NULL))
	        		return true;
	        	return false;
	    }

		/* SERVICIOS */
		public function lista_servicios(){
			$query = "SELECT * 
						FROM tb_productos 
						WHERE bien = 'Servicio' 
						AND estado IN('A') 
						AND cod_empresa = ".$this->session['cod_empresa'];
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}
		
		/*FACTURACION*/
		public function getIdFactElect($cod_producto, $cod_sistema_facturacion){
		    $query = "SELECT * FROM tb_productos_facturacion WHERE cod_producto = $cod_producto AND cod_sistema_facturacion = $cod_sistema_facturacion";
		    return Conexion::buscarRegistro($query);
		} 

		public function getEtiquetas($cod_producto){
			$query = "SELECT * FROM tb_productos_tags WHERE cod_producto = $cod_producto";
			return Conexion::buscarVariosRegistro($query);
		}
		
		public function setEtiquetas($cod_producto, $etiqueta){
			$query = "INSERT INTO tb_productos_tags(cod_producto, tag)
						VALUES($cod_producto, '$etiqueta')";
			return Conexion::ejecutar($query, null);
		}

		public function delEtiquetas($cod_producto){
			$query = "DELETE FROM tb_productos_tags WHERE cod_producto = $cod_producto";
			return Conexion::ejecutar($query, null);
		}
		
		public function updateEmpaque($cod_producto, $unidades, $alto){
			$query = "INSERT INTO tb_producto_empaque_detalle(cod_producto, unidades, alto)
						VALUES($cod_producto, '$unidades', '$alto')";
			return Conexion::ejecutar($query, null);
		}
		
		public function deleteEmpaque($cod_producto){
			$query = "DELETE FROM tb_producto_empaque_detalle WHERE cod_producto = $cod_producto";
			return Conexion::ejecutar($query, null);
		}
		
		
		/*CARACTERISTICAS*/
		public function numCaracteristicas(){
		    
		}
		
		public function getCaracteristicas($cod_producto){
		    $query = "SELECT * FROM tb_producto_caracteristica WHERE cod_producto = $cod_producto";
			$resp = Conexion::buscarVariosRegistro($query);
			foreach($resp as $key => $item){
			    $resp[$key]['detalle'] = $this->getCaracteristicasDetalle($item['cod_producto_caracteristica']);
			}
			return $resp;
		}
		
		public function getCaracteristicasDetalle($cod_caracteristica){
		    $query = "SELECT * FROM tb_producto_caracteristica_detalle WHERE cod_producto_caracteristica = $cod_caracteristica";
			return Conexion::buscarVariosRegistro($query);
		}
		
		public function setCaracteristica($cod_producto, $caracteristica, $tipo){
			$query = "INSERT INTO tb_producto_caracteristica(cod_producto, caracteristica, tipo, posicion, estado)
						VALUES($cod_producto, '$caracteristica', '$tipo', 1, 'A')";
			if(Conexion::ejecutar($query, null)){
			    return Conexion::lastId();
			}else
			    return false;
		}
		
		public function setCaracteristicaDetalle($cod_caracteristica, $detalle, $detalle2){
			$query = "INSERT INTO tb_producto_caracteristica_detalle(cod_producto_caracteristica, detalle, detalle2, posicion, estado)
						VALUES($cod_caracteristica, '$detalle', '$detalle2', 1, 'A')";
			return Conexion::ejecutar($query, null);
		}

		public function cambiarVarianteVisualizacion($tipo, $cod_producto){
			$query = "UPDATE tb_productos
						SET variante_visualizacion = '$tipo'
						WHERE cod_producto = $cod_producto";
			return Conexion::ejecutar($query, null);
		}

		public function getProductosAsignadosSucursal($cod_empresa, $cod_sucursal){
			$query = "SELECT *
						FROM tb_productos p
						WHERE p.cod_empresa = $cod_empresa
						AND p.cod_producto IN(SELECT cod_producto FROM tb_productos_sucursal WHERE cod_sucursal = $cod_sucursal)";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getProductosNoAsignadosSucursal($cod_empresa, $cod_sucursal){
			$query = "SELECT *
						FROM tb_productos p
						WHERE p.cod_empresa = $cod_empresa
						AND p.estado IN ('A','I')
						AND p.cod_producto NOT IN(SELECT cod_producto FROM tb_productos_sucursal WHERE cod_sucursal = $cod_sucursal)";
			return Conexion::buscarVariosRegistro($query);
		}
		
		public function getDays($cod_producto){
		    $days = null;
		    $query = "SELECT dia FROM tb_productos_dias WHERE cod_producto = $cod_producto";
		    $resp = Conexion::buscarVariosRegistro($query);
		    if($resp){
		        $x=0;
		        foreach($resp as $key => $day){
		            $days[$x] = intval($day['dia']);
		            $x++;
		        }
		        return $days;      
		    }else{
		        return false;
		    }
		}
		
		public function deleteDays($cod_producto){
		    $query = "DELETE FROM tb_productos_dias WHERE cod_producto = $cod_producto";
			return Conexion::ejecutar($query, null);
		}
		
		public function setDays($cod_producto, $days){
		    foreach($days as $day){
                $query = "INSERT INTO tb_productos_dias(cod_producto, dia)
						VALUES($cod_producto, $day)";
			    Conexion::ejecutar($query, null);
            }
		}

		// INGREDIENTES
		public function getUnidadesMedidas() {
			$query = "SELECT *
						FROM tb_unidades_medidas";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getIngredientes() {
			$query = "SELECT *
						FROM tb_ingredientes
						WHERE cod_empresa = $this->cod_empresa
						AND estado = 'A'";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getProductoIngredientes($cod_producto) {
			$query = "SELECT pi.cod_producto_ingrediente, i.cod_ingrediente, i.ingrediente, pi.valor, i.cod_unidad_medida
						FROM tb_productos_ingredientes pi, tb_ingredientes i
						WHERE pi.cod_ingrediente = i.cod_ingrediente
						AND pi.cod_producto = $cod_producto";
			return Conexion::buscarVariosRegistro($query);
		}

		public function addProductosIngredientes() {
			$query = "INSERT INTO tb_productos_ingredientes
						SET cod_producto = $this->cod_producto,
							cod_ingrediente = $this->cod_ingrediente,
							valor = $this->valor";
			return Conexion::ejecutar($query, null);
		}

		public function editProductosIngredientes() {
			$query = "UPDATE tb_productos_ingredientes
						SET valor = $this->valor
						WHERE cod_producto_ingrediente = $this->cod_producto_ingrediente";
			return Conexion::ejecutar($query, null);
		}
		
		public function deleteProductosIngredientes() {
			$query = "DELETE FROM tb_productos_ingredientes
						WHERE cod_producto_ingrediente = $this->cod_producto_ingrediente";
			return Conexion::ejecutar($query, null);
		}
		
		public function getProductoOpcionesIngredientes($cod_producto_opcion) {
			$query = "SELECT pi.cod_producto_opcion_ingrediente, i.cod_ingrediente, i.ingrediente, pi.valor, i.cod_unidad_medida
						FROM tb_productos_opciones_ingredientes pi, tb_ingredientes i
						WHERE pi.cod_ingrediente = i.cod_ingrediente
						AND pi.cod_producto_opcion = $cod_producto_opcion";
			return Conexion::buscarVariosRegistro($query);
		}

		public function addProductosOpcionesIngredientes() {
			$query = "INSERT INTO tb_productos_opciones_ingredientes
						SET cod_producto_opcion = $this->cod_producto_opcion,
							cod_ingrediente = $this->cod_ingrediente,
							valor = $this->valor";
			return Conexion::ejecutar($query, null);
		}

		public function editProductosOpcionesIngredientes() {
			$query = "UPDATE tb_productos_opciones_ingredientes
						SET valor = $this->valor
						WHERE cod_producto_opcion_ingrediente = $this->cod_producto_opcion_ingrediente";
			return Conexion::ejecutar($query, null);
		}

		public function deleteProductosOpcionesIngredientes() {
			$query = "DELETE FROM tb_productos_opciones_ingredientes
						WHERE cod_producto_opcion_ingrediente = $this->cod_producto_opcion_ingrediente";
			return Conexion::ejecutar($query, null);
		}

		public function getIngrediente($id_contifico) {
			$cod_empresa = $this->cod_empresa;
			$query = "SELECT *
						FROM tb_ingredientes
						WHERE id_contifico = '$id_contifico'
						AND cod_empresa = $cod_empresa";
			return Conexion::buscarRegistro($query);
		}

		public function saveIngrediente($ingrediente) {
			$cod_empresa = $this->cod_empresa;
			$nombre = $ingrediente["nombre"];
			$id_contifico = $ingrediente["id"];
			$precio = $ingrediente["precio"];
			if($precio == "")
				$precio = 0;
			$cod_unidad_medida = 'kg';

			$query = "INSERT INTO tb_ingredientes
						SET cod_empresa = $cod_empresa,
						ingrediente = '$nombre',
						id_contifico = '$id_contifico',
						cod_unidad_medida = '$cod_unidad_medida',
						precio = $precio";
			return Conexion::ejecutar($query, null);
		}

		public function editIngrediente($ingrediente) {
			$cod_empresa = $this->cod_empresa;
			$nombre = $ingrediente["nombre"];
			$id_contifico = $ingrediente["id"];

			$query = "UPDATE tb_ingredientes
						SET ingrediente = '$nombre',
						estado = 'A'
						WHERE cod_empresa = $cod_empresa
						AND id_contifico = '$id_contifico'";
			return Conexion::ejecutar($query, null);
		}
		
		public function editUnidadMedidaIngrediente($ingrediente) {
			$cod_empresa = $this->cod_empresa;
			$id_contifico = $ingrediente["id"];
			$cod_unidad_medida = $ingrediente["cod_unidad_medida"];

			$query = "UPDATE tb_ingredientes
						SET cod_unidad_medida = '$cod_unidad_medida'
						WHERE cod_empresa = $cod_empresa
						AND id_contifico = '$id_contifico'";
			return Conexion::ejecutar($query, null);
		}
		
		public function deleteIngrediente($id_contifico) {
			$cod_empresa = $this->cod_empresa;

			$query = "UPDATE tb_ingredientes
						SET estado = 'D'
						WHERE id_contifico = '$id_contifico'
						AND cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}
		// INGREDIENTES

		public function setInventarioOpcionDetalle($cod_producto_opcion_detalle, $isInventario){
			$query = "UPDATE tb_productos_opciones_detalle SET debitInventario = $isInventario WHERE cod_producto_opciones_detalle = $cod_producto_opcion_detalle";
			return Conexion::ejecutar($query,NULL);
		}
		
		//PRODUCTOS KIOSCO
		public function getProductosKiosco() {
			$session = $this->session;
			$cod_empresa = $session["cod_empresa"];
			$alias = $session["alias"];
			$url_sistema = url_sistema . "assets/empresas/" . $alias . "/";
			$query = "SELECT p.cod_producto, CONCAT('$url_sistema', p.image_min) as image_min, p.nombre, IFNULL(pk.precio, p.precio) as precio, p.estado, IFNULL(pk.is_custom, 0) as is_custom, IFNULL(pk.estado, p.estado) as visible
						FROM tb_productos p
							LEFT JOIN tb_productos_kiosco pk
								ON p.cod_producto = pk.cod_producto
						WHERE p.cod_empresa = $cod_empresa
						AND p.estado IN('A', 'I')
						GROUP BY p.cod_producto
						ORDER BY p.cod_producto";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getProductsOffices($cod_producto) {
			$session = $this->session;
			$cod_empresa = $session["cod_empresa"];
			$query = "SELECT s.cod_sucursal, s.nombre, IFNULL(pk.precio, 0) as precio, IFNULL(pk.estado, 'I') as estado
						FROM tb_sucursales s
						LEFT JOIN tb_productos_kiosco pk
							ON s.cod_sucursal = pk.cod_sucursal
							AND pk.cod_producto = $cod_producto
						WHERE s.cod_empresa = $cod_empresa
						AND s.estado IN('A', 'I')";
			return Conexion::buscarVariosRegistro($query);
		}

		/* public function setCustomKiosco() {

		} */

		public function setProductoKiosco() {
			$query = "SELECT *
						FROM tb_productos_kiosco
						WHERE cod_producto = $this->cod_producto
						AND cod_sucursal = $this->cod_sucursal";
			$resp = Conexion::buscarRegistro($query);
			if(!$resp) {
				$query = "INSERT INTO tb_productos_kiosco
							SET 
								cod_producto = $this->cod_producto,
								cod_sucursal = $this->cod_sucursal,
								precio = $this->precio,
								estado = '$this->estado',
								is_custom = $this->is_custom";
				return Conexion::ejecutar($query, null);
			}
			else {
				$query = "UPDATE tb_productos_kiosco
							SET 
								precio = $this->precio,
								estado = '$this->estado',
								is_custom = $this->is_custom
							WHERE cod_producto = $this->cod_producto
							AND cod_sucursal = $this->cod_sucursal";
				return Conexion::ejecutar($query, null);
			}
		}
		
		//FIN PRODUCTOS KIOSCO
		public function getCantOptionsAndVariants($product_id){
		    $query = "SELECT
                  (
                    SELECT COUNT(*) FROM tb_productos_opciones WHERE cod_producto = $product_id
                  ) +
                  (
                    SELECT COUNT(*) FROM tb_productos WHERE cod_producto_padre = $product_id AND estado = 'A'
                  ) AS total;";
            $resp = Conexion::buscarRegistro($query);
            return $resp['total'];
		}
		
		public function setOpenDetalleTrue($id){
		    $query = "UPDATE tb_productos SET open_detalle = 1 WHERE cod_producto = $id";
		    return Conexion::ejecutar($query, null);
		}
		
		public function getEmpaque($cod_producto){
			$query = "SELECT * FROM tb_producto_empaque_detalle where cod_producto = $cod_producto";
			return Conexion::buscarRegistro($query);
		}
}
?>