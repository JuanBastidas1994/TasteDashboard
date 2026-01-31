<?php
//require_once "class/c_logs.php";

class Conexion {

    private static $conexion;

    public static function abrir() {
        if (!isset(self::$conexion)) {
            try {
                self::$conexion = new PDO("mysql:host=" . servidor . "; dbname=" . db, usuario, contrasena);
                /* Atribuir errores a la variable */
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                /* Recupera con caracter especial de la base de datos */
                self::$conexion->exec("SET NAMES 'utf8mb4'");
                 /* Igualar sql_mode a PROD solo para este proyecto */
                self::$conexion->exec("SET SESSION sql_mode='IGNORE_SPACE,NO_ENGINE_SUBSTITUTION'");
            } catch (Exception $ex) {
                echo "Error al nivel de Database";
                //print 'Error :' . $ex->getMessage() . "<br>";
            }
        }
    }

    public static function cerrar() {
        if (!isset(self::$conexion)) {
            self::$conexion = null;
        }
    }

    public static function obtenerConexion() {
        self::abrir();
        return self::$conexion;
    }

    public static function beginTransaction()
    {
        $con = self::obtenerConexion();
        return $con->beginTransaction();
    }

    public static function commit()
    {
        $con = self::obtenerConexion();
        return $con->commit();
    }

    public static function rollBack()
    {
        $con = self::obtenerConexion();
        return $con->rollBack();
    }

    public static function buscarRegistro($sql, $data = null) {
        /* retorna los datos de un refistro en un array de una dimensi贸n */
        try {
            $con = self::obtenerConexion();
            $rs = $con->prepare($sql);
            $rs->execute($data);
            return $rs->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            echo "Error al nivel de Database ".$sql.'<br/>';
            //echo "Error: " . $ex->getMessage();
        }
    }
    
    public static function UpdateRegistro($sql) {
        /* retorna los datos de un refistro en un array de una dimensi贸n */
        try {
            $con = self::obtenerConexion();
            $rs = $con->prepare($sql);
            return $rs->execute();
        } catch (Exception $ex) {
            echo "Error al nivel de Database";
            //echo "Error: " . $ex->getMessage();
        }
    }

    public static function buscarVariosRegistro($sql, $data = null) {
        /* retorna los datos de un refistro en un array de una dimensi贸n */
        try {
            $con = self::obtenerConexion();
            $rs = $con->prepare($sql);
            $rs->execute($data);
            return $rs->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            echo "Error al nivel de Database";
            //echo "Error: " . $ex->getMessage();
        }
    }

    public static function getSingleValue($sql, $data = null)
    {
        try
        {
        $con = self::obtenerConexion();
        $rs = $con->prepare($sql);
        $rs->execute($data);
        return $rs->fetchColumn();
        } catch (Exception $ex) {
            echo "Error al nivel de Database";
            //echo "Error: " . $ex->getMessage();
        }
    }

    public static function ejecutar($sql, $data) {
        try
        {
            $con = self::obtenerConexion();
            $rs = $con->prepare($sql);
            if ($rs->execute($data)) {
                return true;
            }
            return false;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            error_log(
                date('[Y-m-d H:i:s] ') . $ex->getMessage() . PHP_EOL,
                3,
                __DIR__ . '/errores_sql.log'
            );
            return false;
        }    
    }
    
    public static function getError()
    {
    
        $con = self::obtenerConexion();
        return $con->errorInfo();
    }
    
    public static function logsLogin($cod_user,$success)
    {
    
        $LogsLogin =new RegisterLogs();
        $return = $LogsLogin->login($cod_user,$success);  
        return $return;
    }
    
    public static function logs($tipo,$accion,$datelle,$medio,$cod_user)
    {
    
        $LogsLog =new RegisterLogs();
        $return = $LogsLog->logsGeneral($tipo,$accion,$datelle,$medio,$cod_user);
        return $return;
    }

    public static function lastId()
    {
        try
        {
        $con = self::obtenerConexion();
        $lastid =$con->lastInsertId();
        return $lastid;
        } catch (Exception $ex) {
            echo "Error al nivel de Database";
            //echo "Error: " . $ex->getMessage();
        }
    }



}
