/*
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
*/

$( document ).ready(function() {
    //Jalankan filter ketika selesai load
    runFilter();
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
            $('#box_farmer_registered').html(number_format(r.dataDisplay.farmer_registered,0,'.',','));
            $('#box_plantation_mapped').html(number_format(r.dataDisplay.plantation_mapped,0,'.',','));
            $('#box_plant_ha_mapped').html(number_format(r.dataDisplay.plant_ha_mapped,0,'.',','));
            $('#box_plantation_polygon_mapped').html(number_format(r.dataDisplay.plantation_polygon_mapped,0,'.',','));
            $('#box_plant_polygon_ha_mapped').html(number_format(r.dataDisplay.plant_polygon_ha_mapped,0,'.',','));
            $('#box_farmer_sales').html(number_format(r.dataDisplay.farmer_sales,0,'.',','));
            $('#box_mills_mapped').html(number_format(r.dataDisplay.mills_mapped,0,'.',','));
            $('#box_agents_mapped').html(number_format(r.dataDisplay.agents_mapped,0,'.',','));

            var details = '<li>'
            +'<a class="link_mill" data-type="total_agent" href="#">'
            +'<span class="label">'+lang('Agent/Dealer/Vendor')+'</span>'
            +'<span class="value" id="total_agent_val">'+r.dataDisplay.agents+'</span>'
            +'</a>'
            +'</li>';
            details += '<li>'
            +'<a class="link_mill" data-type="total_owned_estate" href="#">'
            +'<span class="label">'+lang('Owned Estate')+'</span>'
            +'<span class="value" id="total_owned_estate_val">'+r.dataDisplay.owned_estate+'</span>'
            +'</a>'
            +'</li>';
            details += '<li>'
            +'<a class="link_mill" data-type="total_external_estate" href="#">'
            +'<span class="label">'+lang('External Estate')+'</span>'
            +'<span class="value" id="total_external_estate_val">'+r.dataDisplay.external_estate+'</span>'
            +'</a>'
            +'</li>';
            details += '<li>'
            +'<a class="link_mill" data-type="total_plasma" href="#">'
            +'<span class="label">'+lang('Plasma')+'</span>'
            +'<span class="value" id="total_plasma_val">'+r.dataDisplay.plasma+'</span>'
            +'</a>'
            +'</li>';
            details += '<li>'
            +'<a class="link_mill" data-type="total_direct" href="#">'
            +'<span class="label">'+lang('Direct Smallholder')+'</span>'
            +'<span class="value" id="total_direct_val">'+r.dataDisplay.direct_smallholder+'</span>'
            +'</a>'
            +'</li>';
            $('#sme_detail').html(details);

            var plantaion_sme = r.dataDisplay.garden_total;

            var details_plantation = '<li>'
            +'<a class="link_mill" data-type="total_agent" href="#">'
            +'<span class="label">'+lang('Agent/Dealer/Vendor')+'</span>'
            +'<span class="value" id="total_agent_val">'+r.dataDisplay.garden_agent+'</span>'
            +'</a>'
            +'</li>';
            details_plantation += '<li>'
            +'<a class="link_mill" data-type="total_owned_estate" href="#">'
            +'<span class="label">'+lang('Owned Estate')+'</span>'
            +'<span class="value" id="total_owned_estate_val">'+r.dataDisplay.garden_kebun_inti+'</span>'
            +'</a>'
            +'</li>';
            details_plantation += '<li>'
            +'<a class="link_mill" data-type="total_external_estate" href="#">'
            +'<span class="label">'+lang('External Estate')+'</span>'
            +'<span class="value" id="total_external_estate_val">'+r.dataDisplay.garden_external_estate+'</span>'
            +'</a>'
            +'</li>';
            details_plantation += '<li>'
            +'<a class="link_mill" data-type="total_plasma" href="#">'
            +'<span class="label">'+lang('Plasma')+'</span>'
            +'<span class="value" id="total_plasma_val">'+r.dataDisplay.garden_plasma+'</span>'
            +'</a>'
            +'</li>';
            details_plantation += '<li>'
            +'<a class="link_mill" data-type="total_direct" href="#">'
            +'<span class="label">'+lang('Direct Smallholder')+'</span>'
            +'<span class="value" id="total_direct_val">'+r.dataDisplay.garden_direct+'</span>'
            +'</a>'
            +'</li>';
            $('#sme_plantation_detail').html(details_plantation);
            $('#box_agents_plantaion').html(plantaion_sme);

            //data gauge chart
            gauge_single('gauge_farmer_registered', lang('Palm Oil Farmers Registered'), [{max: r.dataTarget.farmer_registered, data: r.dataDisplay.farmer_registered, name: lang('Oil Palm Farmers Registered')}]);

            gauge_single('gauge_plantation_mapped', lang('Palm Oil Plantations Registered'), [{max: r.dataTarget.plantation_mapped, data: r.dataDisplay.plantation_mapped, name: lang('Oil Palm Plantations Registered')}]);
            gauge_single('gauge_plant_ha_mapped', lang('Palm Oil Plantations Area by Farmer Interview (Ha)'), [{max: r.dataTarget.plant_ha_mapped, data: r.dataDisplay.plant_ha_mapped, name: lang('Oil Palm Plantations Area by Farmer Interview (Ha)')}]);

            gauge_single('gauge_plantation_polygon_mapped', lang('Palm Oil Plantations Mapped with Polygon'), [{max: r.dataTarget.plantation_polygon_mapped, data: r.dataDisplay.plantation_polygon_mapped, name: lang('Oil Palm Plantations Mapped with Polygon')}]);
            gauge_single('gauge_plant_polygon_ha_mapped', lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)'), [{max: r.dataTarget.plant_polygon_ha_mapped, data: r.dataDisplay.plant_polygon_ha_mapped, name: lang('Oil Palm Plantations Hectare Mapped with Polygon (Ha)')}]);

            gauge_single('gauge_farmer_sales', lang('Palm Oil Farmers Sales'), [{max: r.dataTarget.farmer_sales, data: r.dataDisplay.farmer_sales, name: lang('Palm Oil Farmers Sales')}]);
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