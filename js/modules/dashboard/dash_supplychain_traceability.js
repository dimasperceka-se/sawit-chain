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
        data: {prov: m_prov, kab: m_kab, awal: m_awal, akhir: m_akhir},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            $('#box_sales').html(number_format(r.dataDisplay.sales,1,'.',','));
            $('#box_farmer_sales').html(number_format(r.dataDisplay.farmersales,1,'.',','));
            $('#box_nr_transaction').html(number_format(r.dataDisplay.nrtrnsct,1,'.',','));
            $('#box_agent_sales').html(number_format(r.dataDisplay.agentsales,1,'.',','));
            $('#box_farmer_no_sales').html(number_format(r.dataDisplay.farmernosales,1,'.',','));
            $('#box_production').html(number_format(r.dataDisplay.production,1,'.',','));
            $('#box_distance').html(number_format(r.dataDisplay.distance,1,'.',','));

            //Data Chart ================================================= (Begin)
            var dataChart = r.dataChart;

            var dataProduction = dataChart['production'];
            var dataProductionPie = [];
            for (var i=0;i<dataProduction.length;i++) {
                dataProductionPie[i] = [];
                dataProductionPie[i][0] = lang(dataProduction[i]['label']);
                dataProductionPie[i][1] = parseInt(dataProduction[i]['total'])/1000;
            }
            arrReturn.dataProductionPie = dataProductionPie;
            
            var dataTraceableSales = dataChart['traceablesales'];
            var dataTraceableSalesPie = [];
            for (var i=0;i<dataTraceableSales.length;i++) {
                dataTraceableSalesPie[i] = [];
                dataTraceableSalesPie[i][0] = lang(dataTraceableSales[i]['label']);
                dataTraceableSalesPie[i][1] = parseInt(dataTraceableSales[i]['total'])/1000;
            }
            arrReturn.dataTraceableSalesPie = dataTraceableSalesPie;

            var dataNrFarmerTrnsct = dataChart['nrfarmertrnsct'];
            var dataNrFarmerTrnsctBarLabel = [];
            var dataNrFarmerTrnsctBarValue = [];
            for (var i=0;i<dataNrFarmerTrnsct.length;i++) {
                dataNrFarmerTrnsctBarLabel[i] = lang(dataNrFarmerTrnsct[i]['label']);
                dataNrFarmerTrnsctBarValue[i] = parseFloat(dataNrFarmerTrnsct[i]['total'])/1000;
            }
            arrReturn.dataNrFarmerTrnsctBar = [dataNrFarmerTrnsctBarLabel,dataNrFarmerTrnsctBarValue];

            var dataNrFarmerSales = dataChart['nrfarmersales'];
            var dataNrFarmerSalesBarLabel = [];
            var dataNrFarmerSalesBarValue = [];
            for (var i=0;i<dataNrFarmerSales.length;i++) {
                dataNrFarmerSalesBarLabel[i] = lang(dataNrFarmerSales[i]['label']);
                dataNrFarmerSalesBarValue[i] = parseFloat(dataNrFarmerSales[i]['total'])/1000;
            }
            arrReturn.dataNrFarmerSalesBar = [dataNrFarmerSalesBarLabel,dataNrFarmerSalesBarValue];

            var catProdSales = [], dataProd = [], dataSales = [];
            $.each(dataChart['production'], function(index, val) {
                catProdSales.push(val.label);
                dataProd.push(val.total);
            });
            $.each(dataChart['traceablesales'], function(index, val) {
                dataSales.push(val.total);
            });
            column([
                {name: lang('Production'),data: dataProd},
                {name: lang('Sales'),data: dataSales},
            ], 'bar_production_sales', lang('Production vs Sales'), lang('Transactions'), null, catProdSales, 'normal', 0, true);

            var pieAvgDistance = [];
            $.each(dataChart['average_distance'], function(index, val) {
                pieAvgDistance.push([val.label,val.total])
            });
            plot(pieAvgDistance,'pie_average_distance', lang('Average Distance (Km)'),'2',lang('Jumlah'),2);
            //Data Chart ================================================= (End)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    // console.log(arrReturn.dataNrFarmerTrnsctBar);
    return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);
// console.log(arrReturn);
//========================================== Build Chart ====================================================//

plot(arrReturn.dataProductionPie,'pie_production', lang('Registered Production (MT)'),'2',lang('Jumlah'),2);
plot(arrReturn.dataTraceableSalesPie,'pie_sales', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),2);

column([{name: lang('Transactions'),data: arrReturn.dataNrFarmerTrnsctBar[1]}], 'bar_farmer_trnsct', lang('Number of Farmer Transactions'), lang('Transactions'), ['#3B5323'], arrReturn.dataNrFarmerTrnsctBar[0], 'normal', 0);
column([{name: lang('Sales'),data: arrReturn.dataNrFarmerSalesBar[1]}], 'bar_farmer_sales', lang('MT Sales per Month'), lang('Sales'), ['#3B5323'], arrReturn.dataNrFarmerSalesBar[0], 'normal', 2);
