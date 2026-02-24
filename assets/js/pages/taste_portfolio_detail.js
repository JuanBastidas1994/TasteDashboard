$(function () {
    const cod_taste_portfolio = $.urlParam('id');
    getRestaurant(cod_taste_portfolio);

    if (cod_taste_portfolio === 0 || cod_taste_portfolio === null) {
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
    fetch(`controllers/controlador_taste_portfolio.php?metodo=getRestaurant&cod_taste_portfolio=${cod_taste_portfolio}`, {
        method: 'GET',
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                $('#cod_taste_portfolio').val(response.data.cod_taste_portafolio);
                $('#businessId').val(response.data.cod_empresa);
                $('#name').val(response.data.nombre);
                $('#img').attr('src', response.data.image);
                $('#url').val(response.data.url_web);
                $('#categories').val(response.data.categories);
                $('#cities').val(response.data.cities);
                $('#path').val(response.data.path);
            }
            else {
            }
        })
        .catch(error => {
            console.log(error);
        });
}

function saveRestaurant() {
    const cod_taste_portfolio = $('#cod_taste_portfolio').val();
    const businessId = $('#businessId').val();
    const name = $('#name').val();
    const categories = $('#categories').val();
    const cities = $('#cities').val();
    const imageFile = $('#image')[0].files[0];
    const path = $('#path').val();

    if(categories.length === 0 || cities.length === 0) {
        messageDone('Debe seleccionar al menos una categoría y una ciudad', 'error');
        return;
    }

    console.log('cod_taste_portfolio: ' + cod_taste_portfolio);

    if(cod_taste_portfolio === undefined || cod_taste_portfolio === 0 || cod_taste_portfolio === null || cod_taste_portfolio === '') { 
        imageFile ? null : messageDone('Debe seleccionar una imagen', 'error');
        if(!imageFile) return;  
    }

    categories.unshift('*');
    cities.unshift('*');

    const formData = new FormData();
    formData.append('cod_taste_portfolio', cod_taste_portfolio);
    formData.append('businessId', businessId);
    formData.append('name', name);
    formData.append('categories', JSON.stringify(categories));
    formData.append('cities', JSON.stringify(cities));
    formData.append('path', path);

    if (imageFile) {
        formData.append('image', imageFile);
    }

    fetch(`controllers/controlador_taste_portfolio.php?metodo=saveRestaurant`, {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                messageDone(response.mensaje, 'success');
            }
            else {
                messageDone(response.mensaje, 'error');
            }
        })
        .catch(error => {
            console.log(error);
            CloseLoad();
            messageDone('Ocurrió un error', 'error');
        });
}

function getRestaurantWithoutPortfolio() {
    fetch(`controllers/controlador_taste_portfolio.php?metodo=getRestaurantWithoutPortfolio`, {
        method: 'GET',
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let options = '<option value="">Seleccione un restaurante</option>';
                response.data.forEach(restaurant => {
                    options += `<option value="${restaurant.cod_empresa}">${restaurant.nombre}</option>`;
                });
                $('#business').html(options);
            }
            else {
            }
        })
        .catch(error => {
            console.log(error);
        });
}

$("#btnGuardar").on("click", function () {
    saveRestaurant();
});

$("#business").on("change", function(){
    const businessId = $(this).val();
    $('#businessId').val(businessId);
});