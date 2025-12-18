<?php
class cl_sucursales
{
		public $session;
		public $cod_sucursal, $cod_empresa, $nombre, $direccion, $latitud, $longitud, $hora_ini, $hora_fin, $intervalo, $emisor, $telefono, $correo, $estado, $distancia_km, $cantidad, $cod_producto,$cod_ciudad,$sku, $image_min, $delivery, $pickup, $envio_grava_iva, $insite;
		var $pcod_sucursal, $imagen;
		public $cod_sucursal_costo_envio, $base_dinero, $base_km, $adicional_km;
		public $id, $distancia_ini, $distancia_fin, $precio;
		
		public function __construct($pcod_sucursal=null)
		{
			if($pcod_sucursal != null)
				$this->pcod_sucursal = $pcod_sucursal;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function all(){
			$query = "SELECT * FROM tb_sucursales WHERE cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lista(){
			$query = "SELECT * FROM tb_sucursales WHERE estado IN('A','I') AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaActivas(){
			$query = "SELECT * FROM tb_sucursales WHERE estado= 'A' AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaByEmpresa($cod_empresa){
			$query = "SELECT * FROM tb_sucursales WHERE estado IN('A','I') AND cod_empresa = ".$cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function isMySucursal($cod_sucursal){
			$query = "SELECT * FROM tb_sucursales WHERE estado IN('A','I') AND cod_sucursal=".$cod_sucursal." AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
		}
		
		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "INSERT INTO tb_sucursales(cod_empresa,cod_ciudad, nombre, direccion, latitud, longitud, hora_ini, hora_fin, intervalo, emisor, telefono, correo, estado, distancia_km,image, image_min, delivery, pickup, envio_grava_iva, insite) ";
        	$query.= "VALUES($this->cod_empresa, '$this->cod_ciudad', '$this->nombre', '$this->direccion', '$this->latitud', '$this->longitud', '$this->hora_ini', '$this->hora_fin', '$this->intervalo', '$this->emisor', '$this->telefono', '$this->correo', '$this->estado', $this->distancia_km,'$this->imagen', '$this->image_min', '$this->delivery', '$this->pickup', '$this->envio_grava_iva', '$this->insite')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_sucursales SET nombre='$this->nombre',cod_ciudad='$this->cod_ciudad', direccion='$this->direccion', latitud='$this->latitud', longitud='$this->longitud', hora_ini='$this->hora_ini', hora_fin='$this->hora_fin', intervalo='$this->intervalo', emisor='$this->emisor', telefono='$this->telefono', correo='$this->correo', estado='$this->estado', distancia_km=$this->distancia_km, delivery = $this->delivery, pickup = $this->pickup, envio_grava_iva = $this->envio_grava_iva, insite = $this->insite WHERE cod_sucursal = $this->cod_sucursal";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function setImage($name, $scale, $cod_sucursal){
		    $option = "image='$name'";
		    if($scale=="min")
		        $option = "image_min='$name'";
		    $query = "UPDATE tb_sucursales SET $option WHERE cod_sucursal = $cod_sucursal";
		    return Conexion::ejecutar($query,NULL);
		}
		
		public function setTransferImage($name, $cod_sucursal){
		    $query = "UPDATE tb_sucursales SET transferencia_img = '$name' WHERE cod_sucursal = $cod_sucursal";
		    return Conexion::ejecutar($query,NULL);
		}
		
		public function setBannerImage($name, $cod_sucursal){
		    $query = "UPDATE tb_sucursales SET banner_xl = '$name' WHERE cod_sucursal = $cod_sucursal";
		    return Conexion::ejecutar($query,NULL);
		}

		public function set_estado($cod_sucursal, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_sucursales SET estado='$estado' WHERE cod_sucursal = $cod_sucursal";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getCouriers($cod_sucursal){
			$query = "SELECT c.*, s.validar_cobertura
					FROM tb_sucursal_courier s, tb_courier c
					WHERE s.cod_courier = c.cod_courier
					AND s.cod_sucursal = $cod_sucursal
					AND s.estado = 'A'
					ORDER BY s.prioridad ASC";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function getCouriersIn($sucursales){
			$query = "SELECT c.*, s.validar_cobertura
					FROM tb_sucursal_courier s, tb_courier c
					WHERE s.cod_courier = c.cod_courier
					AND s.cod_sucursal in  ({$sucursales})
					AND s.estado = 'A'
					GROUP BY c.cod_courier
					ORDER BY s.prioridad ASC";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function getgacelaSucursal($cod_sucursal){
            $query = "SELECT * FROM tb_gacela_sucursal gs WHERE gs.cod_sucursal = $cod_sucursal AND gs.estado = 'A' LIMIT 0,1";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }

		public function getPickerSucursal($cod_sucursal){
            $query = "SELECT * FROM tb_picker_sucursal
						WHERE cod_sucursal = $cod_sucursal AND estado = 'A' LIMIT 0,1";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getLaarSucursal($cod_sucursal){
            $query = "SELECT * FROM tb_laar_sucursal WHERE cod_sucursal = ".$cod_sucursal." and estado = 'A' limit 0,1";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        
        public function getgacelaSucursalAmbiente($cod_sucursal, $ambiente)
        {
            $query = "SELECT * FROM tb_gacela_sucursal gs WHERE gs.cod_sucursal = $cod_sucursal AND gs.ambiente = '$ambiente'";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }

        public function getpickerSucursalAmbiente($cod_sucursal, $ambiente)
        {
            $query = "SELECT * FROM tb_picker_sucursal gs WHERE gs.cod_sucursal = $cod_sucursal AND gs.ambiente = '$ambiente'";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
		public function get($cod_sucursal)
		{
			$query = "select * from tb_sucursales where cod_sucursal = ".$cod_sucursal;
			$row = Conexion::buscarRegistro($query);
			if($row)
			{
				$this->cod_sucursal = $row['cod_sucursal'];
				$this->cod_empresa = $row['cod_empresa'];
				$this->nombre = $row['nombre'];
				$this->direccion = $row['direccion'];
				$this->latitud = $row['latitud'];
				$this->longitud = $row['longitud'];
				$this->hora_ini = $row['hora_ini'];
				$this->hora_fin = $row['hora_fin'];
				$this->intervalo = $row['intervalo'];
				$this->emisor = $row['emisor'];
				$this->telefono = $row['telefono'];
				$this->correo = $row['correo'];
				$this->hora_fin = $row['hora_fin'];
				$this->estado = $row['estado'];
				$this->imagen = $row['image'];
				$this->cod_ciudad = $row['cod_ciudad'];
				return true;
			}
			else
			{
				return false;
			}
		}

		public function getInfo($cod_sucursal)
		{
			$query = "SELECT * from tb_sucursales where cod_sucursal = ".$cod_sucursal;
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getArray($cod_sucursal, &$array)
		{
			$query = "SELECT s.* from tb_sucursales s where s.cod_sucursal = ".$cod_sucursal;
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

		/*DISPONIBILIDAD*/
		public function lista_disponibilidad(){
			$fecha = fecha_only()." 00:00:00";
			$query = "SELECT f.*, s.nombre 
					FROM tb_sucursal_festivos f, tb_sucursales s 
					WHERE f.cod_sucursal = s.cod_sucursal
					AND f.fecha_inicio >= '$fecha' 
					AND s.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lista_disponibilidadBySucursal($cod_sucursal){
			$fecha = fecha_only()." 00:00:00";
			$query = "SELECT f.*, s.nombre 
					FROM tb_sucursal_festivos f, tb_sucursales s 
					WHERE f.cod_sucursal = s.cod_sucursal
					AND f.fecha_inicio >= '$fecha' 
					AND s.cod_sucursal = $cod_sucursal";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear_disponibilidad($cod_sucursal, $fecha, $hora_ini, $hora_fin){
			$fecha_inicio = $fecha." ".$hora_ini;
			$fecha_fin    = $fecha." ".$hora_fin;
			$query = "INSERT INTO tb_sucursal_festivos(cod_sucursal, fecha, hora_inicio, hora_fin, fecha_inicio, fecha_fin) ";
        	$query.= "VALUES($cod_sucursal, '$fecha', '$hora_ini', '$hora_fin', '$fecha_inicio', '$fecha_fin')";
        	if(Conexion::ejecutar($query,NULL)){
        		//$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function eliminar_disponibilidad($cod_sucursal_festivos){
			$query = "DELETE FROM tb_sucursal_festivos WHERE cod_sucursal_festivos = $cod_sucursal_festivos";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		/*--NUEVO--*/
		public function eliminar_disponibilidadSucursal($cod_sucursal){
			$query = "DELETE FROM tb_sucursal_disponibilidad WHERE cod_sucursal = $cod_sucursal";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function crear_disponibilidadSucursal($cod_sucursal, $dia, $hora_ini, $hora_fin){
			$query = "INSERT INTO tb_sucursal_disponibilidad(cod_sucursal, dia, hora_ini, hora_fin) ";
        	$query.= "VALUES($cod_sucursal, $dia, '$hora_ini', '$hora_fin')";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function getDisponibilidadDay($cod_sucursal,$dia)
		{
		    $query = "select * from tb_sucursal_disponibilidad where cod_sucursal = '".$cod_sucursal."' and dia = '".$dia."' ";
		    $resp = Conexion::buscarRegistro($query);
            return $resp;
		}
		/*--NUEVO--*/
		
		//INICIO STOCK
		public function getStockBySucursal($cod_sucursal){
		    $query = "SELECT vps.cod_producto, vps.nombre, vps.sku, vps.alias, COALESCE(s.cantidad, 0) as cantidad
                        FROM vw_producto_sucursal vps
                        LEFT JOIN tb_stock s
                        ON vps.sku = s.sku
                        AND vps.cod_sucursal = s.cod_sucursal
                        WHERE vps.cod_sucursal IN (0,$cod_sucursal)
                        AND vps.cod_empresa = $this->cod_empresa
						GROUP BY vps.sku";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function existeStock($cod_sucursal, $sku){
		    $query = "SELECT * FROM tb_stock WHERE cod_sucursal = $cod_sucursal AND sku = '$sku'";
			$resp = Conexion::buscarRegistro($query);
			if($resp)
			    return true;
			else
			    return false;
		}
		
		public function insertarStock(){
		    $query = "INSERT INTO tb_stock(sku, cod_sucursal, cantidad) ";
        	$query.= "VALUES('".$this->sku."', ".$this->cod_sucursal.", ".$this->cantidad.")";
        	if(Conexion::ejecutar($query,NULL)){
        		//$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function editarStock(){
		    $query = "UPDATE tb_stock SET cantidad = $this->cantidad WHERE cod_sucursal = $this->cod_sucursal AND sku = '$this->sku'";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		//FIN STOCK
		
		public function getProvincias()
      {
          $query = "SELECT provincia FROM `tb_ciudades` GROUP by provincia order by provincia ASC";
          $row = Conexion::buscarVariosRegistro($query);
          return $row;
          
      }
      
      public function getInfoByCiudad($cod_ciudad)
      {
          $query = "SELECT * FROM `tb_ciudades` where cod_ciudad='$cod_ciudad' ";
          $row = Conexion::buscarRegistro($query);
          return $row;
          
      }
      
      public function getCiudades($provincia)
      {
          $query = "SELECT * FROM `tb_ciudades` where provincia = '$provincia' and estado = 'A' order by nombre ASC ";
          $row = Conexion::buscarVariosRegistro($query);
          return $row;
          
      }

	  	public function setProgramarPedido($cod_sucursal, $programa, $cod_empresa, $diasProgramar){
			$query = "UPDATE tb_sucursales
						SET programar_pedido = $programa 
						WHERE cod_sucursal = $cod_sucursal";
			Conexion::ejecutar($query, null);
			// echo $query;

			$query = "UPDATE tb_empresas
						SET cant_dias_programar_pedido = $diasProgramar
						WHERE cod_empresa = $cod_empresa";
			// echo $query;
			return Conexion::ejecutar($query, null);
		}

		public function getFestivosHoy($cod_sucursal){
			$fecha = fecha();
			$query = "SELECT *
						FROM tb_sucursal_festivos
						WHERE cod_sucursal = $cod_sucursal
						AND fecha_inicio <= '$fecha'
						AND fecha_fin >= '$fecha'
						ORDER BY cod_sucursal_festivos DESC
						LIMIT 0, 1";
			return Conexion::buscarRegistro($query);
		}

		public function guardarCierreFestivo($cod_sucursal, $tiempo, &$id, &$hora_fin){
			$fecha = fecha_only();
			$fecha_inicio = fecha();
			$fecha_fin = AddIntervalo($fecha_inicio, $tiempo);
			$fyhIni = explode(" ", $fecha_inicio);
			$fyhFin = explode(" ", $fecha_fin);
			$hora_inicio = $fyhIni[1];
			$hora_fin = $fyhFin[1];

			$query = "INSERT INTO tb_sucursal_festivos(cod_sucursal, fecha, hora_inicio, hora_fin, fecha_inicio, fecha_fin)
						VALUES($cod_sucursal, '$fecha', '$hora_inicio', '$hora_fin', '$fecha_inicio', '$fecha_fin')";
			if(Conexion::ejecutar($query, null)){
				$id = Conexion::lastId();
				return true;
			}
			return false;
		}

		public function quitarCierreFestivos($cod_sucursal_festivos){
			$query = "DELETE 
						FROM tb_sucursal_festivos 
						WHERE cod_sucursal_festivos = $cod_sucursal_festivos";
			return Conexion::ejecutar($query, null);
		}

		public function courierValidarCobertura($cod_courier, $cod_sucursal, $estado){
			$query = "UPDATE tb_sucursal_courier
						SET validar_cobertura = $estado
						WHERE cod_sucursal = $cod_sucursal
						AND cod_courier = $cod_courier";
			return Conexion::ejecutar($query, null);
		}
		
		public function gurdarPosicionesCouriers($cod_courier, $cod_sucursal, $prioridad){
			$query = "UPDATE tb_sucursal_courier
						SET prioridad = $prioridad
						WHERE cod_sucursal = $cod_sucursal
						AND cod_courier = $cod_courier";
			return Conexion::ejecutar($query, null);
		}

		public function getCourierOffice($cod_sucursal, $cod_courier){
			$query = "SELECT * 
						FROM tb_sucursal_courier 
						WHERE cod_sucursal = $cod_sucursal
						AND cod_courier = $cod_courier";
			return Conexion::buscarRegistro($query);
		}

		public function setCourierOffice($cod_sucursal, $cod_courier, $estado, $update=false){
			if($update){
				$query = "UPDATE tb_sucursal_courier
							SET estado = '$estado'
							WHERE cod_sucursal = $cod_sucursal
							AND cod_courier = $cod_courier";
				Conexion::ejecutar($query, null);
				return true;
			}
			$query = "INSERT INTO tb_sucursal_courier
						SET estado = '$estado', cod_sucursal = $cod_sucursal, cod_courier = $cod_courier, detalle=''";
			Conexion::ejecutar($query, null);
			return true;
		}
		
		public function getFlotaOffice($cod_sucursal, $cod_flota){
			$query = "SELECT * 
						FROM tb_sucursal_flota 
						WHERE cod_sucursal = $cod_sucursal
						AND cod_flota = $cod_flota";
			return Conexion::buscarRegistro($query);
		}

		public function setFlotaOffice($cod_sucursal, $cod_flota){
			$query = "INSERT INTO tb_sucursal_flota
						SET cod_sucursal = $cod_sucursal, cod_flota = $cod_flota";
			return Conexion::ejecutar($query, null);
		}
		
		public function deleteFlotaOffice($cod_sucursal, $cod_flota){
		    $query = "DELETE FROM tb_sucursal_flota
							WHERE cod_sucursal = $cod_sucursal
							AND cod_flota = $cod_flota";
			return Conexion::ejecutar($query, null);
		}

		public function getPedidosYaSucursalAmbiente($cod_sucursal, $ambiente) {
            $query = "SELECT * 
						FROM tb_pedidosya_sucursales gs 
						WHERE gs.cod_sucursal = $cod_sucursal 
						AND gs.ambiente = '$ambiente'";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getCoberturaByOffices($office_id){
            $poligonos = [];
            $query = "SELECT ST_AsGeoJSON(sc.zone) as vertices 
                        FROM tb_sucursal_cobertura sc WHERE sc.cod_sucursal = $office_id";
            $resp = Conexion::buscarVariosRegistro($query);
            foreach($resp as $poligono){
                $data = json_decode($poligono['vertices'], true);
                $poligonos[] = $data['coordinates'][0];
            }
            return $poligonos;
        }
        
        public function deletePolygonsByBusiness(){
            $query = "DELETE sc
                    FROM tb_sucursal_cobertura sc
                    INNER JOIN tb_sucursales s ON s.cod_sucursal = sc.cod_sucursal
                    WHERE s.cod_empresa = ".$this->cod_empresa;
            return Conexion::ejecutar($query, null);
        }
        
        public function saveCoberturaInOffice($office_id, $vertices){
            $zone = implode(", ", $vertices);
            $query = "INSERT INTO tb_sucursal_cobertura (cod_sucursal, zone)
                        VALUES($office_id, ST_GeomFromText('POLYGON(($zone))') )";
            //return Conexion::ejecutar($query, null);
            Conexion::ejecutar($query, null);
            return $query;
        }

		public function getCostosEnvio() {
			$query = "SELECT s.cod_sucursal, s.nombre, IFNULL(sce.cod_sucursal_costo_envio, 0) as cod_sucursal_costo_envio, IFNULL(sce.base_dinero, 0) as base_dinero, IFNULL(sce.base_km, 0) as base_km, IFNULL(sce.adicional_km, 0) as adicional_km 
						FROM tb_sucursales s
						LEFT JOIN tb_sucursal_costo_envio sce ON s.cod_sucursal = sce.cod_sucursal
						WHERE s.estado IN ('A','I')
						AND s.cod_empresa = {$this->cod_empresa}";
			return Conexion::buscarVariosRegistro($query);
		}

		public function saveCostosEnvio() {
			$query = "INSERT INTO tb_sucursal_costo_envio
						SET cod_sucursal = {$this->cod_sucursal},
						base_dinero = {$this->base_dinero},
						base_km = {$this->base_km},
						adicional_km = {$this->adicional_km}";
			return Conexion::ejecutar($query, null);
		}
		
		public function editCostosEnvio() {
			$query = "UPDATE tb_sucursal_costo_envio
						SET base_dinero = {$this->base_dinero},
						base_km = {$this->base_km},
						adicional_km = {$this->adicional_km}
						WHERE cod_sucursal_costo_envio = {$this->cod_sucursal_costo_envio}";
			return Conexion::ejecutar($query, null);
		}

		public function getCostosEnvioRango() {
			$query = "SELECT * 
						FROM tb_sucursal_costo_envio_rango
						WHERE cod_sucursal = {$this->cod_sucursal}";
			return Conexion::buscarVariosRegistro($query, null);
		}

		public function saveCostosEnvioRango() {
			$query = "INSERT INTO tb_sucursal_costo_envio_rango
						SET cod_sucursal = {$this->cod_sucursal},
						distancia_ini = {$this->distancia_ini},
						distancia_fin = {$this->distancia_fin},
						precio = {$this->precio}";
			return Conexion::ejecutar($query, null);
		}

		public function editCostosEnvioRango() {
			$query = "UPDATE tb_sucursal_costo_envio_rango
						SET distancia_ini = {$this->distancia_ini},
						distancia_fin = {$this->distancia_fin},
						precio = {$this->precio}
						WHERE id = {$this->id}";
			return Conexion::ejecutar($query, null);
		}

		public function removeCostosEnvioRango($id) {
			$query = "DELETE FROM tb_sucursal_costo_envio_rango
						WHERE id = $id";
			return Conexion::ejecutar($query, null);
		}
}
?>