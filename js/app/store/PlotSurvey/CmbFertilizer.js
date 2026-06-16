/*
* @Author: nikolius
* @Date:   2017-07-27 11:55:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-27 11:59:18
*/

Ext.define('Koltiva.store.PlotSurvey.CmbFertilizer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbFertilizer',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('NPK')
    },{
        "id": "2",
        "label": lang('TSP')
    },{
        "id": "3",
        "label": lang('CU')
    },{
        "id": "4",
        "label": lang('KCL')
    },{
        "id": "5",
        "label": lang('Phonska')
    },{
        "id": "6",
        "label": lang('NPK Mutiara')
    },{
        "id": "7",
        "label": lang('Borat')
    },{
        "id": "8",
        "label": lang('Dolomit')
    },{
        "id": "9",
        "label": lang('Janjangan Kosong')
    },{
        "id": "10",
        "label": lang('Abu Janjangan Kosong')
    },{
        "id": "11",
        "label": lang('Kompos Janjangan Sawit')
    },{
        "id": "12",
        "label": lang('Pupuk Kandang')
    },{
        "id": "13",
        "label": lang('Solid')
    },{
        "id": "14",
        "label": lang('Abu Boiler')
    }]
});