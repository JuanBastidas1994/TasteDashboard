<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_empresas.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$Clempresas = new cl_empresas();
$session = getSession();

$percentIva = 12;
$ivaDivider = 1 + ($percentIva / 100);
$empresa = $Clempresas->get($session["cod_empresa"]);
if($empresa) {
    $percentIva = (float)$empresa["impuesto"];
    $ivaDivider = 1 + ($percentIva / 100);
}

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$imagen = url_sistema . '/assets/img/200x200.jpg';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $orden = $Clordenes->get_orden_array($id);

    if ($orden) {

        $numOrden = str_pad($orden['cod_orden'], 6, "0", STR_PAD_LEFT);
        $estado = $orden['estado'];
        $textoEstado = getEstado($estado);
        
        $numVersion = explode("v", $orden['api_version'])[1];

        $badge = 'primary';
        if ($orden['estado'] == 'I')
            $badge = 'danger';
        else if ($orden['estado'] == "ENTREGADA")
            $badge = 'success';
        else if ($orden['estado'] == "ASIGNADA")
            $badge = 'warning';
        else if ($orden['estado'] == "CANCELADA" || $orden['estado'] == "ANULADA")
            $badge = 'danger';

        $cod_usuario = $orden['cod_usuario'];
        $nombre = $orden['nombre'] . ' ' . $orden['apellido'];
        $telefono = ($orden['telefono'] !== "") ? $orden['telefono'] : $orden['telefono_user'];
        $direccion = $orden['referencia'];
        $correo = $orden['correo'];
        $fecha = fechaLatinoShort($orden['fecha']);
        $hora = explode(" ", $orden['fecha'])[1];
        $cod_sucursal = $orden['cod_sucursal'];
        $Clsucursales->get($cod_sucursal);
        $medio = $orden['medio_compra'];
        $cod_descuento = $orden['cod_descuento'];

        /*--NUEVO-*/
        $is_envio = $orden['is_envio'];
        $styleLinea = "display:block";
        if ($is_envio == 0)
            $styleLinea = "display:none";

        $cod_courier = $orden['cod_courier'];
        /*--NUEVO-*/
        /*UBICACION*/
        $latitud =  $orden['latitud'];
        $longitud =  $orden['longitud'];
        $distancia = $orden['distancia'];

        /*DINERO*/
        $subtotal0 = number_format($orden['subtotal0'], 2);
        $subtotal12 = number_format($orden['subtotal12'], 2);
        $subtotal = number_format($orden['subtotal'], 2);
        $descuento = number_format($orden['descuento'], 2);
        $envio = number_format($orden['envio'], 2);
        $envio_iva = number_format($orden['envio_iva'], 2);
        $servicio = number_format($orden['service'], 2);
        $iva = number_format($orden['iva'], 2);
        $iva_porcentaje = $orden['iva_porcentaje'];
        $total = number_format($orden['total'], 2);

        /*PROGRAMADO*/
        $is_programado = $orden['is_programado'];
        $fp = explode(" ", $orden['hora_retiro']);
        $fecha_programada = fechaLatino($fp[0]);
        $hora_programada = $fp[1];

        /*MOTIVO ANULACION*/
        $motivos = $Clordenes->getMotivoAnulacion($id);
        if ($motivos) {
            $motivoAnulacion = "";
            foreach ($motivos as $motivo) {
                if ($motivo['motivo'] <> "" && $motivo['motivo'] <> null)
                    $motivoAnulacion .= '<div class="col-md-12 col-xs-12">' . $motivo['motivo'] . '</div>';
                else
                    $motivoAnulacion = '<div class="col-md-12 col-xs-12">Ninguno</div>';
            }
        }

        /*RETAIL*/
        $detalleRetail = $Clordenes->GetRetailDestino($id);
        $displayMapa = "";
        $displayFormulario = "display: none;";
        if ($detalleRetail) {
            $displayMapa = "display: none;";
            $displayFormulario = "";
        }

        //DATOS FACTURA
        $facturaDisplay = "";
        $facturaHtml = "";
        $factura = $Clordenes->getFacturaByOrden($id);
        if(!$factura) {
            $facturaDisplay = "display: none;";
        }
        else {
            $facturaRazon = $factura["razon_social"];
            $facturaRuc = $factura["ruc"];
            $facturaTipo = $factura["tipo"];
            $facturaNumero = $factura["num_factura"];
            $facturaHtml = "<b>$facturaRazon</b> <br>
                            <p>$facturaRuc</p> 
                            <p>Num. $facturaTipo: $facturaNumero</p>";
        }
        
        //DATOS INVENTARIO
        $inventarioHtml = "";
        $inventarioDisplay = "display: none;";
        $inventario = $Clordenes->getInventarioByOrden($id);
        if($inventario) {
            foreach ($inventario as $inv) {
                $codigoInv = $inv["codigo"];
                $idInv = $inv["id"];
                $fechaInv = fechaHoraLatinoShort($inv["fecha"]);
                $tipoInv = "EGRESO";
                if($inv["tipo"] == "ING")
                    $tipoInv = "INGRESO";

                $inventarioHtml.= "<div class='col-12'>
                                        <b>$tipoInv</b> <br>
                                        $codigoInv - $idInv
                                        <p>$fechaInv</p>
                                        <hr>
                                    </div>";
                $inventarioDisplay = "";
            }
        }
    } else {
        header("location: ./index.php");
    }
} else {
    header("location: ./index.php");
}

function datetimeShort($fecha)
{
    $separate = explode(" ", $fecha);
    $fecha = $separate[0];
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('y', strtotime($fecha));

    $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return "$nombreMes $numeroDia/$anio " . substr($separate[1], 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--<meta charset="gb18030">-->
    <meta charset="utf8">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
    <style type="text/css">
        .respGalery>div {
            margin-top: 15px;
        }

        .itemSucursal {
            border-radius: 6px;
            border: 1px solid #e0e6ed;
            padding: 14px 26px;
            margin-bottom: 10px;
        }

        .itemSucursal .title {
            font-size: 16px;
            font-weight: bold;
        }

        .switch.s-icons {
            height: auto;
        }

        .feather-16 {
            width: 16px;
            height: 16px;
        }

        .programado {
            color: #2196f3;
            border: 2px;
            border-style: dashed;
            border-radius: 5px;
            padding: 5px;
        }
        
        .bloque-transaction .row{
            border-bottom: 1px solid #d7d7d7;
            height: 35px;
            line-height: 35px;
        }
        
        .bloque-transaction .titulo{
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!--MODAL PAYMENTEZ INFO -->
    <div class="modal fade bs-example-modal-lg" id="detallePaymentezModal" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="x_content">    
                        <div class="row">
                            <div class="col-12 bloque-transaction">
                                <h3>Transacción</h3>
                                <div class="row">
                                    <div class="col-6 titulo">Estado</div>
                                    <div class="col-6" id="pay-status"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 titulo">Id</div>
                                    <div class="col-6" id="pay-id"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 titulo">Fecha de pago</div>
                                    <div class="col-6" id="pay-date"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 titulo">Número de Lote</div>
                                    <div class="col-6" id="pay-lote"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 titulo">Número de Seguimiento</div>
                                    <div class="col-6" id="pay-trace"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 titulo">Código de autorización</div>
                                    <div class="col-6" id="pay-auth"></div>
                                </div>
                                
                            </div>
                            <div class="col-12 bloque-transaction mt-3">
                                <h3>Tarjeta</h3>
                                <div class="row">
                                    <div class="col-6">Tarjeta usada</div>
                                    <div class="col-6"><span id="card-bin"></span>XXXXXX<span id="card-number"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Expiración</div>
                                    <div class="col-6"><span id="card-mes"></span>/<span id="card-year"></span></div>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <?php echo sidebar(); ?>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><a id="btnBack" data-module-back="ordenes.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Ordenes</span></a>
                    </div>
                    <h3 id="titulo"><?php echo ($numOrden != "") ? "Orden #" . $numOrden : "Orden"; ?> <span class="shadow-none badge badge-<?php echo $badge; ?>"><?php echo $textoEstado; ?></span></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px;">
                        <span id="" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="calendar"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> <?php echo $fecha; ?></span>
                        </span>

                        <span id="" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="clock"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> <?php echo $hora; ?></span>
                        </span>

                        <span id="" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="compass"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> <?php echo $Clsucursales->nombre; ?></span>
                        </span>

                        <?php if (trim($medio) != "") { ?>
                            <span id="" style="cursor: pointer;margin-right: 15px;">
                                <i class="feather-16" data-feather="smartphone"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> <?php echo $medio; ?></span>
                            </span>
                        <?php } ?>
                        
                        
                        
                        <span id="" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="git-branch"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> V. <?php echo $numVersion; ?></span>
                        </span>

                        <?php if ($is_programado == 1) { ?>
                            <span class="programado" id="" style="cursor: pointer;margin-right: 15px;">
                                <span style="font-size: 16px; vertical-align: middle;">
                                    <b> Para: </b>
                                    <i class="feather-16" data-feather="calendar"></i>
                                    <?php echo " " . $fecha_programada; ?>
                                    <i class="feather-16" data-feather="clock"></i>
                                    <?php echo " " . $hora_programada; ?>
                                </span>
                            </span>
                        <?php } ?>

                        <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;display:none;">
                            <i class="feather-16" data-feather="mail"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Enviar Correo</span>
                        </span>

                        <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;display:none;">
                            <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Anular</span>
                        </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Informacion basica -->
                        <div class="widget-content widget-content-area br-6">
                            <div>
                                <h4>Detalle</h4>
                            </div>
                            <input type="hidden" name="id" id="id" value="<?php echo $cod_producto; ?>">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cant.</th>
                                                <th>Precio</th>
                                                <th>DESC</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            //$resp = $Clordenes->get_detalle_orden($id);
                                            foreach ($orden['detalle'] as $detalle) {
                                                $comentariosDetalle = '';
                                                if($detalle['cobra_iva'] == 1){
                                    		        $detalle['precio'] = $detalle['precio'] / $ivaDivider;
                                    		        $detalle['total'] = $detalle['total'] / $ivaDivider;
                                    		        $detalle['adicional_total'] = $detalle['adicional_total'] / $ivaDivider;
                                    		        $detalle['descuento'] = ($detalle['descuento'] > 0) ? $detalle['descuento'] / $ivaDivider : 0;
                                    		    }
                                                
                                                if ($detalle['descripcion'] != "") {
                                                    if ($numVersion < 1 && $empresa["cod_empresa"] != 152)
                                                        $texto = $str = str_replace("&nbsp;", " ", $detalle['descripcion']);
                                                    else {
                                                        $texto = "";
                                                        $arr = json_decode($detalle['descripcion'], true);
                                                        if (JSON_ERROR_NONE !== json_last_error()) {
                                                            $texto = $detalle['descripcion'];
                                                        } else {
                                                            foreach ($arr as $det) {
                                                                $classText = "";
                                                                if (strrpos($det['text'], 'Promo martes') === 0)
                                                                    $classText = 'style="margin-left: -20px !important;font-size: 15px !important; font-weight: bold !important; color: black !important;"';

                                                                $textdetail = isset($det['text']) ? $det['text'] : $det['nombre'];
                                                                $texto .= '<p class="font-weight-bold mt-4" ' . $classText . '>' . replaceUnicode($textdetail) . '</p>';
                                                                foreach ($det['detalles'] as $det2) {
                                                                    $textdetail = isset($det2['text']) ? $det2['text'] : $det2['nombre'];
                                                                    $preAdi = "";
                                                                    if (0 < $det2['precio_adicional'])
                                                                        $preAdi = "+$" . $det2['precio_adicional'];
                                                                    $texto .= '<p class="m-0">' . $det2['cantidad'] . ' ' . replaceUnicode($textdetail) . ' ' . $preAdi;
                                                                }
                                                            }
                                                            if ($detalle['comentarios'] !== "") {
                                                                $texto .= '<p class="font-weight-bold mt-4">Comentario</p>';
                                                                $texto .= '<p class="m-0">' . $detalle['comentarios'] . '</p>';
                                                            }
                                                        }
                                                    }

                                                    $comentariosDetalle = '<dl>
										  <dd class="text-comments" style="padding-left: 20px;">' . $texto . '</dd>
										</dl>';
                                                }
                                                echo '<tr>
                                        <td>' . $detalle['nombre'] . '
                                          ' . $comentariosDetalle . '
                                        </td>
                                        <td>' . $detalle['cantidad'] . '</td>
                                        <td>$' . number_format($detalle['precio'], 2) . '</td>
                                        <td>(-)$' . number_format($detalle['descuento'], 2) . '</td>
                                        <td>$' . number_format(($detalle['precio_final']), 2) . '</td>
                                      </tr>';
                                            }

                                            ?>
                                            <tr>
                                                <td>Envío</td>
                                                <td>1</td>
                                                <td>$<?php echo $envio_iva; ?></td>
                                                <td>--</td>
                                                <td>$<?php echo $envio; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="">
                                        <div class="inv--total-amounts text-sm-right">
                                            <div class="row">
                                                <div class="col-sm-8 col-7">
                                                    <p class=" discount-rate">Descuento : </p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p class="">$<?php echo $descuento; ?></p>
                                                </div>
                                                <div class="col-sm-8 col-7">
                                                    <p class="">SubTotal 0: </p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p class="">$<?php echo $subtotal0; ?></p>
                                                </div>
                                                <div class="col-sm-8 col-7">
                                                    <p class="">SubTotal <?= $iva_porcentaje ?>: </p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p class="">$<?php echo $subtotal12; ?></p>
                                                </div>
                                                <div class="col-sm-8 col-7">
                                                    <p class="">Impuestos: </p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p class="">$<?php echo $iva; ?></p>
                                                </div>
                                                
                                                <?php
                                                if($servicio > 0){
                                                    echo '<div class="col-sm-8 col-7">
                                                        <p class="">Servicio: </p>
                                                    </div>
                                                    <div class="col-sm-4 col-5">
                                                        <p class="">$'.$servicio.'</p>
                                                    </div>';    
                                                }
                                                ?>
                                                
                                                

                                                <div class="col-sm-8 col-7 grand-total-title">
                                                    <h4 class="">Total : </h4>
                                                </div>
                                                <div class="col-sm-4 col-5 grand-total-amount">
                                                    <h4 class="">$<?php echo $total; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    if ($descuento > 0)
                                        echo '<div><h6>Descuento: $' . $descuento . ' <span class="badge badge-secondary">' . $cod_descuento . '</span></h6></div>';
                                    ?>
                                </div>
                            </form>
                        </div>

                        <!-- Formas de Pago -->
                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                            <div>
                                <h4>Formas de Pago</h4>
                            </div>
                            <div class="row">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Forma</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($orden['pagos'] as $pagos) {
                                            
                                            echo '<tr>
                                                <td>' . $pagos['descripcion'] . ' ' . $pagos['observacion'] . ' <i data-feather="eye" onclick="getDetailPayPaymentez('.$id.')"></i> </td>
                                                <td>$' . number_format($pagos['monto'], 2) . '</td>
                                              </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <?php
                        $isAnulada = 0;
                        foreach ($orden['pagos'] as $pagos) {
                            if ($pagos['forma_pago'] == "T") {
                                $idTransaction = $pagos['observacion'];
                            }
                        }
                        if ($idTransaction <> "" && $idTransaction <> null) {
                            $datosAnulacion = $Clordenes->getDetalleAnulada($idTransaction);
                            if ($datosAnulacion) {
                                $trAnulacion = "";

                                foreach ($datosAnulacion as $datAnula) {
                                    $badge = "success";
                                    $isAnulada = 1;
                                    if ($datAnula['estado'] <> "success") {
                                        $badge = "danger";
                                        $isAnulada = 0;
                                    }
                                    $trAnulacion .= '  <tr>
                                                  <td>' . $datAnula['fecha'] . '</td>
                                                  <td><span class="shadow-none badge badge-' . $badge . '">' . $datAnula['estado'] . '</td>
                                                  <td>' . $datAnula['respuesta'] . '</td>
                                                </tr>';
                                }
                                echo '<!-- Estado del Pago -->
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                                <div><h4>Estado de anulaci&oacute;n del Pago</h4></div>
                                <div class="row">
                                  <table class="table style-3">
                                    <thead>
                                      <tr>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Respuesta</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      ' . $trAnulacion . '
                                    </tbody>
                                  </table>
                                  <div class="col-sm-12 col-xs-12" style="text-align: right;">
                                    <button type="button" class="btn btn-danger btn-anular" data-value="' . $id . '" data-estado="ANULADA">Anular</button>
                                  </div>
                                </div>
                            </div> ';
                            }
                        }

                        $factElectronica = $Clordenes->getFacturaElectronica($id);
                        if ($factElectronica) {
                            $badge = "success";
                            if ($factElectronica['estado'] == "ANULADA")
                                $badge = "danger";

                            $trAnulacion .= '<tr>
                              <td>' . $factElectronica['num_factura'] . '</td>
                              <td>' . $factElectronica['clave_acceso'] . '</td>
                              <td><span class="shadow-none badge badge-' . $badge . '">' . $factElectronica['estado'] . '</td>
                            </tr>';
                            echo '<!-- Estado de la Factura Electr&oacute;nica -->
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                              <div><h4>Estado de la Factura Electr&oacute;nica<span style="font-size: 14px;"> - ' . $factElectronica['facturero'] . '</span></h4> </div>
                              <div class="row">
                                <table class="table style-3">
                                  <thead>
                                    <tr>
                                      <th>N&uacute;m. factura</th>
                                      <th>Clave Acceso</th>
                                      <th>Estado</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    ' . $trAnulacion . '
                                  </tbody>
                                </table>
                              </div>
                          </div> ';
                        }
                        ?>

                        <input type="hidden" id="hdAnulada" value="<?= $isAnulada ?>">
                    </div>

                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">

                        <!-- Datos de Cliente -->
                        <div class="widget-content widget-content-area br-6">
                            <div>
                                <h4>Datos del cliente</h4>
                            </div>
                            <div class="row">
                                <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="user"></i> </b></span><?php echo $nombre; ?>
                                </a>
                                <a class="col-12" href="mailto:<?php echo $correo; ?>" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="mail"></i> </b></span><?php echo $correo; ?>
                                </a>
                                <a class="col-12" href="tel:<?php echo $telefono; ?>" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="phone"></i> </b></span><?php echo $telefono; ?>
                                </a>
                                <div class="col-12" style="text-align: right;">
                                    <a class="btn btn-primary" href="cliente_detalle.php?id=<?php echo $cod_usuario; ?>" style="margin-top: :8px;">
                                        Ver m&aacute;s
                                    </a>
                                </div>

                            </div>
                        </div>
                        
                        <?php if($orden['datos_facturacion']){ $datos_facturacion = $orden['datos_facturacion']; ?>
                        <!-- Datos de Cliente -->
                        <div class="widget-content widget-content-area br-6 mt-3">
                            <div>
                                <h4>Datos de Facturación</h4>
                            </div>
                            <div class="row">
                                <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="user"></i> </b></span><?php echo $datos_facturacion['nombre']; ?>
                                </a>
                                <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="credit-card"></i> </b></span><?php echo $datos_facturacion['num_documento']; ?>
                                </a>
                                <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="map"></i> </b></span><?php echo $datos_facturacion['direccion']; ?>
                                </a>
                                <a class="col-12" href="mailto:<?php echo $datos_facturacion['correo']; ?>" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="mail"></i> </b></span><?php echo $datos_facturacion['correo']; ?>
                                </a>
                                <a class="col-12" href="tel:<?php echo $datos_facturacion['telefono']; ?>" style="padding:8px; color:#7e7b80;">
                                    <span><b><i data-feather="phone"></i> </b></span><?php echo $datos_facturacion['telefono']; ?>
                                </a>

                            </div>
                        </div>
                        <?php } ?>

                        <div class="widget-content widget-content-area  br-6" style="margin-top: 15px; <?=$facturaDisplay?>;">
                            <div>
                                <h4>Datos de documento</h4>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <?=$facturaHtml?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="widget-content widget-content-area  br-6" style="margin-top: 15px; <?=$inventarioDisplay?>">
                            <div>
                                <h4 class="mb-3">Datos de inventario</h4>
                            </div>
                            <div class="row">
                                <?=$inventarioHtml?>
                            </div>
                        </div>

                        <?php if ($motivos) { ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                                <div>
                                    <h4>Motivo de Anulación</h4>
                                </div>
                                <div class="row">
                                    <?php echo $motivoAnulacion ?>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Ubicacion -->
                        <?php if ($is_envio == 1) { ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px; <?= $displayMapa ?>">

                                <div class="d-flex align-items-center">
                                    <h4 class="flex-grow-1">Ubicaci&oacute;n</h4>
                                    <?php
                                    if($distancia > 0){
                                        echo '<div>Distancia: '.$distancia.' km</div>';
                                    }else{
                                        echo '<div>Distancia: unknow</div>';
                                    }
                                    ?>
                                    
                                </div>
                                <div class="row">
                                    <div id="mapa" class="gllpMap" style="margin-left: 0; width: 100%; height: 250px;" data-latitud="<?php echo $latitud; ?>" data-longitud="<?php echo $longitud; ?>">Google Maps</div>
                                </div>
                            </div>

                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px; <?= $displayFormulario ?>">
                                <div>
                                    <h4>Datos Env&iacute;o</h4>
                                </div>

                                <p style="text-align:left; font-size:14px; <?= $displayFormulario ?>">Bodega: <span><?= $detalleRetail['nom_sucursal'] ?></span></p>
                                <p style="text-align:left; font-size:14px; <?= $displayFormulario ?>">Ciudad origen: <span><?= $detalleRetail['nom_ciudad_origen'] ?></span></p>
                                <p style="text-align:left; font-size:14px; <?= $displayFormulario ?>">Ciudad destino: <span><?= $detalleRetail['nom_ciudad_destino'] ?></span></p>
                                <p style="text-align:left; font-size:14px; <?= $displayFormulario ?>">C&oacute;digo postal: <span><?= $detalleRetail['cod_postal'] ?></span></p>
                                <p style="text-align:left; font-size:14px; <?= $displayFormulario ?>">N&uacute;mero de casa: <span><?= $detalleRetail['num_casa'] ?></span></p>
                            </div>

                        <?php } else if($is_envio == 0){ ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">

                                <div>
                                    <h4>Retiro</h4>
                                </div>
                                <div class="row">
                                    <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                        <span><b><i data-feather="calendar"></i> </b></span><?php echo $fecha_programada; ?>
                                    </a>
                                    <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                        <span><b><i data-feather="clock"></i> </b></span><?php echo $hora_programada; ?>
                                    </a>
                                </div>
                            </div>
                        <?php }else{ ?>
                             <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">

                                <div>
                                    <h4>El Pedido fue en MESA</h4>
                                </div>
                            </div>
                        <?php } ?>
                        <!--Tracking-->
                        <?php
                        if ($cod_courier != 0) {
                            $query = "SELECT * FROM tb_courier WHERE cod_courier = " . $cod_courier;
                            $courier = Conexion::buscarRegistro($query);
                            if($courier){
                        ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                                <div>
                                    <h4>Tracking</h4>
                                </div>
                                <div class="row">
                                    <a class="col-12" href="javascript:void:0;" style="padding:8px; color:#7e7b80;">
                                        <div class="row">
                                            <div class="col-2">
                                                <img src="<?php echo $courier['imagen']; ?>" class="rounded-circle w-100" alt="">
                                            </div>
                                            <div class="col-10">
                                                <h3><?php echo $courier['nombre']; ?></h3>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="col-12" style="text-align: right;">
                                        <a class="btn btn-primary" href="orden_tracking.php?id=<?php echo $id; ?>" style="margin-top: :8px;">
                                            Ver m&aacute;s
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                        }
                        ?>
                        
                        <!-- TimeLine -->
                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;<?php echo $styleLinea; ?>">
                            <div>
                                <h4>L&iacute;nea de Tiempo</h4>
                            </div>
                            <div class="mt-container mx-auto">
                                <div class="timeline-line">
                                    <?php
                                    /*--NUEVO--*/

                                    $queryH = "SELECT o.fecha as fecha_ FROM tb_orden_cabecera o where o.cod_orden = " . $id;
                                    $respH = Conexion::buscarVariosRegistro($queryH);
                                    $cl = "primary";
                                    $text = "Cliente realizo la orden";
                                    $fecha = datetimeShort($respH[0]['fecha_']);
                                    echo '<div class="item-timeline">
                                                      <p class="t-time">' . $fecha . '</p>
                                                      <div class="t-dot t-dot-' . $cl . '">
                                                      </div>
                                                      <div class="t-text">
                                                          <p>' . $text . '</p>
                                                          <p class="t-meta-time"></p>
                                                      </div>
                                                  </div>';
                                    /*HISTORIAL*/
                                    $queryH = "SELECT h.*,o.fecha as fecha_ FROM `tb_orden_historial` h,tb_orden_cabecera o where o.cod_orden = h.cod_orden and h.estado IN ('ENTRANTE','ASIGNADA','ENVIANDO','ENTREGADA','NO_ENTREGADA', 'ASIGNACION_CANCELADA') and h.cod_orden = " . $id;
                                    $respH = Conexion::buscarVariosRegistro($queryH);
                                    foreach ($respH as $h) {
                                        $fecha = datetimeShort($h['fecha']);
                                        switch ($h['estado']) {
                                            case "ASIGNADA":
                                                $cl = "danger";
                                                $text = "La Orden fue asignada";
                                                break;
                                            case "ENVIANDO":
                                                $cl = "warning";
                                                $text = "Motorizado empez&oacute; la carrera";
                                                break;
                                            case "ENTREGADA":
                                                $cl = "success";
                                                $text = "Orden Entregada";
                                                break;
                                            case "ASIGNACION_CANCELADA":
                                                $cl = "danger";
                                                $text = "Asignación al courier cancelada";
                                                break;
                                        }

                                        echo '<div class="item-timeline">
                                                      <p class="t-time">' . $fecha . '</p>
                                                      <div class="t-dot t-dot-' . $cl . '">
                                                      </div>
                                                      <div class="t-text">
                                                          <p>' . $text . '</p>
                                                          <p class="t-meta-time"></p>
                                                      </div>
                                                  </div>';
                                    }
                                    /*-----*/

                                    /*$query = "SELECT oc.*, m.fecha_asignacion, m.fecha_salida, m.fecha_llegada, m.cod_motorizado
                                                    FROM tb_orden_cabecera oc LEFT JOIN tb_motorizado_asignacion m
                                                    ON oc.cod_orden = m.cod_orden
                                                    WHERE oc.cod_orden = 123";
                                            $row = Conexion::buscarRegistro($query);
                                            
                                            $fecha = datetimeShort($row['fecha']);
                                            echo '<div class="item-timeline">
                                                    <p class="t-time">'.$fecha.'</p>
                                                    <div class="t-dot t-dot-primary">
                                                    </div>
                                                    <div class="t-text">
                                                        <p>Cliente realizo la orden</p>
                                                        <p class="t-meta-time"></p>
                                                    </div>
                                                </div>';

                                            if($row['fecha_asignacion'] != NULL){

                                              $nombreMotorizado = "";
                                              $moto = $Clusuarios->get($row['cod_motorizado']);
                                              if($moto){
                                                $nombreMotorizado = " a ".$moto['nombre']." ".$moto['apellido'];
                                              }

                                              $fecha = datetimeShort($row['fecha_asignacion']);
                                              echo '<div class="item-timeline">
                                                      <p class="t-time">'.$fecha.'</p>
                                                      <div class="t-dot t-dot-danger">
                                                      </div>
                                                      <div class="t-text">
                                                          <p>La orden fue asignada '.$nombreMotorizado.'</p>
                                                          <p class="t-meta-time"></p>
                                                      </div>
                                                  </div>';
                                            } 

                                            if($row['fecha_salida'] != NULL){
                                              $fecha = datetimeShort($row['fecha_salida']);
                                              echo '<div class="item-timeline">
                                                      <p class="t-time">'.$fecha.'</p>
                                                      <div class="t-dot t-dot-warning">
                                                      </div>
                                                      <div class="t-text">
                                                          <p>Motorizado empezó la carrera</p>
                                                          <p class="t-meta-time"></p>
                                                      </div>
                                                  </div>';
                                            }  

                                            if($row['fecha_llegada'] != NULL){
                                              $fecha = datetimeShort($row['fecha_llegada']);
                                              echo '<div class="item-timeline">
                                                      <p class="t-time">'.$fecha.'</p>
                                                      <div class="t-dot t-dot-success">
                                                      </div>
                                                      <div class="t-text">
                                                          <p>Orden Entregada</p>
                                                          <p class="t-meta-time"></p>
                                                      </div>
                                                  </div>';
                                            }  */
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Calificacion -->
                        <?php
                        $calificacion = 0;
                        $query = "SELECT * FROM tb_orden_calificacion WHERE cod_orden = " . $id;
                        $resp = Conexion::buscarRegistro($query);
                        if ($resp) {
                            $calificacion = $resp['calificacion'];
                            $texto = $resp['texto'];
                        ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                                <div>
                                    <h4>Calificaci&oacute;n</h4>
                                </div>

                                <div class="review" data-rating-stars="5" data-rating-value="<?php echo $calificacion; ?>" data-rating-half="true" data-rating-readonly="true" style="font-size: 30px;"></div>
                                <p><?php echo $texto; ?></p>

                            </div>
                        <?php } ?>



                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/orden_detalle.js" type="text/javascript"></script>
    <script src="assets/js/rating.js" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>

</html>