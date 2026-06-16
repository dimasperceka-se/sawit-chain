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
            var garden_baseline = 0, garden_postline = 0, productivity_baseline = 0, productivity_postline = 0, total1 = 0, total2 = 0;

            var s11 = [];
            var categories = [];
            var years = m_now - 2010 + 1;
            for (var i=0;i<years;i++) {
                s11[i] = [];
                s11[i]['name'] = m_now-(years-1-i);
                s11[i]['categories'] = ["Baseline", "Post-Line"];
                categories[i] = {};
                categories[i].name = m_now-(years-1-i);
                categories[i].categories = ["Baseline", "Post-Line"];
            }

            var s12 = [];
            var s13 = [];
            var s14 = [];
            var s15 = [];
            var data1 = r['garden'];
            var farmer_baseline = 0
            var farmer_postline = 0
            for (var i=0;i<data1.length;i++) {
                s12[i] = lang(data1[i]['label']);
                s13[i] = [];
                s14[i] = [];
                s15[i] = [];
                s13[i][0] = s15[i][0] = parseInt(data1[i]['garden_baseline_2010']);
                s13[i][2] = s15[i][2] = parseInt(data1[i]['garden_baseline_2011']);
                s13[i][4] = s15[i][4] = parseInt(data1[i]['garden_baseline_2012']);
                s13[i][6] = s15[i][6] = parseInt(data1[i]['garden_baseline_2013']);
                s13[i][8] = s15[i][8] = parseInt(data1[i]['garden_baseline_2014']);
                s13[i][10] = s15[i][10] = parseInt(data1[i]['garden_baseline_2015']);
                s13[i][12] = s15[i][12] = parseInt(data1[i]['garden_baseline_2016']);
                s13[i][14] = s15[i][14] = parseInt(data1[i]['garden_baseline_2017']);
                s13[i][1] = s13[i][3] = s13[i][5] = s13[i][7] = s13[i][9] = s13[i][11] = s13[i][13] = 0;
                s14[i][0] = s15[i][1] = parseInt(data1[i]['garden_postline_2010']);
                s14[i][2] = s15[i][3] = parseInt(data1[i]['garden_postline_2011']);
                s14[i][4] = s15[i][5] = parseInt(data1[i]['garden_postline_2012']);
                s14[i][6] = s15[i][7] = parseInt(data1[i]['garden_postline_2013']);
                s14[i][8] = s15[i][9] = parseInt(data1[i]['garden_postline_2014']);
                s14[i][10] = s15[i][11] = parseInt(data1[i]['garden_postline_2015']);
                s14[i][12] = s15[i][13] = parseInt(data1[i]['garden_postline_2016']);
                s14[i][14] = s15[i][15] = parseInt(data1[i]['garden_postline_2017']);
                s14[i][1] = s14[i][3] = s14[i][5] = s14[i][7] = s14[i][9] = s14[i][11] = s14[i][13] = 0;
                garden_baseline += parseInt(data1[i]['garden_baseline']);
                garden_postline += parseInt(data1[i]['garden_postline']);
                farmer_baseline += parseInt(data1[i]['farmer_baseline']);
                farmer_postline += parseInt(data1[i]['farmer_postline']);
            }

            var s21 = [];
            var s22 = [];
            var s23 = [];
            var s31 = [];
            var s32 = [];
            var s33 = [];
            var data2 = r['garden'];
            var avg_productivity_base = 0;
            var avg_productivity_post = 0;
            var count_baseline = 0;
            var count_postline = 0;
            for (var i=0;i<data2.length;i++) {
                s21[i] = s31[i] = lang(data2[i]['label']);
                s22[i] = parseFloat(data2[i]['production_baseline'])/parseFloat(data2[i]['ha_baseline']);
                s32[i] = parseFloat(data2[i]['production_baseline'])/parseFloat(data2[i]['tree_baseline']);
                s23[i] = parseFloat(data2[i]['production_postline'])/parseFloat(data2[i]['ha_postline']);
                s33[i] = parseFloat(data2[i]['production_postline'])/parseFloat(data2[i]['tree_postline']);
                productivity_baseline += parseFloat(data2[i]['production_baseline']);
                total1 += parseFloat(data2[i]['ha_baseline']);
                // if (parseFloat(data2[i]['ha_baseline'])) {
                //     avg_productivity_base += parseFloat(data2[i]['production_baseline'])/parseFloat(data2[i]['ha_baseline']);
                //     count_baseline++;
                // }
                productivity_postline += parseFloat(data2[i]['production_postline']);
                total2 += parseFloat(data2[i]['ha_postline']);
                // if (parseFloat(data2[i]['ha_postline'])) {
                //     avg_productivity_post += parseFloat(data2[i]['production_postline'])/parseFloat(data2[i]['ha_postline']);
                //     count_postline++;
                // }
            }
            productivity_baseline = productivity_baseline/total1;
            // productivity_baseline = avg_productivity_base/count_baseline;
            productivity_postline = productivity_postline/total2;
            // productivity_postline = avg_productivity_post/count_postline;
            // console.log(productivity_postline);

            s = [[s11,s12,s13,s14,s15],[s21,s22,s23],[s31,s32,s33]]
            //    0   1   2   3  4    5  6

            // district = s[7] = r['district'];

            tree_baseline_prod = 0;
            tree_baseline_prod_count = 0;
            if (r['garden']) {
                $.each(r['garden'], function(index, val) {
                    tree_baseline_prod += parseFloat(val.production_baseline);
                    tree_baseline_prod_count += parseFloat(val.tree_baseline);
                });
                tree_baseline_prod /= tree_baseline_prod_count;
            };
            tree_postline_prod = 0;
            tree_postline_prod_count = 0;
            if (r['garden']) {
                $.each(r['garden'], function(index, val) {
                    tree_postline_prod += parseFloat(val.production_postline);
                    tree_postline_prod_count += parseFloat(val.tree_postline);
                });
                tree_postline_prod /= tree_postline_prod_count;
            };

            s['categories']                 = categories;

            $('#garden_baseline').html(number_format(garden_baseline,0,'.',','));
            $('#garden_postline').html(number_format(garden_postline,0,'.',','));
            $('#productivity_baseline').html(number_format(productivity_baseline,0,'.',','));
            $('#productivity_postline').html(number_format(productivity_postline,0,'.',','));
            $('#tree_baseline_prod').html(number_format(tree_baseline_prod,2,'.',','));
            $('#tree_postline_prod').html(number_format(tree_postline_prod,2,'.',','));
            $('#farmer_baseline').html(number_format(farmer_baseline,0,'.',','));
            $('#farmer_postline').html(number_format(farmer_postline,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
return s; 
};

var s = ajaxDataRenderer(m_data); 

// console.log(s)

var ddata = [];
for (var i=0;i<s[0][1].length;i++) {
    ddata[i] = {name: s[0][1][i],data: s[0][4][i]};
}

column(ddata, 
    'pie1', lang('Total Jumlah Survey Kebun'), lang('Jumlah'), ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F'], 
    [
    {name:s[0][0][0]['name'],categories:s[0][0][0]['categories']},
    {name:s[0][0][1]['name'],categories:s[0][0][1]['categories']},
    {name:s[0][0][2]['name'],categories:s[0][0][2]['categories']},
    {name:s[0][0][3]['name'],categories:s[0][0][3]['categories']},
    {name:s[0][0][4]['name'],categories:s[0][0][4]['categories']},
    {name:s[0][0][5]['name'],categories:s[0][0][5]['categories']},
    {name:s[0][0][6]['name'],categories:s[0][0][6]['categories']},
    {name:s[0][0][7]['name'],categories:s[0][0][7]['categories']},
    ],
    'normal',0,true,-90)
column([{name: lang('Baseline'),data: s[1][1], stack:'Baseline'},{name: lang('Post-Line'),data: s[1][2], stack:'Post-Line'}], 'pie2', 
    lang('Rata-Rata Hasil Panen'), lang('Kg/Ha/Thn'), ['#3B5323','#589C14'], s[1][0],'normal',0,true);
column([{name: lang('Baseline'),data: s[2][1], stack:'Baseline'},{name: lang('Post-Line'),data: s[2][2], stack:'Post-Line'}], 'tree_avg_prod', 
    lang('Average Tree Productivity'), lang('Kg/Pohon/Thn'), ['#3B5323','#589C14'], s[2][0],'normal',2,true);
