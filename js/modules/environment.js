// if (m_prov!='') dataDistrict(m_data,'finance');

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
            var diversification = 0, carbon = 0, emission = 0, fertilizer = 0, compost = 0;

            var data = r['environment'];

            var chart_categories = new Array();
            var chart_carbon = new Array();
            chart_carbon[0]            = new Array();
            chart_carbon[0]['name']    = lang('Carbon Stock');
            chart_carbon[0]['data']    = new Array();

            var keys_emmission = [
                {'key':'CO2_Urea', 'label' : 'Urea'},
                {'key':'CO2_NPK', 'label' : 'NPK'},
                {'key':'CO2_ZA', 'label' : 'ZA'},
                {'key':'CO2_Kompos', 'label' : 'Compost'},
            ]
            var chart_emission = new Array();
            for (var i = keys_emmission.length - 1; i >= 0; i--) {
                chart_emission[i]            = new Array();
                chart_emission[i]['name']    = lang(keys_emmission[i].label);
                chart_emission[i]['data']    = new Array();
            };

            var chart_compost = new Array();
            chart_compost[0]            = new Array();
            chart_compost[0]['name']    = lang('Compost');
            chart_compost[0]['data']    = new Array();

            var chart_fertilizer = new Array();
            chart_fertilizer[0]            = new Array();
            chart_fertilizer[0]['name']    = lang('Fertilizer');
            chart_fertilizer[0]['data']    = new Array();

            // var keys_fertilizer = [
            //     {'key':'Application_Urea', 'label' : 'Urea'},
            //     {'key':'Application_TSP', 'label' : 'TSP'},
            //     {'key':'Application_NPK', 'label' : 'NPK'},
            //     {'key':'Application_KCl', 'label' : 'KCL'},
            //     {'key':'Application_ZA', 'label' : 'ZA'},
            // ]
            // var pie_fertilizer = new Array();
            // for (var i = keys_fertilizer.length - 1; i >= 0; i--) {
            //     pie_fertilizer[i]       = new Array();
            //     pie_fertilizer[i][0]    = lang(keys_fertilizer[i].label);
            //     pie_fertilizer[i][1]    = 0;
            // }

            // var keys_compost = [
            //     {'key':'Kompos_Kandang', 'label' : lang('Manure')},
            //     {'key':'Kompos_Cair', 'label' : lang('Liquid Fertilizer')},
            //     {'key':'Kompos_Granula', 'label' : lang('Granular/Solid Fertilizer')},
            // ]
            // var pie_compost = new Array();
            // for (var i = keys_compost.length - 1; i >= 0; i--) {
            //     pie_compost[i]       = new Array();
            //     pie_compost[i][0]    = lang(keys_compost[i].label);
            //     pie_compost[i][1]    = 0;
            // };

            // var keys_pesticide = [
            //     {'key':'bravoxone', 'label' : lang('Bravoxone')},
            //     {'key':'gramoxone', 'label' : lang('Gramoxone')},
            //     {'key':'noxone', 'label' : lang('Noxone')},
            //     {'key':'para_spesial', 'label' : lang('Para Spesial')},
            //     {'key':'paratop', 'label' : lang('Paratop')},
            //     {'key':'primaxone', 'label' : lang('Primaxone')},
            //     {'key':'supertox', 'label' : lang('Supertox')},
            // ];
            // var pie_pesticide = new Array();
            // var chart_pesticide = new Array();
            // var cat_pesticide = new Array();
            // chart_pesticide[0] = [];
            // chart_pesticide[0]['name'] = lang('Baseline');
            // chart_pesticide[0]['data'] = [];
            // chart_pesticide[1] = [];
            // chart_pesticide[1]['name'] = lang('Postline');
            // chart_pesticide[1]['data'] = [];
            // for (var i = keys_pesticide.length - 1; i >= 0; i--) {
            //     pie_pesticide[i]       = new Array();
            //     pie_pesticide[i][0]    = keys_pesticide[i].label;
            //     pie_pesticide[i][1]    = 0;

            //     // cat_pesticide[i] = keys_pesticide[i].label;

            //     // chart_pesticide[0]['data'][i] = 0;
            //     // chart_pesticide[1]['data'][i] = 0;
            // };

            // var keys_24d = [
            //     {'key':'bimastar', 'label' : lang('Bimastar')},
            //     {'key':'polado', 'label' : lang('Polado')},
            //     {'key':'primastar', 'label' : lang('Primastar')},
            //     {'key':'rumat', 'label' : lang('Rumat')},
            // ];
            // var pie_24d = new Array();
            // for (var i = keys_24d.length - 1; i >= 0; i--) {
            //     pie_24d[i]       = new Array();
            //     pie_24d[i][0]    = keys_24d[i].label;
            //     pie_24d[i][1]    = 0;

                // cat_pesticide[i] = keys_pesticide[i].label;

                // chart_pesticide[0]['data'][i] = 0;
                // chart_pesticide[1]['data'][i] = 0;
            // };

            // var pest_prov_total = 0;
            // var chart_pesticide_province = [];
            // chart_pesticide_province[0] = [];
            // chart_pesticide_province[0]['name'] = lang('');
            // chart_pesticide_province[0]['data'] = [];
            // var pest24d_prov_total = 0;
            // var chart_pest24d_province = [];
            // chart_pest24d_province[0] = [];
            // chart_pest24d_province[0]['name'] = lang('');
            // chart_pest24d_province[0]['data'] = [];
            // var cat_pesticide_province = [];
            // if (data_pest_latest = r['pesticide_latest']) {
                // $.each(data_pest_latest, function(index, val) {
                    // cat_pesticide_province[index] = lang(val.label);
                    // chart_pesticide_province[0]['data'][index] = 0;
                    // chart_pest24d_province[0]['data'][index] = 0;
                // });
                // $.each(data_pest_latest, function(index, val) {
                    // $.each(keys_pesticide, function(i, v) {
                    //     pie_pesticide[i][1] += parseInt(val[v.key]);

                    //     chart_pesticide_province[0]['data'][index] += parseInt(val[v.key]);
                    //     pest_prov_total += parseInt(val[v.key]);
                    // });
                    // $.each(keys_24d, function(i, v) {
                    //     pie_24d[i][1] += parseInt(val[v.key]);

                    //     chart_pest24d_province[0]['data'][index] += parseInt(val[v.key]);
                    //     pest24d_prov_total += parseInt(val[v.key]);
                    // });
                // });
            // };
            // console.log(pie_pesticide);
            // chart['pie_pesticide']       = pie_pesticide;
            // console.log(pie_24d);
            // chart['pie_24d']       = pie_24d;
            // var pest_base_total = 0;
            // var pest_post_total = 0;
            // if (data_pesticide = r['pesticide_baseline_postline']) {
            //     $.each(data_pesticide, function(index, val) {
            //         $.each(keys_pesticide, function(k, v) {
            //             key_base = v.key+'_baseline';
            //             key_post = v.key+'_postline';
            //             // chart_pesticide[0]['data'][k] += parseInt(val[key_base]);
            //             // pest_base_total += parseInt(val[key_base]);
            //             // chart_pesticide[1]['data'][k] += parseInt(val[key_post]);
            //             // pest_post_total += parseInt(val[key_post]);
            //         });
            //     })
            // };
            // for (var i = 0; i < chart_pesticide[0]['data'].length; i++) {
            //     chart_pesticide[0]['data'][i] = chart_pesticide[0]['data'][i]/pest_base_total*100;
            // };
            // for (var i = 0; i < chart_pesticide[1]['data'].length; i++) {
            //     chart_pesticide[1]['data'][i] = chart_pesticide[1]['data'][i]/pest_base_total*100;
            // };
            // for (var i = 0; i < chart_pesticide_province[0]['data'].length; i++) {
            //     chart_pesticide_province[0]['data'][i] = chart_pesticide_province[0]['data'][i]/pest_prov_total*100;
            // };
            // for (var i = 0; i < chart_pest24d_province[0]['data'].length; i++) {
            //     chart_pest24d_province[0]['data'][i] = chart_pest24d_province[0]['data'][i]/pest24d_prov_total*100;
            // };
            // console.log(chart_pesticide);
            // chart['cat_pesticide']      = cat_pesticide;
            // chart['chart_pesticide']    = chart_pesticide;

            // chart['cat_pesticide_province']     = cat_pesticide_province;
            // chart['chart_pesticide_province']   = chart_pesticide_province;
            // chart['chart_pest24d_province']     = chart_pest24d_province;

            // var keys_tree_cat = [
            //     {'key':'TBM_Application', 'label' : 'Not yet yielding'},
            //     {'key':'TM_Application', 'label' : 'Mature Tree'},
            //     {'key':'TR_Application', 'label' : 'Old, diseased'},
            // ]
            // var pie_tree_cat = new Array();
            // for (var i = keys_tree_cat.length - 1; i >= 0; i--) {
            //     pie_tree_cat[i]       = new Array();
            //     pie_tree_cat[i][0]    = lang(keys_tree_cat[i].label);
            //     pie_tree_cat[i][1]    = 0;
            // };

            // var production_count = 0;
            var production = 0;
            if (data) {
                for (var i = data.length - 1; i >= 0; i--) {
                    chart_categories[i]             = lang(data[i]['label']);

                    chart_carbon[0]['data'][i]      = parseInt(data[i]['C_Stock'])/parseInt(data[i]['Production']);
                    chart_fertilizer[0]['data'][i]  = parseInt(data[i]['Kg_Fertilizer_'])/parseFloat(data[i]['Hectare']);
                    chart_compost[0]['data'][i]     = parseInt(data[i]['Kg_Kompos_'])/parseFloat(data[i]['Hectare']);
                    // chart_emission[0]['data'][i]    = parseInt(data[i]['CO2_Hectare']);

                    for (var j = keys_emmission.length - 1; j >= 0; j--) {
                        chart_emission[j]['data'][i]    = parseFloat(data[i][keys_emmission[j].key])/parseInt(data[i]['Production']);
                    };

                    if (parseInt(data[i]['Production'])) {
                        carbon          += parseFloat(data[i]['C_Stock']);
                        emission        += parseFloat(data[i]['CO2_Total']);
                        production      += parseFloat(data[i]['Production']);
                        // production_count++;
                    }

                    // fertilizer          += parseInt(data[i]['Kg_Fertilizer_Hectare']);
                    // compost             += parseInt(data[i]['Kg_Kompos_Hectare']);
                }
                carbon          /= production;
                emission        /= production;
                // fertilizer      /= data.length;
                // compost         /= data.length;
            }
            // var data_fert = r['fertilizer'];
            // if (data_fert) {
            //     for (var i = data_fert.length - 1; i >= 0; i--) {
                    // for (var j = keys_fertilizer.length - 1; j >= 0; j--) {
                    //     pie_fertilizer[j][1] += parseInt(data_fert[i][keys_fertilizer[j].key]);
                    // };
                    // for (var j = keys_compost.length - 1; j >= 0; j--) {
                    //     pie_compost[j][1] += parseInt(data_fert[i][keys_compost[j].key]);
                    // };
                    // for (var j = keys_tree_cat.length - 1; j >= 0; j--) {
                    //     pie_tree_cat[j][1] += parseInt(data_fert[i][keys_tree_cat[j].key]);
                    // };
            //     };
            // };

            var div_categories = [];
            var data_div = r['garden'];
            var chart_shade_tree = [];
            chart_shade_tree[0]            = [];
            chart_shade_tree[0]['name']    = lang('Shade Trees');
            chart_shade_tree[0]['data']    = [];

            var chart_tree_category = [];
            chart_tree_category[0]            = [];
            chart_tree_category[0]['name']    = lang('Estate Crops');
            chart_tree_category[0]['data']    = [];
            chart_tree_category[1]            = [];
            chart_tree_category[1]['name']    = lang('Hard Wood');
            chart_tree_category[1]['data']    = [];
            chart_tree_category[2]            = [];
            chart_tree_category[2]['name']    = lang('Fruit Trees');
            chart_tree_category[2]['data']    = [];
            chart_tree_category[3]            = [];
            chart_tree_category[3]['name']    = lang('Leguminosae');
            chart_tree_category[3]['data']    = [];
            chart_other = [];
            var keys_other = [
                {'key': 'Tanaman_Produksi_Selain_Kakao', 'label': lang('Tanaman Produksi Selain Kakao')},
                {'key': 'Kayu_Keras', 'label': lang('Kayu Keras')},
                {'key': 'Buah_buahan', 'label': lang('Buah-buahan')},
                {'key': 'Leguminosa', 'label': lang('Leguminosa')},
                {'key': 'Lainnya', 'label': lang('Lainnya')},
            ]
            for (var i = keys_other.length - 1; i >= 0; i--) {                
                chart_other[i]      = [];
                chart_other[i][0]   = lang(keys_other[i].label);
                chart_other[i][1]   = 0;
            };
            if (data_div) {
                for (var i = 0; i < data_div.length; i++) {
                    div_categories[i]             = lang(data_div[i]['label']);
                    chart_shade_tree[0]['data'][i]     = parseInt(data_div[i]['Total_Nr_Diversification'])/parseFloat(data_div[i]['TotalHa']);

                    chart_tree_category[0]['data'][i] = parseInt(data_div[i]['Nr_Coconut'])+parseInt(data_div[i]['Nr_Areca_Palm'])+parseInt(data_div[i]['Nr_Rubber'])+parseInt(data_div[i]['Nr_Clove'])+parseInt(data_div[i]['Nr_Oil_Palm'])+parseInt(data_div[i]['Nr_Sugar_Palm'])+parseInt(data_div[i]['Nr_Nutmeg'])+parseInt(data_div[i]['Nr_Hazelnut']);
                    chart_tree_category[1]['data'][i] = parseInt(data_div[i]['Nr_Mahagony'])+parseInt(data_div[i]['Nr_Teak'])+parseInt(data_div[i]['Nr_Vitex'])+parseInt(data_div[i]['Nr_Elmerilla'])+parseInt(data_div[i]['Nr_Anthocephalus']);
                    chart_tree_category[2]['data'][i] = parseInt(data_div[i]['Nr_Jackfruit'])+parseInt(data_div[i]['Nr_Banana'])+parseInt(data_div[i]['Nr_Rambutan'])+parseInt(data_div[i]['Nr_Mango'])+parseInt(data_div[i]['Nr_Langsat'])+parseInt(data_div[i]['Nr_Durian'])+parseInt(data_div[i]['Nr_Avocado'])+parseInt(data_div[i]['Nr_Breadfruit'])+parseInt(data_div[i]['Nr_Papaya'])+parseInt(data_div[i]['Nr_Mangosteen'])+parseInt(data_div[i]['Nr_Citrus']);
                    chart_tree_category[3]['data'][i] = parseInt(data_div[i]['Nr_Gliricidia'])+parseInt(data_div[i]['Nr_Leucaena'])+parseInt(data_div[i]['Nr_Parkia'])+parseInt(data_div[i]['Nr_Archidendron']);

                    for (var j = keys_other.length - 1; j >= 0; j--) {
                        chart_other[j][1]   += parseFloat(data_div[i][keys_other[j].key]);
                        diversification += parseFloat(data_div[i][keys_other[j].key]);
                    };

                };
            };

            chart['chart_carbon']           = chart_carbon;
            chart['chart_emission']         = chart_emission;
            chart['chart_fertilizer']       = chart_fertilizer;
            chart['chart_compost']          = chart_compost;
            // chart['pie_fertilizer']      = pie_fertilizer;
            // chart['pie_compost']         = pie_compost;
            // chart['pie_tree_cat']        = pie_tree_cat;

            chart['chart_categories']       = chart_categories;

            chart['div_categories']         = div_categories;
            chart['chart_shade_tree']       = chart_shade_tree;
            chart['chart_tree_category']    = chart_tree_category;

            // var chart_other = new Array();
            // var data_other   = r['other'];
            // var keys = Object.keys(data_other[0]);
            // for (var i=0;i<keys.length;i++) {
            //    chart_other[i] = new Array();
            //    chart_other[i][0] = lang(keys[i]);
            //    chart_other[i][1] = parseFloat(data_other[0][keys[i]]);
            //    diversification += parseFloat(data_other[0][keys[i]]);
            // }
            chart['chart_other']       = chart_other;

            $('#box_diversification').html(number_format(diversification, 0, '.', ','));
            $('#box_carbon').html(number_format(carbon, 2, '.', ','));
            $('#box_emission').html(number_format(emission, 2, '.', ','));
            // $('#box_fertilizer').html(number_format(fertilizer, 1, '.', ','));
            // $('#box_compost').html(number_format(compost, 1, '.', ','));
            
            var box_emission_reduction = 0,
            box_emission_reduction_farm = 0,
            box_emission_reduction_ha = 0,
            box_emission_reduction_mt = 0;

            var cat_base = [];
            var chart_tCO2e_cocoa = [];
            var chart_tCO2e_farm = [];
            var chart_tCO2e_ha = [];
            var chart_tCO2e_mt = [];

            if (tCO2e = r.base) {
                var farmers = 0
                , Farmers_Baseline = 0, CO2_Total_Baseline = 0, CO2_Hectare_Baseline = 0, CO2_Kg_Baseline = 0
                , Farmers_Postline = 0, CO2_Total_Postline = 0, CO2_Hectare_Postline = 0, CO2_Kg_Postline = 0
                , emission_reduction_farm_baseline = 0, emission_reduction_baseline = 0, emission_reduction_ha_baseline = 0, emission_reduction_mt_baseline = 0
                , emission_reduction_farm_postline = 0, emission_reduction_postline = 0, emission_reduction_ha_postline = 0, emission_reduction_mt_postline = 0
                ;
                $.each(tCO2e, function(index, val) {
                    cat_base[index] = lang(val.label);
                    farmers                 += parseFloat(val.farmers);

                    Farmers_Baseline        += parseFloat(val.Farmers_Baseline);
                    CO2_Total_Baseline      += parseFloat(val.CO2_Total_Baseline);
                    CO2_Hectare_Baseline    += parseFloat(val.CO2_Hectare_Baseline);
                    CO2_Kg_Baseline         += parseFloat(val.CO2_Kg_Baseline);

                    Farmers_Postline        += parseFloat(val.Farmers_Postline);
                    CO2_Total_Postline      += parseFloat(val.CO2_Total_Postline);
                    CO2_Hectare_Postline    += parseFloat(val.CO2_Hectare_Postline);
                    CO2_Kg_Postline         += parseFloat(val.CO2_Kg_Postline);

                    // chart
                    emission_reduction_farm_baseline     = parseFloat(val.Farmers_Baseline) > 0 ? parseFloat(val.CO2_Total_Baseline)/parseFloat(val.Farmers_Baseline)/1000 : 0;
                    emission_reduction_farm_postline     = parseFloat(val.Farmers_Postline) > 0 ? parseFloat(val.CO2_Total_Postline)/parseFloat(val.Farmers_Postline)/1000 : 0;
                    emission_reduction_baseline          = emission_reduction_farm_baseline*parseFloat(val.farmers);
                    emission_reduction_postline          = emission_reduction_farm_postline*parseFloat(val.farmers);
                    emission_reduction_ha_baseline       = parseFloat(val.Farmers_Baseline) > 0 ? parseFloat(val.CO2_Hectare_Baseline)/parseFloat(val.Farmers_Baseline)/1000 : 0;
                    emission_reduction_ha_postline       = parseFloat(val.Farmers_Postline) > 0 ? parseFloat(val.CO2_Hectare_Postline)/parseFloat(val.Farmers_Postline)/1000 : 0;
                    emission_reduction_mt_baseline       = parseFloat(val.Farmers_Baseline) > 0 ? parseFloat(val.CO2_Kg_Baseline)/parseFloat(val.Farmers_Baseline)/1000 : 0;
                    emission_reduction_mt_postline       = parseFloat(val.Farmers_Postline) > 0 ? parseFloat(val.CO2_Kg_Postline)/parseFloat(val.Farmers_Postline)/1000 : 0 ;

                    chart_tCO2e_cocoa[index]    = parseFloat(emission_reduction_baseline)-parseFloat(emission_reduction_postline);;
                    chart_tCO2e_farm[index]     = (emission_reduction_farm_postline/emission_reduction_farm_baseline-1)*100;
                    chart_tCO2e_ha[index]       = (emission_reduction_ha_postline/emission_reduction_ha_baseline-1)*100;;
                    chart_tCO2e_mt[index]       = (emission_reduction_mt_postline/emission_reduction_mt_baseline-1)*100;;

                });
                // reset vallue for box
                // console.log('CO2_Total_Baseline : '+CO2_Total_Baseline);
                // console.log('Farmers_Baseline : '+Farmers_Baseline);
                // console.log('CO2_Total_Postline : '+CO2_Total_Postline);
                // console.log('Farmers_Postline : '+Farmers_Postline);
                emission_reduction_farm_baseline     = CO2_Total_Baseline/Farmers_Baseline/1000;
                emission_reduction_farm_postline     = CO2_Total_Postline/Farmers_Postline/1000;
                emission_reduction_baseline          = emission_reduction_farm_baseline*farmers;
                emission_reduction_postline          = emission_reduction_farm_postline*farmers;
                // console.log('emission_reduction_farm_baseline : '+emission_reduction_farm_baseline);
                // console.log('emission_reduction_farm_postline : '+emission_reduction_farm_postline);
                // console.log('emission_reduction_baseline : '+emission_reduction_baseline);
                // console.log('emission_reduction_postline : '+emission_reduction_postline);
                // console.log('CO2_Hectare_Baseline : '+CO2_Hectare_Baseline);
                // console.log('CO2_Hectare_Postline : '+CO2_Hectare_Postline);
                // console.log('CO2_Kg_Baseline : '+CO2_Kg_Baseline);
                // console.log('CO2_Kg_Postline : '+CO2_Kg_Postline);
                emission_reduction_ha_baseline       = CO2_Hectare_Baseline/Farmers_Baseline/1000;
                emission_reduction_ha_postline       = CO2_Hectare_Postline/Farmers_Postline/1000;
                emission_reduction_mt_baseline       = CO2_Kg_Baseline/Farmers_Baseline/1000;
                emission_reduction_mt_postline       = CO2_Kg_Postline/Farmers_Postline/1000;
            }
            box_emission_reduction          = emission_reduction_baseline-emission_reduction_postline;
            // console.log('emission_reduction_postline = '+emission_reduction_postline);
            // console.log('emission_reduction_baseline = '+emission_reduction_baseline);
            box_emission_reduction_farm     = (emission_reduction_farm_postline/emission_reduction_farm_baseline-1)*100
            // console.log('emission_reduction_farm_postline = '+emission_reduction_farm_postline);
            // console.log('emission_reduction_farm_baseline = '+emission_reduction_farm_baseline);
            box_emission_reduction_ha       = (emission_reduction_ha_postline/emission_reduction_ha_baseline-1)*100;
            // console.log('emission_reduction_ha_postline = '+emission_reduction_ha_postline);
            // console.log('emission_reduction_ha_baseline = '+emission_reduction_ha_baseline);
            box_emission_reduction_mt       = (emission_reduction_mt_postline/emission_reduction_mt_baseline-1)*100;
            // console.log('emission_reduction_mt_postline = '+emission_reduction_mt_postline);
            // console.log('emission_reduction_mt_baseline = '+emission_reduction_mt_baseline);
            
            $('#box_emission_reduction').text(number_format(box_emission_reduction,0,'.',','));
            $('#box_emission_reduction_farm').text(number_format(box_emission_reduction_farm,0,'.',','));
            $('#box_emission_reduction_ha').text(number_format(box_emission_reduction_ha,0,'.',','));
            $('#box_emission_reduction_mt').text(number_format(box_emission_reduction_mt,0,'.',','));

            chart['chart_tree_category']    = chart_tree_category;

            chart['cat_base']           = cat_base;
            chart['chart_tCO2e_cocoa']  = chart_tCO2e_cocoa;
            chart['chart_tCO2e_farm']   = chart_tCO2e_farm;
            chart['chart_tCO2e_ha']     = chart_tCO2e_ha;
            chart['chart_tCO2e_mt']     = chart_tCO2e_mt;

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
    return chart;
};

var chart = ajaxDataRenderer(m_data);

console.log(chart.chart_other);
plot(chart.chart_other,'chart_other', lang('Diversification (Shade Trees on Cocoa Farm)'),'1',lang('Jumlah'));

column(chart.chart_carbon, 'chart_carbon', lang('Carbon Stock'), lang('tC/t cocoa beans'), null, chart.chart_categories, 'normal',2,false);
column(chart.chart_emission, 'chart_emission', lang('Emission per MT Cocoa'), lang('tCO2e/MT Cocoa'), null, chart.chart_categories, 'stack',2,true);
// column(chart.chart_fertilizer, 'chart_fertilizer', lang('Fertilizer per Hectare'), lang('Kg/Hectare'), null, chart.chart_categories, 'normal',0,false);
// column(chart.chart_compost, 'chart_compost', lang('Compost per Hectare'), lang('Kg/Hectare'), null, chart.chart_categories, 'normal',0,false);
column(chart.chart_shade_tree, 'chart_shade_tree', lang('Number of Shade Trees per Ha'), lang('Tree/Ha'), null, chart.chart_categories, 'normal',0,false);
column(chart.chart_tree_category, 'chart_tree_category', lang('Tree Category'), lang('%'), null, chart.chart_categories, 'percent',0,true);

// plot(chart.pie_fertilizer,'pie_fertilizer', lang('Fertilizer Application by Farms'),'1',lang('Jumlah'));
// plot(chart.pie_compost,'pie_compost', lang('Compost Application by Farms'),'1',lang('Jumlah'));
// plot(chart.pie_tree_cat,'pie_tree_cat', lang('Tree Categories Application'),'1',lang('Jumlah'));
// plot(chart.pie_pesticide,'pie_pesticide', lang('Paraquat Products'),'1',lang('Jumlah'));

// column_one(chart.chart_pesticide, 'chart_pesticide', lang('Farms with Paraquat Application'), lang('%'), ['#3B5323','#589C14'], chart.cat_pesticide,'normal',1,true);
// column_one(chart.chart_pesticide_province, 'chart_pesticide_province', lang('Farms with Paraquat Application'), lang('%'), ['#3B5323','#589C14'], chart.cat_pesticide_province,'normal',1,false);

// plot(chart.pie_24d,'pie_24d', lang('2,4 D Products'),'1',lang('Jumlah'));
// column_one(chart.chart_pest24d_province, 'chart_pest24d_province', lang('Farms with 2,4 D Application'), lang('%'), ['#3B5323','#589C14'], chart.cat_pesticide_province,'normal',1,false);

column([{'name': 'tCO2e', 'data': chart.chart_tCO2e_cocoa}], 'chart_tCO2e', lang('Emission Net Reduction (tCO2e Cocoa)'), lang('%'), null, chart.cat_base, 'normal',0,true);
column([{'name': 'tCO2e', 'data': chart.chart_tCO2e_farm}], 'chart_tCO2e_farm', lang('Emission Net Reduction (tCO2e/Farm Cocoa) (%)'), lang('%'), null, chart.cat_base, 'normal',0,true);
column([{'name': 'tCO2e', 'data': chart.chart_tCO2e_ha}], 'chart_tCO2e_ha', lang('Emission Net Reduction (tCO2e/Ha Cocoa) (%)'), lang('%'), null, chart.cat_base, 'normal',0,true);
column([{'name': 'tCO2e', 'data': chart.chart_tCO2e_mt}], 'chart_tCO2e_mt', lang('Emission Net Reduction (tCO2e/MT Cocoa) (%)'), lang('%'), null, chart.cat_base, 'normal',0,true);