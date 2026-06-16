/*
* @Author: nikolius
* @Date:   2017-08-04 16:15:17
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 16:17:20
*/

Ext.define('Koltiva.store.MainBuyerSurvey.CmbConditionState', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MainBuyerSurvey.CmbConditionState',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Worse')
    },{
        "id": "2",
        "label": lang('Same')
    },{
        "id": "3",
        "label": lang('Better')
    }]
});