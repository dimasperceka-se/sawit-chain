/*
* @Author: nikolius
* @Date:   2017-09-08 14:50:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-12-08 11:05:51
*/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    let fcountry   = $("#fcountry").val();
    let fprovince  = $("#fprovince").val();
    let fdistrict  = $("#fdistrict").val();
    let fyear      = $("#fyear").val();

    $.ajax({
        type: "GET",
        url: url,
        // data: {prov: m_ProvinceID,kab: m_DistrictID},
        data: {
            country: fcountry == 'all_country' ? m_country : fcountry
            ,prov: fprovince == 'all_province' ? m_prov : fprovince
            ,kab: fdistrict == 'all_district' ? m_kab : fdistrict
            ,farm_type: m_farm_type
            ,year: fyear == null ? null : fyear
        },
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
            $('#box_consent_signed').html(number_format(r.dataDisplay.consent_signed,0,'.',','));
            $('#box_mills_mapped').html(number_format(r.dataDisplay.mills_mapped,0,'.',','));
            $('#box_agents_mapped').html(number_format(r.dataDisplay.agents_mapped,0,'.',','));

            //data gauge chart
            gauge_single('gauge_farmer_registered', lang('Palm Oil Farmers Registered'), [{max: r.dataTarget.farmer_registered, data: r.dataDisplay.farmer_registered, name: lang('Palm Oil Farmers Registered')}]);

            gauge_single('gauge_plantation_mapped', lang('Palm Oil Plantations Registered'), [{max: r.dataTarget.plantation_mapped, data: r.dataDisplay.plantation_mapped, name: lang('Palm Oil Plantations Registered')}]);
            gauge_single('gauge_plant_ha_mapped', lang('Palm Oil Plantations Area by Farmer Interview (Ha)'), [{max: r.dataTarget.plant_ha_mapped, data: r.dataDisplay.plant_ha_mapped, name: lang('Palm Oil Plantations Area by Farmer Interview (Ha)')}]);

            gauge_single('gauge_plantation_polygon_mapped', lang('Palm Oil Plantations Mapped with Polygon'), [{max: r.dataTarget.plantation_polygon_mapped, data: r.dataDisplay.plantation_polygon_mapped, name: lang('Palm Oil Plantations Mapped with Polygon')}]);
            gauge_single('gauge_plant_polygon_ha_mapped', lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)'), [{max: r.dataTarget.plant_polygon_ha_mapped, data: r.dataDisplay.plant_polygon_ha_mapped, name: lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)')}]);

            gauge_single('gauge_consent_signed', lang('Consent Letters Signed'), [{max: r.dataTarget.consent_signed, data: r.dataDisplay.consent_signed, name: lang('Consent Letters Signed')}]);
            gauge_single('gauge_mills_mapped', lang('Palm Oil Mills Mapped'), [{max: r.dataTarget.mills_mapped, data: r.dataDisplay.mills_mapped, name: lang('Palm Oil Mills Mapped')}]);
            gauge_single('gauge_agents_mapped', lang('Palm Oil SME Mapped'), [{max: r.dataTarget.agents_mapped, data: r.dataDisplay.agents_mapped, name: lang('Palm Oil SME Mapped')}]);

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

};

$(document).ready(function () {
    var s = ajaxDataRenderer(m_data);
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

    Ext.Ajax.request({
        url: m_api+'/common/combo_filter_country',
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);
            //console.log(r);

            $('#fcountry').find('option').remove().end().append('<option value="all_country">'+lang('All Country')+'</option>');
            $.each(r, function(index, val) {
                $('#fcountry').append('<option value="'+val.id+'">'+val.label+'</option>');
            });
        },
        failure: function(rp, o) {
            $('#fcountry').find('option').remove().end().append('<option value="all_country">'+lang('All Country')+'</option>');
            $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All')+' '+lang(m_label_prov)+'</option>');
        }
    });

    //cek filter partner
    if(m_partner_id != 1) { //bukan partner koltiva
        $('#DivFilterPartner').hide();
    }

    //Load combo year
    Ext.Ajax.request({
        url: m_api+'/dboard/combo_filter_year_dash_kpi',
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);

            $('#fyear').find('option').remove().end();
            $.each(r, function(index, val) {
                $('#fyear').append('<option value="'+val.id+'">'+val.label+'</option>');
            });
        },
        failure: function(rp, o) {
            $('#fyear').find('option').remove().end();
        }
    });
});

$(document).on('change', '#fcountry', function(e) {
    if(e.target.value == 'all_country') {
        $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All')+' '+lang(m_label_prov)+'</option>');
        $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All')+' '+lang(m_label_district)+'</option>');
    } else {
        Ext.Ajax.request({
            url: m_api+'/common/combo_filter_province',
            method: 'GET',
            params: {
                CountryID: e.target.value
            },
            success: function(rp, o) {
                var r = Ext.decode(rp.responseText);

                $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All')+' '+lang(m_label_prov)+'</option>');
                $.each(r, function(index, val) {
                    $('#fprovince').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            },
            failure: function(rp, o) {
                $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All')+' '+lang(m_label_prov)+'</option>');
            }
        });
    }
});

$(document).on('change', '#fprovince', function(e) {
    if(e.target.value == 'all_province') {
        $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All')+' '+lang(m_label_district)+'</option>');
    } else {
        Ext.Ajax.request({
            url: m_api+'/common/combo_filter_district',
            method: 'GET',
            params: {
                ProvinceID: e.target.value
            },
            success: function(rp, o) {
                var r = Ext.decode(rp.responseText);

                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All')+' '+lang(m_label_district)+'</option>');
                $.each(r, function(index, val) {
                    $('#fdistrict').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            },
            failure: function(rp, o) {
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All')+' '+lang(m_label_district)+'</option>');
            }
        });
    }
});

var arrReturn = ajaxDataRenderer(m_data);