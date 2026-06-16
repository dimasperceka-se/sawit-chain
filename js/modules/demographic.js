var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_ProvinceID,kab: m_DistrictID,regen: m_regen},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            //console.log(r);

            //data display
            $('#box_total_farmer').html(number_format(r.dataDisplay.Total_Farmer,0,'.',','));
            $('#box_male_member').html(number_format(r.dataDisplay.Male_Farmer,0,'.',','));
            $('#box_female_member').html(number_format(r.dataDisplay.Female_Farmer,0,'.',','));
            $('#box_average_age').html(number_format(r.dataDisplay.Average_Age,0,'.',','));
            $('#box_completed_primary_school').html(number_format(r.dataDisplay.Completed_Primary_School,1,'.',','));
            $('#box_below_35_age').html(number_format(r.dataDisplay.Below_35_Age,0,'.',','));
            $('#box_average_hh_member').html(number_format(r.dataDisplay.Average_HH_Members,0,'.',','));
            $('#box_province').html(number_format(r.dataDisplay.Province,0,'.',','));
            $('#box_district').html(number_format(r.dataDisplay.District,0,'.',','));
            $('#box_subdistrict').html(number_format(r.dataDisplay.SubDistrict,0,'.',','));
            $('#box_village').html(number_format(r.dataDisplay.Village,0,'.',','));
            $('#box_farmer_group').html(number_format(r.group.Farmer_Group,0,'.',','));
            $('#box_cooperative').html(number_format(r.group.Cooperative,0,'.',','));
            $('#box_gapoktan').html(number_format(r.group.Gapoktan,0,'.',','));

            $('#box_ppi_index_125').html(number_format(r.dataDisplay.Total_125_index,0,'.',','));
            $('#box_ppi_index_25').html(number_format(r.dataDisplay.Total_25_index,0,'.',','));

            var total_own_hp = parseInt(r.dataDisplay.Hp_Smart) + parseInt(r.dataDisplay.Hp_Feature);
            $('#box_farmer_own_hp').html(number_format(total_own_hp,0,'.',','));

            $('#box_hp_smart').html(number_format(parseInt(r.dataDisplay.Hp_Smart_Access),0,'.',','));

            //data chart ======================================================================== (begin)
                var dataChart = r.dataChart;
                var varPieHHMember = [];
                var varBarGenderJumlahMale = [];
                var varBarGenderJumlahFemale = [];
                var varLabelDaerah = [];
                var varBarAveAge = [];
                var varBarHouseholdSize = [];
                var varPieAgeClass = [];
                var varPieAge15to24 = 0;
                var varPieAge25to34 = 0;
                var varPieAge35to44 = 0;
                var varPieAge45to54 = 0;
                var varPieAge55More = 0;
                var varPieEducation = [];
                var varPieEduNoEducation = 0;
                var varPieEduPrimarySchoolIncompleted = 0;
                var varPieEduPrimarySchoolCompleted = 0;
                var varPieEduGraduatedMiddleSchool = 0;
                var varPieEduGraduatedHighSchool = 0;
                var varPieEduGraduatedCollege = 0;
                var varPieMaritalStatus = [];
                var varPieMStatusMarried = 0;
                var varPieMStatusSingle = 0;
                var varPieMStatusWidow = 0;

                var PieHandphone = [];
                var PieHandphone_Hp_Smart = 0;
                var PieHandphone_Hp_Feature = 0;
                var PieHandphone_Hp_NoHp = 0;

                $.each(dataChart, function(key, value) {
                    //pie_hh_member_count
                    varPieHHMember[key] = [value.label, parseFloat(value.Total_Farmer)];

                    //chart bar_gender_per_daerah
                    varBarGenderJumlahMale[key] = [parseFloat(value.Male_Farmer)];
                    varBarGenderJumlahFemale[key] = [parseFloat(value.Female_Farmer)];
                    varLabelDaerah[key] = [value.label];

                    //chart bar_member_age
                    varBarAveAge[key] = [parseFloat(value.Average_Age)];

                    //bar_average_hh_members
                    varBarHouseholdSize[key] = [parseFloat(value.Average_HH_Members)];

                    //pie age_class
                    varPieAge15to24 = varPieAge15to24 + parseInt(value.Age_15to24);
                    varPieAge25to34 = varPieAge25to34 + parseInt(value.Age_25to34);
                    varPieAge35to44 = varPieAge35to44 + parseInt(value.Age_35to44);
                    varPieAge45to54 = varPieAge45to54 + parseInt(value.Age_45to54);
                    varPieAge55More = varPieAge55More + parseInt(value.Age_MoreThan55);

                    //pie education
                    varPieEduNoEducation = varPieEduNoEducation + parseInt(value.Edu_NoEducation);
                    varPieEduPrimarySchoolIncompleted = varPieEduPrimarySchoolIncompleted + parseInt(value.Edu_PrimarySchoolIncompleted);
                    varPieEduPrimarySchoolCompleted = varPieEduPrimarySchoolCompleted + parseInt(value.Edu_PrimarySchoolCompleted);
                    varPieEduGraduatedMiddleSchool = varPieEduGraduatedMiddleSchool + parseInt(value.Edu_GraduatedMiddleSchool);
                    varPieEduGraduatedHighSchool = varPieEduGraduatedHighSchool + parseInt(value.Edu_GraduatedHighSchool);
                    varPieEduGraduatedCollege = varPieEduGraduatedCollege + parseInt(value.Edu_GraduatedCollege);

                    //pie_marital_status
                    varPieMStatusMarried = varPieMStatusMarried + parseInt(value.MStatus_Married);
                    varPieMStatusSingle = varPieMStatusSingle + parseInt(value.MStatus_Single);
                    varPieMStatusWidow = varPieMStatusWidow + parseInt(value.MStatus_Widow);

                    PieHandphone_Hp_Smart = PieHandphone_Hp_Smart + parseInt(value.Hp_Smart);
                    PieHandphone_Hp_Feature = PieHandphone_Hp_Feature + parseInt(value.Hp_Feature);
                    PieHandphone_Hp_NoHp = PieHandphone_Hp_NoHp + parseInt(value.Hp_NoHp);
                });

                arrReturn.varPieHHMember = varPieHHMember;
                arrReturn.varBarGenderJumlahMale = varBarGenderJumlahMale;
                arrReturn.varBarGenderJumlahFemale = varBarGenderJumlahFemale;
                arrReturn.varLabelDaerah = varLabelDaerah;
                arrReturn.varBarAveAge = varBarAveAge;
                arrReturn.varBarHouseholdSize = varBarHouseholdSize;

                //pie age_class
                varPieAgeClass = [
                    [lang('Between 15 to 24'), varPieAge15to24],
                    [lang('Between 25 to 34'), varPieAge25to34],
                    [lang('Between 35 to 44'), varPieAge35to44],
                    [lang('Between 45 to 54'), varPieAge45to54],
                    [lang('More than 55'), varPieAge55More]
                ];
                arrReturn.varPieAgeClass = varPieAgeClass;

                //pie education
                varPieEducation = [
                    [lang('No Education'), varPieEduNoEducation],
                    [lang('Primary School Incompleted'), varPieEduPrimarySchoolIncompleted],
                    [lang('Primary School Completed'), varPieEduPrimarySchoolCompleted],
                    [lang('Graduated Middle School'), varPieEduGraduatedMiddleSchool],
                    [lang('Graduated High School'), varPieEduGraduatedHighSchool],
                    [lang('Graduated College'), varPieEduGraduatedCollege]
                ];
                arrReturn.varPieEducation = varPieEducation;

                //pie_marital_status
                varPieMaritalStatus = [
                    [lang('Married'), varPieMStatusMarried],
                    [lang('Single'), varPieMStatusSingle],
                    [lang('Widow'), varPieMStatusWidow]
                ];
                arrReturn.varPieMaritalStatus = varPieMaritalStatus;

                PieHandphone = [
                    [lang('Smartphone (Android/iPhone)'), PieHandphone_Hp_Smart],
                    [lang('Feature Phone (Basic Mobile Phone)'), PieHandphone_Hp_Feature],
                    [lang('No Handphone'), PieHandphone_Hp_NoHp]
                ];
                arrReturn.PieHandphone = PieHandphone;

                arrReturn.TotalAccessSmartphone = parseInt(r.dataDisplay.Hp_Smart_Access) - parseInt(r.dataDisplay.Hp_Smart);
                arrReturn.TotalSmartphone = parseInt(r.dataDisplay.Hp_Smart);

                //chart poverty level
                    var chart_poverty       = [];
                    var cat_poverty_daerah = [];
                    var keys_poverty = [
                        {'key':'Ave_125_index', 'label' : lang('$ 1.25/Day')},
                        {'key':'Ave_25_index', 'label' : lang('$ 2.5/Day')},
                    ];
                    for (var i = keys_poverty.length - 1; i >= 0; i--) {
                        chart_poverty[i]    = [];
                        chart_poverty[i]['name']    = lang(keys_poverty[i].label);
                        chart_poverty[i]['data']    = []
                    }

                    for (var iForPover = 0;iForPover < dataChart.length; iForPover++) {
                        cat_poverty_daerah[iForPover] = lang(dataChart[iForPover].label);

                        for (var jForPover = keys_poverty.length - 1; jForPover >= 0; jForPover--) {
                           chart_poverty[jForPover]['data'][iForPover]    = parseInt(dataChart[iForPover][keys_poverty[jForPover].key]);
                        }
                    }

                    arrReturn.chart_poverty = chart_poverty;
                    arrReturn.cat_poverty_daerah = cat_poverty_daerah;
                //chart poverty level
            
            var cat_fg = [], fg = [], coop = [], gapoktan = [];
            if (r.group.detail) {
                $.each(r.group.detail, function(index, val) {
                    cat_fg.push(lang(val.label));
                    fg.push(parseInt(val.Farmer_Group));
                    coop.push(parseInt(val.Cooperative));
                    gapoktan.push(parseInt(val.Gapoktan));
                });
            }
            arrReturn.cat_fg = cat_fg;
            arrReturn.fg = fg;
            arrReturn.coop = coop;
            arrReturn.gapoktan = gapoktan;
            // dipindah tempat render karena UI tidak seragam
            // column([{name: lang('Farmer Group'), data: fg}], 'column_farmer_group', lang('Farmer Groups'), lang('Jumlah'), null, cat_fg,'normal',0,true);
            // column([{name: lang('Cooperative'), data: coop}], 'column_cooperative', lang('Cooperatives'), lang('Jumlah'), null, cat_fg,'normal',0,false);
            // column([{name: lang('Gapoktan'), data: gapoktan}], 'column_gapoktan', lang('Gapoktan'), lang('Jumlah'), null, cat_fg,'normal',0,false);
            //data chart ======================================================================== (end)


            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //console.log(arrReturn);
    return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);

//pie chart
plot(arrReturn.varPieHHMember,'pie_hh_member_count', lang('Farmers Households'),'1', lang('Jumlah'));

//column column_farmer_group
column(
    [{name: lang('Farmer Group'), data: arrReturn.fg}], 
    'column_farmer_group', 
    lang('Farmer Groups'),
    lang('Jumlah'),
    null,
    arrReturn.cat_fg,
    'normal',
    0,
    true
);

//column column_cooperative
column(
    [{name: lang('Cooperative'), data: arrReturn.coop}], 
    'column_cooperative', 
    lang('Cooperatives'), 
    lang('Jumlah'), 
    null, 
    arrReturn.cat_fg,
    'normal',
    0,
    false
);

//column column_gapoktan
column(
    [{name: lang('Gapoktan'), data: arrReturn.gapoktan}], 
    'column_gapoktan', 
    lang('Gapoktan'), 
    lang('Jumlah'), 
    null, 
    arrReturn.cat_fg,
    'normal',
    0,
    false
);

//column bar_gender_per_daerah
column(
    [{name: lang('Laki-laki'),data: arrReturn.varBarGenderJumlahMale},{name: lang('Perempuan'),data: arrReturn.varBarGenderJumlahFemale}],
    'bar_gender_per_daerah',
    lang('Gender of the Registered Farmers'), '%', ['#95130b','#FFBC65'],
    arrReturn.varLabelDaerah,
    'percent',
    0,
    true
);

//column bar_member_age
column(
    [{name: lang('Usia'),data: arrReturn.varBarAveAge}],
    'bar_member_age',
    lang('Average Farmer Age'),
    lang('Tahun'), ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 1
);

//bar_average_hh_members
column(
    [{name: lang('Household Size'),data: arrReturn.varBarHouseholdSize}],
    'bar_average_hh_members',
    lang('Average Household Size'),
    lang('Total'), ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 1
);

//pie pie_age_class
plot(arrReturn.varPieAgeClass,'pie_age_class', lang('Age Classification (Year)'),'1',lang('Jumlah'));

//pie pie_education
plot(arrReturn.varPieEducation,'pie_education', lang('Farmers Education'),'1',lang('Jumlah'));

//pie_marital_status
plot(arrReturn.varPieMaritalStatus,'pie_marital_status', lang('Farmers Marital Status'),'1',lang('Jumlah'));

//bar_poverty_level
column_one(arrReturn.chart_poverty, 'bar_poverty_level', lang('Rate Below Poverty Level'), lang('Household living below poverty line')+' %', null, arrReturn.cat_poverty_daerah, 'normal', 1, true);

plot(arrReturn.PieHandphone,'pie_handphone', lang('Farmers with own Handphone'),'1',lang('Jumlah'));

plot([
    [lang('Own Smartphone'), arrReturn.TotalSmartphone],
    [lang('Access to a Smartphone'), arrReturn.TotalAccessSmartphone]
],'pie_hp_smartphone', lang('Farmers with Smartphone or Access to a Smartphone'),'1', lang('Jumlah'));