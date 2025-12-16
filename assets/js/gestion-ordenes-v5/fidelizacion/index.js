const purchaseCode = document.querySelector(".purchaseCodeInput");

const purchaseStart = document.getElementById("purchaseStart");
const purchaseLoading = document.getElementById("purchaseLoading");
const purchaseClient = document.getElementById("purchaseClient");

const clientNameElement = document.getElementById("clientName");
const clientDniElement = document.getElementById("clientDni");
const clientTotalSaldoReal = document.getElementById("clientTotalSaldoReal");

const usePointsButton = document.getElementById("usePointsButton");
const detailsSection = document.getElementById("detailsSection");
const backButton = document.getElementById("backButton");

const openFidelizacionModal = () => {
    $("#burbujaModal").modal();
}

$(".purchaseCodeInput").on("search", function(e){
    e.preventDefault();
    getClientePuntos();
});

function getClientePuntos(){
    hideSteps();
    purchaseLoading.style.display = "initial";
    
    let cedula = purchaseCode.value;
    fetch(`https://api.mie-commerce.com/pos/v4/puntos/calcular/${cedula}`,{
            method: 'GET',
            headers: { 'Api-Key':$("#apikey_empresa").val() },
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            fetch(`https://api.mie-commerce.com/pos/v4/puntos/${cedula}`,{
                    method: 'GET',
                    headers: { 'Api-Key':$("#apikey_empresa").val() },
                })
                .then(res => res.json())
                .then(response => {
                    console.log(response);
                    hideSteps();
                    
                    if(response.success == 1){
                        
                        const client = response.data.cliente;
                        const dataResponse = response.data;
                        //alert(client.nombre)
                        purchaseClient.style.display = "initial";
                        
                        clientNameElement.textContent = client.nombre || "N/A";
                        clientDniElement.textContent = client.num_documento || "N/A";
                        clientTotalSaldoReal.textContent = `$${dataResponse.total_saldo_real || "0.00"}`;
                        
                        
                        document.getElementById("clientCod").textContent = client.cod_cliente;
                        
                        resizePip();
                    }else{
                        console.log("ERROR NO ENCONTRO AL CLIENTE");
                        purchaseStart.style.display = "initial";
                        alert("Error no encontro al cliente")
                    }
                })
                .catch(error=>{
                    console.log(error);
                });

        })
        .catch(error=>{
            console.log(error);
        });
}

function calcularPuntosUsuarioByOrden(order_id){
    const businessLoyalty = $("#fidelizacion_empresa").val();
    const ApiKey = $("#apikey_empresa").val();
    if(businessLoyalty){
        fetch(`${ApiUrl}/puntos/calcular_orden/${order_id}`,{
                method: 'GET',
                headers: {
                'Api-Key':ApiKey
                }
            })
            .then(res => res.json())
            .then(response => {
                console.log(response);
                if(response.success == 1){
                    console.log(response.mensaje);
                }
                else
                    console.log(response.mensaje);
            })
            .catch(error=>{
                console.log(error);
            });
    }
}


function saveDataFidelizacion(){
   
   
   const userId = document.getElementById("clientCod");
   const token = purchaseCode;
   const secuencial = document.getElementById("invoiceNumber");
   const casherId = document.getElementById("casher_id");
   const total = document.getElementById("totalInput");
   const points = document.getElementById("pointsInput");
   
   const dataToSend = {
       userId : userId,
       token: token,
       secuencial: secuencial,
       casherId: casherId,
       total: total,
       points: points
   }
   
   alert(dataToSend)
   
}

function hideSteps(){
    purchaseStart.style.display = "none";
    purchaseLoading.style.display = "none";
    purchaseClient.style.display = "none";
}

const saveDataFidelizacionButton = document.getElementById("saveDataFidelizacionButton");
saveDataFidelizacionButton.addEventListener("click", () => {
    saveDataFidelizacion();
});


// Mostrar detalles cuando se presiona "Usar Puntos"
usePointsButton.addEventListener("click", () => {
    detailsSection.style.display = "flex";
    numberInvoice.style.display = "block";
});

// Mostrar "purchaseStart" y ocultar "purchaseClient" al hacer clic en "back"
backButton.addEventListener("click", () => {
    numberInvoice.style.display = "none";
    purchaseStart.style.display = "block";
});


const totalInput = document.getElementById("totalInput");
const pointsInput = document.getElementById("pointsInput");
const percentageDisplay = document.getElementById("percentageDisplay");

// Función para calcular el porcentaje
function calculatePercentage() {
    console.log('TEST');
    
    const total = parseFloat(totalInput.value) || 0; // Si está vacío, toma 0
    const points = parseFloat(pointsInput.value) || 0; // Si está vacío, toma 0
    
    console.log(total, points);
    
    if (total > 0) {
        const percentage = ((points / total) * 100).toFixed(2); // Calcula porcentaje con 2 decimales
        percentageDisplay.textContent = `${percentage}%`;
    } else {
        percentageDisplay.textContent = "0%"; // Si total es 0, el porcentaje es 0
    }
}

// Agregar eventos de escucha
totalInput.addEventListener("input", calculatePercentage);
pointsInput.addEventListener("input", calculatePercentage);