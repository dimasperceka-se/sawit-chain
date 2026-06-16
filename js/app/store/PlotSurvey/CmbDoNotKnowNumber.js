/*
* @Author: nikolius
* @Date:   2017-07-27 10:13:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-27 10:14:37
*/

Ext.define('Koltiva.store.PlotSurvey.CmbDoNotKnowNumber', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbDoNotKnowNumber',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Do not know')
    },{
        "id": "2",
        "label": '1'
    },{
        "id": "3",
        "label": '2'
    },{
        "id": "4",
        "label": '3'
    },{
        "id": "5",
        "label": '4'
    },{
        "id": "6",
        "label": lang('Other')
    }]
});