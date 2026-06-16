//load combo propinsi
$.ajax({
    type: "GET",
    url: m_api+'/common/combo_propinsi_access',
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    async: false,
    success: function (data) {
        if (data.length > 0) {
            $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All Province')+'</option>');
            $.each(data, function(index, val) {
                $('#fprovince').append('<option value="'+val.id+'">'+val.label+'</option>');
            });
        }
    }
});

$(document).on('change', '#fprovince', function(e) {
    //load district
    $.ajax({
        type: "GET",
        url: m_api+'/common/combo_district_access',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {ProvinceID: e.target.value},
        success: function (data) {
            if (data.length > 0) {
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
                $.each(data, function(index, val) {
                    $('#fdistrict').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            } else {
                //tidak ada datanya
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
            }
        },
        error: function(data) {
            //tidak ada datanya
            $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
        }
    });
});

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    let ftype = $("#ftype").val();
    let fprovince = $("#fprovince").val();
    let fdistrict = $("#fdistrict").val();

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: fprovince,kab: fdistrict,ftype: ftype},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            //hitung total plantation with other cert
            var total_farmer_land_owner_percentage = 0;
            var total_land_owner_data = parseInt(r.dataDisplay.plantation_land_document_nodoc) + parseInt(r.dataDisplay.plantation_land_document_skt) + parseInt(r.dataDisplay.plantation_land_document_shm) + parseInt(r.dataDisplay.plantation_land_document_hgu) + parseInt(r.dataDisplay.plantation_land_document_skgr) + parseInt(r.dataDisplay.plantation_land_document_other);
            var total_land_owner = parseInt(r.dataDisplay.plantation_land_document_shm) + parseInt(r.dataDisplay.plantation_land_document_hgu);
            total_farmer_land_owner_percentage = (total_land_owner / total_land_owner_data) * 100;
            //console.log(total_land_owner);
            //console.log(total_land_owner_data);

            //data display
            $('#box_garden').html(number_format(r.dataDisplay.garden_total,0,'.',','));
            $('#box_hectare').html(number_format(r.dataDisplay.garden_ha,0,'.',','));
            $('#box_ave_garden_ha').html(number_format(r.dataDisplay.ave_garden_ha,1,'.',','));

            $('#box_ave_count_plantation_by_farmer').html(number_format(r.dataDisplay.ave_count_plantation_by_farmer,1,'.',','));
            $('#box_ave_farm_yield').html(number_format(r.dataDisplay.ave_plantation_productivity,1,'.',','));

            $('#box_oil_palm_trees').html(number_format(r.dataDisplay.total_tree,0,'.',','))
            $('#box_oil_palm_trees_per_hectare').html(number_format(r.dataDisplay.tree_per_hectare,0,'.',','));

            $('#box_ave_age_palmoil_plantation').html(number_format(r.dataDisplay.ave_year_planting,0,'.',','));
            $('#box_smallholder_farmers').html(number_format(r.dataDisplay.total_farmer,0,'.',','));
            $('#box_smallholder_farmers_land_owners').html(number_format(total_farmer_land_owner_percentage,0,'.',','));

            $('#box_oil_palm_production').html(number_format(r.dataDisplay.calcprod_total_production,0,'.',','));
            $('#box_oil_palm_tree_yield').html(number_format(r.dataDisplay.ave_tree_productivity,0,'.',','));

            //data chart ======================================================================== (begin)
            //console.log(r.dataChart);
            var dataChart = r.dataChart;
            var varLabelDaerah = [];
            var varPieTotalPlantation = [];
            var varPieTotalHa = [];
            var varPieTotalProduction = [];
            var varBarTotalProductivity = [];
            var varBarPlantationAge = [];

            var treeComposition = [];
            var totalTreeTBM = 0;
            var totalTreeTM = 0;
            var totalTreeTR = 0;

            var varBarTreePerHectare = [];
            var varBarTreeProductivity = [];

            var sizeClassifications = [];
            var size2Ha = 0;
            var size2Ha5Ha = 0;
            var size5Ha = 0;

            var sizeDetClassifications = [];
            var sizeDet1Ha = 0;
            var sizeDet1Ha2ha = 0;
            var sizeDet2Ha3halfHa = 0;
            var sizeDet3halfHa5Ha = 0;
            var sizeDet5Ha = 0;

            var productivityCategories = [];
            var productivityBelow6 = 0;
            var productivityBetween6_15 = 0;
            var productivityBetween16_25 = 0;
            var productivityBetween26_35 = 0;
            var productivityAbove35 = 0;

            var managementClassifications = [];
            var productivity15 = 0;
            var productivity1525 = 0;
            var productivity25 = 0;

            var plantationOwnership = [];
            var pOwnershipOwned = 0;
            var pOwnershipRented = 0;
            var pOwnershipPsharing = 0;
            var pOwnershipOther = 0;

            var plantationDocument = [];
            var pDocumentNodoc = 0;
            var pDocumentSkt = 0;
            var pDocumentShm = 0;
            var pDocumentHgu = 0;
            var pDocumentSkgr = 0;
            var pDocumentOther = 0;

            var plantationOwner = [];
            var pOwnerRegisfarmer = 0;
            var pOwnerFammember = 0;
            var pOwnerOtherpeople = 0;
            var pOwnerDonotknow = 0;

            var aveTreeAge = [];
            var aveTreeAge1_3 = 0;
            var aveTreeAge4_6 = 0;
            var aveTreeAge7_18 = 0;
            var aveTreeAge19 = 0;

            $.each(dataChart, function(key, value) {
                varLabelDaerah[key] = [value.label];

                //pie_total_plantation
                varPieTotalPlantation[key] = [value.label, parseFloat(value.garden_total)];

                //pie_total_ha
                varPieTotalHa[key] = [value.label, parseFloat(value.garden_ha)];

                //pie_total_production
                varPieTotalProduction[key] = [value.label, parseFloat(value.total_production)];

                //bar_total_productivity
                varBarTotalProductivity[key] = [parseFloat(value.ave_plantation_productivity)];

                //bar_plantation_age
                varBarPlantationAge[key] = [parseFloat(value.ave_plantation_age)];

                //pie_plantation_composition
                totalTreeTBM = totalTreeTBM + parseInt(value.total_tree_tbm);
                totalTreeTM = totalTreeTM + parseInt(value.total_tree_tm);
                totalTreeTR = totalTreeTR + parseInt(value.total_tree_tr);

                //bar_tree_per_hectare
                varBarTreePerHectare[key]  = [parseFloat(value.tree_per_hectare)];

                //bar_tree_productivity
                varBarTreeProductivity[key]  = [parseFloat(value.ave_tree_productivity)];

                //pie_total_productivity_categories
                productivityBelow6 = productivityBelow6 + parseInt(value.productivity_below_6);
                productivityBetween6_15 = productivityBetween6_15 + parseInt(value.productivity_between_6_15);
                productivityBetween16_25 = productivityBetween16_25 + parseInt(value.productivity_between_16_25);
                productivityBetween26_35 = productivityBetween26_35 + parseInt(value.productivity_between_26_35);
                productivityAbove35 = productivityAbove35 + parseInt(value.productivity_above_35);

                //pie_plantation_size_classifications
                size2Ha = size2Ha + parseInt(value.plantation_less_2ha);
                size2Ha5Ha = size2Ha5Ha + parseInt(value.plantation_2ha_5ha);
                size5Ha = size5Ha + parseInt(value.plantation_more_5ha);

                //pie_plantation_size_detail_classifications
                sizeDet1Ha = sizeDet1Ha + parseInt(value.plantation_det_below_1);
                sizeDet1Ha2ha = sizeDet1Ha2ha + parseInt(value.plantation_det_between_1_2);
                sizeDet2Ha3halfHa = sizeDet2Ha3halfHa + parseInt(value.plantation_det_between_2_3half);
                sizeDet3halfHa5Ha = sizeDet3halfHa5Ha + parseInt(value.plantation_det_between_3half_5);
                sizeDet5Ha = sizeDet5Ha + parseInt(value.plantation_det_above_5);

                //pie_plantation_management_classifications
                productivity15 = productivity15 + parseInt(value.plantation_unprofessional);
                productivity1525 = productivity1525 + parseInt(value.plantation_progressing);
                productivity25 = productivity25 + parseInt(value.plantation_professional);

                //pie_plantation_land_ownership
                pOwnershipOwned = pOwnershipOwned + parseInt(value.plantation_land_ownership_owned);
                pOwnershipRented = pOwnershipRented + parseInt(value.plantation_land_ownership_rented);
                pOwnershipPsharing = pOwnershipPsharing + parseInt(value.plantation_land_ownership_psharing);
                pOwnershipOther = pOwnershipOther + parseInt(value.plantation_land_ownership_other);

                //pie_plantation_land_document
                pDocumentNodoc = pDocumentNodoc + parseInt(value.plantation_land_document_nodoc);
                pDocumentSkt = pDocumentSkt + parseInt(value.plantation_land_document_skt);
                pDocumentShm = pDocumentShm + parseInt(value.plantation_land_document_shm);
                pDocumentHgu = pDocumentHgu + parseInt(value.plantation_land_document_hgu);
                pDocumentSkgr = pDocumentSkgr + parseInt(value.plantation_land_document_skgr);
                pDocumentOther = pDocumentOther + parseInt(value.plantation_land_document_other);

                //pie_plantation_owner
                pOwnerRegisfarmer = pOwnerRegisfarmer + parseInt(value.plantation_owner_regisfarmer);
                pOwnerFammember = pOwnerFammember + parseInt(value.plantation_owner_fammember);
                pOwnerOtherpeople = pOwnerOtherpeople + parseInt(value.plantation_owner_otherpeople);
                pOwnerDonotknow = pOwnerDonotknow + parseInt(value.plantation_owner_donotknow);

                //pie_ave_tree_age
                aveTreeAge1_3 = aveTreeAge1_3 + parseInt(value.tree_age_1_3);
                aveTreeAge4_6 = aveTreeAge4_6 + parseInt(value.tree_age_4_6);
                aveTreeAge7_18 = aveTreeAge7_18 + parseInt(value.tree_age_7_18);
                aveTreeAge19 = aveTreeAge19 + parseInt(value.tree_age_19);
            });

            arrReturn.varLabelDaerah = varLabelDaerah;
            arrReturn.varPieTotalPlantation = varPieTotalPlantation;
            arrReturn.varPieTotalHa = varPieTotalHa;
            arrReturn.varPieTotalProduction = varPieTotalProduction;
            arrReturn.varBarTotalProductivity = varBarTotalProductivity;
            arrReturn.varBarPlantationAge = varBarPlantationAge;
            arrReturn.varBarTreePerHectare = varBarTreePerHectare;
            arrReturn.varBarTreeProductivity = varBarTreeProductivity;

            //pie_plantation_composition
            treeComposition = [
                [lang('TBM - Plants yet to produce'), totalTreeTBM],
                [lang('TM - Producing plants'), totalTreeTM],
                [lang('TR - Old/diseased'), totalTreeTR]
            ];
            arrReturn.treeComposition = treeComposition;

            //pie_total_productivity_categories
            arrReturn.productivityCategories = [
                [lang('Less than 6'), productivityBelow6],
                [lang('Between 6 to 15'), productivityBetween6_15],
                [lang('Between 16 to 25'), productivityBetween16_25],
                [lang('Between 26 to 35'), productivityBetween26_35],
                [lang('More than 35'), productivityAbove35]
            ];

            //pie_plantation_size_classifications
            arrReturn.sizeClassifications = [
                [lang('Small (Less than 2 ha)'), size2Ha],
                [lang('Medium (Between 2 to 5 ha)'), size2Ha5Ha],
                [lang('Large (More than 5 ha)'), size5Ha]
            ];

            //pie_plantation_size_detail_classifications
            arrReturn.sizeDetClassifications = [
                [lang('Less than 1 ha'), sizeDet1Ha],
                [lang('> 1 - 2 ha'), sizeDet1Ha2ha],
                [lang('> 2 - 3.5 ha'), sizeDet2Ha3halfHa],
                [lang('> 3.5 - 5 ha'), sizeDet3halfHa5Ha],
                [lang('More than 5 ha'), sizeDet5Ha]
            ];

            //pie_plantation_management_classifications
            arrReturn.managementClassifications = [
                [lang('Unprofessional (less than 15 Mt/Ha/Year)'), productivity15],
                [lang('Progressing (between 15 to 25 Mt/Ha/Year)'), productivity1525],
                [lang('Professional (more than 25 Mt/Ha/Year)'), productivity25]
            ];

            //pie_plantation_land_ownership
            arrReturn.plantationOwnership = [
                [lang('Owned'), pOwnershipOwned],
                [lang('Rented'), pOwnershipRented],
                [lang('Profit Sharing'), pOwnershipPsharing],
                [lang('Others'), pOwnershipOther]
            ];

            //pie_plantation_land_document
            arrReturn.plantationDocument = [
                [lang('No Document'), pDocumentNodoc],
                [lang('SKT (Surat Keterangan Tanah)'), pDocumentSkt],
                [lang('SHM (Sertifikat Hak Milik) / Certificate'), pDocumentShm],
                [lang('HGU (Hak Guna Usaha)'), pDocumentHgu],
                [lang('SKGR (Surat Keterangan Ganti Rugi)'), pDocumentSkgr],
                [lang('Other'), pDocumentOther]
            ];

            //pie_plantation_owner
            arrReturn.plantationOwner = [
                [lang('Registered Farmer'), pOwnerRegisfarmer],
                [lang('Family Members'), pOwnerFammember],
                [lang('Other People'), pOwnerOtherpeople],
                [lang('Do Not Know'), pOwnerDonotknow]
            ];

            //pie_ave_tree_age
            arrReturn.aveTreeAge = [
                [lang('Seedling (1-3)'), aveTreeAge1_3],
                [lang('Young (4-6)'), aveTreeAge4_6],
                [lang('Prime (7-18)'), aveTreeAge7_18],
                [lang('Old (above 19)'), aveTreeAge19]
            ];

            plot([
                [lang('Inheritance'), parseInt(r.dataDisplay.obtain_plantation_inheritance)],
                [lang('Purchased'), parseInt(r.dataDisplay.obtain_plantation_purchased)],
                [lang('Convert Existing Plantation'), parseInt(r.dataDisplay.obtain_plantation_convert)],
                [lang('Received from Government'), parseInt(r.dataDisplay.obtain_plantation_government)],
                [lang('Others'), parseInt(r.dataDisplay.obtain_plantation_others)],
            ],'pie_obtain_plantation', lang('Obtain Plantaion'),'0',lang('Total'));

            plot([
                [lang('Flat'), parseInt(r.dataDisplay.topography_flat)],
                [lang('Hilly'), parseInt(r.dataDisplay.topography_hilly)],
                [lang('Mountainous'), parseInt(r.dataDisplay.topography_mountainous)],
            ],'pie_topography', lang('Topography'),'0',lang('Total'));
            //data chart ======================================================================== (end)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //console.log(arrReturn);
    //return arrReturn;

    //pie_plantation_size_classifications
    plot(arrReturn.sizeClassifications,'pie_plantation_size_classifications', lang('Plantation Size Classifications'),'1',lang('Total Plantations'));
    //pie_plantation_size_detail_classifications
    plot(arrReturn.sizeDetClassifications,'pie_plantation_size_detail_classifications', lang('Plantation Size Detail Classifications'),'1',lang('Total Plantations'));

    //pie_plantation_management_classifications
    plot(arrReturn.managementClassifications,'pie_plantation_management_classifications', lang('Average Oil Palm Tree Yield'),'1',lang('Jumlah'));

    //pie chart
    plot(arrReturn.varPieTotalPlantation,'pie_total_plantation', lang('Oil Palm Plantations'),'2', lang('Jumlah'));
    plot(arrReturn.varPieTotalHa,'pie_total_ha', lang('Oil Palm Plantation Area (Ha)'),'2', lang('Total Hectare'));
    plot(arrReturn.varPieTotalProduction,'pie_total_production', lang('Annual FFB Production (Ton/Year)'),'2', lang('Jumlah'));
    plot(arrReturn.productivityCategories,'pie_total_productivity_categories', lang('Oil Palm Plantation Yields (Mt/Ha/Year)'),'1',lang('Jumlah'));

    //bar chart
    column(
        [{name: lang('Average'),data: arrReturn.varBarTotalProductivity}],
        'bar_total_productivity',
        lang('Average Oil Palm Plantation Yield'),
        'Mt/Ha/Year', ['#95130b'],
        arrReturn.varLabelDaerah, 'normal', 1
    );


    //pie_plantation_composition
    plot(arrReturn.treeComposition,'pie_plantation_composition', lang('Oil Palm Plantation Composition'),'1',lang('Jumlah'));

    //bar_tree_per_hectare
    column(
        [{name: lang('trees/ha'),data: arrReturn.varBarTreePerHectare}],
        'bar_tree_per_hectare',
        lang('Average Number of Oil Palm Trees per Hectare'),
        lang('trees/ha'), ['#95130b'],
        arrReturn.varLabelDaerah, 'normal', 1
    );

    //bar_tree_productivity
    column(
        [{name: lang('Kg/Tree/Year'),data: arrReturn.varBarTreeProductivity}],
        'bar_tree_productivity',
        lang('Average Oil Palm Tree Yield'),
        lang('Kg/Tree/Year'), ['#95130b'],
        arrReturn.varLabelDaerah, 'normal', 1
    );

    //bar_plantation_age
    column(
        [{name: lang('Year'),data: arrReturn.varBarPlantationAge}],
        'bar_plantation_age',
        lang('Average Plantation Age'),
        lang('Year'), ['#95130b'],
        arrReturn.varLabelDaerah, 'normal', 1
    );

    //pie_plantation_land_ownership
    plot(arrReturn.plantationOwnership,'pie_plantation_land_ownership', lang('Plantation Land Ownership'),'1',lang('Jumlah'));

    //pie_plantation_land_document
    plot(arrReturn.plantationDocument,'pie_plantation_land_document', lang('Land Documentation'),'1',lang('Jumlah'));

    //pie_plantation_owner
    plot(arrReturn.plantationOwner,'pie_plantation_owner', lang('Plantation Land Owner'),'1',lang('Jumlah'));

    //pie_ave_tree_age
    plot(arrReturn.aveTreeAge,'pie_ave_tree_age', lang('Average Oil Palm Plantation Tree Age (Years)'),'1',lang('Jumlah'));
};

var arrReturn = ajaxDataRenderer(m_data);