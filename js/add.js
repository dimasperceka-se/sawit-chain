var dataDistrict = function(url,mod,petani) {
    // console.log(url)
    $.ajax({
        type: "GET",
        url: m_district,
        data: {prov: m_prov},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var html_d=jud='';
            for (var i=0;i<r.length;i++) {
                html_d += '<li><a onClick="link(this.href);return false" href="'+m_url+'/home/home/'+mod+'/'+m_prov+'/0/'+
                r[i]['id']+'?petani='+petani+'">'+r[i]['label']+'</a></li>';
                if (r[i]['id']==m_kab) jud = r[i]['label']
            }
        document.getElementById('dLabeli').innerHTML = document.getElementById('dLabeli').innerHTML + html_d;
        if (jud!='') document.getElementById('judul').innerHTML = jud;
    }
})
}
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
        colors: ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'],
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

function donut(data, div, judul, format, juduls, koma) {
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
        colors: ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'],
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

function line(data, div, judul, yJudul, warna, kategori, stack, koma,legen,rotate) {
    stack   = typeof stack !== 'undefined' ? stack : 'normal';
    koma    = typeof koma !== 'undefined' ? koma : 0;
    legen   = typeof legen !== 'undefined' ? legen : false;
    rotate  = typeof rotate !== 'undefined' ? rotate : -45;
    warna   = warna !== null ? warna : ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'];

    new Highcharts.Chart({
        chart: {
            renderTo: div,
            type: 'line'
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
                else return '<b>'+ this.x +'</b><br/>'+this.series.name +': '+ number_format(this.y,koma,'.',',')
                    // +'<br/>'+'Total: '+ number_format(this.point.stackTotal,koma,'.',',');
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

function gauge_single(div, title, data) {
    var gaugeOptions = {
        chart: {
            type: 'solidgauge',
            renderTo: div
        },

        title: {
            text: title
        },

        pane: {
            center: ['50%', '80%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#535353',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: true,
            followPointer: true
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    // The speed gauge
    var percent = (data[0].data/data[0].max)*100;
    if (percent > 1) {
        percent = Math.round(percent);
    } else {
        percent = Math.round(percent*100)/100;
    }
    data[0].percent_value = (data[0].data > 0 && data[0].max > 0) ? percent : 0;
    // data[1].percent_value = (data[1].data > 0 && max > 0) ? Math.round((data[1].data/data[1].max)*100) : 0;
    var max_label = 0;
    // if (data[0].max > 1000000) {
    //     max_label = number_format(data[0].max/1000,0,'.',',') + 'k';
    // } else {
        max_label = number_format(data[0].max,0,'.',',');
    // }
    new Highcharts.Chart(Highcharts.merge(gaugeOptions, {
        yAxis: [
        {
            stops: [
                [0.1, '#95130b'], // green
                [0.5, '#95130b'], // yellow
                [0.9, '#95130b'] // red
            ],
            min: 0,
            max: 100, /*data[0].max*/
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 100,
            tickAmount: 2,
            tickWidth: 0,
            title: {
                text: "0",
                y: 140,
                x: -185,
            },
            // labels: {
            //     x: 10,
            //     y: 15
            // }
            showFirstLabel:false,
            showLastLabel:false,
        }, {
            stops: [
                [0.1, '#6BCD0A'], // green
                [0.5, '#6BCD0A'], // yellow
                [0.9, '#6BCD0A'] // red
            ],
            min: 0,
            max: 100, /*data[1].max,*/
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 100,
            tickAmount: 2,
            tickWidth: 0,
            title: {
                text: max_label,
                y: 140,
                x: 190,
            },
            // labels: {
            //     x: -20,
            //     y: 15
            // }
            showFirstLabel:false,
            showLastLabel:false,
        }],

        series: [{
            name: data[0].name,
            data: [data[0].percent_value],/*[data[0].data],*/
            yAxis: 0,
            dataLabels: {
                x: 0,
                allowOverlap: true,
                y: -50,
                format: '<div style="text-align:center"><span style="font-size:24px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || '#666666') + '">'+data[0].percent_value+' %</span><br/>'
                // + '<span style="font-size:14px;color:silver">'+data[0].name+'</span></div>',
            },
            innerRadius:'60%',
            radius: '100%',
            tooltip: {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>'+number_format(data[0].data,0,'.',',')+'</b><br/>',
            }
        }]

    }));

}

function gauge_double(div, title, data) {
    var gaugeOptions = {
        chart: {
            type: 'solidgauge',
            renderTo: div
        },

        title: {
            text: title
        },

        pane: {
            center: ['50%', '80%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#535353',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: true,
            followPointer: true
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    // The speed gauge
    var percent_0 = (data[0].data/data[0].max)*100;
    if (percent_0 > 1) {
        percent_0 = Math.round(percent_0);
    } else {
        percent_0 = Math.round(percent_0*100)/100;
    }
    data[0].percent_value = (data[0].data > 0 && data[0].max > 0) ? percent_0 : 0;
    var percent_1 = (data[1].data/data[1].max)*100;
    if (percent_1 > 1) {
        percent_1 = Math.round(percent_1);
    } else {
        percent_1 = Math.round(percent_1*100)/100;
    }
    data[1].percent_value = (data[1].data > 0 && data[1].max > 0) ? percent_1 : 0;
    new Highcharts.Chart(Highcharts.merge(gaugeOptions, {
        yAxis: [
        {
            stops: [
                [0.1, '#589C14'], // green
                [0.5, '#589C14'], // yellow
                [0.9, '#589C14'] // red
            ],
            min: data[0].min || 0,
            max: 100, /*data[0].max*/
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 100,
            tickAmount: 2,
            tickWidth: 0,
            title: {
                // text: data[0].name + ' / ' +data[1].name,
                text: '0 / 0',
                y: 140,
                x: -185,
            },
            // labels: {
            //     x: 10,
            //     y: 15
            // }
            showFirstLabel:false,
            showLastLabel:false,
        }, {
            stops: [
                [0.1, '#6BCD0A'], // green
                [0.5, '#6BCD0A'], // yellow
                [0.9, '#6BCD0A'] // red
            ],
            min: data[1].min || 0,
            max: 100, /*data[1].max,*/
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 100,
            tickAmount: 2,
            tickWidth: 0,
            title: {
                text: number_format(data[1].max,0,'.',',')+' / '+number_format(data[0].max,0,'.',','),
                y: 140,
                x: 190,
            },
            // labels: {
            //     x: -20,
            //     y: 15
            // }
            showFirstLabel:false,
            showLastLabel:false,
        }],

        series: [{
            name: data[0].name,
            data: [data[0].percent_value],/*[data[0].data],*/
            yAxis: 0,
            dataLabels: {
                x: -50,
                allowOverlap: true,
                y: -50,
                format: '<div style="text-align:center"><span style="font-size:24px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || '#666666') + '">'+data[0].percent_value+' %</span><br/>' +
                '<span style="font-size:14px;color:#666666">'+data[0].name+'</span></div>',
            },
            innerRadius:'80%',
            radius: '100%',
            tooltip: {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>'+number_format(data[0].data,0,'.',',')+'</b><br/>',
            }
        }, {
            name: data[1].name,
            data: [data[1].percent_value],/*[data[1].data],*/
            yAxis: 1,
            allowOverlap: true,
            dataLabels: {
                x: 50,
                allowOverlap: true,
                y: -50,
                format: '<div style="text-align:center"><span style="font-size:24px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || '#666666') + '">'+data[1].percent_value+' %</span><br/>' +
                '<span style="font-size:14px;color:#666666">'+data[1].name+'</span></div>',
            },
            innerRadius:'60%',
            radius: '80%',
            tooltip: {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>'+number_format(data[1].data,0,'.',',')+'</b><br/>',
            }
        }]

    }));

}