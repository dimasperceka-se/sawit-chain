/*
* @Author: nikolius
* @Date:   2017-05-31 14:53:57
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-31 14:54:34
*/

Ext.define('Koltiva.store.PlotSurvey.CmbSoilType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbSoilType',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Mineral')
    },{
        "id": "2",
        "label": lang('Peat')
    }]
});