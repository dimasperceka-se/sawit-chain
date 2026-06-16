// if (m_prov!='') dataDistrict(m_data,'traceability'); 
function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function plot(data, div, judul, format, juduls, koma) {
    koma = typeof koma !== 'undefined' ? koma : 0;

    var pf = '{series.name}: <b>{point.percentage:.2f}%</b>';
    if (format=='1') pf = '{series.name}: <b>{point.y}</b>';
    new Highcharts.Chart({
        chart: {
            renderTo: div,
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        colors: ['#888A8E','#FFBC65'],
        title: {
            text: judul
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',');
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        if (format=='1') return number_format(this.percentage,1,'.','.')+'%';
                        else return number_format(this.y,koma,'.',',');
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: juduls,
            data: data
        }]
    });
}
function column_one(data, div, judul, yJudul, warna, kategori, stack, koma, legen, rotate, y_interval, suffix) {
    stack   = typeof stack !== 'undefined' ? stack : 'normal';
    koma    = typeof koma !== 'undefined' ? koma : 0;
    legen   = typeof legen !== 'undefined' ? legen : false;
    rotate  = typeof rotate !== 'undefined' ? rotate : -45;
    warna   = warna !== null ? warna : ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'];
    suffix  = suffix ? suffix : '';

    Highcharts.chart(div, {
        chart: {
            type: 'bar'
        },
        colors: warna,
        title: {
            text: judul
        },
        xAxis: {
            categories: kategori,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Population',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' '
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: data
    });
}
var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');

    var s = [];
    m_mill = m_mill == false ? '' : m_mill;
    m_type = m_type == false ? '' : m_type;
    $.ajax({
        type: "GET",
        url: url,
        data: {priv: m_priv,awal: m_awal,akhir: m_akhir,mill:m_mill,type:m_type},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            s=r;
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
    return s; 
};
var s = ajaxDataRenderer(m_data); 
console.log(s.categories);
console.log(s.data);
column_one(s.data, 'achievment_fa', lang('FA Achievement'), '', s.color, s.categories, 'normal', 1, 2, true)
