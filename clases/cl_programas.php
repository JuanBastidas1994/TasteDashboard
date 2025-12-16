<?php
    class cl_programas
    {
		var $session;
		var $cod_programa, $nombre, $precio, $descripcion, $posicion, $estado;
		
		public function __construct($pcod_programa=null){
			if($pcod_programa != null)
				$this->cod_programa= $pcod_programa;
			    $this->session = getSession();
		}

		public function lista(){
			$query = "SELECT * 
                        FROM tb_programas 
                        WHERE estado IN('A','I') 
                        AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function get($cod_programa, &$array){
			$query = "SELECT * 
                        FROM tb_programas 
                        WHERE cod_programa = $cod_programa";
            $array = Conexion::buscarVariosRegistro($query);
            return $array;
		}

        public function crear(){
            $cod_empresa = $this->session['cod_empresa'];
            $query = "INSERT INTO tb_programas
                        SET cod_empresa = $cod_empresa, nombre = '$this->nombre', precio = $this->precio, descripcion = '$this->descripcion', estado = 'A'";
            return Conexion::ejecutar($query, null);
        }

        public function editar($cod_programa){
            $query = "UPDATE tb_programas
                        SET nombre = '$this->nombre', precio = $this->precio, descripcion = '$this->descripcion', estado = '$this->estado'
                        WHERE cod_programa = $cod_programa";
            return Conexion::ejecutar($query, null);
        }

        public function getProgramasByUser($cod_usuario){
            $query = "SELECT pu.*, u.nombre, u.correo, u.telefono, p.nombre as programa
                        FROM tb_programa_usuario pu, tb_usuarios u, tb_programas p
                        WHERE pu.cod_usuario = u.cod_usuario
                        AND pu.cod_programa = p.cod_programa
                        AND pu.cod_usuario = $cod_usuario";
            return Conexion::buscarVariosRegistro($query);
        }

        public function aceptarPrograma($cod_programa_usuario)
        {
            $query = "UPDATE tb_programa_usuario
                        SET precio = '$this->precio', estado = '$this->estado'
                        WHERE cod_programa_usuario = $cod_programa_usuario";
            return Conexion::ejecutar($query, null);
        }

        public function getProgramaUsuario($cod_programa_usuario){
            $query = "SELECT pu.*, CONCAT(u.nombre,' ',u.apellido) as nombrePadre, u.correo, p.nombre
                        FROM tb_programa_usuario pu, tb_usuarios u, tb_programas p
                        WHERE pu.cod_usuario = u.cod_usuario
                        AND pu.cod_programa = p.cod_programa
                        AND pu.cod_programa_usuario = $cod_programa_usuario";
            return Conexion::buscarRegistro($query);
        }
    }
?>