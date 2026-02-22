$(function() {
    getPortfolio();
});

function getPortfolio() {
    fetch(`controllers/controlador_taste_portfolio.php?metodo=getListRestaurants`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let target = $("#tbody");
            let template = Handlebars.compile($("#portfolio-row-template").html());
            target.html(template(response.data));
        }
        else{
        }
        feather.replace();
    })
    .catch(error=>{
        console.log(error);
    });
}