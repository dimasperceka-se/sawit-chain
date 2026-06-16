// if (m_prov!='') dataDistrict(m_data,'finance'); 

var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    var chart = [];
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var gfp = female = fin = account = saving = loan = 0;

            var count = r;  
                      
            var keys_saving = [
                {'key':'saving_money', 'label' : 'Money'},
                {'key':'saving_invest', 'label' : 'Invested in other businesses'},
                {'key':'saving_gold', 'label' : 'In-Kind'},
                {'key':'saving_no', 'label' : 'No Saving'},
            ]
            var pie_saving = new Array();
            for (var i = keys_saving.length - 1; i >= 0; i--) {                
                pie_saving[i]       = new Array();
                pie_saving[i][0]    = lang(keys_saving[i].label);
                pie_saving[i][1]    = 0;
            };

            var keys_account = [
                {'key' : 'account_active', 'label' : 'Yes, Active'},
                {'key' : 'account_inactive', 'label' : 'Yes, Not Active'},
                {'key' : 'account_no', 'label' : 'No'},
            ]
            var pie_account = new Array();
            for (var i = keys_account.length - 1; i >= 0; i--) {                
                pie_account[i]       = new Array();
                pie_account[i][0]    = lang(keys_account[i].label);
                pie_account[i][1]    = 0;
            };

            var keys_loan_exp = [
                {'key':'loan_yes_current', 'label':'Yes, at the moment (loan from bank)'},
                {'key':'loan_yes_past_current', 'label':'Yes, at the moment (other loan sources)'},
                {'key':'loan_yes_past', 'label':'Yes, but not at the moment'},
                {'key':'loan_no', 'label':'No'},
            ]
            var pie_loan_exp = new Array();
            for (var i = keys_loan_exp.length - 1; i >= 0; i--) {                
                pie_loan_exp[i]       = new Array();
                pie_loan_exp[i][0]    = lang(keys_loan_exp[i].label);
                pie_loan_exp[i][1]    = 0;
            };

            var keys_loan_from = [
                {'key':'loan_from_family','label':'Family and Friends'},
                {'key':'loan_from_bank','label':'Bank'},
                {'key':'loan_from_trader','label':'Traders'},
                {'key':'loan_from_coops','label':'Coops'},
            ]            
            var chart_loan_from = new Array();
            chart_loan_from[0] = {};
            chart_loan_from[0]['name'] = lang('Loan From');
            chart_loan_from[0]['data'] = [];
            var cat_loan_from = [];
            var count_loan_from = 0;
            for (var i = keys_loan_from.length - 1; i >= 0; i--) {                
                cat_loan_from[i] = lang(keys_loan_from[i].label);
                chart_loan_from[0]['data'][i] = 0;
            };
            // var chart_loan_from = new Array();
            // for (var i = keys_loan_from.length - 1; i >= 0; i--) {                
            //     chart_loan_from[i]       = new Array();
            //     chart_loan_from[i][0]    = lang(keys_loan_from[i].label);
            //     chart_loan_from[i][1]    = 0;
            // };

            var keys_loan_for = [
                {'key':'loan_for_farm','label':'Cocoa Farm'},
                {'key':'loan_for_other','label':'Other Business'},
                {'key':'loan_for_school','label':'School Fees'},
                {'key':'loan_for_daily','label':'Daily Expenses'},
                {'key':'loan_for_emergency','label':'Emergencies'},
                // {'key':'need_loan_no','label':'No Loan'},
            ]
            var chart_loan_for = new Array();
            chart_loan_for[0] = {};
            chart_loan_for[0]['name'] = lang('Loan Use');
            chart_loan_for[0]['data'] = [];
            var cat_loan_for = [];
            var count_loan_for = 0;
            for (var i = keys_loan_for.length - 1; i >= 0; i--) {                
                cat_loan_for[i] = lang(keys_loan_for[i].label);
                chart_loan_for[0]['data'][i] = 0;
            };
            // var chart_loan_for = new Array();
            // for (var i = keys_loan_for.length - 1; i >= 0; i--) {                
            //     chart_loan_for[i]            = new Array();
            //     chart_loan_for[i]['name']    = lang(keys_loan_for[i].label);
            //     chart_loan_for[i]['data']    = new Array();
            // };

            var keys_product = [
                {'key':'product_saving','label':'Only Savings'},
                {'key':'product_saving_loan','label':'Both, Saving and Loans'},
                {'key':'product_loan','label':'Only Loans'},
            ]
            var chart_product = new Array();
            for (var i = keys_product.length - 1; i >= 0; i--) {
                chart_product[i]            = new Array();
                chart_product[i]['name']    = lang(keys_product[i].label);
                chart_product[i]['data']    = new Array();
            };

            var keys_future = [
                {'key':'future_school','label':'School fees'},
                {'key':'future_invest_farm','label':'Farm investment'},
                {'key':'future_invest_other','label':'Other Business'},
                {'key':'future_emergency','label':'Emergencies'},
                {'key':'future_health','label':'Health Care'},
            ]
            var chart_future = new Array();
            chart_future[0] = {};
            chart_future[0]['name'] = lang('Future money needs');
            chart_future[0]['data'] = [];
            var cat_future = [];
            var count_future = 0;
            for (var i = keys_future.length - 1; i >= 0; i--) {                
                cat_future[i] = lang(keys_future[i].label);
                chart_future[0]['data'][i] = 0;
            };

            // var keys_value = [
            //     {'key':'value_10','label':'&lt; 10 '+lang('Million')},
            //     {'key':'value_10_20','label':'10 - 20 '+lang('Million')},
            //     {'key':'value_20_50','label':'20 - 50 '+lang('Million')},
            //     {'key':'value_50_100','label':'50 - 100 '+lang('Million')},
            //     {'key':'value_100_200','label':'100 - 200 '+lang('Million')},
            //     {'key':'value_200','label':'&gt; 200 '+lang('Million')},
            //     {'key':'value_0','label':lang('Don\'t know')},
            // ]
            // var pie_value = new Array();
            // for (var i = keys_value.length - 1; i >= 0; i--) {                
            //     pie_value[i]       = new Array();
            //     pie_value[i][0]    = lang(keys_value[i].label);
            //     pie_value[i][1]    = 0;
            // };

            var keys_kelamin = [
                {'key':'male','label':lang('male')},
                {'key':'female','label':lang('female')},
                // {'key':'other','label':lang('other')},
            ]
            var chart_kelamin = new Array();
            var cat_kelamin = [];
            for (var i = keys_kelamin.length - 1; i >= 0; i--) {                
                chart_kelamin[i]            = new Array();
                chart_kelamin[i]['name']    = lang(keys_kelamin[i].label);
                chart_kelamin[i]['data']    = new Array();
            };

            var count_loan_exp = 0;
            var chart_categories = new Array();
            var avg_female = gfp_count = 0;
            if (count) {
                for (var i = count.length - 1; i >= 0; i--) {
                    chart_categories[i] = lang(count[i]['label']);

                    gfp         += parseInt(count[i]['gfp']);
                    female      += parseInt(count[i]['female']);
                    // if (parseInt(count[i]['gfp'])) {
                    //     avg_female  += parseInt(count[i]['female'])/parseInt(count[i]['gfp']);
                    //     gfp_count++;
                    // }
                    fin         += parseInt(count[i]['fin']);
                    account     += parseInt(count[i]['account']);
                    saving      += parseInt(count[i]['saving']);
                    loan        += parseInt(count[i]['loan']);

                    for (var j = keys_saving.length - 1; j >= 0; j--) {         
                        pie_saving[j][1] += parseInt(count[i][keys_saving[j].key]);
                    };

                    for (var j = keys_account.length - 1; j >= 0; j--) {         
                        pie_account[j][1] += parseInt(count[i][keys_account[j].key]);
                    };

                    for (var j = keys_loan_exp.length - 1; j >= 0; j--) {         
                        pie_loan_exp[j][1] += parseInt(count[i][keys_loan_exp[j].key]);
                    };

                    count_loan_exp += parseInt(count[i]['loan_yes_current'])+parseInt(count[i]['loan_yes_past_current'])+parseInt(count[i]['loan_yes_current']);
                    for (var j = keys_loan_from.length - 1; j >= 0; j--) {         
                        // chart_loan_from[j][1] += parseInt(count[i][keys_loan_from[j].key]);
                        // count_loan_from += parseInt(count[i][keys_loan_from[j].key]);
                        chart_loan_from[0]['data'][j] += parseInt(count[i][keys_loan_from[j].key]);
                    };

                    for (var j = keys_loan_for.length - 1; j >= 0; j--) {
                        // chart_loan_for[j]['data'][i]    = parseInt(count[i][keys_loan_for[j].key]);
                        // count_loan_for += parseInt(count[i][keys_loan_for[j].key]);
                        chart_loan_for[0]['data'][j] += parseInt(count[i][keys_loan_for[j].key]);
                    };

                    for (var j = keys_product.length - 1; j >= 0; j--) {
                        chart_product[j]['data'][i]    = parseInt(count[i][keys_product[j].key]);
                    };

                    count_future += parseInt(count[i]['future_count']);
                    for (var j = keys_future.length - 1; j >= 0; j--) {         
                        // chart_future[j]['data'][i] = parseInt(count[i][keys_future[j].key]);
                        chart_future[0]['data'][j] += parseInt(count[i][keys_future[j].key]);
                    };
                    // console.log(count_future);
                    // console.log(chart_future);

                    // for (var j = keys_value.length - 1; j >= 0; j--) {         
                    //     pie_value[j][1] += parseInt(count[i][keys_value[j].key]);
                    // };

                    for (var j = keys_kelamin.length - 1; j >= 0; j--) {
                        chart_kelamin[j]['data'][i]    = parseInt(count[i][keys_kelamin[j].key]);
                    };
                };
                // avg_female = avg_female/gfp_count*100;
            };

            for (var j = keys_future.length - 1; j >= 0; j--) {        
                chart_future[0]['data'][j] = chart_future[0]['data'][j]/count_future*100;
            };

            for (var j = keys_loan_for.length - 1; j >= 0; j--) {        
                chart_loan_for[0]['data'][j] = chart_loan_for[0]['data'][j]/count_loan_exp*100;
            };

            for (var j = keys_loan_from.length - 1; j >= 0; j--) {        
                chart_loan_from[0]['data'][j] = chart_loan_from[0]['data'][j]/count_loan_exp*100;
            };
            
            chart['chart_saving']       = pie_saving;
            chart['chart_account']      = pie_account;
            chart['chart_loan_exp']     = pie_loan_exp;
            chart['chart_loan_from']    = chart_loan_from;
            chart['cat_loan_from']      = cat_loan_from;
            chart['chart_loan_for']     = chart_loan_for;
            chart['cat_loan_for']       = cat_loan_for;
            chart['chart_product']      = chart_product;
            chart['chart_future']       = chart_future;
            chart['cat_future']         = cat_future;
            // chart['chart_value']        = pie_value;

            chart['chart_categories']   = chart_categories;


            var chart_household = new Array();
            if (household = r) {
                $.each(household, function(index, val) {
                    chart_household[index] = [];
                    chart_household[index][0] = lang(val.label);
                    chart_household[index][1] = parseInt(val.gfp);
                });
            };
            chart['chart_household'] = chart_household;

            // if (kelamin = r['kelamin']) {
            //     for (var i = 0; i < kelamin.length; i++) {
            //         cat_kelamin[i] = lang(kelamin[i]['label']);
            //         for (var j = keys_kelamin.length - 1; j >= 0; j--) {
            //             chart_kelamin[j]['data'][i]    = parseInt(kelamin[i][keys_kelamin[j].key]);
            //         };
            //     };
            // };
            chart['chart_kelamin'] = chart_kelamin;
            // chart['cat_kelamin'] = cat_kelamin;

            $('#box_gfp').html(number_format(gfp, 0, '.', ','));
            $('#box_female').html(number_format(100*female/gfp, 1, '.', ','));
            // $('#box_female').html(number_format(avg_female, 1, '.', ','));
            $('#box_fin').html(number_format(fin, 0, '.', ','));
            $('#box_account').html(number_format(account/fin*100, 0, '.', ','));
            $('#box_saving').html(number_format(saving/fin*100, 0, '.', ','));
            $('#box_loan').html(number_format(loan/fin*100, 0, '.', ','));

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
    return chart; 
};

var chart = ajaxDataRenderer(m_data);

plot(chart.chart_account,'chart_account', lang('Farmer with bank account'),'1',lang('Jumlah'));
plot(chart.chart_saving,'chart_saving', lang('Farmer with Savings'),'1',lang('Jumlah'));
plot(chart.chart_loan_exp,'chart_loan_exp', lang('Farmer with Loan Experience'),'1',lang('Jumlah'));
// plot(chart.chart_loan_from,'chart_loan_from', lang('Loans from (all farmers with loan experience)'),'1',lang('Jumlah'));
// column(chart.chart_loan_for, 'chart_loan_for', lang('Loan used for'), lang('Percent'), null, chart.chart_categories, 'percent',0,true);
column(chart.chart_product, 'chart_product', lang('Financial Products from Banks and Coops'), lang('Percent'), null, chart.chart_categories, 'percent',0,true);
column_one(chart.chart_future, 'chart_future', lang('Future money needs'), lang('Percent'), null, chart.cat_future, 'normal',2,false,-45,null,'%');
column_one(chart.chart_loan_for, 'chart_loan_for', lang('Loan used for'), lang('Percent'), null, chart.cat_loan_for, 'normal',2,false,-45,null,'%');
column_one(chart.chart_loan_from, 'chart_loan_from', lang('Loans from (all farmers with loan experience)'), lang('Percent'), null, chart.cat_loan_from, 'normal',2,false,-45,null,'%');
// plot(chart.chart_future,'chart_future', lang('Future money needs'),'1',lang('Jumlah'));
// plot(chart.chart_value,'chart_value', lang('Value of Farmers Cocoa Farm'),'1',lang('Jumlah'));
plot(chart.chart_household,'chart_household', lang('Household Members trained in Financial Literacy'),'2',lang('Jumlah'));
column(chart.chart_kelamin, 'chart_kelamin', lang('Gender Financial Literacy Participants'), lang('Percent'), ['#3B5323','#589C14'], chart.chart_categories, 'percent',0,true);