<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_usuarios.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);

if(isset($_GET['alias'])){  //NO ES POR SESSION
    $empresa = $Clempresas->getByAlias($_GET['alias']);
}else{                      //ES POR SESSION
    $session = getSession();
    $empresa = $Clempresas->get($session['cod_empresa']);
}

if($empresa){
    $cod_empresa = $empresa['cod_empresa'];
    $api = $empresa['api_key'];
}else{
    header("location:index.php");
}

$htmlSucursales = "";
$sucursales = $Clsucursales->listaByEmpresa($cod_empresa);
if($sucursales){
    foreach ($sucursales as $sucursal) {
        $htmlSucursales.='<option value="'.$sucursal['cod_sucursal'].'">'.$sucursal['nombre'].'</option>';
    }
}

$htmlUsuarios = "";
$usuarios = $Clusuarios->listaRegistradosByEmpresa($cod_empresa);
if($usuarios){
    foreach ($usuarios as $usuario) {
        $htmlUsuarios.='<option value="'.$usuario['cod_usuario'].'">'.$usuario['nombre'].' '.$usuario['apellido'].'</option>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <!-- END PAGE LEVEL CUSTOM STYLES -->
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(); ?>
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
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6" style="height: 980px;">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Simulador Asignación Ordenes</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                                <div class="row">
                                    <input type="hidden" id="txtDate" value="<?= fecha()?>">
                                    <input type="hidden" id="key" value="<?= $api?>">
                                    <div class="col-sm-4 col-12">
                                        <label>Sucursal</label>
                                        <select class="form-control" name="cmbSucursal" id="cmbSucursal">
                                            <?= $htmlSucursales?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-12">
                                        <label>Usuarios</label>
                                        <select class="form-control" name="cmbUsuario" id="cmbUsuario">
                                            <?= $htmlUsuarios?>
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <hr/>
                            </div>
                            
                            <!---->
                            <div class="mb-4 mt-4">
                                <div class="col-xl-7 col-md-7 col-sm-7 col-12">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="infoMotorizado">
                                        <h3 style="border-bottom: 1px solid #dee2e6;">Resultados</h3> 
                                    </div>
                                    <script id="no-data" type="text/x-handlebars-template">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
                                            <lottie-player src="https://assets9.lottiefiles.com/private_files/lf30_oqpbtola.json"  background="transparent"  speed="1"  style="height: 300px;" autoplay></lottie-player>
                                            <p style="color: #999;">No hay cobertura</p>
                                    	</div>
                                    </script>
                                    <script id="loading-data" type="text/x-handlebars-template">
                                        <div class="col-md-12" style="margin-top:30px;padding: 0; text-align:center;">
                                			<h4><i class="fa fa-spinner fa-spin"></i> Cargando información...</h4>
                                		</div>
                                    </script>
                                    <script id="ordenes-item" type="text/x-handlebars-template">
                                        <div class="row">
                                            {{#each this}}
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <p>{{nombre}}</p>
                                                <p>{{referencia}}</p>
                                                <p>{{fecha}}</p>
                                                <p>${{total}}</p>
                                                <p style="text-align: right;"><button class="btn btn-primary btnNotificarOrden" 
                                                    data-id="{{cod_orden}}" data-sucursal="{{cod_sucursal}}">Notificar</button></p>
                                                <hr/>
                                            </div>
                                            {{/each}}
                                        </div>
                                    </script>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 lstResultado">
                                    </div>
                                   
                                   
                                </div>
                                <div class="col-xl-5 col-md-5 col-sm-5 col-12">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="">
                                        <h3 style="border-bottom: 1px solid #dee2e6;">Nueva Orden</h3> 
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="">
                                        <textarea id="jsonNewOrder" class="form-control" style="height: 500px;"></textarea>
                                        <p class="text-right mt-4"><button class="btn btn-primary btnSendOrden">Nueva Orden</button></p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <?php js_mandatory(); ?>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- Mapas -->
    <script type="text/javascript">
        var marker;
        /*TEMPLATES EN EL MISMO ARCHIVO*/
        var templateloading = Handlebars.compile($("#loading-data").html()); //DEBEN SER CREADOS CON LA ETIQUETA SCRIPT
        var templatenodata = Handlebars.compile($("#no-data").html());
        var templateOrdenes = Handlebars.compile($("#ordenes-item").html());

    $(document).ready(function(){
        getDataNewOrder();
        getOrdenesEntrantes();
    });
    
    function getOrdenesEntrantes(){
        let cod_sucursal = $("#cmbSucursal").val();
        let apiKey = $("#apikey").val();
        var parametros = {
          "estado": "ENTRANTE",
          "tipo": 1,
          "cod_sucursal": cod_sucursal
        }
        console.log(parametros);
	    $.ajax({
	        beforeSend: function(){
                OpenLoad("Buscando ordenes, por favor espere...");
             },
	        url:'controllers/controlador_ordenes.php?metodo=getOrdenesEntrantes',
	        type: "GET",
	        data: parametros,
	        headers: {
	            'Api-Key':apiKey
	        },
	        success: function(response){
	            console.log(response);
	            if(response.success==1){
	                $(".lstResultado").html(templateOrdenes(response.data));
	            }
	        },
	        error: function(data){
	            alert("Esta empresa no tiene sucursales error");
	        },
	        complete: function()
	        {
	          CloseLoad();
	        }
	    });
	}
	
	$("body").on("click",".btnNotificarOrden", function(){
	    let id = $(this).data("id");
	    let sucursal = $(this).data("sucursal");
	    
	    var parametros = {
          "id": id,
          "cod_sucursal": sucursal
        }
        console.log(parametros);
	    $.ajax({
	        beforeSend: function(){
                OpenLoad("Buscando ordenes, por favor espere...");
             },
	        url:'controllers/controlador_ordenes.php?metodo=renotificarOrden',
	        type: "GET",
	        data: parametros,
	        success: function(response){
	            console.log(response);
	            if(response.success==1){
	                notify(response.mensaje, "success", 2);
	            }
	        },
	        error: function(data){
	            notify("Error al notificar, por favor intentelo nuevamente", "success", 2);
	        },
	        complete: function()
	        {
	          CloseLoad();
	        }
	    });
    });
    
    $("body").on("click",".btnSendOrden", function(){
        let JsonOrden = $("#jsonNewOrder").val();
        console.log(JsonOrden);
        fetch(`https://api.mie-commerce.com/v3/ordenes`,{
                method: 'POST',
                headers: {
                  'Api-Key': $("#key").val()
                },
                body: JsonOrden
            })
            .then(res => res.json())
            .then(response => {
                if(response.success == 1){
                    notify(response.mensaje, "success", 2);
                }else{
                    notify(response.mensaje, "error", 2);
                }
            })
            .catch(error=>{
                console.log(error);
                statusError("No se pudo procesar la orden, por favor intentelo mas tarde");
            });
    });
    
    function getDataNewOrder(){
        let sucursal = $("#cmbSucursal").val();
        let usuario = $("#cmbUsuario").val();
        let fecha = $("#txtDate").val();

        let data = `{"cod_usuario":${usuario},"cod_sucursal":${sucursal},"telefono":"0988911516","base0":0,"base12":15.51,"subtotal":11.96,"descuento":0,"iva":1.86,"total":17.37,"metodoEnvio":[{"tipo":"envio","precio":3.55,"lat":"-2.145977422135318","lon":"-79.86560314893721","direccion":"C. Segunda 9, Samborondón 092301, Ecuador","referencia":"familia vela","programado":0,"hora":"${fecha}"}],"metodoPago":[{"tipo":"E","monto":17.37}],"productos":[{"id":"611","precio":"13.40","cantidad":"1","descripcion":"Orden de prueba por favor anular"}],"origen":"WEB","comentarios":"","paymentId":"","paymentAuth":""}`;

        $("#jsonNewOrder").val(data);
    }
    
    $("#cmbSucursal").on("change", function(){
        getDataNewOrder();
    });
    $("#cmbUsuario").on("change", function(){
        getDataNewOrder();
    });
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>