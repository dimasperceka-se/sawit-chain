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
                        if (format=='1') return number_format(this.percentage,2,'.','.')+'%';
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

    new Highcharts.Chart({
        chart: {
            renderTo: div,
            type: 'column'
        },
        colors: warna,
        title: {
            text: judul
        },
        xAxis: {
            categories: kategori,
            labels: {
                rotation: rotate,
                align: 'right',
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            title: {
                text: yJudul,
                style: {
                    fontWeight: 'normal'
                }
            }
            ,stackLabels: {
                enabled: true,
                formatter: function () {
                    return number_format(this.total,koma,'.',',');
                }
            }
            ,tickInterval: y_interval?y_interval:null
        },
        tooltip: {
            formatter: function() {
                if(warna.length==1) return '<b>'+ this.x +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',');
                else return '<b>'+ this.x +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',') +' '+suffix+'<br/>'
                    // +'Total: '+ number_format(this.point.stackTotal,koma,'.',',');
            }
        },
        legend: {
            enabled: legen
        },
        series: data
        ,plotOptions: {
            column: {
                dataLabels: {
                    enabled : true,
                    formatter: function () {
                        return number_format(this.y,koma,'.',',') +' '+suffix;
                    }
                }
            }
        }
    });
}
function column(data, div, judul, yJudul, warna, kategori, stack, koma,legen,rotate) {
    stack   = typeof stack !== 'undefined' ? stack : 'normal';
    koma    = typeof koma !== 'undefined' ? koma : 0;
    legen   = typeof legen !== 'undefined' ? legen : false;
    rotate  = typeof rotate !== 'undefined' ? rotate : -45;
    warna   = warna !== null ? warna : ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'];

    new Highcharts.Chart({
        chart: {
            renderTo: div,
            type: 'column'
        },
        colors: warna,
        title: {
            text: judul
        },
        xAxis: {
            categories: kategori,
            labels: {
                rotation: rotate,
                align: 'right',
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            title: {
                text: yJudul,
                style: {
                    fontWeight: 'normal'
                }
            }
            ,stackLabels: {
                enabled: true,
                formatter: function () {
                    return number_format(this.total,koma,'.',',');
                }
            }
        },
        tooltip: {
            formatter: function() {
                if(warna.length==1) return '<b>'+ this.x +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',');
                else return '<b>'+ this.x +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',') +'<br/>'+
                    'Total: '+ number_format(this.point.stackTotal,koma,'.',',');
            }
        },
        plotOptions: {
            column: {
                stacking: stack,
                dataLabels: {
                    enabled: (stack=='percent'?true:false),
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black, 0 0 3px black'
                    },
                    formatter: function () {
                        if (this.percentage>0) {
                            if(stack=='percent') return number_format(this.percentage,1,',',',')+'%';
                            else return number_format(this.y,koma,'.',',');
                        }
                    }
                }
            }
        },
        legend: {
            enabled: legen
        },
        series: data
    });
}

var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');

   var s = [];
   m_mill = m_mill == false ? '' : m_mill;
   m_do = m_do == false ? '' : m_do;
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: localStorage.getItem("prov"),kab: m_kab,kec: m_kec, desa: m_desa, priv: m_priv,daer: m_daer,awal: m_awal,akhir: m_akhir,partner:m_partner,traceability_partner:m_traceability_partner,mill:m_mill,do:m_do,agent:m_agent},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {           
            var box1 = box2 = box3 = box4 = box5 = box6 = box7 = box8 = 0;
            if(r){
                // box1 = r.transaksi;
                // box2 = r.mill;
                // box3 = r.sme;
                // box4 = r.plot;
                // box5 = r.farmer;
                // box6 = r.total_traceable_sales;
                // box7 = r.traceable_volume_sme;
                // box8 = r.traceable_volume_mill;
                box1 = r.do;
                box2 = r.sme;
                box3 = r.batch;
                box4 = r.farmer;
                box5 = r.transaksi;
                box6 = r.plot;
                box7 = r.production;
            }
            $('#box1').html(number_format(box1,0,'.',','));
            $('#box2').html(number_format(box2,0,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));

            $('#box5').html(number_format(box5,0,'.',','));
            $('#box6').html(number_format(box6,2,'.',','));
            $('#box7').html(number_format(box7,2,'.',','));
            $('#box8').html(number_format(box8,2,'.',','));

            s=r;
            var cat_sales = [];
            var keys_sales = [
                {'key':'netto_certified','label':('Certified')},
                {'key':'netto_uncertified','label':('Not Certified')}
            ];
            var keys_farmer = [
                {'key':'farmer_certified','label':('Certified')},
                {'key':'farmer_uncertified','label':('Not Certified')}
            ];
            var chart_farmer = [];
            for (var i = keys_farmer.length - 1; i >= 0; i--) {                
                chart_farmer[i]            = [];
                chart_farmer[i]['name']    = (keys_farmer[i].label);
                chart_farmer[i]['data']    = [];
            };
            if (certified = r.jumlah_penjualan.certified) {
               $.each(certified, function(index, value) {
                    cat_sales[index] = (value['label']);
                    $.each(keys_farmer, function(idx, val) {
                        chart_farmer[idx]['data'][index] = parseInt(value[val.key]);
                    });
               });
            }
            s['cat_sales']    = cat_sales;
            s['chart_farmer']   = chart_farmer;

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
         }
   });
   return s; 
};
var s = ajaxDataRenderer(m_data); 
column_one(s.potential_annual.data, 'potential_annual', s.potential_annual.judul, s.potential_annual.yjudul, ['#B6BAC2  ','#FFBC65'], s.potential_annual.label, 'normal', 1, 2, true)
//column_one(s.jumlah_penjualan.data, 'jumlah_penjualan', s.jumlah_penjualan.judul, s.jumlah_penjualan.yjudul, ['#B6BAC2  ','#FFBC65'], s.jumlah_penjualan.label, 'normal', 1, 1, true)
// column(s.chart_farmer, 'jumlah_penjualan', ('Number of Farmer Sales Detail'), '', ['#95130b','#FFBC65'], s.cat_sales, 'total', 0, true);
plot(s.traceable_volume.data,'traceable_volume',  s.traceable_volume.judul, '2', s.traceable_volume.yjudul, 1);
