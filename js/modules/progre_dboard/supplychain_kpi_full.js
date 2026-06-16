/*
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
 */
function runSearch() {
    $('#wrapper').addClass('cover');
    var fprovince = $("#fprovince").val();
    var fdistrict = $("#fdistrict").val();
    // console.log(url);

    $.ajax({
        type: "GET",
        url: m_data,
        data: {prov: fprovince, kab: fdistrict},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (r) {
//            box_plantation_mapped
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

            var plant_ha_registered = parseInt(r.dataDisplay.plant_ha_registered_farmer) + parseInt(r.dataDisplay.plant_ha_registered_sme) + parseInt(r.dataDisplay.plant_ha_registered_mill);
            $('#box_plant_ha_registered').html(number_format(plant_ha_registered, 0, '.', ','));
            $('#farmer_plant_ha').html(number_format(r.dataDisplay.plant_ha_registered_farmer, 0, '.', ','));
            $('#sme_plant_ha').html(number_format(r.dataDisplay.plant_ha_registered_sme, 0, '.', ','));
            $('#mill_plant_ha').html(number_format(r.dataDisplay.plant_ha_registered_mill, 0, '.', ','));

            var plant_ha_mapped = parseInt(r.dataDisplay.plant_ha_mapped_farmer) + parseInt(r.dataDisplay.plant_ha_mapped_sme) + parseInt(r.dataDisplay.plant_ha_mapped_mill);
            $('#box_plant_ha_mapped').html(number_format(plant_ha_mapped, 0, '.', ','));
            $('#farmer_plant_ha_mapped').html(number_format(r.dataDisplay.plant_ha_mapped_farmer, 0, '.', ','));
            $('#sme_plant_ha_mapped').html(number_format(r.dataDisplay.plant_ha_mapped_sme, 0, '.', ','));
            $('#mill_plant_ha_mapped').html(number_format(r.dataDisplay.plant_ha_mapped_mill, 0, '.', ','));

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
            gauge_single('gauge_plantation_registered', lang('Palm Oil Plantations Registered'), [{max: r.dataTarget.plantation_registered, data: plantation_registered, name: lang('Oil Palm Plantations Registered')}]);
            
            gauge_single('gauge_farmer_mapped', lang('Palm Oil Farmers Mapped'), [{max: r.dataTarget.farmer_mapped, data: r.dataDisplay.farmer_mapped, name: lang('Oil Palm Farmers Mapped')}]);
            gauge_single('gauge_plantation_mapped', lang('Palm Oil Plantations Mapped'), [{max: r.dataTarget.plantation_mapped, data: plantation_mapped, name: lang('Oil Palm Plantations Mapped')}]);
            
            gauge_single('gauge_plant_ha_registered', lang('Palm Oil Plantations Area Registered (Ha)'), [{max: r.dataTarget.plant_ha_registered, data: plant_ha_registered, name: lang('Oil Palm Plantations Area Registered (Ha)')}]);
            gauge_single('gauge_plant_ha_mapped', lang('Palm Oil Plantations Area Mapped (Ha)'), [{max: r.dataTarget.plant_ha_mapped, data: plant_ha_mapped, name: lang('Oil Palm Plantations Area Mapped (Ha)')}]);

            gauge_single('gauge_plantation_polygon_mapped', lang('Palm Oil Plantations Mapped with Polygon'), [{max: r.dataTarget.plantation_polygon_mapped, data: plantation_polygon_mapped, name: lang('Oil Palm Plantations Mapped with Polygon')}]);
            gauge_single('gauge_plant_polygon_ha_mapped', lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)'), [{max: r.dataTarget.plant_polygon_ha_mapped, data: polygon_ha_mapped, name: lang('Oil Palm Plantations Hectare Mapped with Polygon (Ha)')}]);

            gauge_single('gauge_mills_mapped', lang('Palm Oil Mills Mapped'), [{max: r.dataTarget.mills_mapped, data: r.dataDisplay.mills_mapped, name: lang('Palm Oil Mills Mapped')}]);
            gauge_single('gauge_agents_mapped', lang('Palm Oil SME Mapped'), [{max: r.dataTarget.agents_mapped, data: r.dataDisplay.agents_mapped, name: lang('Palm Oil SME Mapped')}]);

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display = '';
            $(".dashDateGen").html('Generated on ' + r.dataDisplay.DateGenerated);
        }
    });

}

$(document).on('change', '#fprovince', function (e) {
    //load district
    $.ajax({
        type: "GET",
        url: m_api + '/dashboard/region',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {prov: e.target.value, daer: m_daer},
        success: function (data) {
            if (data.data) {
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">' + lang('All District') + '</option>');
                $.each(data.data, function (index, val) {
                    $('#fdistrict').append('<option value="' + val.id + '">' + val.name + '</option>');
                });
            } else {
                //tidak ada datanya
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">' + lang('All District') + '</option>');
            }
        }
    });
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

/*
$('.link_farmer').on('click', function (event) {
    event.preventDefault();
    displayWinGardenDetail($(this).data('type'));
});
$('.link_garden').on('click', function (event) {
    event.preventDefault();
    displayWinGardenMappedDetail($(this).data('type'));
});
$('.link_poly').on('click', function (event) {
    event.preventDefault();
    displayWinPolygonDetail($(this).data('type'));
});
$('.link_poly_mapped').on('click', function (event) {
    event.preventDefault();
    displayWinPolygonMappedDetail($(this).data('type'));
});*/

$(function () {
    //Load combo province pertama kali
    $.ajax({
        type: "GET",
        url: m_api + '/dashboard/region',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {daer: m_daer},
        success: function (data) {
            if (data.data) {
                $('#fprovince').find('option').remove().end().append('<option value="all_province">' + lang('All Province') + '</option>');
                $.each(data.data, function (index, val) {
                    $('#fprovince').append('<option value="' + val.id + '">' + val.name + '</option>');
                });
            }
        }
    });

    //Langsung jalankan search pertama kali
    runSearch();
});

/*
var store_garden = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['TypeFarmer', 'FarmerID', 'FirstBatchNr', 'FarmerName', 'GroupName', 'Village', 'SubDistrict', 'District', 'Production', 'GardenHaUncertified', 'Productivity', 'Garden', 'CertificateHolder'],
    autoLoad: false,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/garden/',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});

if (Ext.getCmp('winGardenDetail'))
    Ext.getCmp('winGardenDetail').destroy();

var winGardenDetail = Ext.create('widget.window', {
    title: lang('Detail'),
    id: 'winGardenDetail',
    closable: true,
    modal: false,
    closeAction: 'show',
    width: '80%',
    height: '90%',
    layout: 'fit',
    items: [{
            xtype: 'gridpanel',
            store: store_garden,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: store_garden,
                    dock: 'bottom',
                    displayInfo: true
                }],
            columns: [{
                    text: '#',
                    xtype: 'rownumberer',
                    width: '50'
                }, {
                    text: lang('FarmerID'),
                    dataIndex: 'FarmerID',
                    flex: 1,
                }, {
                    text: lang('Farmer Name'),
                    dataIndex: 'FarmerName',
                    flex: 1,
                }, {
                    text: lang('Group Name'),
                    dataIndex: 'GroupName',
                    flex: 1,
                }, {
                    text: lang('Village'),
                    dataIndex: 'Village',
                    flex: 1,
                }, {
                    text: lang('Sub District'),
                    dataIndex: 'SubDistrict',
                    flex: 1,
                }, {
                    text: lang('District'),
                    dataIndex: 'District',
                    flex: 1,
                }, {
                    text: lang('Gardens'),
                    dataIndex: 'Garden',
                    flex: 1,
                }, {
                    text: lang('Certificate Holder'),
                    dataIndex: 'CertificateHolder',
                    flex: 1,
                }, {
                    text: lang('Garden (Ha)'),
                    dataIndex: 'GardenHaUncertified',
                    flex: 1,
                    xtype: 'numbercolumn',
                    format: '0,000'
                }
            ]
        }
    ]
});

function displayWinGardenDetail(type) {
    if (!winGardenDetail.isVisible()) {
        var fprovince = $("#fprovince").val();
        var fdistrict = $("#fdistrict").val();
        store_garden.load({
            params: {
                ProvinceID: fprovince,
                DistrictID: fdistrict,
                type: type,
                category: 'garden'
            }
        });
        winGardenDetail.center();
        winGardenDetail.show();

        //set title
        switch (type) {
            case 'farmer':
                winGardenDetail.setTitle(lang('Detail Garden for Conventional Farmers'));
                break;
            case 'sme':
                winGardenDetail.setTitle(lang('Detail Garden for SME'));
                break;
            case 'mill':
                winGardenDetail.setTitle(lang('Detail Garden for Mill'));
                break;
            default:
                winGardenDetail.setTitle(lang('Detail'));
                break;
        }

    } else {
        winGardenDetail.close();
    }
}
        */
