/*
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
*/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    //console.log(url);

    $.ajax({
        type: "GET",
        url: url,
        data: {millgroup: m_millgroup,mill: m_mill, awal: m_awal, akhir: m_akhir},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var box1 = box2 = box3 = box4 = box5 = box21 = box22 = box23 = box24 = box25 = 0;
            //console.log(r);
            var data22 = r['agent'];
            for (var i=0;i<data22.length;i++) {
               box22 += parseInt(data22[i]['total']);
            }

            $('#box_sales').html(number_format(r.total_penjualan/1000,1,'.',','));
            $('#box_farmer_sales').html(number_format(r.total_farmer_sell,0,'.',','));
            $('#box_nr_transaction').html(number_format(r.total_transaction,0,'.',','));
            $('#box_agent_sales').html(number_format(box22,0,'.',','));
            //$('#box_farmer_no_sales').html(number_format(r.total_farmer_before_sell,0,'.',','));
            //$('#box_production').html(number_format(0,1,'.',','));
            //$('#box_distance').html(number_format(0,1,'.',','));

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

            s = [s1,s2, [s211,s212],[s221,s222], [s311,s312],[s321,s322]]

            s[7] = r['district'];

            s['cat_month']          = cat_month;
            s['chart_sales_month']  = chart_sales_month;
            s['chart_trans_month']  = chart_trans_month;

            var box_sales = 0;
            var box_cert_sales = 0;
            var box_farmer_sales = 0;
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

                    box_sales += parseFloat(value['netto']);
                    box_cert_sales += parseFloat(value['netto_certified']);
                    box_farmer_sales += parseInt(value['farmer']);
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


            //Data Chart ================================================= (Begin)
            //var dataChart = r.dataChart;

            //console.log(dataChart);

            // var dataProduction = dataChart['production'];
            // var dataProductionPie = [];
            // for (var i=0;i<dataProduction.length;i++) {
            //     dataProductionPie[i] = [];
            //     dataProductionPie[i][0] = lang(dataProduction[i]['label']);
            //     dataProductionPie[i][1] = parseInt(dataProduction[i]['total'])/1000;
            // }
            // arrReturn.dataProductionPie = dataProductionPie;
            
            // var dataTraceableSales = dataChart['traceablesales'];
            // var dataTraceableSalesPie = [];
            // for (var i=0;i<dataTraceableSales.length;i++) {
            //     dataTraceableSalesPie[i] = [];
            //     dataTraceableSalesPie[i][0] = lang(dataTraceableSales[i]['label']);
            //     dataTraceableSalesPie[i][1] = parseInt(dataTraceableSales[i]['total'])/1000;
            // }
            // arrReturn.dataTraceableSalesPie = dataTraceableSalesPie;

            // var dataNrFarmerTrnsct = dataChart['nrfarmertrnsct'];
            // var dataNrFarmerTrnsctBarLabel = [];
            // var dataNrFarmerTrnsctBarValue = [];
            // for (var i=0;i<dataNrFarmerTrnsct.length;i++) {
            //     dataNrFarmerTrnsctBarLabel[i] = lang(dataNrFarmerTrnsct[i]['label']);
            //     dataNrFarmerTrnsctBarValue[i] = parseFloat(dataNrFarmerTrnsct[i]['total'])/1000;
            // }
            // arrReturn.dataNrFarmerTrnsctBar = [dataNrFarmerTrnsctBarLabel,dataNrFarmerTrnsctBarValue];

            // var dataNrFarmerSales = dataChart['nrfarmersales'];
            // var dataNrFarmerSalesBarLabel = [];
            // var dataNrFarmerSalesBarValue = [];
            // for (var i=0;i<dataNrFarmerSales.length;i++) {
            //     dataNrFarmerSalesBarLabel[i] = lang(dataNrFarmerSales[i]['label']);
            //     dataNrFarmerSalesBarValue[i] = parseFloat(dataNrFarmerSales[i]['total'])/1000;
            // }
            // arrReturn.dataNrFarmerSalesBar = [dataNrFarmerSalesBarLabel,dataNrFarmerSalesBarValue];

            // var catProdSales = [], dataProd = [], dataSales = [];
            // $.each(dataChart['production'], function(index, val) {
            //     catProdSales.push(val.label);
            //     dataProd.push(val.total);
            // });
            // $.each(dataChart['traceablesales'], function(index, val) {
            //     dataSales.push(val.total);
            // });
            // column([
            //     {name: lang('Production'),data: dataProd},
            //     {name: lang('Sales'),data: dataSales},
            // ], 'bar_production_sales', lang('Production vs Sales'), lang('Transactions'), null, catProdSales, 'normal', 0, true);

            // var pieAvgDistance = [];
            // $.each(dataChart['average_distance'], function(index, val) {
            //     pieAvgDistance.push([val.label,val.total])
            // });
            // plot(pieAvgDistance,'pie_average_distance', lang('Average Distance (Km)'),'2',lang('Jumlah'),2);
            //Data Chart ================================================= (End)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            //$(".dashDateGen").html('Generated on '+r.DateGenerated);
        }
    });

    // console.log(arrReturn.dataNrFarmerTrnsctBar);
    return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);
// console.log(arrReturn);
//========================================== Build Chart ====================================================//

//plot(arrReturn.dataProductionPie,'pie_production', lang('Registered Production (MT)'),'2',lang('Jumlah'),2);
//plot(arrReturn.dataTraceableSalesPie,'pie_sales', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),2);

//column([{name: lang('Transactions'),data: arrReturn.dataNrFarmerTrnsctBar[1]}], 'bar_farmer_trnsct', lang('Number of Farmer Transactions'), lang('Transactions'), ['#3B5323'], arrReturn.dataNrFarmerTrnsctBar[0], 'normal', 0);
//column([{name: lang('Sales'),data: arrReturn.dataNrFarmerSalesBar[1]}], 'bar_farmer_sales', lang('MT Sales per Month'), lang('Sales'), ['#3B5323'], arrReturn.dataNrFarmerSalesBar[0], 'normal', 2);

plot(s[0],'pie1', lang('Production (MT)'),'2',lang('Jumlah'));
plot(s[1],'pie2', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),1);

//column([{name: lang('Transactions'),data: s[5][1]}], 'pie21', lang('Number of Transactions'), lang('Transactions'), ['#3B5323'], s[5][0], 'normal', 1)
//column([{name: lang('Sales'),data: s[3][1]}], 'pie22', lang('MT Sales per Month'), lang('Sales'), ['#3B5323'], s[3][0], 'normal', 1)

line(s.chart_trans_month, 'pie31', lang('Number of Transaction per Month'), '', null, s.cat_month, 'total', 0, true);
line(s.chart_sales_month, 'pie32', lang('Sales Trend per Month'), '', null, s.cat_month, 'total', 2, true);

column(s.chart_farmer, 'chart_farmer', lang('Number of Farmer Sales Detail'), '', ['#95130b','#FFBC65'], s.cat_sales, 'total', 0, true);

$('#millgroup_list li').remove();
$.get(m_api+'/dboard/mill_group', function(data) {
    var li = '<li><a href="#" class="list_millgroup" data-id="" data-name="'+lang('All Mill Group')+'">'+lang('All Mill Group')+'</a></li>';
    $('#millgroup_list').append(li);
    if (data) {
        $.each(data.data, function(index, val) {
            var li = '<li><a href="#" class="list_millgroup" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
            $('#millgroup_list').append(li);
        });
        // set previously selected
        if (m_millgroup) {
            $('.list_millgroup[data-id="'+m_millgroup+'"]').click();
        }
    }
});

$( "#millgroup_list" ).on( "click", ".list_millgroup", function(event) {
    event.preventDefault();	
    m_millgroup = $(this).data('id');
    if(m_millgroup==''){
        m_mill = "";
        $('#mill_nm').text(lang('All Mill'));
    }

    $('#millgroup_nm').text($(this).data('name'));

    var arrReturn = ajaxDataRenderer(m_data);

    $('#mill_list li').remove();
    var li = '<li><a href="#" class="list_mill" data-id="" data-name="'+lang('All Mill')+'">'+lang('All Mill')+'</a></li>';
    $('#mill_list').append(li);  
    
    $.get(m_api+'/dboard/mill/?millGroupID=' + m_millgroup, function(data) {
        if (data) {
            $.each(data.data, function(index, val) {
                var li = '<li><a href="" class="list_mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                $('#mill_list').append(li);
            });
            // set previously selected
            if (m_mill) {
                $('.list_mill[data-id="'+m_mill+'"]').click();
            }
        }
    });
    
    

    $('#mill_list').on('click', '.list_mill', function(event) {
        event.preventDefault();
        $('#mill_nm').text($(this).data('name'));
        m_mill = $(this).data('id');
        var arrReturn = ajaxDataRenderer(m_data);
    }); 
});

function setFilter() {
    var awal    = $('#datepicker1').val();
    var akhir   = $('#datepicker2').val();
    if (awal!=='' && akhir!=='') {
        link(m_current_url+'?search=&awal='+awal+'&akhir='+akhir+'&millgroup='+m_millgroup+'&mill='+m_mill);
    }
}
