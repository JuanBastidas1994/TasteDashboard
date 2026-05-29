<?php
class cl_promocion_producto_gratis
{
    private $session;
    private $cod_empresa;

    public function __construct()
    {
        $this->session     = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    public function getByCodEmpresa()
    {
        $query = "SELECT pg.*, s.nombre AS nombre_sucursal
                  FROM tb_promocion_producto_gratis pg
                  INNER JOIN tb_sucursales s ON pg.cod_sucursal = s.cod_sucursal
                  WHERE s.cod_empresa = {$this->cod_empresa}
                  ORDER BY s.nombre ASC";
        return Conexion::buscarVariosRegistro($query) ?: [];
    }

    public function getBySucursal($cod_sucursal)
    {
        $cod_sucursal = intval($cod_sucursal);
        $query = "SELECT * FROM tb_promocion_producto_gratis WHERE cod_sucursal = $cod_sucursal LIMIT 1";
        return Conexion::buscarRegistro($query);
    }

    public function upsert($cod_sucursal, $data)
    {
        $cod_sucursal  = intval($cod_sucursal);
        $cod_producto  = intval($data['cod_producto']);
        $nombre        = addslashes($data['producto_nombre']);
        $monto         = floatval($data['monto_minimo']);
        $imagen        = addslashes($data['imagen']);
        $f_ini         = addslashes($data['fecha_inicio']);
        $f_fin         = addslashes($data['fecha_fin']);
        $is_web        = intval($data['is_web']);
        $is_app        = intval($data['is_app']);
        $estado        = addslashes($data['estado']);
        $titulo        = addslashes($data['titulo'] ?? '');
        $desc          = '@product gratis';
        $desc_no       = addslashes('Te hacen falta $@differenceAmount para acceder a tu producto gratis');

        $existing = $this->getBySucursal($cod_sucursal);

        if ($existing) {
            $query = "UPDATE tb_promocion_producto_gratis SET
                        cod_producto          = $cod_producto,
                        producto_nombre       = '$nombre',
                        monto_minimo          = $monto,
                        imagen                = '$imagen',
                        fecha_inicio          = '$f_ini',
                        fecha_fin             = '$f_fin',
                        is_web                = $is_web,
                        is_app                = $is_app,
                        estado                = '$estado',
                        titulo                = '$titulo',
                        descripcion           = '$desc',
                        descripcion_no_aplica = '$desc_no'
                      WHERE cod_sucursal = $cod_sucursal";
        } else {
            $query = "INSERT INTO tb_promocion_producto_gratis
                        (cod_sucursal, cod_producto, fecha_inicio, fecha_fin, imagen, tipo,
                         producto_nombre, is_web, is_app, monto_minimo, titulo,
                         descripcion, descripcion_no_aplica, estado)
                      VALUES
                        ($cod_sucursal, $cod_producto, '$f_ini', '$f_fin', '$imagen', 'FIRST_ORDER',
                         '$nombre', $is_web, $is_app, $monto, '$titulo',
                         '$desc', '$desc_no', '$estado')";
        }

        return Conexion::ejecutar($query, null);
    }

    public function setEstado($cod_sucursal, $estado)
    {
        $cod_sucursal = intval($cod_sucursal);
        $estado       = addslashes($estado);
        $query = "UPDATE tb_promocion_producto_gratis SET estado = '$estado' WHERE cod_sucursal = $cod_sucursal";
        return Conexion::ejecutar($query, null);
    }
}
