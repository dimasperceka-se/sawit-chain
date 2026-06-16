// if (m_prov!='') dataDistrict(m_data,'survey'); 
var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    var s = [];
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {

            var box_gfp_baseline = 0, box_gfp_postline = 0, box_bank_account_baseline = 0, box_bank_account_postline = 0
            series_bank_account_baseline = [], series_bank_account_postline = [], series_saving_baseline = [], series_saving_postline = [],
            finance_categories = [];
            if (r.finance) {
                $.each(r.finance, function(index, val) {
                    box_gfp_baseline += parseFloat(val.gfp_baseline);
                    box_gfp_postline += parseFloat(val.gfp_postline);
                    box_bank_account_baseline += parseFloat(val.bank_account_baseline);
                    box_bank_account_postline += parseFloat(val.bank_account_postline);

                    finance_categories[index] = lang(val.label);
                    series_bank_account_baseline[index] = parseInt(val.gfp_baseline)?(parseInt(val.bank_account_baseline)/parseInt(val.gfp_baseline)*100):0;
                    series_bank_account_postline[index] = parseInt(val.gfp_postline)?(parseInt(val.bank_account_postline)/parseInt(val.gfp_postline)*100):0;
                    series_saving_baseline[index] = parseFloat(val.gfp_baseline)?(parseInt(val.saving_baseline)/parseFloat(val.gfp_baseline)*100):0;
                    series_saving_postline[index] = parseFloat(val.gfp_postline)?(parseInt(val.saving_postline)/parseFloat(val.gfp_postline)*100):0;
                });
            }
            s.finance_categories  = finance_categories;
            s.series_bank_account_baseline  = series_bank_account_baseline;
            s.series_bank_account_postline  = series_bank_account_postline;
            s.series_saving_baseline        = series_saving_baseline;
            s.series_saving_postline        = series_saving_postline;

            $('#box_gfp_baseline').text(number_format(box_gfp_baseline,0,'.',','));
            $('#box_gfp_postline').text(number_format(box_gfp_postline,0,'.',','));
            $('#box_bank_account_baseline').text(number_format(box_bank_account_baseline/box_gfp_baseline*100,1,'.',','));
            $('#box_bank_account_postline').text(number_format(box_bank_account_postline/box_gfp_postline*100,1,'.',','));

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
return s; 
};

var s = ajaxDataRenderer(m_data); 

column([{name: lang('Baseline'),data: s.series_bank_account_baseline, stack:'Baseline'},{name: lang('Post-Line'),data: s.series_bank_account_postline, stack:'Post-Line'}], 'chart_bank_accounts', 
    lang('Number of Bank Accounts'), lang('%'), ['#3B5323','#589C14'], s.finance_categories,'normal',0,true);
column([{name: lang('Baseline'),data: s.series_saving_baseline, stack:'Baseline'},{name: lang('Post-Line'),data: s.series_saving_postline, stack:'Post-Line'}], 'chart_farmer_saving', 
    lang('Number of Farmers with Savings'), lang('%'), ['#3B5323','#589C14'], s.finance_categories,'normal',0,true);
