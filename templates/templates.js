Handlebars.registerHelper('eq', function(arg1, arg2, options) {
    return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('different', function(arg1, arg2, options) {
    return (arg1 !== arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('diferent', function(arg1, arg2, options) {
    return (arg1 != arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('mayor', function(arg1, arg2, options) {
    return (arg1 > arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('menor', function(arg1, arg2, options) {
    return (arg1 < arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('decimal', function(number) {
  return parseFloat(number).toFixed(2);
});
Handlebars.registerHelper('count', function (arrayElement) {
    return arrayElement.length;
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