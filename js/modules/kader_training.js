// if (m_prov!='') dataDistrict(m_data,'training'); 

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
            var gap = gnp = gfp = 0;

            var year_categories = new Array();
            for (var i=0;i<7;i++) {
                year_categories[i] = m_now-(6-i);
            }

            var chart_categories = new Array();
            var chart_data_gap = new Array();
            var chart_data_gnp = new Array();
            var chart_data_gfp = new Array();
            var chart_tahun_gap = new Array();
            var chart_tahun_gnp = new Array();
            var chart_tahun_gfp = new Array();
            var jumlah = r['jumlah'];
            for (var i=0;i<jumlah.length;i++) {
                chart_categories[i] = lang(jumlah[i]['label']);
                gap += parseInt(jumlah[i]['gap']);
                gnp += parseInt(jumlah[i]['gnp']);
                gfp += parseInt(jumlah[i]['gfp']);
                chart_data_gap[i] = parseInt(jumlah[i]['gap']);
                chart_data_gnp[i] = parseInt(jumlah[i]['gnp']);
                chart_data_gfp[i] = parseInt(jumlah[i]['gfp']);

                chart_tahun_gap[i] = [];
                chart_tahun_gap[i][0] = parseInt(jumlah[i]['gap_1']);
                chart_tahun_gap[i][1] = parseInt(jumlah[i]['gap_2']);
                chart_tahun_gap[i][2] = parseInt(jumlah[i]['gap_3']);
                chart_tahun_gap[i][3] = parseInt(jumlah[i]['gap_4']);
                chart_tahun_gap[i][4] = parseInt(jumlah[i]['gap_5']);
                chart_tahun_gap[i][5] = parseInt(jumlah[i]['gap_6']);
                chart_tahun_gap[i][6] = parseInt(jumlah[i]['gap_7']);

                chart_tahun_gnp[i] = [];
                chart_tahun_gnp[i][0] = parseInt(jumlah[i]['gnp_1']);
                chart_tahun_gnp[i][1] = parseInt(jumlah[i]['gnp_2']);
                chart_tahun_gnp[i][2] = parseInt(jumlah[i]['gnp_3']);
                chart_tahun_gnp[i][3] = parseInt(jumlah[i]['gnp_4']);
                chart_tahun_gnp[i][4] = parseInt(jumlah[i]['gnp_5']);
                chart_tahun_gnp[i][5] = parseInt(jumlah[i]['gnp_6']);
                chart_tahun_gnp[i][6] = parseInt(jumlah[i]['gnp_7']);

                chart_tahun_gfp[i] = [];
                chart_tahun_gfp[i][0] = parseInt(jumlah[i]['gfp_1']);
                chart_tahun_gfp[i][1] = parseInt(jumlah[i]['gfp_2']);
                chart_tahun_gfp[i][2] = parseInt(jumlah[i]['gfp_3']);
                chart_tahun_gfp[i][3] = parseInt(jumlah[i]['gfp_4']);
                chart_tahun_gfp[i][4] = parseInt(jumlah[i]['gfp_5']);
                chart_tahun_gfp[i][5] = parseInt(jumlah[i]['gfp_6']);
                chart_tahun_gfp[i][6] = parseInt(jumlah[i]['gfp_7']);
            }

            // console.log(chart_tahun_gap);

            chart['chart_categories'] = chart_categories;
            chart['chart_data_gap'] = chart_data_gap;
            chart['chart_data_gnp'] = chart_data_gnp;
            chart['chart_data_gfp'] = chart_data_gfp;

            chart['year_categories'] = year_categories;
            chart['chart_tahun_gap'] = chart_tahun_gap;
            chart['chart_tahun_gnp'] = chart_tahun_gnp;
            chart['chart_tahun_gfp'] = chart_tahun_gfp;

            $('#gap').html(number_format(gap,0,'.',','));
            $('#gnp').html(number_format(gnp,0,'.',','));
            $('#gfp').html(number_format(gfp,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
return chart; 
};

var chart = ajaxDataRenderer(m_data);

column([{name: lang('Peserta GAP'),data: chart.chart_data_gap}], 'chart_gap', lang('Peserta GAP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GNP'),data: chart.chart_data_gnp}], 'chart_gnp', lang('Peserta GNP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GFP'),data: chart.chart_data_gfp}], 'chart_gfp', lang('Peserta GFP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');

var tahun_gap = new Array();
var tahun_gnp = new Array();
var tahun_gfp = new Array();
for (var i = 0;i < chart.chart_categories.length;i++) {
    tahun_gap[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gap[i]
    };
    tahun_gnp[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gnp[i]
    };
    tahun_gfp[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gfp[i]
    };
}
column(tahun_gap,'tahun_gap', lang('Peserta GAP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gnp,'tahun_gnp', lang('Peserta GNP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gfp,'tahun_gfp', lang('Peserta GFP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);