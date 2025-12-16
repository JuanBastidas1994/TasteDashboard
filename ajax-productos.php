<?php
require_once "funciones.php";
$table = <<<EOT
 (
    SELECT p.cod_producto, p.image_max, p.nombre, p.precio, p.desc_corta, p.estado, p.alias
				FROM tb_productos p
				WHERE p.estado IN('A','I') 
				AND p.cod_producto_padre = 0
				AND p.cod_empresa = 24
 ) temp
EOT;

$primaryKey = 'cod_producto';

$columns = array(
   array( 'db' => 'cod_producto',          'dt' => 0 ),
   array( 'db' => 'image_max',        'dt' => 1 ),
   array( 'db' => 'nombre',    'dt' => 2 ),
   array( 'db' => 'precio',    'dt' => 3 ),
   array( 'db' => 'desc_corta',    'dt' => 4 ),
   array( 'db' => 'estado',    'dt' => 5 ),
   array( 'db' => 'alias',    'dt' => 6 ),
);

$sql_details = array(
    'type'=> 'Mysql',
    'user' => usuario,
    'pass' => contrasena,
    'db'   => db,
    'host' => servidor
);

header("Content-type:application/json; charset=utf-8");
require( 'plugins/table/datatable/ssp.class.php' );
echo json_encode(
   SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);

?>