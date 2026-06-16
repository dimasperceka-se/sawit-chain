// if (m_prov!='') dataDistrict(m_data,'traceability'); 
var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');
   var s = [];
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,awal: m_awal,akhir: m_akhir,partner:m_partner,traceability_partner:m_traceability_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var box1 = box2 = box3 = box4 = box5 = box21 = box22 = box23 = box24 = box25 = 0;
            
            var data1 = r['cpg'];
            for (var i=0;i<data1.length;i++) {
               box1 += parseInt(data1[i]['total']);
            }
            var data2 = r['farmer'];
            for (var i=0;i<data2.length;i++) {
               box2 += parseInt(data2[i]['total']);
            }
            var data3 = r['luas'];
            for (var i=0;i<data3.length;i++) {
               box3 += parseInt(data3[i]['total']);
            }
            var data4 = r['produksi'];
            var s1 = [];
            for (var i=0;i<data4.length;i++) {
               box4 += parseInt(data4[i]['total']);
               s1[i] = [];
               s1[i][0] = lang(data4[i]['label']);
               s1[i][1] = parseInt(data4[i]['total'])/1000;
            }
            box4 = box4/1000;

            var data_total = r['total'];
            var s2      = [];
            var s211 = [];
            var s212 = [];
            var s321    = [];
            var s322    = [];
            var s32     = [];
            var s221    = [];
            var s222    = [];
            var s311    = [];
            var s312    = [];
            var chart_sales_month = [];
            var chart_trans_month = [];
            for (var i=0;i<data_total.length;i++) {
               box5 += data_total[i]['total_penjualan']?parseInt(data_total[i]['total_penjualan']):0;
               s2[i] = [];
               s2[i][0] = lang(data_total[i]['label']);
               s2[i][1] = data_total[i]['total_penjualan']?parseInt(data_total[i]['total_penjualan'])/1000:0;

               box21 += parseInt(data_total[i]['total_transaction']);
               s321[i] = lang(data_total[i]['label']);
               s322[i] = parseFloat(data_total[i]['total_transaction']);

               box25 += parseInt(data_total[i]['total_farmer_sell']);
               s32[i] = [];
               s32[i][0] = lang(data_total[i]['label']);
               s32[i][1] = parseInt(data_total[i]['total_farmer_sell'])/1000;
               s211[i] = lang(data_total[i]['label']);
               s212[i] = parseFloat(data_total[i]['total_farmer_sell']);

               s221[i] = lang(data_total[i]['label']);
               s222[i] = parseFloat(data_total[i]['total_penjualan'])/parseInt(data_total[i]['bulan'])/1000;
               s311[i] = lang(data_total[i]['label']);
               s312[i] = parseFloat(data_total[i]['total_transaction'])/parseInt(data_total[i]['bulan']);

               chart_sales_month[i] = {};
               chart_sales_month[i].name = lang(data_total[i]['label']);
               chart_sales_month[i].data = [];
               $.each(r['months'], function(index, val) {
                  chart_sales_month[i].data[index] = parseFloat(data_total[i]['sell_'+val.yearmonth]/1000);
               });
               chart_trans_month[i] = {};
               chart_trans_month[i].name = lang(data_total[i]['label']);
               chart_trans_month[i].data = [];
               $.each(r['months'], function(index, val) {
                  chart_trans_month[i].data[index] = parseFloat(data_total[i]['trans_'+val.yearmonth]);
               });
            }
            box5 = box5/1000;

            var cat_month = [];
            $.each(r['months'], function(index, val) {
               cat_month[index] = val.month+'/'+val.year;
            });

            // var data5 = r['penjualan'];
            // var s2 = [];
            // for (var i=0;i<data5.length;i++) {
            //    box5 += data5[i]['total']?parseInt(data5[i]['total']):0;
            //    s2[i] = [];
            //    s2[i][0] = lang(data5[i]['label']);
            //    s2[i][1] = data5[i]['total']?parseInt(data5[i]['total'])/1000:0;
            // }
            // box5 = box5/1000;

            // var data21 = r['transaction'];
            // var s321 = [];
            // var s322 = [];
            // for (var i=0;i<data21.length;i++) {
            //    box21 += parseInt(data21[i]['total']);
            //    s321[i] = lang(data21[i]['label']);
            //    s322[i] = parseFloat(data21[i]['total']);
            // }

            // var data25 = r['farmer_sell'];
            // var s32 = [];
            // for (var i=0;i<data25.length;i++) {
            //    box25 += parseInt(data25[i]['total']);
            //    s32[i] = [];
            //    s32[i][0] = lang(data25[i]['label']);
            //    s32[i][1] = parseInt(data25[i]['total'])/1000;
            //    s211[i] = lang(data25[i]['label']);
            //    s212[i] = parseFloat(data25[i]['total']);
            // }
            // var sell = r['sell'];
            // var s221 = [];
            // var s222 = [];
            // var s311 = [];
            // var s312 = [];
            // for (var i=0;i<sell.length;i++) {
            //    s221[i] = lang(sell[i]['label']);
            //    s222[i] = parseFloat(sell[i]['total'])/parseInt(sell[i]['bulan'])/1000;
            //    s311[i] = lang(sell[i]['label']);
            //    s312[i] = parseFloat(sell[i]['total_trans'])/parseInt(sell[i]['bulan']);
            // }

            var data22 = r['trader'];
            for (var i=0;i<data22.length;i++) {
               box22 += parseInt(data22[i]['total']);
            }
            var data23 = r['koperasi'];
            for (var i=0;i<data23.length;i++) {
               box23 += parseInt(data23[i]['total']);
            }
            var data24 = r['warehouse'];
            for (var i=0;i<data24.length;i++) {
               box24 += parseInt(data24[i]['total']);
            }

            s = [s1,s2, [s211,s212],[s221,s222], [s311,s312],[s321,s322]]

            s[7] = r['district'];

            s['cat_month']          = cat_month;
            s['chart_sales_month']  = chart_sales_month;
            s['chart_trans_month']  = chart_trans_month;

            var box_cert_sales = 0;
            var box_cert_farmer_sales = 0;

            var cat_sales = [];
            var keys_sales = [
                {'key':'netto_certified','label':lang('Certified')},
                {'key':'netto_uncertified','label':lang('Not Certified')},
            ]
            var chart_sales = [];
            for (var i = keys_sales.length - 1; i >= 0; i--) {                
                chart_sales[i]            = [];
                chart_sales[i]['name']    = lang(keys_sales[i].label);
                chart_sales[i]['data']    = [];
            };
            var keys_farmer = [
                {'key':'farmer_certified','label':lang('Certified')},
                {'key':'farmer_uncertified','label':lang('Not Certified')},
            ]
            var chart_farmer = [];
            for (var i = keys_farmer.length - 1; i >= 0; i--) {                
                chart_farmer[i]            = [];
                chart_farmer[i]['name']    = lang(keys_farmer[i].label);
                chart_farmer[i]['data']    = [];
            };
            if (certified = r['certified']) {
               $.each(certified, function(index, value) {
                    cat_sales[index] = lang(value['label']);

                    box_cert_sales += parseFloat(value['netto_certified']);
                    box_cert_farmer_sales += parseInt(value['farmer_certified']);

                    $.each(keys_sales, function(idx, val) {
                        chart_sales[idx]['data'][index]    = parseFloat(value[val.key]/1000);
                    });
                    $.each(keys_farmer, function(idx, val) {
                        chart_farmer[idx]['data'][index]    = parseInt(value[val.key]);
                    });
               });
            }
            s['cat_sales']    = cat_sales;
            s['chart_sales']    = chart_sales;
            s['chart_farmer']   = chart_farmer;

            var cat_traceability_farmer = [];
            var keys_certified_farmer = [
                {'key':'farmer','label':lang('All Farmer')},
                {'key':'farmer_certified','label':lang('Certified Farmer')},
                {'key':'farmer_certified_selling','label':lang('Selling Certified')},
            ]
            var chart_certified_farmer = [];
            for (var i = keys_certified_farmer.length - 1; i >= 0; i--) {                
                chart_certified_farmer[i]            = [];
                chart_certified_farmer[i]['name']    = lang(keys_certified_farmer[i].label);
                chart_certified_farmer[i]['data']    = [];
            };
            var keys_uncertified_farmer = [
                {'key':'farmer','label':lang('All Farmer')},
                {'key':'farmer_uncertified','label':lang('Non Certified Farmer')},
                {'key':'farmer_uncertified_selling','label':lang('Selling Non Certified')},
            ]
            var chart_uncertified_farmer = [];
            for (var i = keys_uncertified_farmer.length - 1; i >= 0; i--) {                
                chart_uncertified_farmer[i]            = [];
                chart_uncertified_farmer[i]['name']    = lang(keys_uncertified_farmer[i].label);
                chart_uncertified_farmer[i]['data']    = [];
            };
            if (traceability_farmer = r['traceability_farmer']) {
               $.each(traceability_farmer, function(index, value) {
                    cat_traceability_farmer[index] = lang(value['label']);

                    $.each(keys_uncertified_farmer, function(idx, val) {
                        chart_uncertified_farmer[idx]['data'][index]    = parseInt(value[val.key]);
                    });

                    $.each(keys_certified_farmer, function(idx, val) {
                        chart_certified_farmer[idx]['data'][index]    = parseInt(value[val.key]);
                    });
               });
            }
            s['cat_traceability_farmer']    = cat_traceability_farmer;
            s['chart_certified_farmer']     = chart_certified_farmer;
            s['chart_uncertified_farmer']   = chart_uncertified_farmer;

            var cat_traceability_production = [];
            var keys_certified_production = [
                {'key':'production','label':lang('Total Production')},
                {'key':'production_certified','label':lang('Certified Production')},
                {'key':'farmer_certified_selling','label':lang('Certified Sales')},
            ]
            var chart_certified_production = [];
            for (var i = keys_certified_production.length - 1; i >= 0; i--) {                
                chart_certified_production[i]            = [];
                chart_certified_production[i]['name']    = lang(keys_certified_production[i].label);
                chart_certified_production[i]['data']    = [];
            };
            var keys_uncertified_production = [
                {'key':'production','label':lang('Total Production')},
                {'key':'production_uncertified','label':lang('Non Certified Production')},
                {'key':'farmer_uncertified_selling','label':lang('Non Certified Sales')},
            ]
            var chart_uncertified_production = [];
            for (var i = keys_uncertified_production.length - 1; i >= 0; i--) {                
                chart_uncertified_production[i]            = [];
                chart_uncertified_production[i]['name']    = lang(keys_uncertified_production[i].label);
                chart_uncertified_production[i]['data']    = [];
            };
            if (traceability_production = r['traceability_production']) {
               $.each(traceability_production, function(index, value) {
                    cat_traceability_production[index] = lang(value['label']);

                    $.each(keys_uncertified_production, function(idx, val) {
                        chart_uncertified_production[idx]['data'][index]    = parseInt(value[val.key]);
                    });

                    $.each(keys_certified_production, function(idx, val) {
                        chart_certified_production[idx]['data'][index]    = parseInt(value[val.key]);
                    });
               });
            }
            s['cat_traceability_production']    = cat_traceability_production;
            s['chart_certified_production']     = chart_certified_production;
            s['chart_uncertified_production']   = chart_uncertified_production;

            $('#box_cert_sales').text(number_format(box_cert_sales/1000,2,'.',','));
            $('#box_cert_farmer_sales').text(number_format(box_cert_farmer_sales,0,'.',','));

            $('#box1').html(number_format(box1,0,'.',','));
            $('#box2').html(number_format(box2,0,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));
            $('#box5').html(number_format(box5,1,'.',','));

            $('#box21').html(number_format(box25,0,'.',','));
            $('#box22').html(number_format(box21,0,'.',','));
            $('#box23').html(number_format(box22,0,'.',','));
            $('#box24').html(number_format(box23,0,'.',','));
            $('#box25').html(number_format(box24,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
         }
   });
   return s; 
};

var s = ajaxDataRenderer(m_data); 

plot(s[0],'pie1', lang('Production (MT)'),'2',lang('Jumlah'));
plot(s[1],'pie2', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),1);

column([{name: lang('Transactions'),data: s[5][1]}], 'pie21', lang('Number of Transactions'), lang('Transactions'), ['#3B5323'], s[5][0], 'normal', 1)
column([{name: lang('Sales'),data: s[3][1]}], 'pie22', lang('MT Sales per Month'), lang('Sales'), ['#3B5323'], s[3][0], 'normal', 1)
//plot(s[2],'pie21', lang('Number of Transactions'),'2',lang('Jumlah'));
//plot(s[3],'pie22', lang('MT Sales per Month'),'2',lang('Jumlah'),1);

// column([{name: lang('Transaction'),data: s[4][1]}], 'pie31', lang('Transaction per Month'), lang('Transaction'), ['#3B5323'], s[4][0], 'normal')
line(s.chart_trans_month, 'pie31', lang('Number of Transaction per Month'), '', null, s.cat_month, 'total', 0, true);
// column([{name: lang('Farmer'),data: s[2][1]}], 'pie32', lang('Number of Farmer Sales'), lang('Farmer'), ['#3B5323'], s[2][0], 'normal')
line(s.chart_sales_month, 'pie32', lang('Sales Trend per Month'), '', null, s.cat_month, 'total', 0, true);
//plot(s[4],'pie31', lang('Transaction per Month'),'2',lang('Jumlah'),1);
//plot(s[5],'pie32', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),1);
column(s.chart_sales, 'chart_sales', lang('Traceable Sales Detail (MT)'), '', ['#3B5323','#589C14'], s.cat_sales, 'total', 0, true);
column(s.chart_farmer, 'chart_farmer', lang('Number of Farmer Sales Detail'), '', ['#3B5323','#589C14'], s.cat_sales, 'total', 0, true);

column_one(s.chart_certified_farmer, 'chart_certified_farmer', lang('Certified Producers vs Certified Sales'), '', ['#3B5323','#589C14'], s.cat_traceability_farmer, 'normal', 0, true);
column_one(s.chart_uncertified_farmer, 'chart_uncertified_farmer', lang('Non Certified Producers vs Non Certified sales'), '', ['#3B5323','#589C14'], s.cat_traceability_farmer, 'normal', 0, true);
column_one(s.chart_certified_production, 'chart_certified_production', lang('Certified Production vs Certified Sales'), '', ['#3B5323','#589C14'], s.cat_traceability_production, 'normal', 0, true);
column_one(s.chart_uncertified_production, 'chart_uncertified_production', lang('Non Certified Production vs Non Certified Sales'), '', ['#3B5323','#589C14'], s.cat_traceability_production, 'normal', 0, true);
