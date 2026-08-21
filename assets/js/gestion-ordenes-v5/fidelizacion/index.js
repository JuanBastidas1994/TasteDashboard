const { API_POS } = window.__CONFIG__;

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

let currentCliente = null;
let currentPurchaseCode = null;

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
    fetch(`${API_POS}/puntos/${cedula}`,{
            method: 'GET',
            headers: { 'Api-Key':$("#apikey_empresa").val() },
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            hideSteps();

            if(response.success == 1){

                const client = response.cliente;
                const dataResponse = response.data;
                currentCliente = client;
                currentPurchaseCode = cedula;

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
    if(!currentCliente){
        alert("Primero escanea el código del cliente");
        return;
    }

    const total = parseFloat(totalInput.value) || 0;
    if(total <= 0){
        alert("Ingresa un total válido");
        return;
    }

    const subtotal = parseFloat((total / 1.15).toFixed(2));
    const iva = parseFloat((total - subtotal).toFixed(2));
    const orderId = `POS-${Date.now()}-${Math.floor(Math.random() * 100000)}`;

    const dataToSend = {
        id: orderId,
        descuento: 0,
        envio: 0,
        iva: iva,
        subtotal: subtotal,
        total: total,
        metodoPago: [{ monto: total, observacion: "", tipo: "E" }],
        scanner: currentPurchaseCode,
        cod_sucursal: "1",
        cliente: {
            num_documento: currentCliente.num_documento || "",
            nombre: currentCliente.nombre || "",
            telefono: currentCliente.telefono || "",
            correo: currentCliente.correo || ""
        }
    };

    fetch(`${API_POS}/ordenes-pos`,{
            method: 'POST',
            headers: {
                'Api-Key': $("#apikey_empresa").val(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                alert("Orden registrada correctamente");
                $("#burbujaModal").modal('hide');
            }else{
                alert(response.mensaje || "No se pudo registrar la orden");
            }
        })
        .catch(error=>{
            console.log(error);
            alert("Ocurrió un error al registrar la orden");
        });
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
if(usePointsButton){
    usePointsButton.addEventListener("click", () => {
        detailsSection.style.display = "flex";
        numberInvoice.style.display = "block";
    });
}

// Mostrar "purchaseStart" y ocultar "purchaseClient" al hacer clic en "back"
if(backButton){
    backButton.addEventListener("click", () => {
        numberInvoice.style.display = "none";
        purchaseStart.style.display = "block";
    });
}


const totalInput = document.getElementById("totalInput");
const pointsInput = document.getElementById("pointsInput");
const percentageDisplay = document.getElementById("percentageDisplay");

// Función para calcular el porcentaje
function calculatePercentage() {
    const total = parseFloat(totalInput.value) || 0; // Si está vacío, toma 0
    const points = parseFloat(pointsInput.value) || 0; // Si está vacío, toma 0

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
