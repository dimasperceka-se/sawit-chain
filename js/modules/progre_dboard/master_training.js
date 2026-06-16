/*
* @Author: nikolius
* @Date:   2017-09-22 14:33:51
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-29 11:52:01
*/

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_ProvinceID,kab: m_DistrictID},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            arrReturn = r;

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.DateGenerated);
        }
    });

    return arrReturn;
};
var arrReturn = ajaxDataRenderer(m_data);

//bentuk array daerah & perulangan data chart
var arrRegion = [];
var valBarCountTraining = [];
var valBarTrainingKoltiva = [];
var valBarTrainingMill = [];
var valBarTrainingNgo = [];
var valBarTrainingPrivate = [];
var valPieTrainKoltivaCount = 0;
var valPieTrainMillCount = 0;
var valPieTrainNgoCount = 0;
var valPieTrainPrivateCount = 0;
var valPieTrain = [];

$.each(arrReturn.barChart, function(key, value) {
    arrRegion[key] = [value.label];

    valBarCountTraining[key] = [parseInt(value.total_training)];
    valBarTrainingKoltiva[key] = [parseInt(value.count_koltiva_staff)];
    valBarTrainingMill[key] = [parseInt(value.count_mill_staff)];
    valBarTrainingNgo[key] = [parseInt(value.count_ngo_staff)];
    valBarTrainingPrivate[key] = [parseInt(value.count_private_staff)];

    valPieTrainKoltivaCount += parseInt(value.count_koltiva_staff);
    valPieTrainMillCount += parseInt(value.count_mill_staff);
    valPieTrainNgoCount += parseInt(value.count_ngo_staff);
    valPieTrainPrivateCount += parseInt(value.count_private_staff);
});

//bar_chart_count_training ============================================ (begin)
column(
    [{name: lang('Participants'),data: valBarCountTraining}],
    'bar_chart_count_training',
    lang('Total Training Participant'),
    lang('Participants'), ['#95130b'],
    arrRegion, 'normal', 1
);
//bar_chart_count_training ============================================ (end)

//bar_chart_count_training_peryear ============================================ (begin)
var dataYearCate = [];
$.each(arrReturn.barChartPerYear.yearRange, function(key, value) {
    dataYearCate[key] = value.YearTrain;
});

var dataBarChartTrainPerYear = [];
//console.log(arrReturn.barChartPerYear.TrainYearData);
$.each(arrReturn.barChartPerYear.TrainYearData, function(key, value) {
    var dataTmpTrain = [];

    var increObj = 0;
    for(var objTmpTrain in value) {
        if(objTmpTrain == "label") continue;
        dataTmpTrain[increObj] = parseInt(value[objTmpTrain]);
        increObj++;
    }

    dataBarChartTrainPerYear[key] = {name: value.label, data: dataTmpTrain};
});

//define
column(dataBarChartTrainPerYear,'bar_chart_count_training_peryear', lang('Total Training Participant per Year'), lang('Participants'), null,dataYearCate,'normal',0,true,-90);
//bar_chart_count_training_peryear ============================================ (end)

//bar_chart_training_kategori ============================================ (begin)
column(
    [{name: lang('Koltiva Staff'),data: valBarTrainingKoltiva},{name: lang('Mill Staff'),data: valBarTrainingMill}, {name:lang('NGO Staff'),data:valBarTrainingNgo}, {name:lang('Private Staff'),data:valBarTrainingPrivate}],
    'bar_chart_training_kategori',
    lang('Staff Trainings'), '%', null,
    arrRegion,
    'percent',
    0,
    true
);
//bar_chart_training_kategori ============================================ (end)

//pie_chart_training_kategori ============================================ (begin)
valPieTrain = [
    [lang('Koltiva Staff'), valPieTrainKoltivaCount],
    [lang('Mill Staff'), valPieTrainMillCount],
    [lang('NGO Staff'), valPieTrainNgoCount],
    [lang('Private Staff'), valPieTrainPrivateCount]
];
plot(valPieTrain,'pie_chart_training_kategori', lang('Staff Trainings'),'1',lang('Participants'));
//pie_chart_training_kategori ============================================ (end)