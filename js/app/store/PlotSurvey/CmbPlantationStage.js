/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Apr 30 2019
 *  File : CmbPlantationStage.js
 *******************************************/
Ext.define('Koltiva.store.PlotSurvey.CmbPlantationStage', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbPlantationStage',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Stage 1: Registration & Orientation')
    },{
        "id": "2",
        "label": lang('Stage 2: Legal Documentation')
    },{
        "id": "3",
        "label": lang('Stage 3: Farm Surveys')
    },{
        "id": "4",
        "label": lang('Stage 4: Member Action Plan')
    },{
        "id": "5",
        "label": lang('Stage 5: Management Reports Completed')
    },{
        "id": "6",
        "label": lang('Stage 6: Group Records & Reports Updated')
    },{
        "id": "7",
        "label": lang('Stage 7: Member Internal Review Completed')
    },{
        "id": "8",
        "label": lang('Stage 8: Certification')
    }]
});