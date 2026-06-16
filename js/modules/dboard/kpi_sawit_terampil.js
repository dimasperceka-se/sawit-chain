/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 20 2020
 *  File : kpi_koltiva.js
 *******************************************/

// function runSearch() {
var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');

    let wave = $("#filter_wave_sawit").val();
    let chdistrict = $("#filter_cluster_sawit").val();
    let lockdate = $("#filter_lock_date_sawit").val();
    
    $.ajax({
        type: "GET",
        url: url,
        data: {
            lockdate: lockdate == null ? null : lockdate,
            wave : wave == null ? null : wave,
            chdistrict : chdistrict == null ? null : chdistrict
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (r) {
            console.log(r.data.farmers_registered_detail.length);

            //dashlet
            $("#d_palm_oil_mill").text(number_format(r.data.palm_oil_mill,0,'.',','));
            $("#d_farmers_registered").text(number_format(r.data.farmers_registered,0,'.',','));
            $("#d_farm_registered").text(number_format(r.data.farm_registered,0,'.',','));
            $("#d_farm_ha").text(number_format(r.data.farm_ha,0,'.',','));
            $("#d_soc_sel").text(number_format(r.data.soc_sel,0,'.',','));
            $("#d_farmer_survey").text(number_format(r.data.farmer_survey,0,'.',','));
            $("#d_farm_survey").text(number_format(r.data.farm_survey,0,'.',','));
            $("#d_polygon_mapping").text(number_format(r.data.polygon_mapping,0,'.',','));
            $("#d_individual_farmer_coaching").text(number_format(r.data.individual_farmer_coaching,0,'.',','));
            $("#d_individual_farmer_coaching_session").text(number_format(r.data.individual_farmer_coaching_session,0,'.',','));
            $("#d_broadcast_sms").text(number_format(r.data.broadcast_sms,0,'.',','));
            $("#d_farmer_id_card").text(number_format(r.data.farmer_id_card,0,'.',','));
            $("#d_farmx").text(number_format(r.data.farmx,0,'.',','));
            $("#d_farmgate").text(number_format(r.data.farmgate,0,'.',','));
            $("#d_farmretail").text(number_format(r.data.farmretail,0,'.',','));
            $("#d_farmcloud").text(number_format(r.data.farmcloud,0,'.',','));

            var farmer_registered_detail = '';
            for (let i = 0; i < r.data.farmers_registered_detail.length; i++) {
                farmer_registered_detail += '<li><a data-type="'+r.data.farmers_registered_detail[i].objtype+'" href="#"><span class="label">'+r.data.farmers_registered_detail[i].objtype+'</span><span class="value" id="'+r.data.farmers_registered_detail[i].objtype+'">'+parseFloat(r.data.farmers_registered_detail[i].total)+'</span> </a></li>';
            }
            $('#farmer_register_detail').html(farmer_registered_detail);

            var mill_registered_detail = '';
            for (let i = 0; i < r.data.mill_detail.length; i++) {
                mill_registered_detail += '<li><a data-type="'+r.data.mill_detail[i].objtype+'" href="#"><span class="label">'+r.data.mill_detail[i].objtype+'</span><span class="value" id="'+r.data.mill_detail[i].objtype+'">'+parseFloat(r.data.mill_detail[i].total)+'</span> </a></li>';
            }
            $('#mill_register_detail').html(mill_registered_detail);

            var farm_registered_detail = '';
            var ha_registered_detail = '';
            for (let i = 0; i < r.data.farm_detail.length; i++) {
                farm_registered_detail += '<li><a data-type="'+r.data.farm_detail[i].objtype+'" href="#"><span class="label">'+r.data.farm_detail[i].objtype+'</span><span class="value" id="'+r.data.farm_detail[i].objtype+'">'+parseFloat(r.data.farm_detail[i].total)+'</span> </a></li>';
                ha_registered_detail += '<li><a data-type="'+r.data.farm_detail[i].objtype+'" href="#"><span class="label">'+r.data.farm_detail[i].objtype+'</span><span class="value" id="'+r.data.farm_detail[i].objtype+'">'+parseFloat(r.data.farm_detail[i].total_ha)+'</span> </a></li>';
            }
            $('#farm_register_detail').html(farm_registered_detail);
            $('#ha_register_detail').html(ha_registered_detail);

            //gauge target
            gauge_single("gauge_palm_oil_mill", lang('Palm Oil Mills'), [{max: r.target.palm_oil_mill, data: r.data.palm_oil_mill, name: lang('Palm Oil Mills')}]);            
            gauge_single("gauge_farmers_registered", lang('Farmers Registered'), [{max: r.target.farmers_registered, data: r.data.farmers_registered, name: lang('Farmers Registered')}]);
            gauge_single("gauge_farm_registered", lang('Farm Registered'), [{max: r.target.farm_registered, data: r.data.farm_registered, name: lang('Farm Registered')}]);
            gauge_single("gauge_farm_ha", lang('Farm Ha'), [{max: r.target.farm_ha, data: r.data.farm_ha, name: lang('Farm Ha')}]);
            gauge_single("gauge_soc_sel", lang('Socialization and Selection'), [{max: r.target.soc_sel, data: r.data.soc_sel, name: lang('Socialization and Selection')}]);
            gauge_single("gauge_farmer_survey", lang('Farmer Survey'), [{max: r.target.farmer_survey, data: r.data.farmer_survey, name: lang('Farmer Survey')}]);
            gauge_single("gauge_farm_survey", lang('Farm Survey'), [{max: r.target.farm_survey, data: r.data.farm_survey, name: lang('Farm Survey')}]);
            gauge_single("gauge_polygon_mapping", lang('Polygon Mapping'), [{max: r.target.polygon_mapping, data: r.data.polygon_mapping, name: lang('Polygon Mapping')}]);
            gauge_single("gauge_individual_farmer_coaching", lang('Individual Farmer Coaching'), [{max: r.target.individual_farmer_coaching, data: r.data.individual_farmer_coaching, name: lang('Individual Farmer Coaching')}]);
            gauge_single("gauge_individual_farmer_coaching_session", lang('Individual Farmer Coaching Session'), [{max: r.target.individual_farmer_coaching_session, data: r.data.individual_farmer_coaching_session, name: lang('Individual Farmer Coaching Session')}]);
            gauge_single("gauge_broadcast_sms", lang('Broadcast SMS'), [{max: r.target.broadcast_sms, data: r.data.broadcast_sms, name: lang('Broadcast SMS')}]);
            gauge_single("gauge_farmer_id_card", lang('Farmer ID Card'), [{max: r.target.farmer_id_card, data: r.data.farmer_id_card, name: lang('Farmer ID Card')}]);
            gauge_single("gauge_farmx", lang('FarmX Users'), [{max: r.target.farmx, data: r.data.farmx, name: lang('FarmX Users')}]);
            gauge_single("gauge_farmgate", lang('FarmGate Users'), [{max: r.target.farmgate, data: r.data.farmgate, name: lang('FarmGate Users')}]);
            gauge_single("gauge_farmretail", lang('FarmRetail Users'), [{max: r.target.farmretail, data: r.data.farmretail, name: lang('FarmRetail Users')}]);
            gauge_single("gauge_farmcloud", lang('FarmCloud Users'), [{max: r.target.farmcloud, data: r.data.farmcloud, name: lang('FarmCloud Users')}]);

            $('#row-fluid').css('display', '');
            $('#wrapper').removeClass('cover');

            //Buat sesuaikan posisi chartnya lagi, ntah kenapa gk mau center
            // setTimeout(function(){ 
            //     $('#c_farmers').highcharts().reflow();
            //     $('#c_farmers_tc').highcharts().reflow();
            //     $('#c_farm_registered').highcharts().reflow();
            //     $('#c_farm_ha').highcharts().reflow();
            //     $('#c_farmers_responsource').highcharts().reflow();
            //     $('#c_trace_trans').highcharts().reflow();
            //     $('#c_platform_users').highcharts().reflow();
            //     $('#c_sme').highcharts().reflow();
            //     $('#c_farmx').highcharts().reflow();
            //     $('#c_farmgate').highcharts().reflow();
            //     $('#c_farmretail').highcharts().reflow();
            //     $('#c_farmcloud').highcharts().reflow();
            // }, 1250);
        },
        error: function(result){
            $('#row-fluid').css('display', '');
            $('#wrapper').removeClass('cover');

            Ext.MessageBox.show({
                title: 'Failed',
                msg: 'Failed to retrieve data',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-error'
            });
        }
    });
}

function selectWaveSawit(sel) {
    console.log(sel);
    var Wave = sel.value;
    var Cluster = $("#filter_cluster_sawit option:selected").text();
    getComboLockDate(Wave);

    //Load combo cluster
    Ext.Ajax.request({
        url: m_api+'/common/combo_cluster',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: {wave: Wave},
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);
            if (r) {
                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Clusters') + '</option>');
                $.each(data.data, function (index, val) {
                    $('#filter_cluster_sawit').append('<option value="' + val.id + '">' + val.name + '</option>');
                });
            } else {
                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Clusters') + '</option>');
                $('#filter_cluster_sawit').val("all_cluster");
            }
        },
        failure: function(rp, o) {
            $('#filter_cluster_sawit').find('option').remove().end();
        }
    });
}

function getComboLockDate(wave){
    //Load combo lockdate
    Ext.Ajax.request({
        url: m_api+'/dboard/combo_filter_lock_date_sawit',
        params:{'wave':wave},
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);

            $('#filter_lock_date_sawit').find('option').remove().end();
            $.each(r, function(index, val) {
                $('#filter_lock_date_sawit').append('<option value="'+val.id+'">'+val.label+'</option>');
            });
        },
        failure: function(rp, o) {
            $('#filter_lock_date_sawit').find('option').remove().end();
        }
    });
}

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

    //Load combo wave
    Ext.Ajax.request({
        url: m_api+'/dboard/combo_filter_wave_sawit',
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);

            $('#filter_wave_sawit').find('option').remove().end();
            $.each(r, function(index, val) {
                $('#filter_wave_sawit').append('<option value="'+val.id+'">'+val.label+'</option>');
            });

            $.get(m_api+'/dboard/default_wave_sawit', function(data) {
                if (data) {
                    $('#filter_wave_sawit').val(data.data.id);
                    //Combo Lock Date
                    var wave = data.data.id;
                    $.ajax({
                        type: "GET",
                        url: m_api+'/dboard/combo_filter_lock_date_sawit/?search&wave=' + wave,
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        async: false,
                        success: function (data) {
                            if (data) {
                                $.each(data, function(index, val) {
                                    $('#filter_lock_date_sawit').append('<option value="'+val.id+'">'+val.label+'</option>');
                                });
                                ajaxDataRenderer(m_data);
                            }
                        }
                    });

                    //Combo Cluster
                    Ext.Ajax.request({
                        url: m_api+'/common/combo_cluster?wave='+wave,
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        method: 'GET',
                        success: function(rp, o) {
                            var r = Ext.decode(rp.responseText);
                            if (r) {
                                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Clusters') + '</option>');
                                $.each(r.data, function (index, val) {
                                    $('#filter_cluster_sawit').append('<option value="' + val.id + '">' + val.label + '</option>');
                                });
                            } else {
                                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Clusters') + '</option>');
                                $('#filter_cluster_sawit').val("all_cluster");
                            }
                        },
                        failure: function(rp, o) {
                            $('#filter_cluster_sawit').find('option').remove().end();
                        }
                    });
                }
            });
        },
        failure: function(rp, o) {
            $('#filter_wave_sawit').find('option').remove().end();
        }
    });
});

function ClickExportExcelDetail(OpsiCall) {
    lockdate = $("#filter_lock_date_sawit").val();
    cluster = $("#filter_cluster_sawit").val();
    wave = $("#filter_wave_sawit").val();
    
    $("#topImgLoadingSce" + OpsiCall).show();
    $.get(m_api + '/dashboard_export_detail_sawit/' + OpsiCall + '/' + wave + '/' + cluster + '/' + lockdate, function (data) {
        if (data) {
            console.log(data);
            if (data.count_data == 0) {
                Ext.MessageBox.show({
                    title: 'Attention',
                    msg: lang('No data found'),
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-info'
                });
            } else {
                window.location = m_url + '/api/' + data.UrlFilenya;
            }
        }
        $("#topImgLoadingSce" + OpsiCall).hide();
    });
}

function ClickExportExcelSummary() {
    lockdate = $("#filter_lock_date_sawit").val();
    cluster = $("#filter_cluster_sawit").val();
    wave = $("#filter_wave_sawit").val();
    
    $("#topImgLoadingSceSummary").show();
    $.get(m_api + '/dashboard_export_detail_sawit/export_summary/' + wave + '/' + cluster + '/' + lockdate, function (data) {
        if (data) {
            //console.log(data);
            if (data.count_data == 0) {
                Ext.MessageBox.show({
                    title: 'Attention',
                    msg: lang('No data found'),
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-info'
                });
            } else {
                window.location = m_url + '/api/' + data.UrlFilenya;
                // console.log(data);
            }
        }
        $("#topImgLoadingSceSummary").hide();
    });
}