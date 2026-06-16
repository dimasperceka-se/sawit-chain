// if (m_prov!='') dataDistrict(m_data,'nutrition');
var lifestock_label     = [];
var Spinach             = [];
var Chilli              = [];
var LongBean            = [];
var WaterCress          = [];
var Mustard             = [];
var Eggplant            = [];
var Tomato              = [];
var Goat                = [];
var Cow                 = [];
var Duck                = [];
var Chicken             = [];
var Fish                = [];
var Sheep               = [];
var Buffalo             = [];
var Pig                 = [];

var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    var s = [];
    $.ajax({
        type: "GET",
        url: url,
        data: {
            prov: m_prov,
            kab: m_kab,
            priv: m_priv,
            daer: m_daer,
            partner: m_partner
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var box1 = 0, box2 = 0, box3 = 0, box4 = 0, box_total_nutrition_garden_area = 0, box_fish_pond_area = 0, total = 0;
            var s1 = [];
            var data1 = r['farmer'];
            for (var i = 0; i < data1.length; i++) {
                s1[i] = [];
                s1[i][0] = lang(data1[i]['label']);
                s1[i][1] = parseInt(data1[i]['farmer']);
                box1 += parseInt(data1[i]['farmer']);
            }
            var s21 = [];
            var s22 = [];
            var s23 = [];
            var data2 = r['farmer'];
            var avg_female = 0;
            var count = 0;
            for (var i = 0; i < data2.length; i++) {
                s21[i] = lang(data2[i]['label']);
                s22[i] = parseFloat(data2[i]['male']);
                s23[i] = parseFloat(data2[i]['female']);
                box2 += parseFloat(data2[i]['female']);
                total += parseFloat(data2[i]['male']) + parseFloat(data2[i]['female']);
                if ((parseFloat(data2[i]['male']) && parseFloat(data2[i]['female']))) {
                    avg_female += parseFloat(data2[i]['female']) / (parseFloat(data2[i]['male']) + parseFloat(data2[i]['female']));
                    count++;
                }
            }
            // box2 = box2/total*100
            box2 = avg_female / count * 100
            var s31 = [];
            var s32 = [];
            var cat_idds = [];
            var chart_idds = [];
            chart_idds['male'] = [];
            chart_idds['female'] = [];
            var data3 = r['data'];
            total = 0;
            count = 0;
            var avg_idds = 0;
            for (var i = 0; i < data3.length; i++) {
                s31[i] = lang(data3[i]['label']);
                chart_idds['male'][i] = parseFloat(data3[i]['score_male']) / parseFloat(data3[i]['male_idds']);
                chart_idds['female'][i] = parseFloat(data3[i]['score_female']) / parseFloat(data3[i]['female_idds']);
                s32[i] = parseFloat(data3[i]['score_total']);
                box3 += parseFloat(data3[i]['score_female']);
                total += parseFloat(data3[i]['female_idds']);
                if (parseFloat(data3[i]['female_idds'])) {
                    avg_idds += parseFloat(data3[i]['score_female']) / parseFloat(data3[i]['female_idds']);
                    count++;
                }
            }
            cat_idds = s31;
            // box3 = box3/total;
            box3 = avg_idds / count;

            var region_nutrition_area = [];
            var chart_nutrition_area = [];
            var chart_nutrition_garden = [];
            var region_fishpond_area = [];
            var chart_fishpond_area = [];
            var chart_fishpond = [];

            var s41 = [];
            var s42 = [];
            var data4 = r['data'];
            total = 0;
            count = 0;
            var avg_area = 0;
            for (var i = 0; i < data4.length; i++) {
                s41[i] = lang(data4[i]['label']);
                region_nutrition_area[i] = lang(data4[i].label);
                s42[i] = parseFloat(data4[i]['GardenSizeMod_Farmer']);
                box_total_nutrition_garden_area += parseFloat(data4[i]['sumGardenSizeMod']);
                chart_nutrition_area[i] = parseFloat(data4[i]['sumGardenSizeMod']);
                chart_nutrition_garden[i] = parseFloat(data4[i]['GardenYes']);
                total += parseFloat(data4[i]['farmerMod']);
                if (parseFloat(data4[i]['farmerMod'])) {
                    avg_area += parseFloat(data4[i]['sumGardenSizeMod']) / parseFloat(data4[i]['farmerMod']);
                    count++;
                }
            }
            // box4 = box4/total;
            // box4 = avg_area / count;

            //box 5
            var data5 = r['farmer'];
            var totalFarmer5 = 0;
            var totalSumAgeFarmer5 = 0;
            for (var i = 0; i < data5.length; i++) {
                totalFarmer5 += parseInt(data5[i]['farmer']);
                totalSumAgeFarmer5 += parseInt(data5[i]['sum_farmer_age']);
            }
            var box5 = totalSumAgeFarmer5 / totalFarmer5;

            //box 6
            var data6 = r['data'];
            var totalEstGarden = 0;
            for (var i = 0; i < data6.length; i++) {
                totalEstGarden += parseInt(data6[i]['Established_Garden']);
            }
            var box6 = totalEstGarden;

            //box 7
            var totalFishPond = 0;
            for (var i = 0; i < data6.length; i++) {
                totalFishPond += parseInt(data6[i]['Fish_Pond']);
            }
            var box7 = totalFishPond;

            //box 8
            var totalFarmerFishPondArea = 0;
            var totalSumFishPondArea = 0;
            var s51 = [];
            var s52 = [];
            for (var i = 0; i < data6.length; i++) {
                region_fishpond_area[i] = lang(data6[i].label);
                totalFarmerFishPondArea += parseInt(data6[i]['count_farmer_fish_pond_area']);
                totalSumFishPondArea += parseInt(data6[i]['sum_fish_pond_area']);
                chart_fishpond_area[i] = parseInt(data6[i]['sum_fish_pond_area']);
                chart_fishpond[i] = parseInt(data6[i]['Fish_Pond']);

                s51[i] = lang(data6[i]['label']);
                s52[i] = parseFloat(number_format(parseInt(data6[i]['sum_fish_pond_area']) / parseInt(data6[i]['count_farmer_fish_pond_area']), 1, '.', ','));
            }
            var box8 = totalSumFishPondArea / totalFarmerFishPondArea;
            //console.log(s52);

            var lifestock = r['data'];
            var livestock_key = [
                {id:'Goat', label:lang('Goat')},
                {id:'Cow', label:lang('Cow')},
                {id:'Duck', label:lang('Duck')},
                {id:'Chicken', label:lang('Chicken')},
                {id:'Fish', label:lang('Fish')},
                {id:'Sheep', label:lang('Sheep')},
                {id:'Buffalo', label:lang('Buffalo')},
                {id:'Pig', label:lang('Pig')},             
            ];
            var cat_livestock = [];
            $.each(livestock_key, function(index, val) {
                cat_livestock[index] = val.label;
            });
            var chart_livestock = [];
            for (var i = 0; i < lifestock.length; i++) {
                lifestock_label[i]  = lang(lifestock[i]['label']);
                Spinach[i]          = parseFloat(lifestock[i]['Spinach']);
                Chilli[i]           = parseFloat(lifestock[i]['Chilli']);
                LongBean[i]         = parseFloat(lifestock[i]['LongBean']);
                WaterCress[i]       = parseFloat(lifestock[i]['WaterCress']);
                Mustard[i]          = parseFloat(lifestock[i]['Mustard']);
                Eggplant[i]         = parseFloat(lifestock[i]['Eggplant']);
                Tomato[i]           = parseFloat(lifestock[i]['Tomato']);
                Goat[i]             = parseFloat(lifestock[i]['Goat']);
                Cow[i]              = parseFloat(lifestock[i]['Cow']);
                Duck[i]             = parseFloat(lifestock[i]['Duck']);
                Chicken[i]          = parseFloat(lifestock[i]['Chicken']);
                Fish[i]             = parseFloat(lifestock[i]['Fish']);
                Sheep[i]    = parseFloat(lifestock[i]['Sheep']);
                Buffalo[i]  = parseFloat(lifestock[i]['Buffalo']);
                Pig[i]      = parseFloat(lifestock[i]['Pig']);
                chart_livestock[i] = {};
                chart_livestock[i].name = lang(lifestock[i]['label']);
                chart_livestock[i].data = [];
                $.each(livestock_key, function(index, val) {
                    chart_livestock[i].data[index] = parseInt(lifestock[i][val.id]);
                });
            }
            
            s = [s1, [s21, s22, s23],
                    [s31, s32],
                    [s41, s42],
                    [s51, s52]
                ];
            s['cat_idds']                   = cat_idds;
            s['chart_idds']                 = chart_idds;
            s['region_nutrition_area']      = region_nutrition_area;
            s['region_fishpond_area']       = region_fishpond_area;
            s['chart_nutrition_area']       = chart_nutrition_area;
            s['chart_nutrition_garden']     = chart_nutrition_garden;
            s['chart_fishpond_area']        = chart_fishpond_area;
            s['chart_fishpond']             = chart_fishpond;
            s['chart_livestock']            = chart_livestock;
            s['cat_livestock']              = cat_livestock;

            $('#box1').html(number_format(box1, 0, '.', ','));
            $('#box2').html(number_format(box2, 1, '.', ','));
            $('#box3').html(number_format(box3, 1, '.', ','));
            // $('#box4').html(number_format(box4, 2, '.', ','));
            $('#box_total_nutrition_garden_area').html(number_format(box_total_nutrition_garden_area, 2, '.', ','));
            $('#box5').html(number_format(box5, 2, '.', ','));
            $('#box6').html(number_format(box6, 0, '.', ','));
            $('#box7').html(box7);
            // $('#box8').html(number_format(box8, 2, '.', ','));
            $('#box_fish_pond_area').html(number_format(totalSumFishPondArea, 2, '.', ','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display = '';
        }
    });
    return s;
};
var s = ajaxDataRenderer(m_data);

// column([{
//     name: lang('Luas'),
//     data: s[3][1]
// }], 'pie4', lang('Rata-Rata luas Kebun Nutrisi'), 'm2', ['#3B5323'], s[3][0], 'normal', 2)

// column([{
//     name: lang('Luas'),
//     data: s[4][1]
// }], 'pie5', lang('Average Fish Pond Area'), 'm2', ['#3B5323'], s[4][0], 'normal', 2)

column_one([{
    name: lang('Male'),
    data: s.chart_idds['male']
}, {
    name: lang('Female'),
    data: s.chart_idds['female']
}], 'pie3', lang('IDDS'), lang('Skor'), ['#3B5323', '#589C14'], s.cat_idds, null, 1, true)

column([{
    name: lang('Laki-laki'),
    data: s[1][1]
}, {
    name: lang('Perempuan'),
    data: s[1][2]
}], 'pie2', lang('Jenis Kelamin Peserta Nutrisi'), '%', ['#3B5323', '#589C14'], s[1][0], 'percent', 0, true)

plot(s[0], 'pie1', lang('Anggota Rumah Tangga Peserta Pelatihan Nutrisi'), '2', lang('Jumlah'));

column([{
    name: lang('Spinach'),
    data: Spinach
}, {
    name: lang('Chilli'),
    data: Chilli
}, {
    name: lang('LongBean'),
    data: LongBean
}, {
    name: lang('WaterCress'),
    data: WaterCress
}, {
    name: lang('Mustard'),
    data: Mustard
}, {
    name: lang('Eggplant'),
    data: Eggplant
}, {
    name: lang('Tomato'),
    data: Tomato
}], 'chart_vegetable', lang('Vegetable Grown'), '%', ['#3B5323', '#446B1E', '#4E8419', '#589C14', '#61B50F', '#6BCD0A', '#75E605', '#7FFF00'], lifestock_label, 'percent', 0, true);

column([{
    name: lang('Goat'),
    data: Goat
}, {
    name: lang('Cow'),
    data: Cow
}, {
    name: lang('Duck'),
    data: Duck
}, {
    name: lang('Chicken'),
    data: Chicken
}, {
    name: lang('Fish'),
    data: Fish
}, {
    name: lang('Sheep'),
    data: Sheep
}, {
    name: lang('Buffalo'),
    data: Buffalo
}, {
    name: lang('Pig'),
    data: Pig
}, 
], 'chart_livestock', lang('Lifestock'), '%', ['#3B5323', '#446B1E', '#4E8419', '#589C14', '#61B50F', '#6BCD0A', '#75E605', '#7FFF00'], lifestock_label, 'percent', 0, true);

column(s.chart_livestock, 'chart_livestock_province', lang('Lifestock'), '%', ['#3B5323', '#446B1E', '#4E8419', '#589C14', '#61B50F', '#6BCD0A', '#75E605', '#7FFF00'], s.cat_livestock, 'percent', 0, true);

column([{name: lang('Number of Household Nutrition Garden'),data: s.chart_nutrition_garden}], 'chart_nutrition_garden', lang('Number of Household Nutrition Garden'), lang('Jumlah'), ['#3B5323'], s.region_nutrition_area, 'normal');
column([{name: lang('Total Nutrition Garden Area (M2)'),data: s.chart_nutrition_area}], 'chart_nutrition_area', lang('Total Nutrition Garden Area (M2)'), lang('Area'), ['#3B5323'], s.region_nutrition_area, 'normal');
column([{name: lang('Total Fish Pond Area (M2)'),data: s.chart_fishpond_area}], 'chart_fishpond_area', lang('Total Fish Pond Area (M2)'), lang('Area'), ['#3B5323'], s.region_fishpond_area, 'normal');
column([{name: lang('Number of Fish Ponds'),data: s.chart_fishpond}], 'chart_fishpond', lang('Number of Fish Ponds'), lang('Jumlah'), ['#3B5323'], s.region_fishpond_area, 'normal');