$(document).ready(function() {
    $('#wrapper').addClass('cover');
    $.ajax({
        url: m_data,
        // type: 'default GET (Other values: POST)',
        // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        data: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            regen: m_regen
        },
    })
    .done(function(data) {
        var FemaleFamilyPercentage,FamilyCount,FemaleFamilyCount;
        FamilyCount = parseInt(data.family);
        FemaleFamilyCount = parseInt(data.female_family);
        FemaleFamilyPercentage = (FemaleFamilyCount/FamilyCount) * 100;

        var FemaleWorkerPercentage, WorkerCount, FemaleWorkerCount;
        WorkerCount = parseInt(data.lab_workers);
        FemaleWorkerCount = parseInt(data.female_lab_workers);
        FemaleWorkerPercentage = (FemaleWorkerCount/WorkerCount) * 100;

        var WorkerPpePercentage, WorkerPpeCount;
        WorkerPpeCount = parseInt(data.lab_workers_use_ppe);
        WorkerPpePercentage = (WorkerPpeCount/WorkerCount) * 100;

        $('#box_farmer').text(number_format(data.members,0,'.',','));
        $('#box_family').text(number_format(data.family,0,'.',','));
        $('#box_female_family_members').text(number_format(FemaleFamilyPercentage,1,'.',','));
        $('#box_school').text(number_format(data.school,0,'.',','));
        $('#box_family_working').text(number_format(data.working,0,'.',','));
        $('#box_working').text(number_format(data.lab_workers,0,'.',','));
        $('#box_female_worker').text(number_format(FemaleWorkerPercentage,1,'.',','));
        $('#box_worker_use_ppe').text(number_format(WorkerPpePercentage,1,'.',','));

        var cat_region = [], farmers = [], child_0 = [], child_1 = [], child_2 = [], child_3 = [], school = [], working = [], lab_workers = [];
        if (data.detail) {
            $.each(data.detail, function(index, val) {
                cat_region.push(lang(val.label));
                farmers.push(parseInt(val.members));
                child_0.push(parseInt(val.child_0));
                child_1.push(parseInt(val.child_1));
                child_2.push(parseInt(val.child_2));
                child_3.push(parseInt(val.child_3));
                school.push(parseInt(val.school));
                working.push(parseInt(val.working));
                lab_workers.push(parseInt(val.lab_workers));
            });
        }
        column([{name: lang('Farmer'), data: farmers}], 'chart_farmer', lang('Farmers'), lang('jumlah'), null, cat_region,'normal',0,false);
        column_one([
            {name: '0', data: child_0},
            {name: '1', data: child_1},
            {name: '2', data: child_2},
            {name: '> 3', data: child_3},
        ], 'chart_child', lang('Children'), lang('jumlah'), null, cat_region,'normal',0,true);

        column([{name: lang('School'), data: school}], 'chart_school', lang('School'), lang('jumlah'), null, cat_region,'normal',0,false);
        column([{name: lang('Family Members working on the Farm'), data: working}], 'chart_family_working', lang('Family Members working on the Farm'), lang('jumlah'), null, cat_region,'normal',0,false);

        column([{name: lang('Workers'), data: lab_workers}], 'chart_working', lang('Workers'), lang('jumlah'), null, cat_region,'normal',0,false);

        plot([
            {name: '< 6', y: parseInt(data.work_lt_6)},
            {name: '6 - 8', y: parseInt(data.work_bt_6_8)},
            {name: '> 8', y: parseInt(data.work_gt_8)},
        ],'pie_working_hour', lang('Working Hour'),'0', lang('Jumlah'));

        plot([
            {name: lang('Seedling'), y: parseInt(data.activity_seedlings)},
            {name: lang('Slashing'), y: parseInt(data.activity_slashing)},
            {name: lang('Circle Weeding'), y: parseInt(data.activity_circle)},
            {name: lang('Pruning'), y: parseInt(data.activity_pruning)},
            {name: lang('Fertilizing'), y: parseInt(data.activity_pemupukan)},
            {name: lang('Pesticide Application'), y: parseInt(data.activity_pest)},
            {name: lang('Harvest'), y: parseInt(data.activity_harvest)},
            {name: lang('Transportation'), y: parseInt(data.activity_transport)},
        ],'pie_family_activity_type', lang('Family Members Working Activity'),'0', lang('Jumlah'));

        plot([
            {name: lang('Seedling'), y: parseInt(data.lab_activity_seedlings)},
            {name: lang('Slashing'), y: parseInt(data.lab_activity_slashing)},
            {name: lang('Circle Weeding'), y: parseInt(data.lab_activity_circle)},
            {name: lang('Pruning'), y: parseInt(data.lab_activity_pruning)},
            {name: lang('Fertilizing'), y: parseInt(data.lab_activity_pemupukan)},
            {name: lang('Pesticide Application'), y: parseInt(data.lab_activity_pest)},
            {name: lang('Harvest'), y: parseInt(data.lab_activity_harvest)},
            {name: lang('Transportation'), y: parseInt(data.lab_activity_transport)},
        ],'pie_worker_activity_type', lang('Workers Working Activity'),'0', lang('Jumlah'));

    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        $('#wrapper').removeClass('cover');        
    });
}); 