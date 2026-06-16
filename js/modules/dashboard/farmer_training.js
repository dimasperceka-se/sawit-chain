$(document).ready(function() {
    $('#wrapper').addClass('cover');
    $.ajax({
        url: m_data,
        // type: 'default GET (Other values: POST)',
        // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        data: {prov: m_ProvinceID,kab: m_DistrictID,regen: m_regen},
    })
    .done(function(data) {
        $('#box_gap').text(number_format(data.gap,0,'.',','));
        $('#box_gap_female').text(number_format(data.gap_female,0,'.',','));
        $('#box_attended').text(number_format(data.attended,0,'.',','));
        $('#box_attendance_percent').text(number_format(parseInt(data.attended)/parseInt(data.planned)*100,2,'.',','));
        $('#box_mt_70').text(number_format(data.mt_70,0,'.',','));
        // $('#box_gnp').text(number_format(data.gnp,0,'.',','));
        // $('#box_gfp').text(number_format(data.gfp,0,'.',','));
        // $('#box_gdp').text(number_format(data.gdp,0,'.',','));

        var start       = 2010;
        var end         = (new Date()).getFullYear();
        var cat_year    = [];
        for (var i = start; i <= end; i++) {
            cat_year.push(i);
        }
        var gap = [], gnp = [], gfp = [], gdp = [];
        var chart_gap = [], chart_gnp = [], chart_gfp = [], chart_gdp = [];
        var chart_gap_tahun = [], chart_gnp_tahun = [], chart_gfp_tahun = [], chart_gdp_tahun = [];
        var cat_region = [];
        $.each(data.detail, function(i, val) {
            cat_region.push(lang(val.label));
            var gap_tahun = [], gnp_tahun = [], gfp_tahun = [], gdp_tahun = [];
            for (var i = start; i <= end; i++) {
                gap_tahun.push(parseInt(val['gap_'+i]));
                // gnp_tahun.push(parseInt(val['gnp_'+i]));
                // gfp_tahun.push(parseInt(val['gfp_'+i]));
                // gdp_tahun.push(parseInt(val['gdp_'+i]));
            }
            chart_gap_tahun.push({name: lang(val.label),data : gap_tahun});
            // chart_gnp_tahun.push({name: lang(val.label),data : gnp_tahun});
            // chart_gfp_tahun.push({name: lang(val.label),data : gfp_tahun});
            // chart_gdp_tahun.push({name: lang(val.label),data : gdp_tahun});
            gap.push(parseInt(val.gap));
            // gnp.push(parseInt(val.gnp));
            // gfp.push(parseInt(val.gfp));
            // gdp.push(parseInt(val.gdp));
        });
        chart_gap.push({name: lang('GAP'),data: gap});
        // chart_gnp.push({name: lang('GNP'),data: gnp});
        // chart_gfp.push({name: lang('GFP'),data: gfp});
        // chart_gdp.push({name: lang('GDP'),data: gdp});
        
        column(chart_gap, 'chart_gap', lang('GAP Participants'), lang('Jumlah'), null, cat_region, 'normal', 0, false);
        // column(chart_gnp, 'chart_gnp', lang('GNP Participants'), lang('Jumlah'), null, cat_region, 'normal', 0, false);
        // column(chart_gfp, 'chart_gfp', lang('GFP Participants'), lang('Jumlah'), null, cat_region, 'normal', 0, false);
        // column(chart_gdp, 'chart_gdp', lang('GDP Participants'), lang('Jumlah'), null, cat_region, 'normal', 0, false);
        
        column(chart_gap_tahun, 'chart_gap_tahun', lang('GAP Participants'), lang('Jumlah'), null, cat_year, 'normal', 0, true);
        // column(chart_gnp_tahun, 'chart_gnp_tahun', lang('GNP Participants'), lang('Jumlah'), null, cat_year, 'normal', 0, true);
        // column(chart_gfp_tahun, 'chart_gfp_tahun', lang('GFP Participants'), lang('Jumlah'), null, cat_year, 'normal', 0, true);
        // column(chart_gdp_tahun, 'chart_gdp_tahun', lang('GDP Participants'), lang('Jumlah'), null, cat_year, 'normal', 0, true);

        column([
            {name: lang('Male'), data: [
                parseInt(data.planned_1_male),
                parseInt(data.attended_1_male),
                parseInt(data.planned_2_male),
                parseInt(data.attended_2_male),
                parseInt(data.planned_3_male),
                parseInt(data.attended_3_male),
                parseInt(data.planned_4_male),
                parseInt(data.attended_4_male),
                parseInt(data.planned_5_male),
                parseInt(data.attended_5_male),
                parseInt(data.planned_6_male),
                parseInt(data.attended_6_male),
                parseInt(data.planned_7_male),
                parseInt(data.attended_7_male),
                parseInt(data.planned_8_male),
                parseInt(data.attended_8_male),
            ]},
            {name: lang('Female'), data: [
                parseInt(data.planned_1_female),
                parseInt(data.attended_1_female),
                parseInt(data.planned_2_female),
                parseInt(data.attended_2_female),
                parseInt(data.planned_3_female),
                parseInt(data.attended_3_female),
                parseInt(data.planned_4_female),
                parseInt(data.attended_4_female),
                parseInt(data.planned_5_female),
                parseInt(data.attended_5_female),
                parseInt(data.planned_6_female),
                parseInt(data.attended_6_female),
                parseInt(data.planned_7_female),
                parseInt(data.attended_7_female),
                parseInt(data.planned_8_female),
                parseInt(data.attended_8_female),
            ]},
        ], 'chart_attendance_session', lang('GAP Training Attendance per Training Session'), lang('Jumlah'), null, [
            {name: lang('Day 1'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 2'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 3'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 4'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 5'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 6'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 7'), categories: [lang('Planned'), lang('Attended')]},
            {name: lang('Day 8'), categories: [lang('Planned'), lang('Attended')]},
        ], 'normal', 0, true, -90);

        // column_one([
        //     {name: lang('Planned'), data: [parseInt(data.planned_male),parseInt(data.planned_female),parseInt(data.planned),]},
        //     {name: lang('Attended'), data: [parseInt(data.attended_male),parseInt(data.attended_female),parseInt(data.attended),]},
        //     {name: lang('Rate'), type: 'spline',  data: [ifNaN(parseInt(data.attended_male)/parseInt(data.planned_male)*100, 0),ifNaN(parseInt(data.attended_female)/parseInt(data.planned_female)*100, 0),ifNaN(parseInt(data.attended)/parseInt(data.planned)*100, 0),]},
        // ], 'chart_attendance', lang('GAP Training Session Attendance'), lang('Jumlah'), null, [
        //     lang('Male'),lang('Female'),lang('All Participants'),
        // ], 'percent', 0, true);

        Highcharts.chart('chart_attendance', {
            chart: {
                zoomType: 'xy'
            },
            colors: ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'],
            title: {
                text: lang('GAP Training Session Attendance')
            },
            subtitle: {
                // text: 'Source: WorldClimate.com'
            },
            xAxis: [{
                categories: [lang('Male'),lang('Female'),lang('All Participants'),],
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    // format: '{value}°C',
                    style: {
                        // color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: lang('Days'),
                    style: {
                        // color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: lang('Persen'),
                    style: {
                        // color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} %',
                    style: {
                        // color: Highcharts.getOptions().colors[0]
                    }
                },
                min: 0,
                max: 100,
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                enabled: true
            },
            series: [
            {
                name: lang('Planned'),
                type: 'column',
                data: [parseInt(data.planned_male),parseInt(data.planned_female),parseInt(data.planned),],
                // tooltip: {
                //     valueSuffix: '°C'
                // }
            }, {
                name: lang('Attended'),
                type: 'column',
                data: [parseInt(data.attended_male),parseInt(data.attended_female),parseInt(data.attended),],
                // tooltip: {
                //     valueSuffix: '°C'
                // }
            }, {
                name: lang('Rate'),
                type: 'spline',
                yAxis: 1,
                data: [ifNaN(Math.round(parseInt(data.attended_male)/parseInt(data.planned_male)*100*100)/100, 0),ifNaN(Math.round(parseInt(data.attended_female)/parseInt(data.planned_female)*100*100)/100, 0),ifNaN(Math.round(parseInt(data.attended)/parseInt(data.planned)*100*100)/100, 0),],
                tooltip: {
                    valueSuffix: ' %'
                }

            }, 
            ]
        });

        $('#row-fluid').show();
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        $('#wrapper').removeClass('cover');        
    });
}); 