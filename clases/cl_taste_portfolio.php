<?php

class cl_taste_portfolio {
    public $cod_empresa, $path, $categories, $cities, $cod_taste_portfolio;
    
    public function __construct() {
    }

    public function getPortfolio() {
        $query = "SELECT e.nombre, e.url_web, tp.* 
                    FROM tb_taste_portafolio tp
                    INNER JOIN tb_empresas e 
                        ON e.cod_empresa = tp.cod_empresa
                    WHERE e.estado = 'A'";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
    }
    
    public function getPortfolioById() {
        $query = "SELECT e.nombre, e.url_web, tp.* 
                    FROM tb_taste_portafolio tp
                    INNER JOIN tb_empresas e 
                        ON e.cod_empresa = tp.cod_empresa
                    WHERE tp.cod_taste_portafolio = '$this->cod_taste_portfolio'";
        $resp = Conexion::buscarRegistro($query);
        return $resp;
    }

    public function createRestaurant() {
        $query = "INSERT INTO tb_taste_portafolio
                    SET cod_empresa = '$this->cod_empresa',
                        path = '$this->path',
                        categories = '$this->categories',
                        cities = '$this->cities'";
        $resp = Conexion::ejecutar($query, null);
        return $resp;
    }

    public function updateRestaurant() {
        $query = "UPDATE tb_taste_portafolio
                    SET path = '$this->path',
                        categories = '$this->categories',
                        cities = '$this->cities'
                    WHERE cod_taste_portafolio = '$this->cod_taste_portfolio'";
        $resp = Conexion::ejecutar($query, null);
        return $resp;
    }

    public function deleteRestaurant() {
        $query = "DELETE FROM tb_taste_portafolio
                    WHERE cod_taste_portafolio = '$this->cod_taste_portfolio'";
        $resp = Conexion::ejecutar($query, null);
        return $resp;
    }
    
    public function getRestauranWithoutPortfolio() {
        $query = "SELECT * 
                    FROM tb_empresas 
                    WHERE cod_empresa NOT IN(SELECT cod_empresa FROM tb_taste_portafolio)
                    AND estado = 'A'
                    AND cod_tipo_empresa = 1";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
    }
}

