/*
* @Author: nikolius
* @Date:   2018-04-04 16:37:04
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-04 16:37:32
*/

Ext.define('Koltiva.store.MainBuyerSurvey.CmbDisagreeAgreeOnly', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MainBuyerSurvey.CmbDisagreeAgreeOnly',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Agree')
    },{
        "id": "2",
        "label": lang('Disagree')
    }]
});