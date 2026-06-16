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

            var poverty = r['ppi'];   
            var chart_poverty_15 = [];
            chart_poverty_15[0]            = [];
            chart_poverty_15[0]['name']    = lang('Baseline');
            chart_poverty_15[0]['data']    = [];
            chart_poverty_15[1]            = [];
            chart_poverty_15[1]['name']    = lang('Post-Line');
            chart_poverty_15[1]['data']    = [];
            var chart_poverty_25 = [];
            chart_poverty_25[0]            = [];
            chart_poverty_25[0]['name']    = lang('Baseline');
            chart_poverty_25[0]['data']    = [];
            chart_poverty_25[1]            = [];
            chart_poverty_25[1]['name']    = lang('Post-Line');
            chart_poverty_25[1]['data']    = [];
            var cat_poverty_province = [];
            var box_poverty = 0;
            var count_poverty = 0;
            if (poverty) {
                for (var i = poverty.length - 1; i >= 0; i--) {
                    cat_poverty_province[i] = lang(poverty[i].label);
                };
            };
            var poverty_baseline = r['ppi'];
            var base_25 = 0;
            var base_125 = 0;
            var count_baseline = 0;
            var National_count_baseline = 0;
            if (poverty_baseline) {
                for (var p = cat_poverty_province.length - 1; p >= 0; p--) {
                    chart_poverty_15[0]['data'][p] = 0;
                    chart_poverty_25[0]['data'][p] = 0;
                    for (var i = poverty_baseline.length - 1; i >= 0; i--) {
                        if (cat_poverty_province[p] == lang(poverty_baseline[i]['label'])) {
                            chart_poverty_15[0]['data'][p] = parseFloat(poverty_baseline[i]['1.25_baseline'])/parseInt(poverty_baseline[i]['National_count_baseline']);
                            chart_poverty_25[0]['data'][p] = parseFloat(poverty_baseline[i]['2.5_baseline'])/parseInt(poverty_baseline[i]['National_count_baseline']);
                        };
                    };
                };
                $.each(poverty_baseline, function(index, val) {
                    count_baseline += parseInt(val.count_baseline);
                    // if (parseInt(val['National_count_baseline'])) base_125 += parseFloat(val['1.25_baseline'])/parseInt(val['National_count_baseline']);
                    // if (parseInt(val['National_count_baseline'])) base_25 += parseFloat(val['2.5_baseline'])/parseInt(val['National_count_baseline']);
                    base_125 += parseFloat(val['1.25_baseline']);
                    base_25 += parseFloat(val['2.5_baseline']);
                    National_count_baseline += parseInt(val['National_count_baseline']);
                });
            };
            base_125    /= National_count_baseline;
            base_25     /= National_count_baseline;
            // console.log(chart_poverty_15);
            var poverty_postline = r['ppi'];
            var post_25 = 0;
            var post_125 = 0;
            var count_postline = 0;
            var National_count_postline = 0;
            if (poverty_postline) {
                for (var p = cat_poverty_province.length - 1; p >= 0; p--) {
                    chart_poverty_15[1]['data'][p] = 0;
                    chart_poverty_25[1]['data'][p] = 0;
                    for (var i = poverty_postline.length - 1; i >= 0; i--) {
                        if (cat_poverty_province[p] == lang(poverty_postline[i]['label'])) {
                        chart_poverty_15[1]['data'][p] = parseFloat(poverty_postline[i]['1.25_postline'])/parseInt(poverty_postline[i]['National_count_postline']);
                        chart_poverty_25[1]['data'][p] = parseFloat(poverty_postline[i]['2.5_postline'])/parseInt(poverty_postline[i]['National_count_postline']);
                        };
                    };
                };
                $.each(poverty_postline, function(index, val) {
                    count_postline += parseInt(val.count_postline);
                    // if (parseInt(val['National_count_postline'])) post_25 += parseFloat(val['2.5_postline'])/parseInt(val['National_count_postline']);
                    // if (parseInt(val['National_count_postline'])) post_125 += parseFloat(val['1.25_postline'])/parseInt(val['National_count_postline']);
                    post_125 += parseFloat(val['1.25_postline']);
                    post_25 += parseFloat(val['2.5_postline']);
                    National_count_postline += parseInt(val['National_count_postline']);
                });
            };
            post_125    /= National_count_postline;
            post_25     /= National_count_postline;
            
            var dec_25 = 0;
            // console.log(base_25);
            // console.log(post_25);
            if (base_25 > post_25) {
                dec_25 = ((post_25/base_25)-1)*100;
            };
            var dec_125 = 0;
            // console.log(base_125);
            // console.log(post_125);
            if (base_125 > post_125) {
                dec_125 = ((post_125/base_125)-1)*100;
            };
            $('#box_ppi_baseline').html(number_format(count_baseline,0,'.',','));
            $('#box_ppi_postline').html(number_format(count_postline,0,'.',','));
            $('#box_dec_pov_25').html(number_format(dec_25,1,'.',','));
            $('#box_dec_pov_125').html(number_format(dec_125,1,'.',','));
            $('#box_poverty_125_baseline').html(number_format(base_125,1,'.',','));
            $('#box_poverty_125_postline').html(number_format(post_125,1,'.',','));
            $('#box_poverty_25_baseline').html(number_format(base_25,1,'.',','));
            $('#box_poverty_25_postline').html(number_format(post_25,1,'.',','));

            s['chart_poverty_15']           = chart_poverty_15;
            s['chart_poverty_25']           = chart_poverty_25;
            s['cat_poverty_province']       = cat_poverty_province;

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
return s; 
};

var s = ajaxDataRenderer(m_data); 

column_one(s['chart_poverty_15'], 'chart_poverty_15', lang('Baseline and Post-Line, $ 1.25/Day'), '', ['#3B5323','#589C14'], s.cat_poverty_province, 'normal', 1, true, -45, 3);
column_one(s['chart_poverty_25'], 'chart_poverty_25', lang('Baseline and Post-Line, $ 2.5/Day'), '', ['#3B5323','#589C14'], s.cat_poverty_province, 'normal', 1, true, -45, 10);
