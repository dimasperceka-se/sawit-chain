
var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');
   var s = {};
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var today = new Date();
            var year_categories = [];
            for (var i=0;i<8;i++) {
                year_categories[i] = today.getFullYear()-(7-i);
            }

            // =========== CPG ===========
            var cpgs = r.cpg;
            var cpg = 0, cpg_female = 0, cpg_male = 0;
            var cat_cpg = [], chart_cpg = [], chart_cpg_tahun = [];            
            var keys_cpg_functional = [
                {'key':'ada_pengurus', 'label' : lang('Yes')},
                {'key':'tidak_ada_pengurus', 'label' : lang("No")},
            ]
            var chart_cpg_functional = [];         
            $.each(keys_cpg_functional, function(i, val) {
                chart_cpg_functional[i]       = [];
                chart_cpg_functional[i][0]    = lang(val.label);
                chart_cpg_functional[i][1]    = 0;
            });

            var cat_cpg_management_gender = [
              lang('Chairman'),lang('Secretary'),lang('Treasurer'),
            ];
            var chart_cpg_management_gender = [];
            chart_cpg_management_gender[0] = [];
            chart_cpg_management_gender[0].name = lang('Male');
            chart_cpg_management_gender[0].data = [];
            chart_cpg_management_gender[1] = [];
            chart_cpg_management_gender[1].name = lang('Female');
            chart_cpg_management_gender[1].data = [];
            $.each(cat_cpg_management_gender, function(i, val) {
                chart_cpg_management_gender[0].data[i] = 0;
                chart_cpg_management_gender[1].data[i] = 0;
            });
            $.each(cpgs, function(i, value) {
                cpg                 += parseInt(value.cpg);
                cpg_male            += parseInt(value.ketua_m)+parseInt(value.sekretaris_m)+parseInt(value.bendahara_m);
                cpg_female          += parseInt(value.ketua_f)+parseInt(value.sekretaris_f)+parseInt(value.bendahara_f);

                cat_cpg[i]      = lang(value.label);
                chart_cpg[i]    = parseInt(value.cpg);
                chart_cpg_tahun[i] = [];
                chart_cpg_tahun[i][0] = parseInt(value.est_2010);
                chart_cpg_tahun[i][1] = parseInt(value.est_2011);
                chart_cpg_tahun[i][2] = parseInt(value.est_2012);
                chart_cpg_tahun[i][3] = parseInt(value.est_2013);
                chart_cpg_tahun[i][4] = parseInt(value.est_2014);
                chart_cpg_tahun[i][5] = parseInt(value.est_2015);
                chart_cpg_tahun[i][6] = parseInt(value.est_2016);
                chart_cpg_tahun[i][7] = parseInt(value.est_2017);

                $.each(keys_cpg_functional, function(i, val) {
                    chart_cpg_functional[i][1]    += parseInt(value[val.key]);
                });
                chart_cpg_management_gender[0].data[0] += parseInt(value.ketua_m);
                chart_cpg_management_gender[0].data[1] += parseInt(value.sekretaris_m);
                chart_cpg_management_gender[0].data[2] += parseInt(value.bendahara_m);
                // console.log(value.bendahara_m);

                chart_cpg_management_gender[1].data[0] += parseInt(value.ketua_f);
                chart_cpg_management_gender[1].data[1] += parseInt(value.sekretaris_f);
                chart_cpg_management_gender[1].data[2] += parseInt(value.bendahara_f);
                // console.log(value.bendahara_f);
            });
            $('#box_cpg').text(number_format(cpg,0,'.',','));
            $('#box_cpg_female').text(number_format(100*cpg_female/(cpg_male+cpg_female),1,'.',','));
            s.cat_cpg           = cat_cpg;
            s.chart_cpg         = chart_cpg;
            s.chart_cpg_tahun   = chart_cpg_tahun;
            s.year_categories   = year_categories;
            s.chart_cpg_functional          = chart_cpg_functional;
            s.cat_cpg_management_gender     = cat_cpg_management_gender;
            s.chart_cpg_management_gender   = chart_cpg_management_gender;            
            // =========== End of CPG ===========

            // =========== Coop ===========
            var coops = r.coop;
            var coop = 0, coop_female = 0, coop_male = 0;
            var keys_coop_functional = [
                {'key':'ada_pengurus', 'label' : lang('Yes')},
                {'key':'tidak_ada_pengurus', 'label' : lang("No")},
            ]
            var chart_coop_functional = [];         
            $.each(keys_coop_functional, function(i, val) {
                chart_coop_functional[i]       = [];
                chart_coop_functional[i][0]    = lang(val.label);
                chart_coop_functional[i][1]    = 0;
            });
            var cat_coop_management_gender = [
              lang('Chairman'),lang('Secretary'),lang('Treasurer'),
            ];
            var chart_coop_management_gender = [];
            chart_coop_management_gender[0] = [];
            chart_coop_management_gender[0].name = lang('Male');
            chart_coop_management_gender[0].data = [];
            chart_coop_management_gender[1] = [];
            chart_coop_management_gender[1].name = lang('Female');
            chart_coop_management_gender[1].data = [];
            $.each(cat_coop_management_gender, function(i, val) {
                chart_coop_management_gender[0].data[i] = 0;
                chart_coop_management_gender[1].data[i] = 0;
            });
            $.each(coops, function(i, value) {
                coop                 += parseInt(value.coop);
                coop_male            += parseInt(value.ketua_m)+parseInt(value.sekretaris_m)+parseInt(value.bendahara_m);
                coop_female          += parseInt(value.ketua_f)+parseInt(value.sekretaris_f)+parseInt(value.bendahara_f);

                $.each(keys_coop_functional, function(i, val) {
                    chart_coop_functional[i][1]    += parseInt(value[val.key]);
                });
                chart_coop_management_gender[0].data[0] += parseInt(value.ketua_m);
                chart_coop_management_gender[0].data[1] += parseInt(value.sekretaris_m);
                chart_coop_management_gender[0].data[2] += parseInt(value.bendahara_m);
                // console.log(value.bendahara_m);

                chart_coop_management_gender[1].data[0] += parseInt(value.ketua_f);
                chart_coop_management_gender[1].data[1] += parseInt(value.sekretaris_f);
                chart_coop_management_gender[1].data[2] += parseInt(value.bendahara_f);
                // console.log(value.bendahara_f);
            });
            $('#box_coop').text(number_format(coop,0,'.',','));
            $('#box_coop_female').text(number_format(100*coop_female/(coop_male+coop_female),1,'.',','));
            s.year_categories   = year_categories;
            s.chart_coop_functional          = chart_coop_functional;
            s.cat_coop_management_gender     = cat_coop_management_gender;
            s.chart_coop_management_gender   = chart_coop_management_gender;
            // =========== End of Coop ===========
            // 
            // =========== Trader ===========
            var traders = r.trader;
            var trader = 0, trader_female = 0, trader_male = 0;
            var chart_trader = [], cat_trader = [], trader_gender_male = [], trader_gender_female = [];
            
            $.each(traders, function(i, value) {
                trader                 += parseInt(value.trader);
                trader_male            += parseInt(value.male);
                trader_female          += parseInt(value.female);

               // pie data
                chart_trader[i]              = [];
                chart_trader[i][0]           = lang(value.label);
                chart_trader[i][1]           = parseInt(value.trader);
               // chart data
                cat_trader[i]               = lang(value.label);
                trader_gender_male[i]       = parseInt(value.male);
                trader_gender_female[i]     = parseInt(value.female);
            });
            $('#box_trader').text(number_format(trader,0,'.',','));
            $('#box_trader_female').text(number_format(100*trader_female/(trader_male+trader_female),1,'.',','));
            s.cat_trader            = cat_trader;
            s.chart_trader          = chart_trader;
            s.trader_gender_male    = trader_gender_male;
            s.trader_gender_female  = trader_gender_female;
            // =========== End of Coop ===========
            
            // =========== Trader ===========
            var nurserys = r.nursery;
            var nursery = 0, nursery_capacity = 0;
            var chart_nursery = [], cat_nursery = [], chart_nursery_capacity = [], chart_nursery_capacity_ownership = [];
            var keys_nursery_ownership = [
                {'key':'farmer', 'label':lang('Petani')},
                {'key':'cpg', 'label':lang('Kelompok Tani')},
                {'key':'coop', 'label':lang('Organisasi Petani')},
                {'key':'trader', 'label':lang('Pedagang')},
            ]
            var chart_nursery_ownership = [];
            for (var i = keys_nursery_ownership.length - 1; i >= 0; i--) {                
                chart_nursery_ownership[i]       = [];
                chart_nursery_ownership[i][0]    = lang(keys_nursery_ownership[i].label);
                chart_nursery_ownership[i][1]    = 0;
                chart_nursery_capacity_ownership[i] = {};
                chart_nursery_capacity_ownership[i].name = lang(keys_nursery_ownership[i].label);
                chart_nursery_capacity_ownership[i].data = [];
            }
            $.each(nurserys, function(i, value) {
                nursery             += parseInt(value.nursery);
                nursery_capacity    += parseInt(value.Kapasitas);

               // pie data
                chart_nursery[i]            = [];
                chart_nursery[i][0]         = lang(value.label);
                chart_nursery[i][1]         = parseInt(value.nursery);
               // chart data
                cat_nursery[i]             = lang(value.label);
                chart_nursery_capacity[i]   = parseInt(value.Kapasitas);
                $.each(keys_nursery_ownership, function(j, val) {
                    chart_nursery_ownership[j][1] += parseInt(value['nursery_'+val.key]);
                    chart_nursery_capacity_ownership[j].data[i] = parseInt(value['nursery_'+val.key]);
                });
            });
            $('#box_nursery').text(number_format(nursery,0,'.',','));
            $('#box_nursery_capacity').text(number_format(nursery_capacity,1,'.',','));
            s.cat_nursery                       = cat_nursery;
            s.chart_nursery                     = chart_nursery;
            s.chart_nursery_capacity            = chart_nursery_capacity;
            s.chart_nursery_ownership           = chart_nursery_ownership;
            s.chart_nursery_capacity_ownership  = chart_nursery_capacity_ownership;
            // =========== End of Coop ===========

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
         }
   });
   return s; 
};

var s = ajaxDataRenderer(m_data); 

column([{name: lang('Kelompok'), data: s.chart_cpg}], 'chart_cpg', lang('Kelompok Produksi Kakao'), lang('Jumlah'), null, s.cat_cpg, 'normal',0,false);
var chart_established_cpg = [];
for (var i = 0;i < s.cat_cpg.length;i++) {
    chart_established_cpg[i] = {
        name: s.cat_cpg[i],
        data: s.chart_cpg_tahun[i]
    };
}
column(chart_established_cpg,'chart_established_cpg', lang('Established Cocoa Producer Groups'), lang('Jumlah'), null,s.year_categories,'normal',0,true);
plot(s.chart_cpg_functional,'chart_cpg_functional', lang('Functional FG Management'),'1',lang('Jumlah'));
column(s.chart_cpg_management_gender, 'chart_cpg_management_gender', lang('Gender in FG Management'), lang('CPG Management'), ['#3B5323','#589C14'], s.cat_cpg_management_gender, 'percent',0,true);

plot(s.chart_coop_functional,'chart_coop_functional', lang('Functional Farmer Organization Management'),'1',lang('Jumlah'));
column(s.chart_coop_management_gender, 'chart_coop_management_gender', lang('Gender in Farmer Organization Management'), lang('Percent'), ['#3B5323','#589C14'], s.cat_coop_management_gender, 'percent',0,true);

plot(s.chart_trader,'chart_trader', lang('Traders'),'2',lang('Jumlah'));
column([{name: lang('Laki-laki'),data: s.trader_gender_male},{name: lang('Perempuan'),data: s.trader_gender_female}], 'chart_trader_gender', lang('Traders Gender'), '%', 
   ['#3B5323','#589C14'], s.cat_trader,'percent',0,true);

plot(s.chart_nursery,'chart_nursery', lang('Pembibitan'),'2',lang('Jumlah'));
column([{name: lang('Kapasitas'), data: s.chart_nursery_capacity}], 'chart_nursery_capacity', lang('Kapasitas Pembibitan Per Tahun'), lang('Bibit'), null, s.cat_nursery, 'normal',0,false);
plot(s.chart_nursery_ownership,'chart_nursery_ownership', lang('Kepemilikan Pembibitan'),'2',lang('Jumlah'));
column(s.chart_nursery_capacity_ownership, 'pie6', lang('Kapasitas Pembibitan Per Pemilik'), '%', 
   ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'], s.cat_nursery,'percent',0,true)