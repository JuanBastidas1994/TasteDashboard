<?php
class cl_productos_descripcion
{
		var $session;
		var $cod_producto,$titulo,$descripcion,$posicion ; 
		/*$cod_categoria_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado;*/
		
		public function __construct($pcod_noticia=null)
		{
			if($pcod_noticia != null)
				$this->cod_noticia= $pcod_noticia;
			    $this->session = getSession();
		}

		public function obtenerPorId($codProductoDescripcion){
            $query = "SELECT * FROM tb_productos_descripciones WHERE cod_productos_descripciones = '$codProductoDescripcion'";
            return Conexion::buscarRegistro($query);
        }


		public function crear(&$id){
		
			$query = "INSERT INTO tb_productos_descripciones(cod_producto,descripcion, titulo) ";
        	$query.= "VALUES('$this->cod_producto', '$this->descripcion', '$this->titulo')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar($productoDescripcion){
        	$query= "UPDATE tb_productos_descripciones SET descripcion = '$this->descripcion', titulo = '$this->titulo' WHERE cod_productos_descripciones = '$productoDescripcion' ";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

        public function listarPorProducto($productoId){
            $query = "SELECT * FROM tb_productos_descripciones as tpd WHERE tpd.cod_producto = '$productoId' ORDER BY tpd.posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }

        public function destroy($productoDescripcion){
            $query = "DELETE FROM tb_productos_descripciones WHERE cod_productos_descripciones = '$productoDescripcion'";
            if(Conexion::ejecutar($query, NULL)){
                return true;
            }else{
                return false;
            }
        }

		public function updatePosition(){
			$query = "UPDATE tb_productos_descripciones SET posicion = '$this->posicion' WHERE cod_productos_descripciones = '$this->descripcion'";
			if(Conexion::ejecutar($query, NULL)){
				return true;
			}else{
				return false;
			}
		}



}
?>