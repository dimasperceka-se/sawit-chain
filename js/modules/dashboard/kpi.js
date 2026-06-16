
$(function () {
    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: m_data,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(result) {
            $('#row-fluid').css('display', '');
            var farmer = 0, farmer_certified = 0, farmer_certified_target = 0, farmer_certified_male = 0, farmer_certified_male_target = 0, farmer_certified_female = 0, farmer_certified_female_target = 0, cpg = 0, cpg_target = 0, nursery_area = 0, nursery_area_target = 0, nutrition_area = 0, nutrition_area_target = 0, gap_basic = 0, gap_basic_target = 0, gap_basic_male = 0, gap_basic_male_target = 0, gap_basic_female = 0, gap_basic_female_target = 0, gap_advanced = 0, gap_advanced_target = 0, gap_advanced_male = 0, gap_advanced_male_target = 0, gap_advanced_female = 0, gap_advanced_female_target = 0, gnp = 0, gnp_target = 0, gnp_male = 0, gnp_male_target = 0, gnp_female = 0, gnp_female_target = 0, gfp = 0, gfp_target = 0, gfp_male = 0, gfp_male_target = 0, gfp_female = 0, gfp_female_target = 0, gep = 0, gep_target = 0, gep_male = 0, gep_male_target = 0, gep_female = 0, gep_female_target = 0, gsp = 0, gsp_target = 0, gsp_male = 0, gsp_male_target = 0, gsp_female = 0, gsp_female_target = 0, gbp = 0, gbp_target = 0, gbp_male = 0, gbp_male_target = 0, gbp_female = 0, gbp_female_target = 0, cst = 0, cst_target = 0, cst_male = 0, cst_male_target = 0, cst_female = 0, cst_female_target = 0, master = 0, master_target = 0;
            $.each(result.data, function(index, val) {
                farmer                              += parseFloat(val.farmer);
                farmer_certified                    += parseFloat(val.farmer_certified);
                farmer_certified_target             += parseFloat(val.farmer_certified_target);
                farmer_certified_male               += parseFloat(val.farmer_certified_male);
                farmer_certified_male_target        += parseFloat(val.farmer_certified_male_target);
                farmer_certified_female             += parseFloat(val.farmer_certified_female);
                farmer_certified_female_target      += parseFloat(val.farmer_certified_female_target);
                cpg                                 += parseFloat(val.cpg);
                cpg_target                          += parseFloat(val.cpg_target);
                gap_basic                           += parseFloat(val.gap_basic);
                gap_basic_target                    += parseFloat(val.gap_basic_target);
                gap_basic_male                      += parseFloat(val.gap_basic_male);
                gap_basic_male_target               += parseFloat(val.gap_basic_male_target);
                gap_basic_female                    += parseFloat(val.gap_basic_female);
                gap_basic_female_target             += parseFloat(val.gap_basic_female_target);
                gap_advanced                        += parseFloat(val.gap_adv);
                gap_advanced_target                 += parseFloat(val.gap_adv_target);
                gap_advanced_male                   += parseFloat(val.gap_adv_male);
                gap_advanced_male_target            += parseFloat(val.gap_adv_male_target);
                gap_advanced_female                 += parseFloat(val.gap_adv_female);
                gap_advanced_female_target          += parseFloat(val.gap_adv_female_target);
                gnp                                 += parseFloat(val.gnp);
                gnp_target                          += parseFloat(val.gnp_target);
                gnp_male                            += parseFloat(val.gnp_male);
                gnp_male_target                     += parseFloat(val.gnp_male_target);
                gnp_female                          += parseFloat(val.gnp_female);
                gnp_female_target                   += parseFloat(val.gnp_female_target);
                gfp                                 += parseFloat(val.gfp);
                gfp_target                          += parseFloat(val.gfp_target);
                gfp_male                            += parseFloat(val.gfp_male);
                gfp_male_target                     += parseFloat(val.gfp_male_target);
                gfp_female                          += parseFloat(val.gfp_female);
                gfp_female_target                   += parseFloat(val.gfp_female_target);
                gep                                 += parseFloat(val.gep);
                gep_target                          += parseFloat(val.gep_target);
                gep_male                            += parseFloat(val.gep_male);
                gep_male_target                     += parseFloat(val.gep_male_target);
                gep_female                          += parseFloat(val.gep_female);
                gep_female_target                   += parseFloat(val.gep_female_target);
                gsp                                 += parseFloat(val.gsp);
                gsp_target                          += parseFloat(val.gsp_target);
                gsp_male                            += parseFloat(val.gsp_male);
                gsp_male_target                     += parseFloat(val.gsp_male_target);
                gsp_female                          += parseFloat(val.gsp_female);
                gsp_female_target                   += parseFloat(val.gsp_female_target);
                gbp                                 += parseFloat(val.gbp);
                gbp_target                          += parseFloat(val.gbp_target);
                gbp_male                            += parseFloat(val.gbp_male);
                gbp_male_target                     += parseFloat(val.gbp_male_target);
                gbp_female                          += parseFloat(val.gbp_female);
                gbp_female_target                   += parseFloat(val.gbp_female_target);
            });
            $.each(result.area, function(index, val) {
                nursery_area                        += parseFloat(val.nursery_area);
                nursery_area_target                 += parseFloat(val.nursery_area_target);
                nutrition_area                        += parseFloat(val.nutrition_area);
                nutrition_area_target                 += parseFloat(val.nutrition_area_target);
            })
            $.each(result.training_master, function(index, val) {
                master                 += parseFloat(val.all);
                master_target          += parseFloat(val.all_target);
                cst                 += parseFloat(val.cst);
                cst_target          += parseFloat(val.cst_target);
                cst_male            += parseFloat(val.cst_male);
                cst_male_target     += parseFloat(val.cst_male_target);
                cst_female          += parseFloat(val.cst_female);
                cst_female_target   += parseFloat(val.cst_female_target);
            })
            $('#box_farmer').text(number_format(farmer,0,'.',','));
            $('#box_farmer_certified').text(number_format(farmer_certified,0,'.',','));
            $('#box_cpg').text(number_format(cpg,0,'.',','));
            $('#box_nursery_area').text(number_format(nursery_area,0,'.',','));
            $('#box_nutrition_area').text(number_format(nutrition_area,0,'.',','));
            $('#box_gap_basic').text(number_format(gap_basic,0,'.',','));
            $('#box_gap_advanced').text(number_format(gap_advanced,0,'.',','));
            $('#box_gnp').text(number_format(gnp,0,'.',','));
            $('#box_gfp').text(number_format(gfp,0,'.',','));
            $('#box_gep').text(number_format(gep,0,'.',','));
            $('#box_gsp').text(number_format(gsp,0,'.',','));
            $('#box_gbp').text(number_format(gbp,0,'.',','));
            $('#box_cst').text(number_format(cst,0,'.',','));

            $('#wrapper').removeClass('cover');
            gauge_single('chart_gap_basic', lang('GAP Basic'), [{max: gap_basic_target, data: gap_basic, name: lang('GAP Basic')}]);            
            gauge_double('chart_gap_basic_gender', lang('GAP Basic by Gender'), [
                {max: gap_basic_male_target, data: gap_basic_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gap_basic_female_target, data: gap_basic_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gap_advanced', lang('GAP Advanced'), [{max: gap_advanced_target, data: gap_advanced, name: lang('GAP Advanced')}]);
            gauge_double('chart_gap_advanced_gender', lang('GAP Advanced by Gender'), [
                {max: gap_advanced_male_target, data: gap_advanced_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gap_advanced_female_target, data: gap_advanced_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gnp', lang('GNP'), [{max: gnp_target, data: gnp, name: lang('GNP')}]);
            gauge_double('chart_gnp_gender', lang('GNP by Gender'), [
                {max: gnp_male_target, data: gnp_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gnp_female_target, data: gnp_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gfp', lang('GFP'), [{max: gfp_target, data: gfp, name: lang('GFP')}]);
            gauge_double('chart_gfp_gender', lang('GFP by Gender'), [
                {max: gfp_male_target, data: gfp_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gfp_female_target, data: gfp_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gep', lang('GEP'), [{max: gep_target, data: gep, name: lang('GEP')}]);
            gauge_double('chart_gep_gender', lang('GEP by Gender'), [
                {max: gep_male_target, data: gep_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gep_female_target, data: gep_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gsp', lang('GSP'), [{max: gsp_target, data: gsp, name: lang('GSP')}]);
            gauge_double('chart_gsp_gender', lang('GSP by Gender'), [
                {max: gsp_male_target, data: gsp_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gsp_female_target, data: gsp_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_gbp', lang('GBP'), [{max: gbp_target, data: gbp, name: lang('GBP')}]);
            gauge_double('chart_gbp_gender', lang('GBP by Gender'), [
                {max: gbp_male_target, data: gbp_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: gbp_female_target, data: gbp_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_cst', lang('CST Cocoa Sector Training'), [{max: cst_target, data: cst, name: lang('CST')}]);
            gauge_double('chart_cst_gender', lang('CST Cocoa Sector Training by Gender'), [
                {max: cst_male_target, data: cst_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: cst_female_target, data: cst_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_certified_farmer', lang('Certified/Traceable Farmer'), [{max: farmer_certified_target, data: farmer_certified, name: lang('Certified Farmer')}]);
            gauge_double('chart_certified_farmer_gender', lang('Certified/Traceable Farmer by Gender'), [
                {max: farmer_certified_male_target, data: farmer_certified_male, name: lang('male'), innerRadius: '82%', radius: '100%',},
                {max: farmer_certified_female_target, data: farmer_certified_female, name: lang('female'), innerRadius: '60%', radius: '78%'},
            ]);
            gauge_single('chart_cpg', lang('Farmer Group'), [{max: cpg_target, data: cpg, name: lang('Farmer Group')}]);
            gauge_single('chart_master', lang('Master Training'), [{max: master_target, data: master, name: lang('Master Training')}]);
            gauge_single('chart_nursery_area', lang('Nurseries Area (sqm)'), [{max: nursery_area_target, data: Math.round(nursery_area), name: lang('Nurseries Area')}]);
            gauge_single('chart_nutrition_area', lang('Nutrition Garden Area (sqm)'), [{max: nutrition_area_target, data: Math.round(nutrition_area), name: lang('Nutrition Area')}]);
        }
    });
});