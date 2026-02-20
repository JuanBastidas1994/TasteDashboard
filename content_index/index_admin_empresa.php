<?php
$respSucursal = $Clsucursales->lista();
?>

<div class="col-12 ">
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
                                <div class="th-content text-center">Estado</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resp = $Clordenes->listaLimit(5);
                        foreach ($resp as $orden) {
                            $badge = 'primary';
                            if ($orden['estado'] == 'ANULADA')
                                $badge = 'danger';
                            else if ($orden['estado'] == "ENTREGADA")
                                $badge = 'success';
                            else if ($orden['estado'] == "ASIGNADA")
                                $badge = 'warning';

                            echo '<tr>
                                <td><div class="td-content customer-name">' . $orden['nombre'] . '</div></td>
                                <td><div class="td-content product-brand">' . $orden['sucursal'] . '</div></td>
                                <td><div class="td-content product-brand">' . fechaLatinoShort($orden['fecha']) . '</div></td>
                                <td><div class="td-content pricing"><span class="">$' . number_format($orden['total'], 2) . '</span></div></td>
                                <td><div class="td-content"><span class="badge badge-' . $badge . '">' . getEstado($orden['estado']) . '</span></div></td>
                            </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <hr>
                <a href="/ordenes.php" class="text-primary">
                    <span style="font-size:16px;">Ver todas las órdenes </span>
                    <i data-feather="chevron-right"></i>
                </a>
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
                                <div class="th-content th-heading">Ventas</div>
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
            <div class="text-center">
                <hr>
                <a href="/reporte_ventas.php" class="text-primary">
                    <span style="font-size:16px;">Ver Reporte de ventas </span>
                    <i data-feather="chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>