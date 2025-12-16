<?php
    class cl_menu_digital{
        public function __construct(){
            
        }

        public function getMenusByEmpresa($cod_empresa){
            $query = "SELECT * 
                        FROM tb_menu_digital
                        WHERE cod_empresa = $cod_empresa";
            return Conexion::buscarVariosRegistro($query);
        }

        public function getImagenes($cod_menu_digital){
            $query = "SELECT *
                        FROM tb_menu_digital_imagenes
                        WHERE cod_menu_digital = $cod_menu_digital
                        ORDER BY posicion ASC";
            return Conexion::buscarVariosRegistro($query);
        }

        public function actPosicion($cod_menu_digital_imagenes, $posicion){
            $query = "UPDATE tb_menu_digital_imagenes 
                        SET posicion = $posicion
                        WHERE cod_menu_digital_imagenes = $cod_menu_digital_imagenes";
            return Conexion::ejecutar($query, null);
        }

        public function insertImagen($cod_menu_digital, $imagen){
            $query = "INSERT INTO tb_menu_digital_imagenes(cod_menu_digital, imagen, posicion, estado)
                        VALUES($cod_menu_digital, '$imagen', 999, 'A')";
            return Conexion::ejecutar($query, null);
        }

        public function eliminarImagen($cod_menu_digital_imagenes){
            $query = "DELETE
                        FROM tb_menu_digital_imagenes
                        WHERE cod_menu_digital_imagenes = $cod_menu_digital_imagenes";
            return Conexion::ejecutar($query, null);
        }
    }
    
?>