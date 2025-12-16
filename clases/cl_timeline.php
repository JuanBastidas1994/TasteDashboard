<?php
class cl_timeline
{
		public $session;
		public $cod_empresa, $nombre, $imagen, $titulo, $subtitulo, $posicion, $estado, $cod_producto;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        /*GET*/
        public function getById($cod_timeline){
            $cod_empresa = $this->session["cod_empresa"];
			$query = "SELECT * 
                        FROM tb_timelines 
                        WHERE cod_timeline = $cod_timeline
                        AND cod_empresa = $cod_empresa";
			return Conexion::buscarRegistro($query);
        }
        
        public function crear(&$id){
            $cod_empresa = $this->session["cod_empresa"];
            $query = "INSERT INTO tb_timelines
                        SET nombre = '$this->nombre', cod_empresa = $cod_empresa,
                        estado = '$this->estado', cod_producto = $this->cod_producto";
            $resp = Conexion::ejecutar($query, null);
            $id = Conexion::lastId();
            return $resp;
        }
        
        public function editar($cod_timeline){
            $query = "UPDATE tb_timelines
                        SET nombre = '$this->nombre',
                        estado = '$this->estado' 
                        WHERE cod_timeline = $cod_timeline";
            return Conexion::ejecutar($query, null);
        }

        public function crearDetalle($cod_timeline){
            $query = "INSERT INTO tb_timeline_detalles
                        SET cod_timeline = $cod_timeline, imagen = '$this->imagen',
                        titulo = '$this->titulo', subtitulo = '$this->subtitulo', posicion = $this->posicion";
            return Conexion::ejecutar($query, null);
        }
        
        public function editarDetalle($cod_timeline_detalle){
            $query = "UPDATE tb_timeline_detalles
                        SET titulo = '$this->titulo', subtitulo = '$this->subtitulo'
                        WHERE cod_timeline_detalle = $cod_timeline_detalle";
            return Conexion::ejecutar($query, null);
        }
        
        public function editarPosicion($cod_timeline_detalle){
            $query = "UPDATE tb_timeline_detalles
                        SET posicion = '$this->posicion'
                        WHERE cod_timeline_detalle = $cod_timeline_detalle";
            return Conexion::ejecutar($query, null);
        }

        public function obtenerDetalles($id){
            $sesion = $this->session;
            $urlImage = url_sistema."assets/empresas/".$sesion["alias"]."/";
            $query = "SELECT *, CONCAT('$urlImage', imagen) as imagen
                        FROM tb_timeline_detalles
                        WHERE cod_timeline = $id
                        ORDER BY posicion";
            return Conexion::buscarVariosRegistro($query);
        }

        public function obtenerDetalle($cod_timeline_detalle){
            $query = "SELECT *
                        FROM tb_timeline_detalles
                        WHERE cod_timeline_detalle = $cod_timeline_detalle";
            return Conexion::buscarRegistro($query);
        }

        public function eliminarDetalle($cod_timeline_detalle){
            $query = "DELETE FROM tb_timeline_detalles
                        WHERE cod_timeline_detalle = $cod_timeline_detalle";
            return Conexion::ejecutar($query, null);
        }

        public function lista($cod_producto){
            $cod_empresa = cod_empresa;
            $query = "SELECT *
                        FROM tb_timelines
                        WHERE cod_empresa = $cod_empresa
                        AND cod_producto = $cod_producto
                        AND estado = 'A'";
            return Conexion::buscarVariosRegistro($query);
        }

        public function eliminar($cod_timeline){
            $query = "UPDATE tb_timelines
                        SET estado = 'I'
                        WHERE cod_timeline = $cod_timeline";
            return Conexion::ejecutar($query, null);
        }

        public function getProductoByAlias($alias){
            $query = "SELECT *
                        FROM tb_productos
                        WHERE alias = '$alias'";
            return Conexion::buscarRegistro($query);
        }
}
?>