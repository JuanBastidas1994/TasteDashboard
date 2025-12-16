const purchaseCode = document.querySelector(".purchaseCodeInput");

const purchaseStart = document.getElementById("purchaseStart");
const purchaseLoading = document.getElementById("purchaseLoading");
const purchaseClient = document.getElementById("purchaseClient");


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
    fetch(`https://api.mie-commerce.com/pos/v2/puntos/calcular/${cedula}`,{
            method: 'GET',
            headers: { 'Api-Key':$("#apikey_empresa").val() },
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            fetch(`https://api.mie-commerce.com/pos/v2/puntos/${cedula}`,{
                    method: 'GET',
                    headers: { 'Api-Key':$("#apikey_empresa").val() },
                })
                .then(res => res.json())
                .then(response => {
                    console.log(response);
                    hideSteps();
                    
                    if(response.success == 1){
                        purchaseClient.style.display = "initial";
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

function hideSteps(){
    purchaseStart.style.display = "none";
    purchaseLoading.style.display = "none";
    purchaseClient.style.display = "none";
}