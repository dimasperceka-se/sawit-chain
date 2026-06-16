/*
* @Author: nikolius
* @Date:   2017-09-11 14:51:03
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-16 09:41:26
*/
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
$(function () {

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

    //Langsung jalankan search pertama kali
    runSearch();
});


function runSearch(){
    
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    let ftype = $("#ftype").val();
    let fprovince = $("#fprovince").val();
    let fdistrict = $("#fdistrict").val();

    $.ajax({
        type: "GET",
        url: m_data,
        data: {prov: fprovince,kab: fdistrict,ftype: ftype},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            //data display
            $('#box_land_convert_forest_2010').html(number_format(r.dataDisplay.land_convert_forest_2010_yes,0,'.',','));
            $('#box_land_convert_peat_2010').html(number_format(r.dataDisplay.land_convert_peat_2010_yes,0,'.',','));
            $('#box_labor_right_abuses').html(number_format(r.dataDisplay.labor_right_abuses_yes,0,'.',','));
            $('#box_sustain_smart_agri_farmer').html(number_format(r.dataDisplay.sustain_smart_agri_farmer_yes,0,'.',','));
            $('#box_farmer_willing_survey').html(number_format(r.dataDisplay.farmer_willing_survey_yes,0,'.',','));

            //data chart ======================================================================== (begin)

            //pie_land_forest ======================= begin
            var varPieLandForest = [];

            var varValuePieLandForest = [];
            varValuePieLandForest[0] = [lang('Yes'), parseFloat(r.dataDisplay.land_convert_forest_2010_yes)];
            varValuePieLandForest[1] = [lang('No'), parseFloat(r.dataDisplay.land_convert_forest_2010_no)];

            arrReturn.varValuePieLandForest = varValuePieLandForest;
            //pie_land_forest ======================= end

            //pie_land_peat ======================= begin
            var varPieLandPeat = [];

            varPieLandPeat[0] = [lang('Yes'), parseFloat(r.dataDisplay.land_convert_peat_2010_yes)];
            varPieLandPeat[1] = [lang('No'), parseFloat(r.dataDisplay.land_convert_peat_2010_no)];

            arrReturn.varPieLandPeat = varPieLandPeat;
            //pie_land_peat ======================= end

            //pie_labor_right ======================= begin
            var varPieLaborRight = [];

            varPieLaborRight[0] = [lang('Yes'), parseFloat(r.dataDisplay.labor_right_abuses_yes)];
            varPieLaborRight[1] = [lang('No'), parseFloat(r.dataDisplay.labor_right_abuses_no)];

            arrReturn.varPieLaborRight = varPieLaborRight;
            //pie_labor_right ======================= end

            var BarLaborAbuseNumberCategory = [];
            BarLaborAbuseNumberCategory[0] = lang('Children age 7-15 not in school');
            BarLaborAbuseNumberCategory[1] = lang('Children work in farm');
            BarLaborAbuseNumberCategory[2] = lang('Overtime work');
            BarLaborAbuseNumberCategory[3] = lang('Underpaid work');
            var BarLaborAbuseNumber = [];
            BarLaborAbuseNumber[0] = [];
            BarLaborAbuseNumber[0].name = lang('Farmer Family');
            BarLaborAbuseNumber[0].data = [];
            BarLaborAbuseNumber[1] = [];
            BarLaborAbuseNumber[1].name = lang('Farmer Labour');
            BarLaborAbuseNumber[1].data = [];
            BarLaborAbuseNumber[0].data[0] = parseInt(r.dataDisplay.labor_right_fam_child_no_school);
            BarLaborAbuseNumber[1].data[0] = null;
            BarLaborAbuseNumber[0].data[1] = parseInt(r.dataDisplay.labor_right_fam_child_work);
            BarLaborAbuseNumber[1].data[1] = parseInt(r.dataDisplay.labor_right_lab_child_work);
            BarLaborAbuseNumber[0].data[2] = parseInt(r.dataDisplay.labor_right_fam_overtime);
            BarLaborAbuseNumber[1].data[2] = parseInt(r.dataDisplay.labor_right_lab_overtime);
            BarLaborAbuseNumber[0].data[3] = parseInt(r.dataDisplay.labor_right_fam_underpaid);
            BarLaborAbuseNumber[1].data[3] = parseInt(r.dataDisplay.labor_right_lab_underpaid);

            //pie_farmer_smart ================================= begin
            var varPieFarmerSmart = [];

            varPieFarmerSmart[0] = [lang('Yes'), parseFloat(r.dataDisplay.sustain_smart_agri_farmer_yes)];
            varPieFarmerSmart[1] = [lang('No'), parseFloat(r.dataDisplay.sustain_smart_agri_farmer_no)];

            arrReturn.varPieFarmerSmart = varPieFarmerSmart;
            //pie_farmer_smart ================================= end

            //pie_farmer_willing_survey ========================================= begin
            var varPieFarmerWillingSurvey = [];

            varPieFarmerWillingSurvey[0] = [lang('Yes'), parseFloat(r.dataDisplay.farmer_willing_survey_yes)];
            varPieFarmerWillingSurvey[1] = [lang('No'), parseFloat(r.dataDisplay.farmer_willing_survey_no)];

            arrReturn.varPieFarmerWillingSurvey = varPieFarmerWillingSurvey;
            //pie_farmer_willing_survey ========================================= end

            var dataChart = r.dataChart;
            var varLabelDaerah = [];
            var varValueBarPersenLandForestYes = [];
            var varValueBarPersenLandForestNo = [];
            var varValueBarPersenLandPeatYes = [];
            var varValueBarPersenLandPeatNo = [];
            var varValueBarPersenLaborRightYes = [];
            var varValueBarPersenLaborRightNo = [];
            var varValueBarPersenFarmerSmartYes = [];
            var varValueBarPersenFarmerSmartNo = [];
            var varValueBarPersenFarmerWillingSurveyYes = [];
            var varValueBarPersenFarmerWillingSurveyNo = [];
            var BarFarmerLaborAbuseNumber = [];


            $.each(dataChart, function(key, value) {
                //label daerah
                varLabelDaerah[key] = [value.label];

                varValueBarPersenLandForestYes[key] = [parseFloat(value.land_convert_forest_2010_yes)];
                varValueBarPersenLandForestNo[key] = [parseFloat(value.land_convert_forest_2010_no)];

                varValueBarPersenLandPeatYes[key] = [parseFloat(value.land_convert_peat_2010_yes)];
                varValueBarPersenLandPeatNo[key] = [parseFloat(value.land_convert_peat_2010_no)];

                varValueBarPersenLaborRightYes[key] = [parseFloat(value.labor_right_abuses_yes)];
                varValueBarPersenLaborRightNo[key] = [parseFloat(value.labor_right_abuses_no)];

                varValueBarPersenFarmerSmartYes[key] = [parseFloat(value.sustain_smart_agri_farmer_yes)];
                varValueBarPersenFarmerSmartNo[key] = [parseFloat(value.sustain_smart_agri_farmer_no)];

                varValueBarPersenFarmerWillingSurveyYes[key] = [parseFloat(value.farmer_willing_survey_yes)];
                varValueBarPersenFarmerWillingSurveyNo[key] = [parseFloat(value.farmer_willing_survey_no)];

                BarFarmerLaborAbuseNumber[key] = [parseInt(value.labor_right_abuses_yes)];
            });
            arrReturn.varLabelDaerah = varLabelDaerah;

            //bar_persen_land_forest ================================= begin
            arrReturn.varValueBarPersenLandForestYes = varValueBarPersenLandForestYes;
            arrReturn.varValueBarPersenLandForestNo = varValueBarPersenLandForestNo;
            //bar_persen_land_forest ================================= end

            //bar_persen_land_peat =================================== begin
            arrReturn.varValueBarPersenLandPeatYes = varValueBarPersenLandPeatYes;
            arrReturn.varValueBarPersenLandPeatNo = varValueBarPersenLandPeatNo;
            //bar_persen_land_peat =================================== end

            //bar_persen_labor_right ====================================== begin
            arrReturn.varValueBarPersenLaborRightYes = varValueBarPersenLaborRightYes;
            arrReturn.varValueBarPersenLaborRightNo = varValueBarPersenLaborRightNo;
            //bar_persen_labor_right ====================================== end

            arrReturn.BarLaborAbuseNumberCategory = BarLaborAbuseNumberCategory
            arrReturn.BarLaborAbuseNumber = BarLaborAbuseNumber

            arrReturn.BarFarmerLaborAbuseNumber = BarFarmerLaborAbuseNumber;

            //bar_persen_farmer_smart ======================================= begin
            arrReturn.varValueBarPersenFarmerSmartYes = varValueBarPersenFarmerSmartYes;
            arrReturn.varValueBarPersenFarmerSmartNo = varValueBarPersenFarmerSmartNo;
            //bar_persen_farmer_smart ======================================= end

            //bar_persen_farmer_willing_survey ======================================= begin
            arrReturn.varValueBarPersenFarmerWillingSurveyYes = varValueBarPersenFarmerWillingSurveyYes;
            arrReturn.varValueBarPersenFarmerWillingSurveyNo = varValueBarPersenFarmerWillingSurveyNo;
            //bar_persen_farmer_willing_survey ======================================= end

            //data chart ======================================================================== (end)

            //pie chart
            plot(arrReturn.varValuePieLandForest,'pie_land_forest', lang('Plantations where land conversion from forest took place after 2010'),'1', lang('Jumlah'));
            plot(arrReturn.varPieLandPeat,'pie_land_peat', lang('Plantations where land conversion from peat land area took place after 2010'),'1', lang('Jumlah'));
            plot(arrReturn.varPieLaborRight,'pie_labor_right', lang('Farmers with labor and family member exploitation'),'1', lang('Jumlah'));
            plot(arrReturn.varPieFarmerSmart,'pie_farmer_smart', lang('Farmers with sustainable and climate smart agriculture practices'),'1', lang('Jumlah'));
            plot(arrReturn.varPieFarmerWillingSurvey,'pie_farmer_willing_survey', lang('Number of farmers willing to be mapped and surveyed'),'1', lang('Jumlah'));

            //bar_persen_land_forest
            column(
                [{name: lang('Yes'),data: arrReturn.varValueBarPersenLandForestYes},{name: lang('No'),data: arrReturn.varValueBarPersenLandForestNo}],
                'bar_persen_land_forest',
                lang('Plantations where land conversion from forest took place after 2010'), '%', ['#95130b','#FFBC65'],
                arrReturn.varLabelDaerah,
                'percent',
                0,
                true
            );

            //bar_persen_land_peat
            column(
                [{name: lang('Yes'),data: arrReturn.varValueBarPersenLandPeatYes},{name: lang('No'),data: arrReturn.varValueBarPersenLandPeatNo}],
                'bar_persen_land_peat',
                lang('Plantations where land conversion from peat land area took place after 2010'), '%', ['#95130b','#FFBC65'],
                arrReturn.varLabelDaerah,
                'percent',
                0,
                true
            );

            //bar_persen_labor_right
            column(
                [{name: lang('Yes'),data: arrReturn.varValueBarPersenLaborRightYes},{name: lang('No'),data: arrReturn.varValueBarPersenLaborRightNo}],
                'bar_persen_labor_right',
                lang('Farmers with labor and family member exploitation'), '%', ['#95130b','#FFBC65'],
                arrReturn.varLabelDaerah,
                'percent',
                0,
                true
            );

            column(
                [{name: lang('Labor Right Abuses'),data: arrReturn.BarFarmerLaborAbuseNumber}],
                'bar_farmer_labor_abuse_number',
                lang('Number of Farmers with labor and family member exploitation'),
                lang('Jumlah'), ['#95130b'],
                arrReturn.varLabelDaerah, 'normal', 0
            );

            column_one(arrReturn.BarLaborAbuseNumber, 'bar_labor_abuse_number', lang('Number of Farmers with labor and family member exploitation'), lang('Jumlah'), null, arrReturn.BarLaborAbuseNumberCategory, null, 0, true, -45);

            //bar_persen_farmer_smart
            column(
                [{name: lang('Yes'),data: arrReturn.varValueBarPersenFarmerSmartYes},{name: lang('No'),data: arrReturn.varValueBarPersenFarmerSmartNo}],
                'bar_persen_farmer_smart',
                lang('Farmers with sustainable and climate smart agriculture practices'), '%', ['#95130b','#FFBC65'],
                arrReturn.varLabelDaerah,
                'percent',
                0,
                true
            );

            //bar_persen_farmer_willing_survey
            column(
                [{name: lang('Yes'),data: arrReturn.varValueBarPersenFarmerWillingSurveyYes},{name: lang('No'),data: arrReturn.varValueBarPersenFarmerWillingSurveyNo}],
                'bar_persen_farmer_willing_survey',
                lang('Number of farmers willing to be mapped and surveyed'), '%', ['#95130b','#FFBC65'],
                arrReturn.varLabelDaerah,
                'percent',
                0,
                true
            );

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    return arrReturn;
}