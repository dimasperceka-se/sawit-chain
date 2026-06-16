/*
* @Author: nikolius
* @Date:   2018-01-08 16:49:59
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-10 14:08:07
*/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');
    //console.log(url);

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_ProvinceID,kab: m_DistrictID},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);
            $('#box_compost').html(number_format(r.dataDisplay.CompostKgPerHa,1,'.',','));
            $('#box_fertilizer').html(number_format(r.dataDisplay.FertKg,1,'.',','));
            $('#box_pesticide').html(number_format(r.dataDisplay.FarmerUsePes,1,'.',','));
            $('#box_organic_pesticide').html(number_format(r.dataDisplay.FarmerUseOrgPest,1,'.',','));
            $('#box_chemical_fertilizer').html(number_format(r.dataDisplay.FarmerUseChemicalFert,1,'.',','));
            $('#box_organic_fertilizer').html(number_format(r.dataDisplay.FarmerUseOrgFert,1,'.',','));
            $('#box_protective_equip').html(number_format(r.dataDisplay.FarmerUseProtectGear,1,'.',','));
            $('#box_handling_safe').html(number_format(r.dataDisplay.FarmerHandPestBotSafe,1,'.',','));
            $('#box_storing_safe').html(number_format(r.dataDisplay.FarmerStorePestSafe,1,'.',','));

            //Data Chart ================================================= (Begin)
            var dataChart = r.dataChart;
            var varLabelDaerah = [];
            var varBarCompPerHa = [];
            var varBarFertPerHa = [];

            var pie_compost_app = [];
            var pie_compost_app_CompAppPBA = 0;
            var pie_compost_app_CompAppPB = 0;
            var pie_compost_app_CompAppFromPB = 0;
            var pie_compost_app_CompAppManure = 0;

            var pie_fert_app_tree = [];
            var pie_fert_app_tree_FertAppTreeTBM = 0;
            var pie_fert_app_tree_FertAppTreeTM = 0;
            var pie_fert_app_tree_FertAppTreeTR = 0;

            var pie_fert_app = [];
            var pie_fert_app_FertAppUrea = 0;
            var pie_fert_app_FertAppSS = 0;
            var pie_fert_app_FertAppNPK = 0;
            var pie_fert_app_FertAppTSP = 0;
            var pie_fert_app_FertAppCU = 0;
            var pie_fert_app_FertAppKCL = 0;
            var pie_fert_app_FertAppNPKMuti = 0;
            var pie_fert_app_FertAppBorat = 0;
            var pie_fert_app_FertAppDolomite = 0;

            var varBarFertUserCategory = [];
            varBarFertUserCategory[0] = lang('Urea');
            varBarFertUserCategory[1] = lang('SS');
            varBarFertUserCategory[2] = lang('NPK');
            varBarFertUserCategory[3] = lang('TSP');
            varBarFertUserCategory[4] = lang('CU');
            varBarFertUserCategory[5] = lang('KCL');
            varBarFertUserCategory[6] = lang('NPK Mutiara');
            varBarFertUserCategory[7] = lang('Borat');
            varBarFertUserCategory[8] = lang('Dolomite/Lime');
            var varBarFertUser = [];
            varBarFertUser[0] = {};
            varBarFertUser[0].name = lang('Jumlah');
            varBarFertUser[0].data = [];
            varBarFertUser[0].data[0] = parseInt(r.dataDisplay.FertAppUrea);
            varBarFertUser[0].data[1] = parseInt(r.dataDisplay.FertAppSS);
            varBarFertUser[0].data[2] = parseInt(r.dataDisplay.FertAppNPK);
            varBarFertUser[0].data[3] = parseInt(r.dataDisplay.FertAppTSP);
            varBarFertUser[0].data[4] = parseInt(r.dataDisplay.FertAppCU);
            varBarFertUser[0].data[5] = parseInt(r.dataDisplay.FertAppKCL);
            varBarFertUser[0].data[6] = parseInt(r.dataDisplay.FertAppNPKMuti);
            varBarFertUser[0].data[7] = parseInt(r.dataDisplay.FertAppBorat);
            varBarFertUser[0].data[8] = parseInt(r.dataDisplay.FertAppDolomite);

            var pie_disease = [];
            var pie_disease_DisBlast = 0;
            var pie_disease_DisGeno = 0;
            var pie_disease_DisSteam = 0;
            var pie_disease_DisBud = 0;
            var pie_disease_DisSpear = 0;
            var pie_disease_DisYellow = 0;
            var pie_disease_DisAnt = 0;
            var pie_disease_DisCrown = 0;
            var pie_disease_DisViscular = 0;
            var pie_disease_DisBunch = 0;

            var BarDiseaseReportingCategory = [];
            BarDiseaseReportingCategory[0] = lang('Blast Disease');
            BarDiseaseReportingCategory[1] = lang('Basal Steam Rot / Genoderma');
            BarDiseaseReportingCategory[2] = lang('Upper Steam Rot');
            BarDiseaseReportingCategory[3] = lang('Bud Rot');
            BarDiseaseReportingCategory[4] = lang('Spear Rot');
            BarDiseaseReportingCategory[5] = lang('Patch Yellow');
            BarDiseaseReportingCategory[6] = lang('Anthracnose');
            BarDiseaseReportingCategory[7] = lang('Crown disease');
            BarDiseaseReportingCategory[8] = lang('Viscular Wilt');
            BarDiseaseReportingCategory[9] = lang('Bunch Rot');
            var BarDiseaseReporting = [];
            BarDiseaseReporting[0] = [];
            BarDiseaseReporting[0].name = lang('Oil Plam Plantations with Disease');
            BarDiseaseReporting[0].data = [];
            BarDiseaseReporting[1] = [];
            BarDiseaseReporting[1].name = lang('Oil Plam Plantations without Disease');
            BarDiseaseReporting[1].data = [];
            BarDiseaseReporting[0].data[0] = parseInt(r.dataDisplay.DisBlast);
            BarDiseaseReporting[1].data[0] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisBlast);
            BarDiseaseReporting[0].data[1] = parseInt(r.dataDisplay.DisGeno);
            BarDiseaseReporting[1].data[1] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisGeno);
            BarDiseaseReporting[0].data[2] = parseInt(r.dataDisplay.DisSteam);
            BarDiseaseReporting[1].data[2] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisSteam);
            BarDiseaseReporting[0].data[3] = parseInt(r.dataDisplay.DisBud);
            BarDiseaseReporting[1].data[3] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisBud);
            BarDiseaseReporting[0].data[4] = parseInt(r.dataDisplay.DisSpear);
            BarDiseaseReporting[1].data[4] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisSpear);
            BarDiseaseReporting[0].data[5] = parseInt(r.dataDisplay.DisYellow);
            BarDiseaseReporting[1].data[5] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisYellow);
            BarDiseaseReporting[0].data[6] = parseInt(r.dataDisplay.DisAnt);
            BarDiseaseReporting[1].data[6] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisAnt);
            BarDiseaseReporting[0].data[7] = parseInt(r.dataDisplay.DisCrown);
            BarDiseaseReporting[1].data[7] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisCrown);
            BarDiseaseReporting[0].data[8] = parseInt(r.dataDisplay.DisViscular);
            BarDiseaseReporting[1].data[8] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisViscular);
            BarDiseaseReporting[0].data[9] = parseInt(r.dataDisplay.DisBunch);
            BarDiseaseReporting[1].data[9] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.DisBunch);

            var pie_pest = [];
            var pie_pest_PestRats = 0;
            var pie_pest_PestOly = 0;
            var pie_pest_PestSatora = 0;
            var pie_pest_PestTira = 0;
            var pie_pest_PestRhino = 0;
            var pie_pest_PestElep = 0;
            var pie_pest_PestOrgUtan = 0;
            var pie_pest_PestLandak = 0;
            var pie_pest_PestBabi = 0;

            var BarPestReportingCategory = [];
            BarPestReportingCategory[0] = lang('Rats');
            BarPestReportingCategory[1] = lang('Olygonichus');
            BarPestReportingCategory[2] = lang('Satora Nitens');
            BarPestReportingCategory[3] = lang('Tirathaba Mundella');
            BarPestReportingCategory[4] = lang('Rinocheros Beetle');
            BarPestReportingCategory[5] = lang('Elephant');
            BarPestReportingCategory[6] = lang('Orang Utan');
            BarPestReportingCategory[7] = lang('Landak');
            BarPestReportingCategory[8] = lang('Babi');
            var BarPestReporting = [];
            BarPestReporting[0] = [];
            BarPestReporting[0].name = lang('Oil Plam Plantations with Pest');
            BarPestReporting[0].data = [];
            BarPestReporting[1] = [];
            BarPestReporting[1].name = lang('Oil Plam Plantations without Pest');
            BarPestReporting[1].data = [];
            BarPestReporting[0].data[0] = parseInt(r.dataDisplay.PestRats);
            BarPestReporting[1].data[0] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestRats);
            BarPestReporting[0].data[1] = parseInt(r.dataDisplay.PestOly);
            BarPestReporting[1].data[1] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestOly);
            BarPestReporting[0].data[2] = parseInt(r.dataDisplay.PestSatora);
            BarPestReporting[1].data[2] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestSatora);
            BarPestReporting[0].data[3] = parseInt(r.dataDisplay.PestTira);
            BarPestReporting[1].data[3] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestTira);
            BarPestReporting[0].data[4] = parseInt(r.dataDisplay.PestRhino);
            BarPestReporting[1].data[4] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestRhino);
            BarPestReporting[0].data[5] = parseInt(r.dataDisplay.PestElep);
            BarPestReporting[1].data[5] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestElep);
            BarPestReporting[0].data[6] = parseInt(r.dataDisplay.PestOrgUtan);
            BarPestReporting[1].data[6] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestOrgUtan);
            BarPestReporting[0].data[7] = parseInt(r.dataDisplay.PestLandak);
            BarPestReporting[1].data[7] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestLandak);
            BarPestReporting[0].data[8] = parseInt(r.dataDisplay.PestBabi);
            BarPestReporting[1].data[8] = parseInt(r.dataDisplay.TotalPlot) - parseInt(r.dataDisplay.PestBabi);

            var BarPestUseCategory = [];
            BarPestUseCategory[0] = lang('Herbicide');
            BarPestUseCategory[1] = lang('Insecticide');
            BarPestUseCategory[2] = lang('Fungicide');
            var BarPestUse = [];
            BarPestUse[0] = [];
            BarPestUse[0].name = lang('Yes');
            BarPestUse[0].data = [];
            BarPestUse[1] = [];
            BarPestUse[1].name = lang('No');
            BarPestUse[1].data = [];
            BarPestUse[0].data[0] = parseInt(r.dataDisplay.HerbiYes);
            BarPestUse[1].data[0] = parseInt(r.dataDisplay.HerbiNo);
            BarPestUse[0].data[1] = parseInt(r.dataDisplay.InsecYes);
            BarPestUse[1].data[1] = parseInt(r.dataDisplay.InsecNo);
            BarPestUse[0].data[2] = parseInt(r.dataDisplay.FungiYes);
            BarPestUse[1].data[2] = parseInt(r.dataDisplay.FungiNo);

            var pie_pest_usage = [];
            var pie_pest_usage_PestUsageInHh = 0;
            var pie_pest_usage_PestUsageSpecPlace = 0;
            var pie_pest_usage_PestUsageOutHouse = 0;
            var pie_pest_usage_PestUsageOutFarm = 0;
            var pie_pest_usage_PestUsageOther = 0;

            var pie_pest_pack = [];
            pie_pest_pack_PestPackRandom = 0;
            pie_pest_pack_PestPackSomeElse = 0;
            pie_pest_pack_PestPackBurry = 0;
            pie_pest_pack_PestPackBurn = 0;
            pie_pest_pack_PestPackRecycle = 0;
            pie_pest_pack_PestPackOther = 0;

            var pie_pest_herbi_use = [];
            pie_pest_herbi_use_HerbiYes = 0;
            pie_pest_herbi_use_HerbiNo = 0;

            var pie_pest_insec_use = [];
            pie_pest_insec_use_InsecYes = 0;
            pie_pest_insec_use_InsecNo = 0;

            var pie_pest_fungi_use = [];
            pie_pest_fungi_use_FungiYes = 0;
            pie_pest_fungi_use_FungiNo = 0;

            var BarProtectGear = [];
            BarProtectGear[0] = [];
            BarProtectGear[0].name = lang('Yes');
            BarProtectGear[0].data = [];
            BarProtectGear[1] = [];
            BarProtectGear[1].name = lang('No');
            BarProtectGear[1].data = [];

            var BarPestHerbiUserCategory = [];
            BarPestHerbiUserCategory[0] = lang('Paraquat Users');
            BarPestHerbiUserCategory[1] = lang('Glyphosate Users');
            BarPestHerbiUserCategory[2] = lang('Allowed Herbicide Users');
            var BarPestHerbiUser = [];
            BarPestHerbiUser[0] = {};
            BarPestHerbiUser[0].name = lang('Herbicide Users');
            BarPestHerbiUser[0].data = [];
            BarPestHerbiUser[0].data[0] = parseInt(r.dataDisplay.HerbiParaquat);
            BarPestHerbiUser[0].data[1] = parseInt(r.dataDisplay.HerbiGlyphosate);
            BarPestHerbiUser[0].data[2] = parseInt(r.dataDisplay.HerbiAllowed);

            var BarPestInsecUserCategory = [];
            BarPestInsecUserCategory[0] = lang('Banned Insecticide Use');
            BarPestInsecUserCategory[1] = lang('Watchlist Insecticide Use');
            BarPestInsecUserCategory[2] = lang('Allowed Insecticide Use');
            var BarPestInsecUser = [];
            BarPestInsecUser[0] = {};
            BarPestInsecUser[0].name = lang('Insecticide Users');
            BarPestInsecUser[0].data = [];
            BarPestInsecUser[0].data[0] = parseInt(r.dataDisplay.InsecBanned);
            BarPestInsecUser[0].data[1] = parseInt(r.dataDisplay.InsecWatchlist);
            BarPestInsecUser[0].data[2] = parseInt(r.dataDisplay.InsecAllowed);

            var BarPestFungiUserCategory = [];
            BarPestFungiUserCategory[0] = lang('Banned Fungicide Use');
            BarPestFungiUserCategory[1] = lang('Watchlist Fungicide Use');
            BarPestFungiUserCategory[2] = lang('Allowed Fungicide Use');
            var BarPestFungiUser = [];
            BarPestFungiUser[0] = {};
            BarPestFungiUser[0].name = lang('Fungicide Users');
            BarPestFungiUser[0].data = [];
            BarPestFungiUser[0].data[0] = parseInt(r.dataDisplay.FungiBanned);
            BarPestFungiUser[0].data[1] = parseInt(r.dataDisplay.FungiWatchlist);
            BarPestFungiUser[0].data[2] = parseInt(r.dataDisplay.FungiAllowed);

            $.each(dataChart, function(key, value) {
                varLabelDaerah[key] = [value.label];

                varBarCompPerHa[key] = [parseInt(value.CompostKgPerHa)];
                varBarFertPerHa[key] = [parseInt(value.FertKg)];

                pie_compost_app_CompAppPBA = pie_compost_app_CompAppPBA + parseInt(value.CompAppPBA);
                pie_compost_app_CompAppPB = pie_compost_app_CompAppPB + parseInt(value.CompAppPB);
                pie_compost_app_CompAppFromPB = pie_compost_app_CompAppFromPB + parseInt(value.CompAppFromPB);
                pie_compost_app_CompAppManure = pie_compost_app_CompAppManure + parseInt(value.CompAppManure);

                pie_fert_app_tree_FertAppTreeTBM = pie_fert_app_tree_FertAppTreeTBM + parseInt(value.FertAppTreeTBM);
                pie_fert_app_tree_FertAppTreeTM = pie_fert_app_tree_FertAppTreeTM + parseInt(value.FertAppTreeTM);
                pie_fert_app_tree_FertAppTreeTR = pie_fert_app_tree_FertAppTreeTR + parseInt(value.FertAppTreeTR);

                pie_fert_app_FertAppUrea = pie_fert_app_FertAppUrea + parseInt(value.FertAppUrea);
                pie_fert_app_FertAppSS = pie_fert_app_FertAppSS + parseInt(value.FertAppSS);
                pie_fert_app_FertAppNPK = pie_fert_app_FertAppNPK + parseInt(value.FertAppNPK);
                pie_fert_app_FertAppTSP = pie_fert_app_FertAppTSP + parseInt(value.FertAppTSP);
                pie_fert_app_FertAppCU = pie_fert_app_FertAppCU + parseInt(value.FertAppCU);
                pie_fert_app_FertAppKCL = pie_fert_app_FertAppKCL + parseInt(value.FertAppKCL);
                pie_fert_app_FertAppNPKMuti = pie_fert_app_FertAppNPKMuti + parseInt(value.FertAppNPKMuti);
                pie_fert_app_FertAppBorat = pie_fert_app_FertAppBorat + parseInt(value.FertAppBorat);
                pie_fert_app_FertAppDolomite = pie_fert_app_FertAppDolomite + parseInt(value.FertAppDolomite);

                pie_disease_DisBlast = pie_disease_DisBlast + parseInt(value.DisBlast);
                pie_disease_DisGeno = pie_disease_DisGeno + parseInt(value.DisGeno);
                pie_disease_DisSteam = pie_disease_DisSteam + parseInt(value.DisSteam);
                pie_disease_DisBud = pie_disease_DisBud + parseInt(value.DisBud);
                pie_disease_DisSpear = pie_disease_DisSpear + parseInt(value.DisSpear);
                pie_disease_DisYellow = pie_disease_DisYellow + parseInt(value.DisYellow);
                pie_disease_DisAnt = pie_disease_DisAnt + parseInt(value.DisAnt);
                pie_disease_DisCrown = pie_disease_DisCrown + parseInt(value.DisCrown);
                pie_disease_DisViscular = pie_disease_DisViscular + parseInt(value.DisViscular);
                pie_disease_DisBunch = pie_disease_DisBunch + parseInt(value.DisBunch);

                pie_pest_PestRats = pie_pest_PestRats + parseInt(value.PestRats);
                pie_pest_PestOly = pie_pest_PestOly + parseInt(value.PestOly);
                pie_pest_PestSatora = pie_pest_PestSatora + parseInt(value.PestSatora);
                pie_pest_PestTira = pie_pest_PestTira + parseInt(value.PestTira);
                pie_pest_PestRhino = pie_pest_PestRhino + parseInt(value.PestRhino);
                pie_pest_PestElep = pie_pest_PestElep + parseInt(value.PestElep);
                pie_pest_PestOrgUtan = pie_pest_PestOrgUtan + parseInt(value.PestOrgUtan);
                pie_pest_PestLandak = pie_pest_PestLandak + parseInt(value.PestLandak);
                pie_pest_PestBabi = pie_pest_PestBabi + parseInt(value.PestBabi);

                pie_pest_usage_PestUsageInHh = pie_pest_usage_PestUsageInHh + parseInt(value.PestUsageInHh);
                pie_pest_usage_PestUsageSpecPlace = pie_pest_usage_PestUsageSpecPlace + parseInt(value.PestUsageSpecPlace);
                pie_pest_usage_PestUsageOutHouse = pie_pest_usage_PestUsageOutHouse + parseInt(value.PestUsageOutHouse);
                pie_pest_usage_PestUsageOutFarm = pie_pest_usage_PestUsageOutFarm + parseInt(value.PestUsageOutFarm);
                pie_pest_usage_PestUsageOther = pie_pest_usage_PestUsageOther + parseInt(value.PestUsageOther);

                pie_pest_pack_PestPackRandom = pie_pest_pack_PestPackRandom + parseInt(value.PestPackRandom);
                pie_pest_pack_PestPackSomeElse = pie_pest_pack_PestPackSomeElse + parseInt(value.PestPackSomeElse);
                pie_pest_pack_PestPackBurry = pie_pest_pack_PestPackBurry + parseInt(value.PestPackBurry);
                pie_pest_pack_PestPackBurn = pie_pest_pack_PestPackBurn + parseInt(value.PestPackBurn);
                pie_pest_pack_PestPackRecycle = pie_pest_pack_PestPackRecycle + parseInt(value.PestPackRecycle);
                pie_pest_pack_PestPackOther = pie_pest_pack_PestPackOther + parseInt(value.PestPackOther);

                pie_pest_herbi_use_HerbiYes = pie_pest_herbi_use_HerbiYes + parseInt(value.HerbiYes);
                pie_pest_herbi_use_HerbiNo = pie_pest_herbi_use_HerbiNo + parseInt(value.HerbiNo);

                pie_pest_insec_use_InsecYes = pie_pest_insec_use_InsecYes + parseInt(value.InsecYes);
                pie_pest_insec_use_InsecNo = pie_pest_insec_use_InsecNo + parseInt(value.InsecNo);

                pie_pest_fungi_use_FungiYes = pie_pest_fungi_use_FungiYes + parseInt(value.FungiYes);
                pie_pest_fungi_use_FungiNo = pie_pest_fungi_use_FungiNo + parseInt(value.FungiNo);

                BarProtectGear[0].data[key] = parseInt(value.FarmerUseProtectGearYes);
                BarProtectGear[1].data[key] = parseInt(value.FarmerUseProtectGearNo);
            });

            arrReturn.varLabelDaerah = varLabelDaerah;
            arrReturn.varBarCompPerHa = varBarCompPerHa;
            arrReturn.varBarFertPerHa = varBarFertPerHa;

            arrReturn.pie_compost_app = [
                [lang('Palm Bunch Ash'), pie_compost_app_CompAppPBA],
                [lang('Palm Bunch'), pie_compost_app_CompAppPB],
                [lang('Compost from Palm Bunch'), pie_compost_app_CompAppFromPB],
                [lang('Manure'), pie_compost_app_CompAppManure]
            ];

            arrReturn.pie_fert_app_tree = [
                [lang('TBM - Plants yet to produce'), pie_fert_app_tree_FertAppTreeTBM],
                [lang('TM - Producing plants'), pie_fert_app_tree_FertAppTreeTM],
                [lang('TR - Old/diseased'), pie_fert_app_tree_FertAppTreeTR]
            ];

            arrReturn.pie_fert_app = [
                [lang('Urea'), pie_fert_app_FertAppUrea],
                [lang('SS'), pie_fert_app_FertAppSS],
                [lang('NPK'), pie_fert_app_FertAppNPK],
                [lang('TSP'), pie_fert_app_FertAppTSP],
                [lang('CU'), pie_fert_app_FertAppCU],
                [lang('KCL'), pie_fert_app_FertAppKCL],
                [lang('NPK Mutiara'), pie_fert_app_FertAppNPKMuti],
                [lang('Borat'), pie_fert_app_FertAppBorat],
                [lang('Dolomite/Lime'), pie_fert_app_FertAppDolomite]
            ];

            arrReturn.varBarFertUser = varBarFertUser;
            arrReturn.varBarFertUserCategory = varBarFertUserCategory;

            arrReturn.pie_disease = [
                [lang('Blast Disease'), pie_disease_DisBlast],
                [lang('Basal Steam Rot / Genoderma'), pie_disease_DisGeno],
                [lang('Upper Steam Rot'), pie_disease_DisSteam],
                [lang('Bud Rot'), pie_disease_DisBud],
                [lang('Spear Rot'), pie_disease_DisSpear],
                [lang('Patch Yellow'), pie_disease_DisYellow],
                [lang('Anthracnose'), pie_disease_DisAnt],
                [lang('Crown disease'), pie_disease_DisCrown],
                [lang('Viscular Wilt'), pie_disease_DisViscular],
                [lang('Bunch Rot'), pie_disease_DisBunch]
            ];

            arrReturn.BarDiseaseReporting = BarDiseaseReporting;
            arrReturn.BarDiseaseReportingCategory = BarDiseaseReportingCategory;

            arrReturn.pie_pest = [
                [lang('Rats'), pie_pest_PestRats],
                [lang('Olygonichus'), pie_pest_PestOly],
                [lang('Satora Nitens'), pie_pest_PestSatora],
                [lang('Tirathaba Mundella'), pie_pest_PestTira],
                [lang('Rinocheros Beetle'), pie_pest_PestRhino],
                [lang('Elephant'), pie_pest_PestElep],
                [lang('Orang Utan'), pie_pest_PestOrgUtan],
                [lang('Landak'), pie_pest_PestLandak],
                [lang('Babi'), pie_pest_PestBabi]
            ];

            arrReturn.BarPestReportingCategory = BarPestReportingCategory;
            arrReturn.BarPestReporting = BarPestReporting;

            arrReturn.BarPestUseCategory = BarPestUseCategory;
            arrReturn.BarPestUse = BarPestUse;

            arrReturn.pie_pest_usage = [
                [lang('In the house'), pie_pest_usage_PestUsageInHh],
                [lang('Pesticide specific place'), pie_pest_usage_PestUsageSpecPlace],
                [lang('Outside of the house (house area)'), pie_pest_usage_PestUsageOutHouse],
                [lang('Outside of the cocoa farm'), pie_pest_usage_PestUsageOutFarm],
                [lang('Others'), pie_pest_usage_PestUsageOther]
            ];

            arrReturn.pie_pest_pack = [
                [lang('Random disposal (Garden or around the house)'), pie_pest_pack_PestPackRandom],
                [lang('Use for something else'), pie_pest_pack_PestPackSomeElse],
                [lang('Thoroughly and then burry it'), pie_pest_pack_PestPackBurry],
                [lang('Burn'), pie_pest_pack_PestPackBurn],
                [lang('Recycle'), pie_pest_pack_PestPackRecycle],
                [lang('Others'), pie_pest_pack_PestPackOther]
            ];

            arrReturn.pie_pest_herbi_use = [
                [lang('Herbicide User'), pie_pest_herbi_use_HerbiYes],
                [lang('Non Herbicide User'), pie_pest_herbi_use_HerbiNo]
            ];

            arrReturn.pie_pest_insec_use = [
                [lang('Insecticide User'), pie_pest_insec_use_InsecYes],
                [lang('Non Insecticide User'), pie_pest_insec_use_InsecNo]
            ];

            arrReturn.pie_pest_fungi_use = [
                [lang('Fungicide User'), pie_pest_fungi_use_FungiYes],
                [lang('Non Fungicide User'), pie_pest_fungi_use_FungiNo]
            ];

            arrReturn.BarProtectGear = BarProtectGear;

            arrReturn.BarPestHerbiUserCategory = BarPestHerbiUserCategory;
            arrReturn.BarPestHerbiUser = BarPestHerbiUser;

            arrReturn.BarPestInsecUserCategory = BarPestInsecUserCategory;
            arrReturn.BarPestInsecUser = BarPestInsecUser;

            arrReturn.BarPestFungiUserCategory = BarPestFungiUserCategory;
            arrReturn.BarPestFungiUser = BarPestFungiUser;

            //Data Chart ================================================= (End)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //console.log(arrReturn);
    return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);

//========================================== Build Chart ====================================================//

column(
    [{name: lang('Organic Fertilizer'),data: arrReturn.varBarCompPerHa}],
    'bar_compost_per_hectare',
    lang('Organic Fertilizer per Hectare'),
    'Kg/Ha', ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 0, false
);

plot(arrReturn.pie_compost_app,'pie_compost_app', lang('Organic Fertilizer Application by Farms'),'1',lang('Jumlah'));

column(
    [{name: lang('Non Organic Fertilizer'),data: arrReturn.varBarFertPerHa}],
    'bar_fert_per_hectare',
    lang('Non Organic Fertilizer per Hectare'),
    'Kg/Ha', ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 0, false
);

plot(arrReturn.pie_fert_app_tree,'pie_fert_app_tree', lang('Non Organic Fertilizer Application by Palm Oil Trees Category'),'1',lang('Jumlah'));

plot(arrReturn.pie_fert_app,'pie_fert_app', lang('Non Organic Fertilizer Application by Farms'),'1',lang('Jumlah'));

column_one(arrReturn.varBarFertUser, 'bar_fert_user', lang('Non Organic Fertilizer Users'), lang('Jumlah'), null, arrReturn.varBarFertUserCategory, 'normal',0,false,-45,null);

plot(arrReturn.pie_disease,'pie_disease', lang('Diseases Monitored in Oil Palm Plantations'),'1',lang('Jumlah'));

column(arrReturn.BarDiseaseReporting, 'bar_disease_reporting', lang('Disease Reporting'), '', null, arrReturn.BarDiseaseReportingCategory, 'percent', 0, true);

plot(arrReturn.pie_pest,'pie_pest', lang('Pest Monitored in Oil Palm Plantations'),'1',lang('Jumlah'));

column(arrReturn.BarPestReporting, 'bar_pest_reporting', lang('Pesticide Reporting'), '', null, arrReturn.BarPestReportingCategory, 'percent', 0, true);

column(arrReturn.BarPestUse, 'bar_pest_use', lang('Pesticide Use in Oil Palm Plantations'), '', null, arrReturn.BarPestUseCategory, 'percent', 0, true);

plot(arrReturn.pie_pest_usage,'pie_pest_usage', lang('Pesticide Storage Before and After Usage'),'1',lang('Jumlah'));

plot(arrReturn.pie_pest_pack,'pie_pest_pack', lang('Empty Pesticides Container Handling'),'1',lang('Jumlah'));

plot(arrReturn.pie_pest_herbi_use,'pie_pest_herbi_use', lang('Herbicide Use'),'1',lang('Jumlah'));

plot(arrReturn.pie_pest_insec_use,'pie_pest_insec_use', lang('Insecticide Use'),'1',lang('Jumlah'));

plot(arrReturn.pie_pest_fungi_use,'pie_pest_fungi_use', lang('Fungicide Use'),'1',lang('Jumlah'));

column(arrReturn.BarProtectGear, 'bar_protect_gear', lang('Farmer Using Protective Equipment'), '', null, arrReturn.varLabelDaerah, 'percent', 0, true);

column_one(arrReturn.BarPestHerbiUser, 'bar_pest_herbi_user', lang('Herbicide Users'), lang('Percent'), null, arrReturn.BarPestHerbiUserCategory, 'normal',1,false,-45,null,'%');

column_one(arrReturn.BarPestInsecUser, 'bar_pest_insec_user', lang('Insecticide Users'), lang('Percent'), null, arrReturn.BarPestInsecUserCategory, 'normal',1,false,-45,null,'%');

column_one(arrReturn.BarPestFungiUser, 'bar_pest_fungi_user', lang('Fungicide Users'), lang('Percent'), null, arrReturn.BarPestFungiUserCategory, 'normal',1,false,-45,null,'%');