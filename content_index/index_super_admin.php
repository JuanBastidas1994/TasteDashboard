<?php
    $files = url_sistema.'assets/empresas/';

    $d = strtotime("today");
    $domingo_anterior = date("Y-m-d", strtotime("last sunday midnight",$d))." 23:59:59";
    $lunes_anterior = date("Y-m-d H:i:s", strtotime("last monday midnight",strtotime("last sunday midnight",$d)));
?>

<!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="modalFunciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Funciones</h5>
                    <input type="hidden" id="alias-mas-funciones" value=""/>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <script id="lista-funciones" type="text/x-handlebars-template">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="lst_web_paginas.php?id={{alias}}">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="layout"></i></div>
                                    <h5>Esquema</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="empresas_buttonPayment.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="credit-card"></i></div>
                                    <h5>Botón de Pagos</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="empresas_courier.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="truck"></i></div>
                                    <h5>Courries</h5>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="simulador-ordenes.php?alias={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="navigation"></i></div>
                                    <h5>Simulador</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="https://{{alias}}.demo.mie-commerce.com" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="monitor"></i></div>
                                    <h5>Demo</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" target="_blank" href="{{web}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="sunrise"></i></div>
                                    <h5>Front Page</h5>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones LoginAdmins" href="javascript:void(0);" style="text-align:center;" data-value="{{alias}}">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="unlock"></i></div>
                                    <h5>Login</h5>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="empresa_productos.php?id={{alias}}" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="coffee"></i></div>
                                    <h5>Productos</h5>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="reporte_empresa_ventas.php?id={{alias}}" target="_blank" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="pie-chart"></i></div>
                                    <h5>Reporte de ventas</h5>
                                </a>
                            </div>
                            
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <a class="col-md-12 col-sm-12 col-xs-12 card-funciones" href="https://dashboard.mie-commerce.com/clearProject/limpiar.php?url={{folder}}" target="_blank" style="text-align:center;">
                                    <div style="justify-content: center; align-items: center;width: 100%;"><i class="feather-18" data-feather="frown"></i></div>
                                    <h5>Regenerar Página</h5>
                                </a>
                            </div>
                        </div>
                    </script>
                    
                    
                    <div class="row lista-funciones">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="modalConfig" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CONFIGURACIONES</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <h5>Courier <a href="empresas_courier.php" target="_blank"><i class="feather-16" data-feather="settings"></i></a></h5>
                            <div id="divCouriers">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <h5>Bot&oacute;n de Pagos <a href="empresas_buttonPayment.php" target="_blank"><i class="feather-16" data-feather="settings"></i></a></h5>
                            <div id="divBotonPagos">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt-4">
                            <h5>Facturaci&oacute;n Electr&oacute;nica</h5>
                            <div id="divFacturacion">

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt-4">
                            <h5>Otros</h5>
                            <div id="divOtros">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row layout-top-spacing">      
        <div class="col-xl-4 col-lg-4 col-md-4 col-xs-12 layout-spacing">
            <div class="widget widget-table-one">
                <div class="widget-heading" style="justify-content: space-between; align-items: center;">
                    <h5 class="">Menos Ventas</h5>
                    <span style="font-size: 11px;">
                        <?php
                            echo fechaLatinoShort($lunes_anterior).' - '.fechaLatinoShort($domingo_anterior);
                        ?>
                    </span>
                </div>
                <div class="widget-content top-menos-ventas">
                    
                </div>
            </div>
        </div>
    
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                        <h4>Empresas</h4>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                        <a href="crear_empresa.php" class="btn btn-primary">Nueva Empresa</a>
                    </div>
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <hr/>
                    </div>
                </div> 
                <div class="table-responsive mb-4 mt-4">
                    <table id="style-3" class="table style-3  table-hover">
                        <thead>
                            <tr>
                                <th class="checkbox-column text-center"> Record Id </th>
                                <th class="text-center">Image</th>
                                <th>Nombre</th>
                                <th>alias</th>
                                <th class="text-center">Asesor</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resp = $Clempresa->lista();
                            foreach ($resp as $categoria) {
                                $query = "SELECT nombre, apellido, imagen FROM tb_usuarios WHERE cod_usuario = ".$categoria['user_create'];
                                $respUser = Conexion::buscarRegistro($query);
                                if($respUser){
                                    $imgUser = $files.'digital-mind/'.$respUser['imagen'];
                                    $nameUser = $respUser['nombre']." ".$respUser['apellido'];
                                }else{
                                    $imgUser = 'assets/img/90x90.jpg';
                                    $nameUser = "Desconocido";
                                }
                                
                                $imagen = $files.$categoria['alias'].'/'.$categoria['logo'];
                                $badge='primary';
                                if($categoria['estado'] == 'I')
                                    $badge='danger';
                                echo '<tr>
                                    <td class="checkbox-column text-center"> '.$categoria['cod_empresa'].' </td>
                                    <td class="text-center">
                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                    </td>
                                    <td>'.$categoria['nombre'].'</td>
                                    <td>'.$categoria['alias'].'</td>
                                    <td class="text-center">
                                        <div class="avatar avatar-sm" title="'.$nameUser.'">
                                            <img alt="avatar" src="'.$imgUser.'" class="rounded-circle" />
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($categoria['estado']).'</span></td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li><a href="crear_empresa.php?id='.$categoria['alias'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                            <li><a href="javascript:void(0);" data-value="'.$categoria['cod_empresa'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                            <li><a data-web="'.$categoria['url_web'].'" data-value="'.$categoria['alias'].'" data-folder="'.$categoria['folder'].'" href="javascript:void(0);" class="bs-tooltip btnMore" data-toggle="tooltip" data-placement="top" title="Más" data-original-title="Más"><i data-feather="more-horizontal"></i></a></li>
                                            <li><a data-value="'.$categoria['alias'].'" href="javascript:void(0);" class="bs-tooltip btnConfig" data-toggle="tooltip" data-placement="top" title="Configuraciones" data-original-title="Configuraciones"><i data-feather="settings"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>