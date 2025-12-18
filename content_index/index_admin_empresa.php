<?php
$i = 0;
$respSucursal = $Clsucursales->lista();
foreach ($respSucursal as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $data = null;
    $j = 0;
    $anio = date('Y');
    for ($j = 0; $j < 12; $j++) {
        $query = "SELECT SUM(oc.total) as monto, DATE_FORMAT(oc.fecha, '%m') as mes, DATE_FORMAT(oc.fecha, '%Y') as year
                        FROM tb_orden_cabecera oc
                        WHERE oc.cod_sucursal = $cod_sucursal
                        AND DATE_FORMAT(oc.fecha, '%m') = ($j+1)
                        AND DATE_FORMAT(oc.fecha, '%Y') = $anio
                        AND oc.estado = 'ENTREGADA'
                        GROUP BY 
                            DATE_FORMAT(oc.fecha, '%m'),
                            DATE_FORMAT(oc.fecha, '%Y')";
        $row = Conexion::buscarRegistro($query, NULL);
        // echo $query;
        if ($row)
            $data[$j] = number_format($row['monto'], 2);
        else
            $data[$j] = number_format(0, 2);
    }
    $serie[$i]['name'] = $sucursal['nombre'];
    $serie[$i]['data'] = $data;
    $i++;
}
if (!isset($serie))
    $serie = [];
$reporteVentas = json_encode($serie);
// echo $reporteVentas;
$reporteVentas = base64_encode($reporteVentas);
?>

<input type="hidden" name="Respventas" id="Respventas" value="<?php echo $reporteVentas; ?>">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
    <div class="widget widget-chart-one">
        <div class="widget-heading">
            <h5 class="" data-translate="home-titulo1">Ingresos</h5>
            <ul class="tabs tab-pills">
                <li><a href="javascript:void(0);" id="tb_1" class="tabmenu revenueYearly">Anual</a></li>
                <li><a href="javascript:void(0);" id="tb_2" class="tabmenu revenueMonthly">Mensual</a></li>
                <li><a href="javascript:void(0);" id="tb_3" class="tabmenu revenueWeekly">Semanal</a></li>
            </ul>
        </div>

        <div class="widget-content" id="tab-revenueYearly">
            <div class="tabs tab-content">
                <div id="content_1" class="tabcontent">
                    <div id="revenueYearly"></div>
                </div>
            </div>
        </div>
        <div class="widget-content d-none" id="tab-revenueMonthly">
            <div class="tabs tab-content">
                <div id="content_1" class="tabcontent">
                    <div id="revenueMonthly"></div>
                </div>
            </div>
        </div>
        <div class="widget-content d-none" id="tab-revenueWeekly">
            <div class="tabs tab-content">
                <div id="content_1" class="tabcontent">
                    <div id="revenueWeekly"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$catNombres = NULL;
$catValues = NULL;
$x = 0;
$Respcategorias = $Clcategorias->lista();
foreach ($Respcategorias as $categorias) {
    $catNombres[] = $categorias['categoria'];
    $query = "SELECT count(oc.cod_orden) as total
                FROM tb_orden_cabecera oc, tb_orden_detalle od, tb_productos_categorias pc
                WHERE oc.cod_orden = od.cod_orden
                AND od.cod_producto = pc.cod_producto
                AND pc.cod_categoria = " . $categorias['cod_categoria'] . "
                AND oc.estado NOT IN('RECHAZADA')";
    $row = Conexion::buscarRegistro($query, NULL);
    if ($row)
        $catValues[] = intval($row['total']);
    else
        $catValues[] = 0;
}
$info['nombres'] = $catNombres;
$info['values'] = $catValues;
$reporteCategorias = base64_encode(json_encode($info));
?>

<div class="col-12">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">Ranking días que más vende</h5>
                </div>
                <div class="widget-content">
                    <div id="" class=""></div>
                    <div class="widget-content">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="table-responsive p-4">
                                    <table class="table" id="ranking-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="th-content text-center">#</div>
                                                </th>
                                                <th>
                                                    <div class="th-content">Día</div>
                                                </th>
                                                <th>
                                                    <div class="th-content th-heading text-right">Total</div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-self-center justify-content-center">
                                <div>
                                    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                                    <dotlottie-player src="https://lottie.host/f9a303bb-3fa8-42eb-beb4-5c43be966778/nCjanIOiNs.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">Ventas exitosas mensuales</h5>
                </div>
                <div class="widget-content">
                    <div id="" class=""></div>
                    <div class="widget-content">
                        <div class="row mx-4">
                            <div class="col-12 col-md-4">
                                <label for="monthly-sales-month-select">Mes</label>
                                <select id="monthly-sales-month-select" class="form-control"></select>
                                   <!-- meses -->
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label>Sucursal</label>
                                <select id="monthly-sales-office-select" class="form-control">
                                   <!-- sucursales -->
                                </select>
                            </div>
                            <div class="col-12 col-md-4 align-content-end text-right">
                                <button class="btn btn-primary" onclick="getMonthlySalesByOrigin()">Buscar</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="successful-sales-chart"></div>
                                <h5 id="error-sales-chart" class="text-center d-none my-4">
                                    Nada que mostrar
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="Respcategorias" id="Respcategorias" value="<?php echo $reporteCategorias; ?>">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing d-none">
    <div class="widget widget-chart-two">
        <div class="widget-heading">
            <h5 class="" data-translate="home-titulo2">Ventas por categor&iacute;a</h5>
        </div>
        <div class="widget-content">
            <div id="chart-2" class=""></div>
        </div>
    </div>
</div>

<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing" style="display:none;">
    <div class="widget-two">
        <div class="widget-content">
            <div class="w-numeric-value">
                <div class="w-content">
                    <span class="w-value">Daily sales</span>
                    <span class="w-numeric-title">Go to columns for details.</span>
                </div>
                <div class="w-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
            <div class="w-chart">
                <div id="daily-sales"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing" style="display:none;">
    <div class="widget-three">
        <div class="widget-heading">
            <h5 class="">Summary</h5>
        </div>
        <div class="widget-content">

            <div class="order-summary">

                <div class="summary-list">
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </div>
                    <div class="w-summary-details">

                        <div class="w-summary-info">
                            <h6>Income</h6>
                            <p class="summary-count">$92,600</p>
                        </div>

                        <div class="w-summary-stats">
                            <div class="progress">
                                <div class="progress-bar bg-gradient-secondary" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="summary-list">
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7" y2="7"></line>
                        </svg>
                    </div>
                    <div class="w-summary-details">

                        <div class="w-summary-info">
                            <h6>Profit</h6>
                            <p class="summary-count">$37,515</p>
                        </div>

                        <div class="w-summary-stats">
                            <div class="progress">
                                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="summary-list">
                    <div class="w-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <div class="w-summary-details">

                        <div class="w-summary-info">
                            <h6>Expenses</h6>
                            <p class="summary-count">$55,085</p>
                        </div>

                        <div class="w-summary-stats">
                            <div class="progress">
                                <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<div class="col-xl-4 col-lg-12 col-md-6 col-sm-12 col-12 layout-spacing" style="display:none;">
    <div class="widget-one">
        <div class="widget-content">
            <div class="w-numeric-value">
                <div class="w-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <div class="w-content">
                    <span class="w-value">3,192</span>
                    <span class="w-numeric-title">Total Orders</span>
                </div>
            </div>
            <div class="w-chart">
                <div id="total-orders"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
    <div class="widget widget-table-two">

        <div class="widget-heading">
            <h5 class="" data-translate="home-titulo3">&Oacute;rdenes Recientes</h5>
        </div>

        <div class="widget-content">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <div class="th-content">Cliente</div>
                            </th>
                            <th>
                                <div class="th-content">Sucursal</div>
                            </th>
                            <th>
                                <div class="th-content">Fecha</div>
                            </th>
                            <th>
                                <div class="th-content th-heading">Total</div>
                            </th>
                            <th>
                                <div class="th-content">Estado</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resp = $Clordenes->listaLimit();
                        foreach ($resp as $orden) {
                            $badge = 'primary';
                            if ($orden['estado'] == 'I')
                                $badge = 'danger';
                            else if ($orden['estado'] == "ENTREGADA")
                                $badge = 'success';
                            else if ($orden['estado'] == "ASIGNADA")
                                $badge = 'warning';

                            echo '<tr>
                                <td><div class="td-content customer-name">' . $orden['nombre'] . ' ' . $orden['apellido'] . '</div></td>
                                <td><div class="td-content product-brand">' . $orden['sucursal'] . '</div></td>
                                <td><div class="td-content product-brand">' . fechaLatinoShort($orden['fecha']) . '</div></td>
                                <td><div class="td-content pricing"><span class="">$' . number_format($orden['total'], 2) . '</span></div></td>
                                <td><div class="td-content"><span class="badge outline-badge-' . $badge . '">' . getEstado($orden['estado']) . '</span></div></td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
    <div class="widget widget-table-three">

        <div class="widget-heading">
            <h5 class="" data-translate="home-titulo4">Producto m&aacute;s vendido</h5>
        </div>

        <div class="widget-content">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <div class="th-content">Producto</div>
                            </th>
                            <th>
                                <div class="th-content th-heading">Cantidad</div>
                            </th>
                            <th>
                                <div class="th-content th-heading">Precio Total</div>
                            </th>
                            <th>
                                <div class="th-content th-heading">Precio Unidad</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resp = $Clordenes->lista_productos_ingresos();
                        foreach ($resp as $productos) {
                            $imagen = $files . $productos['image_min'];
                            $code = $productos['sku'];
                            echo '
                            <tr>
                                <td>
                                    <div class="td-content product-name"><img src="' . $imagen . '" alt="product">
                                        ' . $productos['nombre'] . '
                                        <br><b>Code: ' . $code . '</b>
                                    </div>
                                </td>
                                <td><div class="td-content text-right"><span class="quantity">' . $productos['producto_cantidad'] . '</span></div></td>
                                <td><div class="td-content"><span class="pricing">$' . number_format($productos['dinero'], 2) . '</span></div></td>
                                <td><div class="td-content"><span class="discount-pricing">$' . number_format($productos['precio'], 2) . '</span></div></td>
                            </tr>
                            ';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>