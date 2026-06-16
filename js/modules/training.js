// if (m_prov!='') dataDistrict(m_data,'training'); 

var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    var chart = [];
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner,training:m_training},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var gap = gnp = gfp = gep = gbp = agap = gsp = 0;

            var year_categories = [];
            var years = m_now - 2010 + 1;
            for (var i=0;i<years;i++) {
                year_categories[i] = m_now-(years-1-i);
            }
            // console.log(year_categories);

            var chart_categories = [];
            var chart_data_gap = [];
            var chart_data_gnp = [];
            var chart_data_gfp = [];
            var chart_data_gep = [];
            var chart_data_gbp = [];
            var chart_data_agap = [];
            var chart_data_gsp = [];
            var chart_tahun_gap = [];
            var chart_tahun_gnp = [];
            var chart_tahun_gfp = [];
            var chart_tahun_gep = [];
            var chart_tahun_gbp = [];
            var chart_tahun_agap = [];
            var chart_tahun_gsp = [];
            var jumlah = r['data'];
            for (var i=0;i<jumlah.length;i++) {
                chart_categories[i] = lang(jumlah[i]['label']);

                gap += parseInt(jumlah[i]['gap']);
                gnp += parseInt(jumlah[i]['gnp']);
                gfp += parseInt(jumlah[i]['gfp']);
                gep += parseInt(jumlah[i]['gep']);
                gbp += parseInt(jumlah[i]['gbp']);
                agap += parseInt(jumlah[i]['agap']);
                gsp += parseInt(jumlah[i]['gsp']);

                chart_data_gap[i] = parseInt(jumlah[i]['gap']);
                chart_data_gnp[i] = parseInt(jumlah[i]['gnp']);
                chart_data_gfp[i] = parseInt(jumlah[i]['gfp']);
                chart_data_gep[i] = parseInt(jumlah[i]['gep']);
                chart_data_gbp[i] = parseInt(jumlah[i]['gbp']);
                chart_data_agap[i] = parseInt(jumlah[i]['agap']);
                chart_data_gsp[i] = parseInt(jumlah[i]['gsp']);

                chart_tahun_gap[i] = [];
                chart_tahun_gap[i][0] = parseInt(jumlah[i]['gap_2010']);
                chart_tahun_gap[i][1] = parseInt(jumlah[i]['gap_2011']);
                chart_tahun_gap[i][2] = parseInt(jumlah[i]['gap_2012']);
                chart_tahun_gap[i][3] = parseInt(jumlah[i]['gap_2013']);
                chart_tahun_gap[i][4] = parseInt(jumlah[i]['gap_2014']);
                chart_tahun_gap[i][5] = parseInt(jumlah[i]['gap_2015']);
                chart_tahun_gap[i][6] = parseInt(jumlah[i]['gap_2016']);
                chart_tahun_gap[i][7] = parseInt(jumlah[i]['gap_2017']);

                chart_tahun_gnp[i] = [];
                chart_tahun_gnp[i][0] = parseInt(jumlah[i]['gnp_2010']);
                chart_tahun_gnp[i][1] = parseInt(jumlah[i]['gnp_2011']);
                chart_tahun_gnp[i][2] = parseInt(jumlah[i]['gnp_2012']);
                chart_tahun_gnp[i][3] = parseInt(jumlah[i]['gnp_2013']);
                chart_tahun_gnp[i][4] = parseInt(jumlah[i]['gnp_2014']);
                chart_tahun_gnp[i][5] = parseInt(jumlah[i]['gnp_2015']);
                chart_tahun_gnp[i][6] = parseInt(jumlah[i]['gnp_2016']);
                chart_tahun_gnp[i][7] = parseInt(jumlah[i]['gnp_2017']);

                chart_tahun_gfp[i] = [];
                chart_tahun_gfp[i][0] = parseInt(jumlah[i]['gfp_2010']);
                chart_tahun_gfp[i][1] = parseInt(jumlah[i]['gfp_2011']);
                chart_tahun_gfp[i][2] = parseInt(jumlah[i]['gfp_2012']);
                chart_tahun_gfp[i][3] = parseInt(jumlah[i]['gfp_2013']);
                chart_tahun_gfp[i][4] = parseInt(jumlah[i]['gfp_2014']);
                chart_tahun_gfp[i][5] = parseInt(jumlah[i]['gfp_2015']);
                chart_tahun_gfp[i][6] = parseInt(jumlah[i]['gfp_2016']);
                chart_tahun_gfp[i][7] = parseInt(jumlah[i]['gfp_2017']);

                chart_tahun_gep[i] = [];
                chart_tahun_gep[i][0] = parseInt(jumlah[i]['gep_2010']);
                chart_tahun_gep[i][1] = parseInt(jumlah[i]['gep_2011']);
                chart_tahun_gep[i][2] = parseInt(jumlah[i]['gep_2012']);
                chart_tahun_gep[i][3] = parseInt(jumlah[i]['gep_2013']);
                chart_tahun_gep[i][4] = parseInt(jumlah[i]['gep_2014']);
                chart_tahun_gep[i][5] = parseInt(jumlah[i]['gep_2015']);
                chart_tahun_gep[i][6] = parseInt(jumlah[i]['gep_2016']);
                chart_tahun_gep[i][7] = parseInt(jumlah[i]['gep_2017']);

                chart_tahun_gbp[i] = [];
                chart_tahun_gbp[i][0] = parseInt(jumlah[i]['gbp_2010']);
                chart_tahun_gbp[i][1] = parseInt(jumlah[i]['gbp_2011']);
                chart_tahun_gbp[i][2] = parseInt(jumlah[i]['gbp_2012']);
                chart_tahun_gbp[i][3] = parseInt(jumlah[i]['gbp_2013']);
                chart_tahun_gbp[i][4] = parseInt(jumlah[i]['gbp_2014']);
                chart_tahun_gbp[i][5] = parseInt(jumlah[i]['gbp_2015']);
                chart_tahun_gbp[i][6] = parseInt(jumlah[i]['gbp_2016']);
                chart_tahun_gbp[i][7] = parseInt(jumlah[i]['gbp_2017']);

                chart_tahun_agap[i] = [];
                chart_tahun_agap[i][0] = parseInt(jumlah[i]['agap_2010']);
                chart_tahun_agap[i][1] = parseInt(jumlah[i]['agap_2011']);
                chart_tahun_agap[i][2] = parseInt(jumlah[i]['agap_2012']);
                chart_tahun_agap[i][3] = parseInt(jumlah[i]['agap_2013']);
                chart_tahun_agap[i][4] = parseInt(jumlah[i]['agap_2014']);
                chart_tahun_agap[i][5] = parseInt(jumlah[i]['agap_2015']);
                chart_tahun_agap[i][6] = parseInt(jumlah[i]['agap_2016']);
                chart_tahun_agap[i][7] = parseInt(jumlah[i]['agap_2017']);

                chart_tahun_gsp[i] = [];
                chart_tahun_gsp[i][0] = parseInt(jumlah[i]['gsp_2010']);
                chart_tahun_gsp[i][1] = parseInt(jumlah[i]['gsp_2011']);
                chart_tahun_gsp[i][2] = parseInt(jumlah[i]['gsp_2012']);
                chart_tahun_gsp[i][3] = parseInt(jumlah[i]['gsp_2013']);
                chart_tahun_gsp[i][4] = parseInt(jumlah[i]['gsp_2014']);
                chart_tahun_gsp[i][5] = parseInt(jumlah[i]['gsp_2015']);
                chart_tahun_gsp[i][6] = parseInt(jumlah[i]['gsp_2016']);
                chart_tahun_gsp[i][7] = parseInt(jumlah[i]['gsp_2017']);
            }

            // console.log(chart_tahun_gap);

            chart['chart_categories'] = chart_categories;
            chart['chart_data_gap'] = chart_data_gap;
            chart['chart_data_gnp'] = chart_data_gnp;
            chart['chart_data_gfp'] = chart_data_gfp;
            chart['chart_data_gep'] = chart_data_gep;
            chart['chart_data_gbp'] = chart_data_gbp;
            chart['chart_data_agap'] = chart_data_agap;
            chart['chart_data_gsp'] = chart_data_gsp;

            chart['year_categories'] = year_categories;
            chart['chart_tahun_gap'] = chart_tahun_gap;
            chart['chart_tahun_gnp'] = chart_tahun_gnp;
            chart['chart_tahun_gfp'] = chart_tahun_gfp;
            chart['chart_tahun_gep'] = chart_tahun_gep;
            chart['chart_tahun_gbp'] = chart_tahun_gbp;
            chart['chart_tahun_agap'] = chart_tahun_agap;
            chart['chart_tahun_gsp'] = chart_tahun_gsp;

            $('#gap').html(number_format(gap,0,'.',','));
            $('#gnp').html(number_format(gnp,0,'.',','));
            $('#gfp').html(number_format(gfp,0,'.',','));
            $('#gep').html(number_format(gep,0,'.',','));
            $('#gbp').html(number_format(gbp,0,'.',','));
            $('#agap').html(number_format(agap,0,'.',','));
            $('#gsp').html(number_format(gsp,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
return chart; 
};

var chart = ajaxDataRenderer(m_data);

column([{name: lang('Peserta Basic GAP'),data: chart.chart_data_gap}], 'chart_gap', lang('Peserta Basic GAP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GNP'),data: chart.chart_data_gnp}], 'chart_gnp', lang('Peserta GNP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GFP'),data: chart.chart_data_gfp}], 'chart_gfp', lang('Peserta GFP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GEP'),data: chart.chart_data_gep}], 'chart_gep', lang('Peserta GEP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GBP'),data: chart.chart_data_gbp}], 'chart_gbp', lang('Peserta GBP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta Advance GAP'),data: chart.chart_data_agap}], 'chart_agap', lang('Peserta Advance GAP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');
column([{name: lang('Peserta GSP'),data: chart.chart_data_gsp}], 'chart_gsp', lang('Peserta GSP'), lang('Petani'), ['#3B5323'], chart.chart_categories, 'normal');

var tahun_gap = [];
var tahun_gnp = [];
var tahun_gfp = [];
var tahun_gep = [];
var tahun_gbp = [];
var tahun_agap = [];
var tahun_gsp = [];
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
    tahun_gep[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gep[i]
    };
    tahun_gbp[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gbp[i]
    };
    tahun_agap[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_agap[i]
    };
    tahun_gsp[i] = {
        name: chart.chart_categories[i],
        data: chart.chart_tahun_gsp[i]
    };
}
column(tahun_gap,'tahun_gap', lang('Peserta Basic GAP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gnp,'tahun_gnp', lang('Peserta GNP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gfp,'tahun_gfp', lang('Peserta GFP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gep,'tahun_gep', lang('Peserta GEP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gbp,'tahun_gbp', lang('Peserta GBP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_agap,'tahun_agap', lang('Peserta Advance GAP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);
column(tahun_gsp,'tahun_gsp', lang('Peserta GSP'), lang('Petani'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'],chart.year_categories,'normal',0,true);