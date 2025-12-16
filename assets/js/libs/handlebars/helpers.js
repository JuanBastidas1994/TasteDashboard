Handlebars.registerHelper('eq', function(arg1, arg2, options) {
    return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('diferent', function(arg1, arg2, options) {
    return (arg1 !== arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('mayor', function(arg1, arg2, options) {
    return (arg1 > arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('mayorIgual', function(arg1, arg2, options) {
    return (arg1 >= arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('decimal', function(number) {
    return parseFloat(number).toFixed(2);
});
Handlebars.registerHelper('colorStatus', function(status) {
    if(status == "ENTRANTE")
        return "primary";
    else if(status == "ASIGNADA" || status == "EXPIRADO")
        return "warning";
    else if(status == "ENVIANDO")
        return "secondary";
    else if(status == "ENTREGADA" || status == "CREADA" || status == "A")
        return "success";
    else if(status == "ANULADA" || status == "I" || status == "D")
        return "danger";
    else if(status == "PUNTO_RECOGIDA" || status == "VIGENTE")
        return "info";
    else if(status == "PUNTO_ENTREGA" || status == "UTILIZADO")
        return "dark";
    else
        return "info";
});
Handlebars.registerHelper('ifIn', function(elem, list, options) {
    if(list.indexOf(elem) > -1) {
        return options.fn(this);
    }
    return options.inverse(this);
});
Handlebars.registerHelper('array', function() {
    return Array.prototype.slice.call(arguments, 0, -1);
});
Handlebars.registerHelper('reverse', function(arreglo) {
    return arreglo.reverse();
});
Handlebars.registerHelper('count', function (arrayElement) {
    return arrayElement.length;
});
Handlebars.registerHelper('textStatus', function (estado) {
    switch (estado) {
        case "A":
            estado = "Activo"
            break;
        case "I":
            estado = "Inactivo"
            break;
        case "D":
            estado = "Eliminado"
            break;
    }
    return estado;
});
Handlebars.registerHelper('objectToJson', function (objeto) {
    return JSON.stringify(objeto);
});
Handlebars.registerHelper("strToUpperCase", function(text) {
    return text.toUpperCase();
});
/* Handlebars.registerHelper('reverse', function() {
    let arr = Array.prototype.slice.call(arguments, 0, -1);
    return arr.reverse();
    //return Array.prototype.slice.call(arguments, 0, -1);
}); */