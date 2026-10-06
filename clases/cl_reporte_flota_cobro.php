<?php

/**
 * Reporte de cobro de una flota a sus comercios.
 * Cuenta órdenes ENVIANDO y ENTREGADA (muchas no se pasan a ENTREGADA).
 * Nota: tb_orden_cabecera.envio_iva guarda la BASE neta del envío (envio / (1 + iva%)),
 * no el valor del IVA; si es 0 (o igual al envío) el envío no grava IVA.
 */
class cl_reporte_flota_cobro {
    const ESTADOS = "'ENVIANDO', 'ENTREGADA'";
    const TARIFA_TASTE = 0.10;

    var $cod_flota, $session;

    public function __construct() {
        $this->session = getSession();
        $this->cod_flota = $this->session['cod_empresa'];
    }

    /**
     * Valida los filtros del reporte. Retorna el mensaje de error o null si todo está bien.
     */
    public function validarFiltros($cod_empresa, $fechaInicio, $fechaFin) {
        $esFecha = function ($f) {
            $d = DateTime::createFromFormat('Y-m-d', $f);
            return $d && $d->format('Y-m-d') === $f;
        };

        if (intval($cod_empresa) <= 0 || !$esFecha($fechaInicio) || !$esFecha($fechaFin))
            return "Debe completar todos los campos, inténtelo nuevamente";
        if ($fechaFin < $fechaInicio)
            return "La fecha fin no puede ser menor que la de inicio";
        if (!$this->comercioPerteneceFlota(intval($cod_empresa)))
            return "La empresa no pertenece a tu flota";
        return null;
    }

    public function comercioPerteneceFlota($cod_empresa) {
        $query = "SELECT COUNT(*)
                FROM tb_sucursal_flota sf
                INNER JOIN tb_sucursales s ON s.cod_sucursal = sf.cod_sucursal
                WHERE sf.cod_flota = :cod_flota AND s.cod_empresa = :cod_empresa";
        return Conexion::getSingleValue($query, [':cod_flota' => $this->cod_flota, ':cod_empresa' => $cod_empresa]) > 0;
    }

    public function nombreEmpresa($cod_empresa) {
        return Conexion::getSingleValue("SELECT nombre FROM tb_empresas WHERE cod_empresa = :cod_empresa", [':cod_empresa' => $cod_empresa]);
    }

    public function ordenes($cod_empresa, $fechaInicio, $fechaFin) {
        $query = "SELECT
                o.cod_orden,
                o.fecha,
                o.estado,
                o.envio,
                o.envio_iva AS envio_base,
                s.nombre AS sucursal,
                TRIM(CONCAT(IFNULL(om.nombre, ''), ' ', IFNULL(om.apellido, ''))) AS motorizado
            FROM tb_ordenes_flota ofl
            INNER JOIN tb_orden_cabecera o ON o.cod_orden = ofl.cod_orden
            INNER JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
            -- Último motorizado registrado en la orden (puede haber reasignaciones)
            LEFT JOIN tb_orden_motorizado om ON om.cod_orden_motorizado = (
                SELECT MAX(om2.cod_orden_motorizado)
                FROM tb_orden_motorizado om2
                WHERE om2.cod_orden = o.cod_orden
            )
            WHERE ofl.cod_flota = :cod_flota
                AND o.cod_empresa = :cod_empresa
                AND o.is_envio = 1
                AND o.estado IN (" . self::ESTADOS . ")
                AND o.fecha BETWEEN :fechaInicio AND :fechaFin
            ORDER BY o.fecha ASC";
        return Conexion::buscarVariosRegistro($query, [
            ':cod_flota' => $this->cod_flota,
            ':cod_empresa' => $cod_empresa,
            ':fechaInicio' => $fechaInicio . ' 00:00:00',
            ':fechaFin' => $fechaFin . ' 23:59:59',
        ]);
    }

    public function cantidadOrdenesFlota($fechaInicio, $fechaFin) {
        $query = "SELECT COUNT(DISTINCT o.cod_orden)
            FROM tb_ordenes_flota ofl
            INNER JOIN tb_orden_cabecera o ON o.cod_orden = ofl.cod_orden
            WHERE ofl.cod_flota = :cod_flota
                AND o.is_envio = 1
                AND o.estado IN (" . self::ESTADOS . ")
                AND o.fecha BETWEEN :fechaInicio AND :fechaFin";
        return intval(Conexion::getSingleValue($query, [
            ':cod_flota' => $this->cod_flota,
            ':fechaInicio' => $fechaInicio . ' 00:00:00',
            ':fechaFin' => $fechaFin . ' 23:59:59',
        ]));
    }

    /**
     * Arma el reporte completo (indicadores, detalle con desglose de IVA y totales).
     * Lo usan tanto el controlador como el export a Excel.
     */
    public function generar($cod_empresa, $fechaInicio, $fechaFin) {
        $ordenes = $this->ordenes($cod_empresa, $fechaInicio, $fechaFin) ?: [];

        $detalle = [];
        $totales = ['subtotal_0' => 0, 'subtotal_iva' => 0, 'iva' => 0, 'total' => 0];
        foreach ($ordenes as $o) {
            $envio = round(floatval($o['envio']), 2);
            $base = round(floatval($o['envio_base']), 2);
            // Hay órdenes con envio_iva = envio (sin desglose): se toman como envío sin IVA
            $gravaIva = $base > 0 && $base < $envio;
            $iva = $gravaIva ? round($envio - $base, 2) : 0;

            if ($gravaIva) {
                $totales['subtotal_iva'] += $base;
                $totales['iva'] += $iva;
            } else {
                $totales['subtotal_0'] += $envio;
            }
            $totales['total'] += $envio;

            $detalle[] = [
                'cod_orden' => $o['cod_orden'],
                'fecha' => $o['fecha'],
                'sucursal' => $o['sucursal'],
                'estado' => $o['estado'],
                'motorizado' => $o['motorizado'] !== '' ? $o['motorizado'] : 'Sin asignar',
                'grava_iva' => $gravaIva,
                'subtotal' => $gravaIva ? $base : $envio,
                'iva' => $iva,
                'envio' => $envio,
            ];
        }
        foreach ($totales as $k => $v) {
            $totales[$k] = round($v, 2);
        }

        $ordenesTodas = $this->cantidadOrdenesFlota($fechaInicio, $fechaFin);

        return [
            'indicadores' => [
                'ordenes_empresa' => count($detalle),
                'ordenes_todas' => $ordenesTodas,
                'total_taste' => round($ordenesTodas * self::TARIFA_TASTE, 2),
                'tarifa_taste' => self::TARIFA_TASTE,
            ],
            'detalle' => $detalle,
            'totales' => $totales,
        ];
    }
}
