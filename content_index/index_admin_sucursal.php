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
                            <th><div class="th-content">Cliente</div></th>
                            <th><div class="th-content">Sucursal</div></th>
                            <th><div class="th-content">Fecha</div></th>
                            <th><div class="th-content th-heading">Total</div></th>
                            <th><div class="th-content">Estado</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resp = $Clordenes->listaLimit();
                        foreach ($resp as $orden) {
                            $badge='primary';
                            if($orden['estado'] == 'I')
                                $badge='danger';
                            else if($orden['estado'] == "ENTREGADA")
                                $badge='success';
                            else if($orden['estado'] == "ASIGNADA")
                                $badge='warning';

                            echo '<tr>
                                <td><div class="td-content customer-name">'.$orden['nombre'].' '.$orden['apellido'].'</div></td>
                                <td><div class="td-content product-brand">'.$orden['sucursal'].'</div></td>
                                <td><div class="td-content product-brand">'.hoursAgo($orden['fecha']).'</div></td>
                                <td><div class="td-content pricing"><span class="">$'.number_format($orden['total'],2).'</span></div></td>
                                <td><div class="td-content"><span class="badge outline-badge-'.$badge.'">'.getEstado($orden['estado']).'</span></div></td>
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
                            <th><div class="th-content">Producto</div></th>
                            <th><div class="th-content th-heading">Precio Total</div></th>
                            <th><div class="th-content th-heading">Precio Unidad</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resp = $Clordenes->lista_productos_ingresos();
                        foreach ($resp as $productos) {
                            $imagen = $files.$productos['image_min'];
                            $code = $productos['sku'];
                            echo '
                            <tr>
                                <td>
                                    <div class="td-content product-name"><img src="'.$imagen.'" alt="product">
                                        '.$productos['nombre'].'
                                        <br><b>Code: '.$code.'</b>
                                    </div>
                                </td>
                                <td><div class="td-content"><span class="pricing">$'.number_format($productos['dinero'],2).'</span></div></td>
                                <td><div class="td-content"><span class="discount-pricing">$'.number_format($productos['precio'],2).'</span></div></td>
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