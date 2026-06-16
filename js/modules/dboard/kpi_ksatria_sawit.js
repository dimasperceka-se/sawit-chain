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
            //dashlet
            $("#d_palm_oil_mill").text(number_format(r.data.MillParticipant,0,'.',','));
            $("#d_farmers_registered").text(number_format(r.data.Farmers,0,'.',','));
            $("#d_farm_registered").text(number_format(r.data.Plantation,0,'.',','));
            $("#d_farm_ha").text(number_format(r.data.PlantationArea,0,'.',','));
            $("#d_sme_mapped").text(number_format(r.data.SMEMapped,0,'.',','));
            $("#d_farmcloud").text(number_format(r.data.FarmCUser,0,'.',','));
            $("#d_farmgate").text(number_format(r.data.FarmGUser,0,'.',','));
            $("#d_farmgt").text(number_format(r.data.FarmGMT,0,'.',','));

            //gauge target
            gauge_single("gauge_palm_oil_mill", lang('Palm Oil Mills Participant'), [{max: r.target.MillParticipant, data: r.data.MillParticipant, name: lang('Palm Oil Mills Participant')}]);
            gauge_single("gauge_sme_mapped", lang('Palm Oil SME Mapped'), [{max: r.target.SMEMapped, data: r.data.SMEMapped, name: lang('Palm Oil SME Mapped')}]);
            gauge_single("gauge_farmers_registered", lang('Palm Oil Farmers'), [{max: r.target.Farmers, data: r.data.Farmers, name: lang('Palm Oil Farmers')}]);
            gauge_single("gauge_farm_registered", lang('Palm Oil Plantations'), [{max: r.target.Plantation, data: r.data.Plantation, name: lang('Palm Oil Plantations')}]);
            gauge_single("gauge_farm_ha", lang('Palm Oil Plantations Area (Ha)'), [{max: r.target.PlantationArea, data: r.data.PlantationArea, name: lang('Palm Oil Plantations Area (Ha)')}]);
            gauge_single("gauge_farmcloud", lang('FarmCloud Users'), [{max: r.target.FarmCUser, data: r.data.FarmCUser, name: lang('FarmCloud Users')}]);
            gauge_single("gauge_farmgate", lang('FarmGate Users'), [{max: r.target.FarmGUser, data: r.data.FarmGUser, name: lang('FarmGate Users')}]);
            gauge_single("gauge_farmretail", lang('FarmGate MT Traceable'), [{max: r.target.FarmGMT, data: r.data.FarmGMT, name: lang('FarmGate MT Traceable')}]);

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
    var Wave = sel.value;

    if(Wave == 'all_wave'){
        $("#filter_lock_date_sawit").hide();
    }else{
        $("#filter_lock_date_sawit").show();
    }

    getComboLockDate(Wave);
    getCluster(Wave);
}

function getCluster(wave){
    //Combo Cluster
    Ext.Ajax.request({
        url: m_api+'/common/combo_cluster',
        params: {'wave': wave},
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);
            if (r) {
                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Project Area') + '</option>');
                $.each(r.data, function (index, val) {
                    $('#filter_cluster_sawit').append('<option value="' + val.id + '">' + val.label + '</option>');
                });
            } else {
                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Project Area') + '</option>');
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
        url: m_api+'/dboard/combo_filter_lock_date_ks',
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
    // var s = ajaxDataRenderer(m_data);
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
        url: m_api+'/dboard/combo_filter_wave_ks',
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);

            $('#filter_wave_sawit').find('option').remove().end();
            $('#filter_wave_sawit').append('<option value="all_wave">'+lang('All Program')+'</option>');
            $.each(r, function(index, val) {
                $('#filter_wave_sawit').append('<option value="'+val.id+'">'+val.label+'</option>');
            });

            $.get(m_api+'/dboard/default_wave_ks', function(data) {
                if (data) {
                    $('#filter_wave_sawit').val(data.data.id);
                    //Combo Lock Date
                    var wave = data.data.id;
                    $.ajax({
                        type: "GET",
                        url: m_api+'/dboard/combo_filter_lock_date_ks/?search&wave=' + wave,
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

                    Ext.Ajax.request({
                        url: m_api+'/common/combo_cluster',
                        params: {'wave': wave},
                        method: 'GET',
                        success: function(rp, o) {
                            var r = Ext.decode(rp.responseText);
                            if (r) {
                                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Project Area') + '</option>');
                                $.each(r.data, function (index, val) {
                                    $('#filter_cluster_sawit').append('<option value="' + val.id + '">' + val.label + '</option>');
                                });
                            } else {
                                $('#filter_cluster_sawit').find('option').remove().end().append('<option value="all_cluster">' + lang('All Project Area') + '</option>');
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
    $.get(m_api + '/dashboard_export_detail_ksatria_sawit/' + OpsiCall + '/' + wave + '/' + cluster + '/' + lockdate, function (data) {
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