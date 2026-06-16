/*
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
*/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    // console.log(url);

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_ProvinceID,kab: m_DistrictID},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            //data display
            $('#box_farmer_registered').html(number_format(r.dataDisplay.farmer_registered,0,'.',','));
            $('#box_plantation_mapped').html(number_format(r.dataDisplay.plantation_registered_farmer,0,'.',','));
            $('#box_plant_ha_mapped').html(number_format(r.dataDisplay.plant_ha_registered_farmer,0,'.',','));
            // $('#box_plantation_polygon_mapped').html(number_format(r.dataDisplay.plantation_polygon_mapped,0,'.',','));
            // $('#box_plant_polygon_ha_mapped').html(number_format(r.dataDisplay.plant_polygon_ha_mapped,0,'.',','));
            $('#box_farmer_sales').html(number_format(r.dataDisplay.farmer_sales,0,'.',','));
            $('#box_mills_mapped').html(number_format(r.dataDisplay.mills_mapped,0,'.',','));
            $('#box_agents_mapped').html(number_format(r.dataDisplay.agents_mapped,0,'.',','));

            //data gauge chart
            gauge_single('gauge_farmer_registered', lang('Palm Oil Farmers Registered'), [{max: r.dataTarget.farmer_registered, data: r.dataDisplay.farmer_registered, name: lang('Oil Palm Farmers Registered')}]);

            gauge_single('gauge_plantation_mapped', lang('Palm Oil Plantations Registered'), [{max: r.dataTarget.plantation_mapped, data: r.dataDisplay.plantation_registered_farmer, name: lang('Oil Palm Plantations Registered')}]);
            gauge_single('gauge_plant_ha_mapped', lang('Palm Oil Plantations Area by Farmer Interview (Ha)'), [{max: r.dataTarget.plant_ha_mapped, data: r.dataDisplay.plant_ha_registered_farmer, name: lang('Oil Palm Plantations Area by Farmer Interview (Ha)')}]);

            // gauge_single('gauge_plantation_polygon_mapped', lang('Palm Oil Plantations Mapped with Polygon'), [{max: r.dataTarget.plantation_polygon_mapped, data: r.dataDisplay.plantation_polygon_mapped, name: lang('Oil Palm Plantations Mapped with Polygon')}]);
            // gauge_single('gauge_plant_polygon_ha_mapped', lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)'), [{max: r.dataTarget.plant_polygon_ha_mapped, data: r.dataDisplay.plant_polygon_ha_mapped, name: lang('Oil Palm Plantations Hectare Mapped with Polygon (Ha)')}]);

            // gauge_single('gauge_farmer_sales', lang('Palm Oil Farmers Sales'), [{max: r.dataTarget.farmer_sales, data: r.dataDisplay.farmer_sales, name: lang('Palm Oil Farmers Sales')}]);
            gauge_single('gauge_mills_mapped', lang('Palm Oil Mills Mapped'), [{max: r.dataTarget.mills_mapped, data: r.dataDisplay.mills_mapped, name: lang('Palm Oil Mills Mapped')}]);
            gauge_single('gauge_agents_mapped', lang('Palm Oil SME Mapped'), [{max: r.dataTarget.agents_mapped, data: r.dataDisplay.agents_mapped, name: lang('Palm Oil SME Mapped')}]);

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

};

var arrReturn = ajaxDataRenderer(m_data);