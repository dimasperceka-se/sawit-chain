/*
 * @Author: fikri fauzul
 * @Date:   2020-01-13 10:56:17
*/

$( document ).ready(function() {
    //Jalankan filter ketika selesai load
    runFilter();
});

$('.widget-download-list .widget-head').on('click', function (event) {
    event.preventDefault();
    /* Act on the event */
    $list = $($(this).parent().find('.widget-list')[0]);
    if ($list.hasClass('expanded')) {
        $list.removeClass('expanded');
        $list.addClass('colapsed');
    } else {
        $list.addClass('expanded');
        $list.removeClass('colapsed');
    }
});

function openFilter() {
    //Open Popup
    var WinPopupSupChainMillFilter = Ext.create('Koltiva.view.Dashboard.WinPopupSupChainMillFilter',{
        viewVar: {
            PartnerID: m_partner
        }
    });
    if (!WinPopupSupChainMillFilter.isVisible()) {
        WinPopupSupChainMillFilter.center();
        WinPopupSupChainMillFilter.show();
    } else {
        WinPopupSupChainMillFilter.close();
    }
}

function runFilter() {
    var SupChainMillPartnerIDFilter = $("#SupChainMillPartnerIDFilter").val();
    var SupChainMillFirstLoad = $("#SupChainMillFirstLoad").val();

    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: m_data,
        data: {PartnerIDImp: SupChainMillPartnerIDFilter, SupChainMillFirstLoad:SupChainMillFirstLoad},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            //data display
            $('#box_farmer_registered').html(number_format(r.dataDisplay.farmer_registered, 0, '.', ','));
            $('#box_farmer_mapped').html(number_format(r.dataDisplay.farmer_mapped, 0, '.', ','));

            var plantation_registered = parseInt(r.dataDisplay.plantation_registered_farmer) + parseInt(r.dataDisplay.plantation_registered_sme) + parseInt(r.dataDisplay.plantation_registered_mill);
            $('#box_plantation_registered').html(number_format(plantation_registered, 0, '.', ','));
            $('#farmer_register').html(number_format(r.dataDisplay.plantation_registered_farmer, 0, '.', ','));
            $('#sme_register').html(number_format(r.dataDisplay.plantation_registered_sme, 0, '.', ','));
            $('#mill_register').html(number_format(r.dataDisplay.plantation_registered_mill, 0, '.', ','));

            var plantation_mapped = parseInt(r.dataDisplay.plantation_mapped_farmer) + parseInt(r.dataDisplay.plantation_mapped_sme) + parseInt(r.dataDisplay.plantation_mapped_mill);
            $('#box_plantation_mapped').html(number_format(plantation_mapped, 0, '.', ','));
            $('#farmer_garden_mapped').html(number_format(r.dataDisplay.plantation_mapped_farmer, 0, '.', ','));
            $('#sme_garden_mapped').html(number_format(r.dataDisplay.plantation_mapped_sme, 0, '.', ','));
            $('#mill_garden_mapped').html(number_format(r.dataDisplay.plantation_mapped_mill, 0, '.', ','));

            var plant_ha_mapped = parseInt(r.dataDisplay.plant_ha_mapped_farmer) + parseInt(r.dataDisplay.plant_ha_mapped_sme) + parseInt(r.dataDisplay.plant_ha_mapped_mill);
            $('#box_plant_ha_mapped').html(number_format(plant_ha_mapped, 0, '.', ','));
            $('#farmer_plant_ha').html(number_format(r.dataDisplay.plant_ha_mapped_farmer, 0, '.', ','));
            $('#sme_plant_ha').html(number_format(r.dataDisplay.plant_ha_mapped_sme, 0, '.', ','));
            $('#mill_plant_ha').html(number_format(r.dataDisplay.plant_ha_mapped_mill, 0, '.', ','));

            var plantation_polygon_mapped = parseInt(r.dataDisplay.plantation_polygon_mapped_farmer) + parseInt(r.dataDisplay.plantation_polygon_mapped_sme) + parseInt(r.dataDisplay.plantation_polygon_mapped_mill);
            $('#box_plantation_polygon_mapped').html(number_format(plantation_polygon_mapped, 0, '.', ','));
            $('#farmer_poly').html(number_format(r.dataDisplay.plantation_polygon_mapped_farmer, 0, '.', ','));
            $('#sme_poly').html(number_format(r.dataDisplay.plantation_polygon_mapped_sme, 0, '.', ','));
            $('#mill_poly').html(number_format(r.dataDisplay.plantation_polygon_mapped_mill, 0, '.', ','));

            var polygon_ha_mapped = parseInt(r.dataDisplay.plant_polygon_ha_mapped_farmer) + parseInt(r.dataDisplay.plant_polygon_ha_mapped_sme) + parseInt(r.dataDisplay.plant_polygon_ha_mapped_mill);
            $('#box_plant_polygon_ha_mapped').html(number_format(polygon_ha_mapped, 0, '.', ','));
            $('#farmer_poly_mapped').html(number_format(r.dataDisplay.plant_polygon_ha_mapped_farmer, 0, '.', ','));
            $('#sme_poly_mapped').html(number_format(r.dataDisplay.plant_polygon_ha_mapped_sme, 0, '.', ','));
            $('#mill_poly_mapped').html(number_format(r.dataDisplay.plant_polygon_ha_mapped_mill, 0, '.', ','));

            $('#box_mills_mapped').html(number_format(r.dataDisplay.mills_mapped, 0, '.', ','));
            $('#box_agents_mapped').html(number_format(r.dataDisplay.agents_mapped, 0, '.', ','));

            //data gauge chart
            gauge_single('gauge_farmer_registered', lang('Palm Oil Farmers Registered'), [{max: r.dataTarget.farmer_registered, data: r.dataDisplay.farmer_registered, name: lang('Oil Palm Farmers Registered')}]);
//            gauge_single('gauge_farmer_mapped', lang('Palm Oil Farmers Mapped'), [{max: r.dataTarget.farmer_mapped, data: r.dataDisplay.farmer_mapped, name: lang('Oil Palm Farmers Mapped')}]);
//
//            gauge_single('gauge_plantation_registered', lang('Palm Oil Plantations Registered'), [{max: r.dataTarget.plantation_registered, data: plantation_registered, name: lang('Oil Palm Plantations Registered')}]);
            gauge_single('gauge_plantation_mapped', lang('Palm Oil Plantations Mapped'), [{max: r.dataTarget.plantation_mapped, data: plantation_mapped, name: lang('Oil Palm Plantations Mapped')}]);
            gauge_single('gauge_plant_ha_mapped', lang('Palm Oil Plantations Area (Ha)'), [{max: r.dataTarget.plant_ha_mapped, data: plant_ha_mapped, name: lang('Oil Palm Plantations Area by Farmer Interview (Ha)')}]);
//
            gauge_single('gauge_plantation_polygon_mapped', lang('Palm Oil Plantations Mapped with Polygon'), [{max: r.dataTarget.plantation_polygon_mapped, data: plantation_polygon_mapped, name: lang('Oil Palm Plantations Mapped with Polygon')}]);
            gauge_single('gauge_plant_polygon_ha_mapped', lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)'), [{max: r.dataTarget.plant_polygon_ha_mapped, data: polygon_ha_mapped, name: lang('Oil Palm Plantations Hectare Mapped with Polygon (Ha)')}]);
//
            gauge_single('gauge_mills_mapped', lang('Palm Oil Mills Mapped'), [{max: r.dataTarget.mills_mapped, data: r.dataDisplay.mills_mapped, name: lang('Palm Oil Mills Mapped')}]);
            gauge_single('gauge_agents_mapped', lang('Palm Oil SME Mapped'), [{max: r.dataTarget.agents_mapped, data: r.dataDisplay.agents_mapped, name: lang('Palm Oil SME Mapped')}]);

            if(SupChainMillFirstLoad == "1") {
                console.log('oiii load pertama kali ne');
                document.getElementById("SupChainMillPartnerIDFilter").value = r.PartnerIDStr;
            }

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });
}