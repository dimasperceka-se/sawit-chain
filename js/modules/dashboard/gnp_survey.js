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
            var box1 = box2 = box3 = box4 = total1 = total2 = 0;

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
                box1 += parseInt(data1[i]['garden_baseline']);
                box2 += parseInt(data1[i]['garden_postline']);
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
                box3 += parseFloat(data2[i]['production_baseline']);
                total1 += parseFloat(data2[i]['ha_baseline']);
                // if (parseFloat(data2[i]['ha_baseline'])) {
                //     avg_productivity_base += parseFloat(data2[i]['production_baseline'])/parseFloat(data2[i]['ha_baseline']);
                //     count_baseline++;
                // }
                box4 += parseFloat(data2[i]['production_postline']);
                total2 += parseFloat(data2[i]['ha_postline']);
                // if (parseFloat(data2[i]['ha_postline'])) {
                //     avg_productivity_post += parseFloat(data2[i]['production_postline'])/parseFloat(data2[i]['ha_postline']);
                //     count_postline++;
                // }
            }
            box3 = box3/total1;
            // box3 = avg_productivity_base/count_baseline;
            box4 = box4/total2;
            // box4 = avg_productivity_post/count_postline;
            // console.log(box4);

            s = [[s11,s12,s13,s14,s15],[s21,s22,s23],[s31,s32,s33]]
            //    0   1   2   3  4    5  6

            // district = s[7] = r['district'];

            var nutrition = r['nutrition'];
            var box_nutrition_baseline
            = box_nutrition_postline
            = idds_base_sum
            = idds_post_sum
            = idds_base_count
            = idds_post_count
            = idds_base_avg = idds_base_avg_count
            = idds_post_avg = idds_post_avg_count
            = box_idds_baseline
            = box_idds_postline
            = box_garden_nutrition_baseline
            = box_garden_nutrition_postline
            = area_base_sum
            = area_base_count
            = area_base_avg = area_base_avg_count
            = area_post_sum
            = area_post_count
            = area_post_avg = area_post_avg_count
            = 0;
            var chart_nutrition         = [];
            var cat_nutrition           = [];
            var chart_idds              = [];
            chart_idds[0] = [];
            chart_idds[0]['name']   = lang('Baseline');
            chart_idds[0]['data']   = [];            
            chart_idds[1] = [];
            chart_idds[1]['name']   = lang('Post-Line');
            chart_idds[1]['data']   = [];            
            var chart_garden_nutrition  = [];
            chart_garden_nutrition[0] = [];
            chart_garden_nutrition[0]['name']   = lang('Baseline');
            chart_garden_nutrition[0]['data']   = [];            
            chart_garden_nutrition[1] = [];
            chart_garden_nutrition[1]['name']   = lang('Post-Line');
            chart_garden_nutrition[1]['data']   = [];            
            if (nutrition) {
                $.each(nutrition, function(index, val) {
                    cat_nutrition[index] = lang(val['label']);

                    // chart nutrition
                    chart_nutrition[index] = {};
                    chart_nutrition[index].name = lang(val['label']);
                    chart_nutrition[index].data = [];
                    for (var i = 1; i < 9; i++) {
                        n = i*2-1
                        chart_nutrition[index]['data'][n-1]     = parseInt(val['nutrition_baseline_'+(2009+i)]);
                        chart_nutrition[index]['data'][n]       = parseInt(val['nutrition_postline_'+(2009+i)]);
                    };

                    // chart IDDS
                    chart_idds[0]['data'][index] = parseInt(val['score_female_count_baseline'])?parseFloat(val['score_female_sum_baseline'])/parseInt(val['score_female_count_baseline']):0;
                    chart_idds[1]['data'][index] = parseInt(val['score_female_count_postline'])?parseFloat(val['score_female_sum_postline'])/parseInt(val['score_female_count_postline']):0;

                    // chart garden nutrition
                    chart_garden_nutrition[0]['data'][index] = parseFloat(val['luas_count_baseline'])?parseFloat(val['luas_sum_baseline'])/parseFloat(val['luas_count_baseline']):0;
                    chart_garden_nutrition[1]['data'][index] = parseFloat(val['luas_count_postline'])?parseFloat(val['luas_sum_postline'])/parseFloat(val['luas_count_postline']):0;
                
                    box_nutrition_baseline      += parseInt(val['nutrition_baseline']);
                    box_nutrition_postline      += parseInt(val['nutrition_postline']);

                    idds_base_sum               += parseFloat(val['score_female_sum_baseline']);
                    idds_base_count             += parseInt(val['score_female_count_baseline']);
                    // if (parseInt(val['score_count_base_female'])) {
                    //     idds_base_avg               += parseFloat(val['score_sum_base_female'])/parseInt(val['score_count_base_female']);
                    //     idds_base_avg_count++;
                    // }
                    idds_post_sum               += parseFloat(val['score_female_sum_postline']);
                    idds_post_count             += parseInt(val['score_female_count_postline']);
                    // if (parseInt(val['score_count_post_female'])) {
                    //     idds_post_avg               += parseFloat(val['score_sum_post_female'])/parseInt(val['score_count_post_female']);
                    //     idds_post_avg_count++;
                    // }

                    area_base_sum               += parseFloat(val['luas_sum_baseline']);
                    area_base_count             += parseFloat(val['luas_count_baseline']);
                    // if (parseFloat(val['area_count_base'])) {
                    //     area_base_avg               += parseFloat(val['area_sum_base'])/parseFloat(val['area_count_base']);
                    //     area_base_avg_count++;
                    // }
                    area_post_sum               += parseFloat(val['luas_sum_postline']);
                    area_post_count             += parseFloat(val['luas_count_postline']);
                    // if (parseFloat(val['area_count_post'])) {
                    //     area_post_avg               += parseFloat(val['area_sum_post'])/parseFloat(val['area_count_post']);
                    //     area_post_avg_count++;
                    // }
                
                });
                // average
                box_garden_nutrition_baseline   = parseFloat(area_base_sum/area_base_count);
                box_garden_nutrition_postline   = parseFloat(area_post_sum/area_post_count);

                // average
                box_idds_baseline               = parseFloat(idds_base_sum/idds_base_count);
                box_idds_postline               = parseFloat(idds_post_sum/idds_post_count);
            };

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
                    if (parseInt(val['National_count_baseline'])) base_125 += parseFloat(val['1.25_baseline'])/parseInt(val['National_count_baseline']);
                    if (parseInt(val['National_count_baseline'])) base_25 += parseFloat(val['2.5_baseline'])/parseInt(val['National_count_baseline']);
                });
            };
            // console.log(chart_poverty_15);
            var poverty_postline = r['ppi'];
            var post_25 = 0;
            var post_125 = 0;
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
                    if (parseInt(val['National_count_postline'])) post_25 += parseFloat(val['2.5_postline'])/parseInt(val['National_count_postline']);
                    if (parseInt(val['National_count_postline'])) post_125 += parseFloat(val['1.25_postline'])/parseInt(val['National_count_postline']);
                });
            };
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
            $('#box_dec_pov_25').html(number_format(dec_25,1,'.',','));
            $('#box_dec_pov_125').html(number_format(dec_125,1,'.',','));

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
            var box_gfp_baseline = 0, box_gfp_postline = 0, box_bank_account_baseline = 0, box_bank_account_postline = 0
            series_bank_account_baseline = [], series_bank_account_postline = [], series_saving_baseline = [], series_saving_postline = [],
            finance_categories = [];
            if (r.finance) {
                $.each(r.finance, function(index, val) {
                    box_gfp_baseline += parseFloat(val.gfp_baseline);
                    box_gfp_postline += parseFloat(val.gfp_postline);
                    box_bank_account_baseline += parseFloat(val.bank_account_baseline);
                    box_bank_account_postline += parseFloat(val.bank_account_postline);

                    finance_categories[index] = lang(val.label);
                    series_bank_account_baseline[index] = parseFloat(val.bank_account_baseline);
                    series_bank_account_postline[index] = parseFloat(val.bank_account_postline);
                    series_saving_baseline[index] = parseFloat(val.saving_baseline);
                    series_saving_postline[index] = parseFloat(val.saving_postline);
                });
            }
            s.finance_categories  = finance_categories;
            s.series_bank_account_baseline  = series_bank_account_baseline;
            s.series_bank_account_postline  = series_bank_account_postline;
            s.series_saving_baseline        = series_saving_baseline;
            s.series_saving_postline        = series_saving_postline;

            $('#box_gfp_baseline').text(number_format(box_gfp_baseline,0,'.',','));
            $('#box_gfp_postline').text(number_format(box_gfp_postline,0,'.',','));
            $('#box_bank_account_baseline').text(number_format(box_bank_account_baseline,0,'.',','));
            $('#box_bank_account_postline').text(number_format(box_bank_account_postline,0,'.',','));

            s['categories']                 = categories;
            s['chart_nutrition']            = chart_nutrition;
            s['cat_nutrition']              = cat_nutrition;
            s['chart_idds']                 = chart_idds;
            s['chart_garden_nutrition']     = chart_garden_nutrition;
            s['chart_poverty_15']           = chart_poverty_15;
            s['chart_poverty_25']           = chart_poverty_25;
            s['cat_poverty_province']       = cat_poverty_province;


            $('#box_nutrition_baseline').html(number_format(box_nutrition_baseline,0,'.',','));
            $('#box_nutrition_postline').html(number_format(box_nutrition_postline,0,'.',','));
            $('#box_idds_baseline').html(number_format(box_idds_baseline,1,'.',','));
            $('#box_idds_postline').html(number_format(box_idds_postline,1,'.',','));
            $('#box_garden_nutrition_baseline').html(number_format(box_garden_nutrition_baseline,2,'.',','));
            $('#box_garden_nutrition_postline').html(number_format(box_garden_nutrition_postline,2,'.',','));

            $('#box1').html(number_format(box1,0,'.',','));
            $('#box2').html(number_format(box2,0,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));
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
column(s.chart_nutrition, 'chart_nutrition', lang('Total Nutrition Survey'), lang('Jumlah'), null, s.categories,'normal',0,true,-90);

column_one(s.chart_idds, 'chart_idds', lang(' IDDS'), lang('Score'), ['#3B5323','#589C14'], s.cat_nutrition,'normal',1,true);
column_one(s.chart_garden_nutrition, 'chart_garden_nutrition', lang('Average Garden Area Nutrition (M2)'), lang('M2'), ['#3B5323','#589C14'], s.cat_nutrition,'normal',1,true);
column_one(s['chart_poverty_15'], 'chart_poverty_15', lang('Baseline and Post-Line, $ 1.25/Day'), '', ['#3B5323','#589C14'], s.cat_poverty_province, 'normal', 1, true, -45, 3);
column_one(s['chart_poverty_25'], 'chart_poverty_25', lang('Baseline and Post-Line, $ 2.5/Day'), '', ['#3B5323','#589C14'], s.cat_poverty_province, 'normal', 1, true, -45, 10);
column([{name: lang('Baseline'),data: s.series_bank_account_baseline, stack:'Baseline'},{name: lang('Post-Line'),data: s.series_bank_account_postline, stack:'Post-Line'}], 'chart_bank_accounts', 
    lang('Number of Bank Accounts'), lang('Jumlah'), ['#3B5323','#589C14'], s.finance_categories,'normal',0,true);
column([{name: lang('Baseline'),data: s.series_saving_baseline, stack:'Baseline'},{name: lang('Post-Line'),data: s.series_saving_postline, stack:'Post-Line'}], 'chart_farmer_saving', 
    lang('Number of Farmers with Savings'), lang('Jumlah'), ['#3B5323','#589C14'], s.finance_categories,'normal',0,true);
