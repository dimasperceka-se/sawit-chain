/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Aug 07 2019
 *  File : replanting.js
 *******************************************/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    //console.log(url);

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_ProvinceID,kab: m_DistrictID},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            $('#box_smallholder_farmers').html(number_format(r.dataDisplay.FarmerHasPlantation,0,'.',','));
            $('#box_garden').html(number_format(r.dataDisplay.TotalPlantation,0,'.',','));
            $('#box_hectare').html(number_format(r.dataDisplay.TotalPlantationHa,0,'.',','));
            $('#box_replanting_hectare').html(number_format(r.dataDisplay.ReplantedHa,0,'.',','));
            $('#box_replanting_funding').html(number_format(r.dataDisplay.ReplantedHaFunding,0,'.',','));

            //Chart chart_garden_age
            var data_age = [], cat_age = [];
            for (var i = 0; i < 30; i++) {
                data_age[i] = parseInt(r.dataDisplay['age_'+i]);
                cat_age[i] = i;
            }
            data_age[30] = parseInt(r.dataDisplay['age_30_more']);
            cat_age[30] = lang('30 and above');
            column([{name: lang('Plantation'),data: data_age}], 'chart_garden_age', lang('Oil Palm Plantation Age'), lang('Jumlah'), null, cat_age,'normal', 0);

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //console.log(arrReturn);
    return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);