
var seriesOptions = [],
    seriesCounter = 0,
    names = ['ICCO', 'District'],
    stock_price;

/**
 * Create the chart when all data is loaded
 * @returns {undefined}
 */
function createChart(seriesOptions) {

    stock_price = Highcharts.stockChart('stock_price', {

        rangeSelector: {
            selected: 4
        },

        // yAxis: {
        //     labels: {
        //         formatter: function () {
        //             return (this.value > 0 ? ' + ' : '') + this.value + '%';
        //         }
        //     },
        //     plotLines: [{
        //         value: 0,
        //         width: 2,
        //         color: 'silver'
        //     }]
        // },

        // plotOptions: {
        //     series: {
        //         compare: 'percent',
        //         showInNavigator: true
        //     }
        // },

        // tooltip: {
        //     pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
        //     valueDecimals: 2,
        //     split: true
        // },

        series: seriesOptions
    });
}    
var ajaxDataRenderer = function(url) {
    var s = [];
    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(result) {
            if (result.icco.latest_price) { $('#box_icco_latest').text(number_format(parseFloat(result.icco.latest_price),2,'.',',')) }
            if (result.icco.latest_date) { $('#box_icco_latest_date').text('('+result.icco.latest_date+')') }
            if (result.icco.avg_price) { $('#box_icco_avg').text(number_format(parseFloat(result.icco.avg_price),2,'.',',')) }
            if (result.district.latest_price) { $('#box_district_latest').text(number_format(parseFloat(result.district.latest_price),2,'.',',')) }
            if (result.district.latest_date) { $('#box_district_latest_date').text('('+result.district.latest_date+')') }
            if (result.district.avg_price) { $('#box_district_avg').text(number_format(parseFloat(result.district.avg_price),2,'.',',')) }

            $.each(names, function (i, name) {
                seriesOptions[i] = {
                    name: name,
                    data: result[name.toLowerCase()+'_history']
                };

                // As we're loading the data asynchronously, we don't know what order it will arrive. So
                // we keep a counter and create the chart when all the data is loaded.
                seriesCounter += 1;

                if (seriesCounter === names.length) {
                    createChart(seriesOptions);
                }
            });
            // $(window).resize();
            $('#wrapper').removeClass('cover');
            $('#row-fluid').css('display', '');
            stock_price.reflow();
        }
    });
    return s; 
};

// var s = ajaxDataRenderer(m_data); 

$(function () {

    ajaxDataRenderer(m_data);

    // $.each(names, function (i, name) {

    //     // $.getJSON('https://www.highcharts.com/samples/data/jsonp.php?filename=' + name.toLowerCase() + '-c.json&callback=?',    function (data) {
    //     $.getJSON(m_data+'?type=' + name.toLowerCase() + '&callback=?',    function (data) {

    //         seriesOptions[i] = {
    //             name: name,
    //             data: data
    //         };

    //         // As we're loading the data asynchronously, we don't know what order it will arrive. So
    //         // we keep a counter and create the chart when all the data is loaded.
    //         seriesCounter += 1;

    //         if (seriesCounter === names.length) {
    //             createChart();
    //         }
    //     });
    // });
});