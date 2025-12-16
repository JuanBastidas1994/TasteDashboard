var wpwlOptions = {
    style: "card",
    locale: "es",
    labels: { cvv: "CVV", cardHolder: "Nombre(Igual que en la tarjeta)" },
    
    onReady: function() {
        var datafast = '<br/><br/><img src="https://www.datafast.com.ec/images/verified.png" style="display:block;margin:0 auto; width:100%;">';

        // Buscar el formulario y botón con JavaScript puro
        var form = document.querySelector('form.wpwl-form-card');
        var button = form.querySelector('.wpwl-button');

        // Insertar el elemento antes del botón
        button.insertAdjacentHTML('beforebegin', datafast);

        // Ocultar el elemento con la clase "loading-datafast"
        var loadingElement = document.querySelector('.loading-datafast');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    },
    
    onBeforeSubmitCard: function() {
        var cardHolder = document.querySelector(".wpwl-control-cardHolder");
        if (cardHolder.value === "") {
            cardHolder.classList.add("wpwl-has-error");

            // Crear un nuevo elemento para el mensaje de error
            var errorHint = document.createElement("div");
            errorHint.className = "wpwl-hint-cardHolderError";
            errorHint.textContent = "Campo Requerido";

            // Insertar el mensaje de error después del campo de titular de tarjeta
            cardHolder.insertAdjacentElement('afterend', errorHint);

            // Deshabilitar el botón de pago
            var payButton = document.querySelector(".wpwl-button-pay");
            payButton.classList.add("wpwl-button-error");
            payButton.setAttribute("disabled", "disabled");

            return false;
        } else {
            return true;
        }
    }
};