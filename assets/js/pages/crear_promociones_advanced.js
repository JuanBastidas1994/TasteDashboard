$(function() {
   $("#cmb_condicion").on('change', function(){
    const condicion = $(this).val();
    $(".contentCondicion").hide();
    if(condicion == 0){
        $("#contentXporY").show();
    }else{
        $("#contentMontoMinimo").show();
    }
   });     
});