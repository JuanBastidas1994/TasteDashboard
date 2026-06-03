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
                        <input type="radio" name="options" class="rbPeriod" value="day"> Día
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="week"> Semana
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="month" checked> Mes
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" class="rbPeriod" value="year"> Año
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-12">
    <div id="dashboardLoader" class="text-center py-5">
        <div class="spinner-grow text-primary" role="status" style="width:3rem;height:3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted mt-3 mb-0" style="font-size:15px;">Cargando métricas...</p>
    </div>
    <div id="widgetsInfo"></div>
</div>

<div class="col-12 mt-4" id="bottomTablesRow" style="display:none;">

    <!-- Top 5 Productos -->
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card p-3" style="border-radius:20px; min-height:280px;">
            <h6 style="font-weight:700;color:#e7515a;" class="mb-3">Top 5 Productos</h6>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr style="font-size:11px;text-transform:uppercase;color:#888ea8;">
                            <th>Producto</th>
                            <th class="text-right">Cant.</th>
                            <th class="text-right">Ventas</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTopProductos"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rendimiento por Sucursal -->
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card p-3" style="border-radius:20px; min-height:280px;">
            <h6 style="font-weight:700;color:#e7515a;" class="mb-3">Rendimiento por Sucursal</h6>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr style="font-size:11px;text-transform:uppercase;color:#888ea8;">
                            <th>Sucursal</th>
                            <th class="text-right">Ventas</th>
                            <th class="text-right">% Vta</th>
                            <th class="text-right">Órd.</th>
                            <th class="text-right">T.Prom</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySucursales"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Producto más vendido -->
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
        
    </div>
</div>
