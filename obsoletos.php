<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

$Clempresas = new cl_empresas(null);
$datos = $Clempresas->lista();

$html.= '<select id="cmbEmpresas">';
		foreach ($datos as $d) {
			$html.= '<option value="'.$d['cod_empresa'].'">'.$d['nombre'].'</option>';
		}			
$html.= '</select><br>';

?>

<!DOCTYPE html>
<html>
<head>
	<title>Obsoletos</title>
</head>
<body>
	<span>Empresa: </span>
	<?php echo $html;?>
	<span>Accion: </span>
	<select id="cmbTipo">
		<option value="ordenes">Ordenes</option>
		<option value="productos">Productos</option>
	</select><br>
	<button onclick="eliminar(event);">Eliminar</button>
<script src="assets/js/libs/jquery-3.1.1.min.js"></script>
<script type="text/javascript">
	function eliminar(e){
		e.preventDefault();
		var cod_empresa = $("#cmbEmpresas").val();
		var tipo = $("#cmbTipo").val();
		alert(cod_empresa);
		alert(tipo);
		if(confirm("Estás seguro?")){
			/*$.ajax({
	            url: 'eliminar_obsoletosDM.php?cod_empresa='+cod_empresa+'&eliminar='+tipo,
	            type:'GET',
	        });*/
	        alert("Entro a eliminar");
		}			
	}
</script>
</body>
</html>