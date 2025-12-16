<?php
require_once "../funciones.php";
//Claseso
require_once "../clases/cl_laar.php";
$token ="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IjU2ODQiLCJyb2xlIjoiMSIsImFjdG9ydCI6IjQ3OTczIiwiYWx0ZXJubyI6IjciLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2FjY2Vzc2NvbnRyb2xzZXJ2aWNlLzIwMTAvMDcvY2xhaW1zL2lkZW50aXR5cHJvdmlkZXIiOiJBU1AuTkVUIElkZW50aXR5IiwiTGFhckNvdXJpZXIiOiJBcGktR2VuZXJhY2lvbi1HdWlhcyIsIkRCIjoiMTItMDQtOTAiLCJuYmYiOjE2MTIyMTMwMTQsImV4cCI6MTYxMjIyMDIxNCwiaWF0IjoxNjEyMjEzMDE0LCJpc3MiOiJhcGkubGFhcmNvdXJpZXIuY29tOjkwMjgiLCJhdWQiOiJhcGkubGFhcmNvdXJpZXIuY29tOjkwMjgifQ.mhjqOqC2NWKK9zkKMbVe6G3oAmlOfohE4DZKKGGzRQc";
$Cl_laar = new cl_laar($token);
$session = getSession();
controller_create();


function ver(){
$Cl_laar = new cl_laar();
$guia="SISLC42091216";
$data = $Cl_laar->DataGuia($guia);
$return['success'] = 1;
$return['mensaje'] =$data;
return $return;
}

function crearGuia()
{
$token ="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IjU2ODQiLCJyb2xlIjoiMSIsImFjdG9ydCI6IjQ3OTczIiwiYWx0ZXJubyI6IjciLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2FjY2Vzc2NvbnRyb2xzZXJ2aWNlLzIwMTAvMDcvY2xhaW1zL2lkZW50aXR5cHJvdmlkZXIiOiJBU1AuTkVUIElkZW50aXR5IiwiTGFhckNvdXJpZXIiOiJBcGktR2VuZXJhY2lvbi1HdWlhcyIsIkRCIjoiMTItMDQtOTAiLCJuYmYiOjE2MTIyNzkwMjAsImV4cCI6MTYxMjI4NjIyMCwiaWF0IjoxNjEyMjc5MDIwLCJpc3MiOiJhcGkubGFhcmNvdXJpZXIuY29tOjkwMjgiLCJhdWQiOiJhcGkubGFhcmNvdXJpZXIuY29tOjkwMjgifQ.F4RM1TSrXOyQj_9NRw8Z13OYogDbpAVH_rDPqwiMSAE";
$Cl_laar = new cl_laar($token);  
$orden ="";
    $data = $Cl_laar->crearGuia($orden);
        if(isset($data->guia)){
            $guia=$data->guia;
            $cod_courier = 2; // TIPO LAAR
           /* $isLaar=$Clordenes->order_updCourier($id,$guia,$cod_courier);
            if($isLaar)
            {
                 $return['success'] = 1;
                $return['mensaje'] ="Guia creada correctamente.";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al definir la orden, por favor vuelva a intentarlo";
            } */
              $return['success'] = 1;
                $return['mensaje'] ="Guia creada correctamente.";
                $return['guia'] =$guia;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la guia, por favor vuelva a intentarlo";
        }
        $return['info']=$data;
        
        return $return;
}
?>