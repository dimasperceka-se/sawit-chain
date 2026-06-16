/*
* @Author: nikolius
* @Date:   2018-01-16 07:47:42
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-19 10:36:21
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

            $('#box_total_agent').html(number_format(r.dataDisplay.TotalAgent,0,'.',','));
            $('#box_total_agent_female').html(number_format(r.dataDisplay.TotalAgentFemale,0,'.',','));
            $('#box_total_agent_staff').html(number_format(r.dataDisplay.TotalAgentStaff,0,'.',','));
            $('#box_total_agent_staff_female').html(number_format(r.dataDisplay.TotalAgentStaffFemale,0,'.',','));
            $('#box_avg_agent_age').html(number_format(r.dataDisplay.AvgAgeAgent,0,'.',','));
            $('#box_agent_complete_primary_school').html(number_format(r.dataDisplay.GraduatedPrimarySchoolAgent,0,'.',','));
            $('#box_avg_agent_staff_age').html(number_format(r.dataDisplay.AvgAgeAgentStaff,0,'.',','));
            $('#box_avg_agent_vehicle').html(number_format(r.dataDisplay.AvgAgentVehicle,0,'.',','));

            var varLabelDaerah = [];

            var BarGenderAgentMale = [];
            var BarGenderAgentFemale = [];

            var BarGenderAgentStaffMale = [];
            var BarGenderAgentStaffFemale = [];

            var BarAvgAgeAgent = [];
            var BarAvgAgeAgentStaff = [];

            var PieAgentAgeClass = [];
            var AgentAge15To24 = 0;
            var AgentAge25To34 = 0;
            var AgentAge35To44 = 0;
            var AgentAge45To54 = 0;
            var AgentAge55More = 0;

            var PieAgentStaffAgeClass = [];
            var AgentStaffAge15To24 = 0;
            var AgentStaffAge25To34 = 0;
            var AgentStaffAge35To44 = 0;
            var AgentStaffAge45To54 = 0;
            var AgentStaffAge55More = 0;

            var BarAgentVehicle = [];

            var dataChart = r.dataChart;
            $.each(dataChart, function(key, value) {
            	varLabelDaerah[key] = [value.label];

                BarGenderAgentMale[key] = [parseFloat(value.TotalAgentMale)];
                BarGenderAgentFemale[key] = [parseFloat(value.TotalAgentFemale)];

                BarGenderAgentStaffMale[key] = [parseFloat(value.TotalAgentStaffMale)];
                BarGenderAgentStaffFemale[key] = [parseFloat(value.TotalAgentStaffFemale)];

                BarAvgAgeAgent[key] = [parseFloat(value.AvgAgeAgent)];
                BarAvgAgeAgentStaff[key] = [parseFloat(value.AvgAgeAgentStaff)];

                AgentAge15To24 = AgentAge15To24 + parseInt(value.AgentAge15To24);
                AgentAge25To34 = AgentAge25To34 + parseInt(value.AgentAge25To34);
                AgentAge35To44 = AgentAge35To44 + parseInt(value.AgentAge35To44);
                AgentAge45To54 = AgentAge45To54 + parseInt(value.AgentAge45To54);
                AgentAge55More = AgentAge55More + parseInt(value.AgentAge55More);

                AgentStaffAge15To24 = AgentStaffAge15To24 + parseInt(value.AgentStaffAge15To24);
                AgentStaffAge25To34 = AgentStaffAge25To34 + parseInt(value.AgentStaffAge25To34);
                AgentStaffAge35To44 = AgentStaffAge35To44 + parseInt(value.AgentStaffAge35To44);
                AgentStaffAge45To54 = AgentStaffAge45To54 + parseInt(value.AgentStaffAge45To54);
                AgentStaffAge55More = AgentStaffAge55More + parseInt(value.AgentStaffAge55More);

                BarAgentVehicle[key] = [parseInt(value.AgentVehicle)];
            });
            arrReturn.varLabelDaerah = varLabelDaerah;

            arrReturn.BarGenderAgentMale = BarGenderAgentMale;
            arrReturn.BarGenderAgentFemale = BarGenderAgentFemale;

            arrReturn.BarGenderAgentStaffMale = BarGenderAgentStaffMale;
            arrReturn.BarGenderAgentStaffFemale = BarGenderAgentStaffFemale;

            arrReturn.BarAvgAgeAgent = BarAvgAgeAgent;
            arrReturn.BarAvgAgeAgentStaff = BarAvgAgeAgentStaff;

            PieAgentAgeClass = [
                [lang('Between 15 to 24'), AgentAge15To24],
                [lang('Between 25 to 34'), AgentAge25To34],
                [lang('Between 35 to 44'), AgentAge35To44],
                [lang('Between 45 to 54'), AgentAge45To54],
                [lang('More than 55'), AgentAge55More]
            ];
            arrReturn.PieAgentAgeClass = PieAgentAgeClass;

            PieAgentStaffAgeClass = [
                [lang('Between 15 to 24'), AgentStaffAge15To24],
                [lang('Between 25 to 34'), AgentStaffAge25To34],
                [lang('Between 35 to 44'), AgentStaffAge35To44],
                [lang('Between 45 to 54'), AgentStaffAge45To54],
                [lang('More than 55'), AgentStaffAge55More]
            ];
            arrReturn.PieAgentStaffAgeClass = PieAgentStaffAgeClass;

            arrReturn.BarAgentVehicle = BarAgentVehicle;

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //console.log(arrReturn);
    return arrReturn;
}


var arrReturn = ajaxDataRenderer(m_data);

column(
    [{name: lang('Laki-laki'),data: arrReturn.BarGenderAgentMale},{name: lang('Perempuan'),data: arrReturn.BarGenderAgentFemale}],
    'bar_gender_agent',
    lang('Gender of the Registered SME'), '%', ['#95130b','#FFBC65'],
    arrReturn.varLabelDaerah,
    'percent',
    0,
    true
);

column(
    [{name: lang('Laki-laki'),data: arrReturn.BarGenderAgentStaffMale},{name: lang('Perempuan'),data: arrReturn.BarGenderAgentStaffFemale}],
    'bar_gender_agent_staff',
    lang('Gender of the Registered SME Staffs'), '%', ['#95130b','#FFBC65'],
    arrReturn.varLabelDaerah,
    'percent',
    0,
    true
);

column(
    [{name: lang('Usia'),data: arrReturn.BarAvgAgeAgent}],
    'bar_avg_age_agent',
    lang('Average SME\'s Age'),
    lang('Tahun'), ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 1
);

column(
    [{name: lang('Usia'),data: arrReturn.BarAvgAgeAgentStaff}],
    'bar_avg_age_agent_staff',
    lang('Average SME Staff\'s Age'),
    lang('Tahun'), ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 1
);

plot(arrReturn.PieAgentAgeClass,'pie_agent_age_class', lang('SME Age Classification (Year)'),'1',lang('Jumlah'));

plot(arrReturn.PieAgentStaffAgeClass,'pie_agent_staff_age_class', lang('SME Staff Age Classification (Year)'),'1',lang('Jumlah'));

column(
    [{name: lang('Vehicles'),data: arrReturn.BarAgentVehicle}],
    'bar_agent_vehicle',
    lang('SME\'s Vehicles'),
    lang('Jumlah'), ['#95130b'],
    arrReturn.varLabelDaerah, 'normal', 0
);