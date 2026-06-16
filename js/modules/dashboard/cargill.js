
var ajaxDataRenderer = function(url) {
   var s = [];
    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var total_production = (parseInt(r.CF_Production)+parseInt(r.CL_Production)+parseInt(r.UTZ_Production));
            var total_ha = parseFloat(r.CF_GardenHaUnCertified)+parseFloat(r.CL_GardenHaUnCertified)+parseFloat(r.UTZ_GardenHaUnCertified);
            $('#farmer').text(number_format(parseInt(r.CF)+parseInt(r.CL)+parseInt(r.UTZ),0,',','.'));
            $('#production').text(number_format(total_production/1000,0,',','.'));
            $('#area').text(number_format(total_ha,0,',','.'));
            $('#productivity').text(number_format(total_production/total_ha,0,',','.'));
            $('#nursery').text(number_format(parseInt(r.nursery),0,',','.'));

            $('#row-fluid').show();
            $('#wrapper').removeClass('cover');
         }
   });
   return s
};

$( document ).ready(function() {
   var s = ajaxDataRenderer(m_data);
});

