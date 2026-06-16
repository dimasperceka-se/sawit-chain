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
            var all = r['all'];
            var group = r['group'];

            var chart_categories = [];

            var chart_compost = [];
            chart_compost[0]            = [];
            chart_compost[0]['name']    = lang('Compost');
            chart_compost[0]['data']    = [];

            var chart_fertilizer = [];
            chart_fertilizer[0]            = [];
            chart_fertilizer[0]['name']    = lang('Fertilizer');
            chart_fertilizer[0]['data']    = [];

            var keys_fertilizer = [
                {'key':'ApplicationUrea', 'label' : 'Urea'},
                {'key':'ApplicationTSP', 'label' : 'TSP'},
                {'key':'ApplicationNPK', 'label' : 'NPK'},
                {'key':'ApplicationKCl', 'label' : 'KCL'},
                {'key':'ApplicationZA', 'label' : 'ZA'},
            ]
            var pie_fertilizer = [];
            for (var i = keys_fertilizer.length - 1; i >= 0; i--) {
                pie_fertilizer[i]       = [];
                pie_fertilizer[i][0]    = lang(keys_fertilizer[i].label);
                pie_fertilizer[i][1]    = 0;
            };

            var keys_compost = [
                {'key':'KomposKandang', 'label' : lang('Manure')},
                {'key':'KomposCair', 'label' : lang('Liquid Fertilizer')},
                {'key':'KomposGranula', 'label' : lang('Granular/Solid Fertilizer')},
            ]
            var pie_compost = [];
            for (var i = keys_compost.length - 1; i >= 0; i--) {
                pie_compost[i]       = [];
                pie_compost[i][0]    = lang(keys_compost[i].label);
                pie_compost[i][1]    = 0;
            };

            var keys_tree_cat = [
                {'key':'TBMApplication', 'label' : 'Not yet yielding'},
                {'key':'TMApplication', 'label' : 'Mature Tree'},
                {'key':'TRApplication', 'label' : 'Old, diseased'},
            ]
            var pie_tree_cat = [];
            for (var i = keys_tree_cat.length - 1; i >= 0; i--) {
                pie_tree_cat[i]       = [];
                pie_tree_cat[i][0]    = lang(keys_tree_cat[i].label);
                pie_tree_cat[i][1]    = 0;
            };

            var hectare = 0;
            count_compost = 0;
            count_fertilizer = 0;
            if (group) {
                for (var i = group.length - 1; i >= 0; i--) {
                    chart_categories[i]             = lang(group[i]['label']);
                    chart_fertilizer[0]['data'][i]  = parseInt(group[i]['Fertilizer']);
                    chart_compost[0]['data'][i]     = parseInt(group[i]['kompos']);
               }
            };

            chart['chart_fertilizer']   = chart_fertilizer;
            chart['chart_compost']      = chart_compost;
                    for (var j = keys_fertilizer.length - 1; j >= 0; j--) {
                        pie_fertilizer[j][1] += parseInt(all[0][keys_fertilizer[j].key]);
                    };
                    for (var j = keys_compost.length - 1; j >= 0; j--) {
                        pie_compost[j][1] += parseInt(all[0][keys_compost[j].key]);
                    };
                    for (var j = keys_tree_cat.length - 1; j >= 0; j--) {
                        pie_tree_cat[j][1] += parseInt(all[0][keys_tree_cat[j].key]);
                    };
            chart['pie_fertilizer']     = pie_fertilizer;
            chart['pie_compost']        = pie_compost;
            chart['pie_tree_cat']       = pie_tree_cat;

            chart['chart_categories']   = chart_categories;

            var keys_disease = [
                {'key':'PenyakitKanker', 'label' : lang('Cancer')},
                {'key':'PenyakitBusuk', 'label' : lang('BlackPod')},
                {'key':'PenyakitUpas', 'label' : lang('Pink_Disease')},
                {'key':'PenyakitAkar', 'label' : lang('Root_Rot')},
                {'key':'PenyakitVSD', 'label' : lang('VSD')},
                {'key':'PenyakitAntraknose', 'label' : lang('Antracnose')},
            ]
            var chart_disease = [];
            for (var i = keys_disease.length - 1; i >= 0; i--) {
                chart_disease[i]       = [];
                chart_disease[i][0]    = lang(keys_disease[i].label);
                chart_disease[i][1]    = 0;
            };
            var keys_pest = [
                {'key':'HamaBPK', 'label' : lang('Cocoa pod borer')},
                {'key':'HamaHelopeltis', 'label' : lang('Mosquito bugs')},
                {'key':'HamaBatang', 'label' : lang('Trunk or twig borer')},
            ]
            var chart_pest = [];
            for (var i = keys_pest.length - 1; i >= 0; i--) {
                chart_pest[i]       = [];
                chart_pest[i][0]    = lang(keys_pest[i].label);
                chart_pest[i][1]    = 0;
            };
            var keys_pesticide = [
                {'key':'Herbisida','label':lang('Herbicides')},
                {'key':'Insectisida','label':lang('Insecticides')},
                {'key':'Fungisida','label':lang('Fungicides')},
            ]
            var cat_pesticide = [];
            var chart_pesticide = [];
            chart_pesticide[0]          = [];
            chart_pesticide[0]['name']  = lang('Yes');
            chart_pesticide[0]['data']  = [];
            chart_pesticide[1]          = [];
            chart_pesticide[1]['name']  = lang('No');
            chart_pesticide[1]['data']  = [];
            for (var i = keys_pesticide.length - 1; i >= 0; i--) {
                cat_pesticide[i]            = keys_pesticide[i].label;
                chart_pesticide[0]['data'][i] = 0;
                chart_pesticide[1]['data'][i] = 0;
            };
            var keys_pesticide_storage = [
                {'key':'PestisidaRumah', 'label' : lang('In the house')},
                {'key':'PestisidaKhusus', 'label' : lang('Pesticide Specific Place')},
                {'key':'PestisidaLuar', 'label' : lang('Outside of the house (house area)')},
                {'key':'PestisidaKebun', 'label' : lang('Outside of the cocoa farm')},
                {'key':'PestisidaLain', 'label' : lang('Others')},
            ]
            var chart_pesticide_storage = [];
            for (var i = keys_pesticide_storage.length - 1; i >= 0; i--) {
                chart_pesticide_storage[i]       = [];
                chart_pesticide_storage[i][0]    = lang(keys_pesticide_storage[i].label);
                chart_pesticide_storage[i][1]    = 0;
            };
            var keys_pesticide_handling = [
                {'key':'BuangKebun', 'label' : lang('Random disposal (Cocoa Farm or around the house)')},
                {'key':'BuangGunakan', 'label' : lang('Wash it clean and buried')},
                {'key':'BuangKubur', 'label' : lang('Used for something else')},
                {'key':'BuangBakar', 'label' : lang('Burn')},
                {'key':'BuangLain', 'label' : lang('Others')},
            ]
            var chart_pesticide_handling = [];
            for (var i = keys_pesticide_handling.length - 1; i >= 0; i--) {
                chart_pesticide_handling[i]       = [];
                chart_pesticide_handling[i][0]    = lang(keys_pesticide_handling[i].label);
                chart_pesticide_handling[i][1]    = 0;
            };
            var keys_herbicide_use = [
                {'key':'HerbisidaYes', 'label' : lang('Yes')},
                {'key':'HerbisidaNo', 'label' : lang('No')},
            ]
            var chart_herbicide_use = [];
            for (var i = keys_herbicide_use.length - 1; i >= 0; i--) {
                chart_herbicide_use[i]       = [];
                chart_herbicide_use[i][0]    = lang(keys_herbicide_use[i].label);
                chart_herbicide_use[i][1]    = 0;
            };
            var keys_herbicide_user = [
                {'key':'herbicide_paraquat','label':'Paraquat Users'},
                {'key':'herbicide_glyphosate','label':'Glyphosate Users'},
                {'key':'herbicide_24d','label':'2,4 D Users'},
            ]            
            var chart_herbicide_user = [];
            chart_herbicide_user[0] = {};
            chart_herbicide_user[0]['name'] = lang('Herbicide User');
            chart_herbicide_user[0]['data'] = [];
            var cat_herbicide_user = [];
            var count_herbicide_user = 0;
            for (var i = keys_herbicide_user.length - 1; i >= 0; i--) {                
                cat_herbicide_user[i] = lang(keys_herbicide_user[i].label);
                chart_herbicide_user[0]['data'][i] = parseInt(all[0][keys_herbicide_user[i].key]);
            };
            var keys_insecticide_use = [
                {'key':'InsectisidaYes', 'label' : lang('Insecticide User')},
                {'key':'InsectisidaNo', 'label' : lang('No Insecticide User')},
            ]
            var chart_insecticide_use = [];
            for (var i = keys_insecticide_use.length - 1; i >= 0; i--) {
                chart_insecticide_use[i]       = [];
                chart_insecticide_use[i][0]    = lang(keys_insecticide_use[i].label);
                chart_insecticide_use[i][1]    = 0;
            };
            var keys_insecticide_user = [
                {'key':'insecticide_banned','label':'Banned Insecticide Use'},
                {'key':'insecticide_watchlist','label':'Watchlist Insecticide Use'},
                {'key':'insecticide_allowed','label':'Allowed Insecticide Use'},
            ]            
            var chart_insecticide_user = [];
            chart_insecticide_user[0] = {};
            chart_insecticide_user[0]['name'] = lang('Insecticide User');
            chart_insecticide_user[0]['data'] = [];
            var cat_insecticide_user = [];
            var count_insecticide_user = 0;
            for (var i = keys_insecticide_user.length - 1; i >= 0; i--) {                
                cat_insecticide_user[i] = lang(keys_insecticide_user[i].label);
                chart_insecticide_user[0]['data'][i] = 0;
            };
            var keys_fungicide_use = [
                {'key':'FungisidaYes', 'label' : lang('Fungicide User')},
                {'key':'FungisidaNo', 'label' : lang('No Fungicide User')},
            ]
            var chart_fungicide_use = [];
            for (var i = keys_fungicide_use.length - 1; i >= 0; i--) {
                chart_fungicide_use[i]       = [];
                chart_fungicide_use[i][0]    = lang(keys_fungicide_use[i].label);
                chart_fungicide_use[i][1]    = 0;
            };
            var keys_fungicide_user = [
                {'key':'fungicide_banned','label':'Banned Fungicide Use'},
                {'key':'fungicide_watchlist','label':'Watchlist Fungicide Use'},
                {'key':'fungicide_allowed','label':'Allowed Fungicide Use'},
            ]            
            var chart_fungicide_user = [];
            chart_fungicide_user[0] = {};
            chart_fungicide_user[0]['name'] = lang('Fungicide User');
            chart_fungicide_user[0]['data'] = [];
            var cat_fungicide_user = [];
            var count_fungicide_user = 0;
            for (var i = keys_fungicide_user.length - 1; i >= 0; i--) {                
                cat_fungicide_user[i] = lang(keys_fungicide_user[i].label);
                chart_fungicide_user[0]['data'][i] = 0;
            };
            var keys_cultural_chemical = [
                {'key':'NOGAP_NOFung', 'label' : lang('No Cultural Practices, No Fungicide or Insecticide')},
                {'key':'NOGAP_Fung', 'label' : lang('No Cultural Practices, Apply Fungicide or Insecticide')},
                {'key':'GAP_NOFung', 'label' : lang('Apply Cultural Practices, No Fungicide or Insecticide')},
                {'key':'GAP_Fung', 'label' : lang('Apply Cultural Practices, Apply Fungicide or Insecticide')},
            ]
            var chart_cultural_chemical = [];
            for (var i = keys_cultural_chemical.length - 1; i >= 0; i--) {
                chart_cultural_chemical[i]       = [];
                chart_cultural_chemical[i][0]    = lang(keys_cultural_chemical[i].label);
                chart_cultural_chemical[i][1]    = 0;
            };
            var cat_agriinput = [];
            var keys_protective_equip = [
                {'key':'ProtectiveYes','label':lang('Yes')},
                {'key':'ProtectiveNo','label':lang('No')},
            ]
            var chart_protective_equip = new Array();
            for (var i = keys_protective_equip.length - 1; i >= 0; i--) {                
                chart_protective_equip[i]            = new Array();
                chart_protective_equip[i]['name']    = lang(keys_protective_equip[i].label);
                chart_protective_equip[i]['data']    = new Array();
                for (var j = group.length - 1; j >= 0; j--) {
                     chart_protective_equip[i]['data'][j]    = parseInt(group[j][keys_protective_equip[i].key]);
                 };
            };
               $.each(group, function(index, value) {
                    cat_agriinput[index] = lang(value['label']);
               });
                    $.each(keys_disease, function(idx, val) {
                        chart_disease[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_pest, function(idx, val) {
                        chart_pest[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_pesticide, function(idx, val) {
                        chart_pesticide[0]['data'][idx] += parseInt(all[0][val.key+'Yes']);
                        chart_pesticide[1]['data'][idx] += parseInt(all[0][val.key+'No']);
                    });
                    $.each(keys_pesticide_storage, function(idx, val) {
                        chart_pesticide_storage[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_pesticide_handling, function(idx, val) {
                        chart_pesticide_handling[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_herbicide_use, function(idx, val) {
                        chart_herbicide_use[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_insecticide_use, function(idx, val) {
                        chart_insecticide_use[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_insecticide_user, function(idx, val) {
                        chart_insecticide_user[0]['data'][idx] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_fungicide_use, function(idx, val) {
                        chart_fungicide_use[idx][1] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_fungicide_user, function(idx, val) {
                        chart_fungicide_user[0]['data'][idx] += parseInt(all[0][val.key]);
                    });
                    $.each(keys_cultural_chemical, function(idx, val) {
                        chart_cultural_chemical[idx][1] += parseInt(all[0][val.key]);
                    });
                    var handling_safe   = parseInt(all[0]['PestisidaKebun'])+parseInt(all[0]['PestisidaGunakan'])+
                        parseInt(all[0]['PestisidaKubur'])+parseInt(all[0]['PestisidaBakar'])+parseInt(all[0]['PestisidaLain']);
                    var storing_safe    = parseInt(all[0]['PestisidaRumah'])+parseInt(all[0]['PestisidaKhusus'])+
                        parseInt(all[0]['PestisidaLuar'])+parseInt(all[0]['PestisidaKebun'])+parseInt(all[0]['PestisidaLain']);                 
                    pesticide          = 100*parseInt(all[0]['PestisidaYes'])/(parseInt(all[0]['PestisidaYes'])+
                        parseInt(all[0]['PestisidaNo']));
                    organic_pesticide  = 100*parseInt(all[0]['PestisidaOrganic'])/(parseInt(all[0]['PestisidaYes'])+
                        parseInt(all[0]['PestisidaNo']));
                    chemical_fertilizer     = 100*parseInt(all[0]['ChemicalYes'])/(parseInt(all[0]['ChemicalYes'])+
                        parseInt(all[0]['ChemicalNo']));
                    organic_fertilizer      = 100*parseInt(all[0]['OrganicYes'])/(parseInt(all[0]['OrganicYes'])+
                        parseInt(all[0]['OrganicNo']));
                    protective_equip        = 100*parseInt(all[0]['ProtectiveYes'])/(parseInt(all[0]['ProtectiveYes'])+
                        parseInt(all[0]['ProtectiveNo']));
                    handling_safe           = 100*parseInt(all[0]['BuangKubur'])/handling_safe;
                    storing_safe            = 100*parseInt(all[0]['PestisidaKhusus'])/storing_safe;

            chart['chart_disease']              = chart_disease;
            chart['chart_pest']                 = chart_pest;
            chart['chart_pesticide']            = chart_pesticide;
            chart['cat_pesticide']              = cat_pesticide;
            chart['chart_pesticide_storage']    = chart_pesticide_storage;
            chart['chart_pesticide_handling']   = chart_pesticide_handling;
            chart['chart_herbicide_use']        = chart_herbicide_use;
            chart['chart_herbicide_user']       = chart_herbicide_user;
            chart['cat_herbicide_user']         = cat_herbicide_user;
            chart['chart_insecticide_use']      = chart_insecticide_use;
            chart['chart_insecticide_user']     = chart_insecticide_user;
            chart['cat_insecticide_user']       = cat_insecticide_user;
            chart['chart_fungicide_use']        = chart_fungicide_use;
            chart['chart_fungicide_user']       = chart_fungicide_user;
            chart['cat_fungicide_user']         = cat_fungicide_user;
            chart['chart_cultural_chemical']    = chart_cultural_chemical;
            chart['chart_protective_equip']     = chart_protective_equip;
            chart['cat_agriinput']              = cat_agriinput;
           
            var fertilizer = parseFloat(all[0]['fh']);
            var compost = parseFloat(all[0]['kh']);

            $('#box_fertilizer').html(number_format(all[0]['fh'], 1, '.', ','));
            $('#box_compost').html(number_format(all[0]['kh'], 1, '.', ','));
            $('#box_pesticide').html(number_format(all[0]['pes'], 1, '.', ','));
            $('#box_organic_pesticide').html(number_format(all[0]['pp'], 1, '.', ','));
            $('#box_chemical_fertilizer').html(number_format(all[0]['che'], 1, '.', ','));
            $('#box_organic_fertilizer').html(number_format(all[0]['org'], 1, '.', ','));
            $('#box_protective_equip').html(number_format(all[0]['py'], 1, '.', ','));
            $('#box_handling_safe').html(number_format(all[0]['kp'], 1, '.', ','));
            $('#box_storing_safe').html(number_format(all[0]['op'], 1, '.', ','));

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
        }
    });
    return chart;
};

var chart = ajaxDataRenderer(m_data);

column(chart.chart_fertilizer, 'chart_fertilizer', lang('Fertilizer per Hectare'), lang('Kg/Hectare'), null, chart.chart_categories, 'normal',0,false);
column(chart.chart_compost, 'chart_compost', lang('Compost per Hectare'), lang('Kg/Hectare'), null, chart.chart_categories, 'normal',0,false);

plot(chart.pie_fertilizer,'pie_fertilizer', lang('Fertilizer Application by Farms'),'1',lang('Jumlah'));
plot(chart.pie_compost,'pie_compost', lang('Compost Application by Farms'),'1',lang('Jumlah'));
plot(chart.pie_tree_cat,'pie_tree_cat', lang('Tree Categories Application'),'1',lang('Jumlah'));

plot(chart.chart_disease,'chart_disease', lang('Diseases Monitored in Cocoa Farms'),'1',lang('Jumlah'));
plot(chart.chart_pest,'chart_pest', lang('Pests Monitored in Cocoa Farms'),'1',lang('Jumlah'));
column(chart.chart_pesticide, 'chart_pesticide', lang('Pesticide Use in Cocoa'), '', ['#3B5323','#589C14'], chart.cat_pesticide, 'percent', 0, true);
plot(chart.chart_pesticide_storage,'chart_pesticide_storage', lang('Pesticide Storage Before and After Usage'),'1',lang('Jumlah'));
plot(chart.chart_pesticide_handling,'chart_pesticide_handling', lang('Empty Pesticides Container Handling'),'1',lang('Jumlah'));
plot(chart.chart_herbicide_use,'chart_herbicide_use', lang('Herbicide Use'),'1',lang('Jumlah'));
column_one(chart.chart_herbicide_user, 'chart_herbicide_user', lang('Herbicide Users'), lang('Percent'), null, chart.cat_herbicide_user, 'normal',1,false,-45,null,'%');
plot(chart.chart_insecticide_use,'chart_insecticide_use', lang('Insecticide Use'),'1',lang('Jumlah'));
column_one(chart.chart_insecticide_user, 'chart_insecticide_user', lang('Insecticide Users'), lang('Percent'), null, chart.cat_insecticide_user, 'normal',1,false,-45,null,'%');
plot(chart.chart_fungicide_use,'chart_fungicide_use', lang('Fungicide Use'),'1',lang('Jumlah'));
column_one(chart.chart_fungicide_user, 'chart_fungicide_user', lang('Fungicide Users'), lang('Percent'), null, chart.cat_fungicide_user, 'normal',1,false,-45,null,'%');
plot(chart.chart_cultural_chemical,'chart_cultural_chemical', lang('Farmers Applying Cultural and Chemical Practices'),'1',lang('Jumlah'));
column(chart.chart_protective_equip, 'chart_protective_equip', lang('Farmers using Protective Equipment (%)'), '', ['#3B5323','#589C14'], chart.cat_agriinput, 'percent', 0, true);

