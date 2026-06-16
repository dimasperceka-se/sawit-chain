/*
* @Author: nikolius
* @Date:   2017-05-31 14:44:43
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-26 17:12:04
*/

Ext.define('Koltiva.store.PlotSurvey.CmbBusinessModel', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbBusinessModel',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Independent')
    },{
        "id": "2",
        "label": lang('Independent - Ex Plasma')
    },{
        "id": "3",
        "label": lang('Plasma (has existing contract with plantation)')
    }]
});