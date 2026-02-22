$(function() {
     const cod_taste_portfolio = $.urlParam('id');
     getRestaurant(cod_taste_portfolio);

     if(cod_taste_portfolio === 0 || cod_taste_portfolio === null) {
        getRestaurantWithoutPortfolio();
        $('#business-row').removeClass('d-none');
     }
});

$.urlParam = function (name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (results == null) return null;
  return decodeURI(results[1]) || 0;
};

function getRestaurant(cod_taste_portfolio) {   
    fetch(`controllers/controlador_taste_portfolio.php?metodo=getRestaurant&cod_taste_portfolio=${cod_taste_portfolio}`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            $('#cod_taste_portfolio').val(response.data.cod_taste_portafolio);
            $('#name').val(response.data.nombre);
            $('#url').val(response.data.url_web);
            $('#categories').val(response.data.categories);
            $('#cities').val(response.data.cities);
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function saveRestaurant() {
    const cod_taste_portfolio = $('#cod_taste_portfolio').val();
    const name = $('#name').val();
    const categories = $('#categories').val();
    const cities = $('#cities').val(); 
    const imageFile = $('#image')[0].files[0]; 

    const formData = new FormData();
    formData.append('cod_taste_portfolio', cod_taste_portfolio);
    formData.append('name', name);
    formData.append('categories', categories);
    formData.append('cities', cities);

    if (imageFile) {
        formData.append('image', imageFile);
    }

    fetch(`controllers/controlador_taste_portfolio.php?metodo=saveRestaurant`,{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function getRestaurantWithoutPortfolio() {   
    fetch(`controllers/controlador_taste_portfolio.php?metodo=getRestaurantWithoutPortfolio`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let options = '<option value="">Seleccione un restaurante</option>';
            response.data.forEach(restaurant => {
                options += `<option value="${restaurant.cod_empresa}">${restaurant.nombre}</option>`;
            });
            $('#business').html(options);
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}