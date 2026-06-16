/*
* @Author: nikolius
* @Date:   2017-05-31 14:50:54
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-06 17:16:05
*/

Ext.define('Koltiva.store.PlotSurvey.CmbPlantationConditionEst', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbPlantationConditionEst',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Secondary Veg/Fallow')
    },{
        "id": "2",
        "label": lang('Food Crops')
    },{
        "id": "3",
        "label": lang('Mangrove')
    },{
        "id": "4",
        "label": lang('Other Plantation (rubber, coffee, etc.)')
    },{
        "id": "5",
        "label": lang('Oil palm plantation')
    },{
        "id": "6",
        "label": lang('Forest')
    },{
        "id": "7",
        "label": lang('I don\'t know')
    }]
});