/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 20 2020
 *  File : kpi_koltiva.js
 *******************************************/

// function runSearch() {
var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    let fyear      = $("#fyear").val();
    let PartnerID  = $("#PartnerID").val();
    
    $.ajax({
        type: "GET",
        url: url,
        data: {
            year: fyear == null ? null : fyear,
            PartnerID : PartnerID == null ? m_partner_id : PartnerID
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (r) {
            //console.log(r);

            //dashlet
            $('#d_farmers').text(number_format(r.single.RegisteredFarmer,0,'.',','));
            $('#d_farmers_tc').text(number_format(r.single.TrainOrCoachFarmers,0,'.',','));
            $('#d_farm_registered').text(number_format(r.single.RegisteredPlantation,0,'.',','));
            $('#d_farm_ha').text(number_format(r.single.RegisteredPlantationHectares,0,'.',','));
            $('#d_farmers_responsource').text(number_format(r.single.ResponSourcingFarmers,0,'.',','));
            $('#d_trace_trans').text(number_format(r.single.TraceTransaction,0,'.',','));
            $('#d_sme').text(number_format(r.single.RegisteredSME,0,'.',','));
            $('#d_platform_users').text(number_format(r.single.PlatformUsers,0,'.',','));
            $('#d_farmx').text(number_format(r.single.FarmXUsers,0,'.',','));
            $('#d_farmgate').text(number_format(r.single.FarmGateUsers,0,'.',','));
            $('#d_farmretail').text(number_format(r.single.FarmRetailUsers,0,'.',','));
            $('#d_farmcloud').text(number_format(r.single.FarmCloudUsers,0,'.',','));

            //chart
            gauge_single('c_farmers', lang('Farmer Registered'), [{max: r.target.RegisteredFarmer, data: r.single.RegisteredFarmer, name: lang('Farmer Registered')}]);
            gauge_single('c_farmers_tc', lang('Farmers Trained or Coached'), [{max: r.target.TrainOrCoachFarmers, data: r.single.TrainOrCoachFarmers, name: lang('Farmer Registered')}]);

            gauge_single('c_farm_registered', lang('Farmer Plantation Registered'), [{max: r.target.RegisteredPlantation, data: r.single.RegisteredPlantation, name: lang('Farmer Plantation Registered')}]);
            gauge_single('c_farm_ha', lang('Farmer Plantation (Ha)'), [{max: r.target.RegisteredPlantationHectares, data: r.single.RegisteredPlantationHectares, name: lang('Farmer Plantation (Ha)')}]);
            
            gauge_single('c_farmers_responsource', lang('Farmers active in Responsible Sourcing'), [{max: r.target.ResponSourcingFarmers, data: r.single.ResponSourcingFarmers, name: lang('Farmers active in Responsible Sourcing')}]);
            gauge_single('c_trace_trans', lang('Traceability Transactions'), [{max: r.target.TraceTransaction, data: r.single.TraceTransaction, name: lang('Traceability Transactions')}]);

            gauge_single('c_platform_users', lang('Platform Users'), [{max: r.target.PlatformUsers, data: r.single.PlatformUsers, name: lang('Platform Users')}]);
            gauge_single('c_sme', lang('Small and Medium Enterprises Registered'), [{max: r.target.RegisteredSME, data: r.single.RegisteredSME, name: lang('Cocoa Traceability Transactions')}]);

            gauge_single('c_farmx', lang('FarmXtension Users'), [{max: r.target.FarmXUsers, data: r.single.FarmXUsers, name: lang('FarmXtension Users')}]);
            gauge_single('c_farmgate', lang('FarmGate Users'), [{max: r.target.FarmGateUsers, data: r.single.FarmGateUsers, name: lang('FarmGate Users')}]);
            gauge_single('c_farmretail', lang('FarmRetail Users'), [{max: r.target.FarmRetailUsers, data: r.single.FarmRetailUsers, name: lang('Cocoa Traceability Transactions')}]);
            gauge_single('c_farmcloud', lang('FarmCloud Users'), [{max: r.target.FarmCloudUsers, data: r.single.FarmCloudUsers, name: lang('FarmCloud Users')}]);

            $('#row-fluid').css('display', '');
            $('#wrapper').removeClass('cover');

            //Buat sesuaikan posisi chartnya lagi, ntah kenapa gk mau center
            setTimeout(function(){ 
                $('#c_farmers').highcharts().reflow();
                $('#c_farmers_tc').highcharts().reflow();
                $('#c_farm_registered').highcharts().reflow();
                $('#c_farm_ha').highcharts().reflow();
                $('#c_farmers_responsource').highcharts().reflow();
                $('#c_trace_trans').highcharts().reflow();
                $('#c_platform_users').highcharts().reflow();
                $('#c_sme').highcharts().reflow();
                $('#c_farmx').highcharts().reflow();
                $('#c_farmgate').highcharts().reflow();
                $('#c_farmretail').highcharts().reflow();
                $('#c_farmcloud').highcharts().reflow();
            }, 1250);
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

    //Load combo partner
    Ext.Ajax.request({
        url: m_api+'/common/cmb_partner',
        params : {PartnerID : 1},
        method: 'GET',
        success: function(rp, o) {
            var r = Ext.decode(rp.responseText);

            $('#PartnerID').find('option').remove().end();
            $.each(r.data, function(index, val) {
                $('#PartnerID').append('<option value="'+val.id+'">'+val.label+'</option>');
            });

            //set valuenya
            $('#PartnerID').val(m_partner_id);
        },
        failure: function(rp, o) {
            $('#PartnerID').find('option').remove().end();
        }
    });

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

// $(function () {
//     runSearch();
// });