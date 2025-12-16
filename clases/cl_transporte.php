<?php
//$hostname = '{mail.danilorestaurante.com:993/imap/ssl}INBOX.Sent';
class cl_transporte
{       
  
    var $cod_empresa_costo_envio, $cod_empresa, $base_dinero, $base_km, $adicional_km, $tipo, $peso_maximo;
        public function actualizar(){
            $query = "UPDATE tb_empresa_costo_envio
                SET base_dinero = '$this->base_dinero', base_km = $this->base_km, adicional_km = '$this->adicional_km', peso_maximo = '$this->peso_maximo'
                WHERE cod_empresa_costo_envio = $this->cod_empresa_costo_envio";
            return Conexion::ejecutar($query, null);
        }

}
