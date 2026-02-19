<?php
$respSucursal = $Clsucursales->lista();
?>

<div class="col-12 layout-spacing">
    <div class="">
        <div class="d-block d-md-flex justify-content-between" style="align-items:center">
            <div class="d-flex mb-1" style="align-items:center">
                <h5 class="">Sucursal: </h5>
                <select class="form-control ml-3" id="cmbOffice" style="max-width: 180px;">
                    <option value="0">Todas</option>
                    <?php foreach ($respSucursal as $office): ?>
                        <option value="<?php echo $office['cod_sucursal']; ?>">
                            <?php echo htmlspecialchars($office['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-1">
                <div class="btn-group btn-group-toggle btnInputGroup activeColor" data-toggle="buttons">
                    <label class="btn btn-outline-primary active">
                        <input type="radio" name="options" class="rbPeriod" value="day"> Diario
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="week"> Semanal
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="month" checked> Mensual
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="year"> Anual
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-12 mb-5">
    <div id="widgetsInfo"></div>
</div>

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
                            if ($orden['estado'] == 'ANULADA')
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