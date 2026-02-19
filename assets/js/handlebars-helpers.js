Handlebars.registerHelper('eq', function(arg1, arg2, options) {
    return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('diferent', function(arg1, arg2, options) {
    return (arg1 !== arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('mayor', function(arg1, arg2, options) {
    return (arg1 > arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('menor', function(arg1, arg2, options) {
    return (arg1 < arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('menor_incluido', function(arg1, arg2, options) {
    return (arg1 <= arg2) ? options.fn(this) : options.inverse(this);
});

Handlebars.registerHelper('isEmpty', function (arr, options) {
    if(arr == undefined) return options.fn(this)
    else
        return (arr.length == 0) ? options.fn(this) : options.inverse(this);
})
/* Handlebars.registerHelper('menor', function(arg1, arg2, options) {
    return (arg1 < arg2) ? options.fn(this) : options.inverse(this);
}); */
Handlebars.registerHelper('count', function (arrayElement) {
    return arrayElement.length;
});
Handlebars.registerHelper('times', function(n, block) {
    var accum = '';
    for(var i = 1; i <= n; ++i)
        accum += block.fn(i);
    return accum;
});
Handlebars.registerHelper('decimal', function(number) {
  return parseFloat(number).toFixed(2);
});
Handlebars.registerHelper('maxLength', function(texto) {
    if(texto.length > 200)
        return texto.substring(0, 200) + "...";
    else
        return texto;
});
Handlebars.registerHelper('select', function( value, options ){
    var $el = $('<select />').html( options.fn(this) );
    $el.find('[value="' + value + '"]').attr({'selected':'selected'});
    return $el.html();
});
Handlebars.registerHelper('permitFinish', function(arg1, options) {
    const validStatus = [1,2,3,4,11,13];
    if (validStatus.includes(arg1)) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});
Handlebars.registerHelper('permitWaiting', function(arg1, options) {
    const validStatus = [1,2,3,4,11];
    if (validStatus.includes(arg1)) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});



function loadTemplate(path){
    var promesa = new Promise(function(resolve, reject){
        $.ajax({
            url:path,
            type: "GET",
            success: function(response){
                var template = Handlebars.compile(response);
                resolve(template);
            },
            error: function(data){
                var template = false;
                resolve(template);
            }
        });
    });
    return promesa;
}
