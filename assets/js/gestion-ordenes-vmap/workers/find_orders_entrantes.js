let ApiUrl = "";
let ApiKey = "";
let Office = 0;
let interval = 1000;

onmessage = function(event) {
    let data = event.data;
    ApiUrl = data.ApiUrl;
    ApiKey = data.ApiKey;
    Office = data.Office;
    interval = parseInt((data.tiempo * 1000) * 60);
    console.log("WEB WORKER FIND ORDERS ENTRANTES", data);
    setTimeout("FindOrders()",interval);
}


function FindOrders(){
    console.log(`${ApiUrl}/ordenes/rezagadas/${Office}`);
    fetch(`${ApiUrl}/ordenes/rezagadas/${Office}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        }
    })
    .then(res => res.json())
    .then(response => {
        setTimeout("FindOrders()",interval);
        console.log("Find Orders",response);
        postMessage(response);
    })
    .catch(error=>{
        console.log(error);
        setTimeout("FindOrders()",interval);
        postMessage(error);
    });
}