<div class="x_content" id="backPip">
    <div id="containerPip" >
        
        
        
        <div class="p-2">
            
            <div id="purchaseStart">
                <input type="search" class="form-control purchaseCodeInput" placeholder="Escanea el qr del cliente" style="font-size: 11px;"/>
                <span id="messageError"  style="color: red; font-size: 12px;"></span>
            </div>
            
            <div id="purchaseLoading" class="text-center"  style="display:none;">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-success align-self-center loader-md"></div>
                </div>
                <h5> Cargando...</h5>
            </div>
            
            <div id="purchaseClient" class="text-left"  style="display:none;" >
                    <!--<button id="backButton" class="btn btn-link p-0 m-0" style="font-size: 14px;"> < </button>-->
                <div class="row p-2">
                    <div class="col-4">
                        <div style="font-size: 12px;" class="text-ellipsis">
                            <span id="clientName">Dylan bastidas Navarrete</span>
                        </div>
                        <div style="font-size: 10px;">
                            <span id="clientDni">0952423606</span>
                        </div>
                        
                        <h3 style="padding: 0; margin: 0;" >
                            <span id="clientTotalSaldoReal">$0</span>
                        </h3>
                    </div>
                    
                    <div class="col-8">
                        <div>
                            <input type="text" class="form-control" style="padding: 2px 8px; height: 30px; font-size: 11px;" 
                                    id="invoiceNumber" placeholder="Secuencial de factura" />
                        </div>
                        <div>
                            <div class="row mt-1">
                                <span id="clientCod" style="display:none;"></span>
                                <div class="col-4">
                                    <label style="font-size: 10px;" class="p-0 m-0">Total </label>
                                    <input type="number" id="totalInput" class="form-control" placeholder="0" style="padding: 2px 8px; height: 30px;" />
                                </div>
                                <div class="col-4">
                                    <label style="font-size: 10px;" class="p-0 m-0">Puntos</label>
                                    <input type="number" id="pointsInput" class="form-control" placeholder="0" style="padding: 2px 8px; height: 30px;" />
                                </div>
                                <div class="col-4">
                                    <label style="font-size: 10px;" class="p-0 m-0">Desc</label>
                                    <span id="percentageDisplay">0%</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 text-right">
                            <button class="btn btn-warning" style="padding: 3px 8px; font-size: 12px;" id="saveDataFidelizacionButton">Usar crédito</button>
                            <button class="btn btn-primary" style="padding: 3px 8px; font-size: 12px;" id="saveDataFidelizacionButton"><i data-feather="save"></i></button>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </div>
</div>