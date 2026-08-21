<?php
class cl_cotizacion_precio
{
	public $session, $cod_empresa;

	public function __construct()
	{
		$this->session = getSession();
		$this->cod_empresa = isset($this->session['cod_empresa']) ? $this->session['cod_empresa'] : 0;
	}

	public function lista($sucursal){
		$params = [$this->cod_empresa];
		$query = "SELECT cp.*, s.nombre as sucursal, s.latitud as sucursal_latitud, s.longitud as sucursal_longitud, u.nombre as cliente,
					CASE WHEN cp.vigente_hasta >= NOW() THEN 'VIGENTE' ELSE 'EXPIRADO' END as vigente_estado
					FROM tb_cotizacion_precio cp
					INNER JOIN tb_sucursales s ON s.cod_sucursal = cp.cod_sucursal
					LEFT JOIN tb_usuarios u ON u.cod_usuario = cp.cod_usuario
					WHERE s.cod_empresa = ?";
		if(!empty($sucursal)){
			$query .= " AND cp.cod_sucursal = ?";
			$params[] = $sucursal;
		}
		$query .= " ORDER BY cp.creado_en DESC";
		return Conexion::buscarVariosRegistro($query, $params);
	}
}
?>
