/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Aug 08 2019
 *  File : bluenumber_sdg.js
 *******************************************/
var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    
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
            $('#box_farmer_registered').html(number_format(r.dataDisplay.TotalFarmers,0,'.',','));
            $('#box_consent_signed').html(number_format(r.dataDisplay.ConsentLetterSigned,0,'.',','));
            $('#box_plantation_mapped').html(number_format(r.dataDisplay.GardenTotal,0,'.',','));
            $('#box_plant_ha_mapped').html(number_format(r.dataDisplay.GardenTotalHa,0,'.',','));
            $('#box_family').html(number_format(r.dataDisplay.Family,0,'.',','));
            $('#box_working').html(number_format(r.dataDisplay.Workers,0,'.',','));
            $('#box_ave_sdg_score').html(number_format(r.dataDisplay.SdgScore,2,'.',','));

            //data gauge chart
            gauge_single('gauge_farmer_registered', lang('Oil Palm Farmers Registered'), [{max: 500, data: r.dataDisplay.TotalFarmers, name: lang('Oil Palm Farmers Registered')}]);
            gauge_single('gauge_consent_signed', lang('Consent Letters Signed'), [{max: 500, data: r.dataDisplay.ConsentLetterSigned, name: lang('Consent Letters Signed')}]);
            gauge_single('gauge_plantation_mapped', lang('Oil Palm Plantations Registered'), [{max: 500, data: r.dataDisplay.GardenTotal, name: lang('Oil Palm Plantations Registered')}]);
            //gauge_single('gauge_ave_sdg_score', lang('Average SDG Questions Score'), [{max: 5, data: parseFloat(number_format(r.dataDisplay.SdgScore,2,'.',',')), name: lang('Average SDG Questions Score')}]);


            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        },
        failure: function() {
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
};

var arrReturn = ajaxDataRenderer(m_data);

$(document).on('click', '#btnExportExcel', function(event) {
    event.preventDefault();
    
    Ext.MessageBox.show({
        msg: 'Please wait...',
        progressText: 'Exporting...',
        width: 300,
        wait: true,
        waitConfig: {
            interval: 200
        },
        icon: 'ext-mb-info', //custom class in msg-box.html
        animateTarget: 'mb9'
    });

    Ext.Ajax.request({
        url: m_api + '/dboard/dash_bluenumber_export_excel',
        method: 'POST',
        waitMsg: lang('Please Wait'),
        success: function(rp, o) {
            Ext.MessageBox.hide();
            var r = Ext.decode(rp.responseText);
            window.location = r.filenya;
        },
        failure: function(rp, o) {
            Ext.MessageBox.hide();
            try {
                var r = Ext.decode(rp.responseText);
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: r.message,
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
            catch(err) {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: 'Connection Error',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        }
    });
});