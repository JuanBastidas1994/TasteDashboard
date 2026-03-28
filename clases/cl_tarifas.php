<?php
class cl_tarifas
{
    public $session;
    public $cod_empresa;
    public $cod_tarifa, $cod_sucursal, $nombre, $peso_max_kg;
    public $id, $distancia_ini, $distancia_fin, $precio;

    public function __construct()
    {
        $this->session    = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    /* ─────────────────────────────────────────
     *  TARIFAS
     * ───────────────────────────────────────── */

    /** Lista todas las tarifas de una sucursal */
    public function getByOficina($cod_sucursal)
    {
        $query = "SELECT * FROM tb_tarifa
                  WHERE cod_sucursal = $cod_sucursal
                  ORDER BY cod_tarifa ASC";
        return Conexion::buscarVariosRegistro($query);
    }

    /** Cuenta cuántas tarifas tiene una sucursal (usado en el API de precio) */
    public function countByOficina($cod_sucursal)
    {
        $query = "SELECT COUNT(*) as total FROM tb_tarifa
                  WHERE cod_sucursal = $cod_sucursal";
        $resp = Conexion::buscarRegistro($query);
        return $resp ? (int)$resp['total'] : 0;
    }

    /** Crea una tarifa nueva y devuelve su ID */
    public function crear(&$id)
    {
        $peso = ($this->peso_max_kg !== null && $this->peso_max_kg !== '')
            ? floatval($this->peso_max_kg)
            : 'NULL';

        $query = "INSERT INTO tb_tarifa (cod_sucursal, nombre, peso_max_kg)
                  VALUES ({$this->cod_sucursal}, '{$this->nombre}', $peso)";

        if (Conexion::ejecutar($query, null)) {
            $id = Conexion::lastId();
            return true;
        }
        return false;
    }

    /** Edita nombre y peso de una tarifa existente */
    public function editar()
    {
        $peso = ($this->peso_max_kg !== null && $this->peso_max_kg !== '')
            ? floatval($this->peso_max_kg)
            : 'NULL';

        $query = "UPDATE tb_tarifa
                  SET nombre = '{$this->nombre}',
                      peso_max_kg = $peso
                  WHERE cod_tarifa = {$this->cod_tarifa}
                    AND cod_sucursal IN (
                        SELECT cod_sucursal FROM tb_sucursales
                        WHERE cod_empresa = {$this->cod_empresa}
                    )";
        return Conexion::ejecutar($query, null);
    }

    /** Elimina una tarifa y todos sus rangos en cascada */
    public function eliminar($cod_tarifa)
    {
        // Primero borramos rangos hijos
        $q1 = "DELETE FROM tb_sucursal_costo_envio_rango WHERE cod_tarifa = $cod_tarifa";
        Conexion::ejecutar($q1, null);

        // Luego la tarifa (solo si pertenece a la empresa, seguridad)
        $q2 = "DELETE t FROM tb_tarifa t
               INNER JOIN tb_sucursales s ON s.cod_sucursal = t.cod_sucursal
               WHERE t.cod_tarifa = $cod_tarifa
                 AND s.cod_empresa = {$this->cod_empresa}";
        return Conexion::ejecutar($q2, null);
    }

    /* ─────────────────────────────────────────
     *  RANGOS DE UNA TARIFA
     * ───────────────────────────────────────── */

    /** Devuelve todos los rangos de una tarifa */
    public function getRangos($cod_tarifa)
    {
        $query = "SELECT * FROM tb_sucursal_costo_envio_rango
                  WHERE cod_tarifa = $cod_tarifa
                  ORDER BY distancia_ini ASC";
        return Conexion::buscarVariosRegistro($query);
    }

    /** Inserta un rango nuevo */
    public function saveRango()
    {
        $query = "INSERT INTO tb_sucursal_costo_envio_rango
                     (cod_tarifa, distancia_ini, distancia_fin, precio)
                  VALUES
                     ({$this->cod_tarifa}, {$this->distancia_ini},
                      {$this->distancia_fin}, {$this->precio})";
        return Conexion::ejecutar($query, null);
    }

    /** Edita un rango existente */
    public function editRango()
    {
        $query = "UPDATE tb_sucursal_costo_envio_rango
                  SET distancia_ini = {$this->distancia_ini},
                      distancia_fin = {$this->distancia_fin},
                      precio        = {$this->precio}
                  WHERE id = {$this->id}";
        return Conexion::ejecutar($query, null);
    }

    /** Elimina un rango por ID */
    public function removeRango($id)
    {
        $query = "DELETE FROM tb_sucursal_costo_envio_rango WHERE id = $id";
        return Conexion::ejecutar($query, null);
    }
}
?>
