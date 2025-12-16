<?php

echo '
	<link href="croppie/croppie.css" rel="stylesheet">
	<!-- INICIO EDITAR MODAL CLIENTE -->      
    <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-hidden="true" style="z-index: 9999999 !important;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="myModalInfo">
                
                  <div class="modal-header">

                          <h4 class="modal-title" id="myTitleCroppie">Recorte</h4>
                    </div>
                    <div class="modal-body" id="myModalBody">
                    <div class=""> 
                         <div class="col-md-12 col-sm-12">
                              <img id="my-image" src="#" style="display: none;"/>

                              <button class="crop-rotate" data-deg="-90">Rotate Left</button>
                              <button class="crop-rotate" data-deg="90">Rotate Right</button>

                              <button id="crop-get" class="btn btn-warning"  >Recortar</button>
                          </div>
                    
                        <div class="modal-footer" id="footer_cliente_guardar" >
                          
                        </div>
                      
                     </div> 
                   </div>
                
            </div>
        </div>
    </div>  
	<!-- FIN EDITAR MODAL CLIENTE -->  
';


?>