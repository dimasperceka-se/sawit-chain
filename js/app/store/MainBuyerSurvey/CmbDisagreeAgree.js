/*
* @Author: nikolius
* @Date:   2017-07-26 10:22:51
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-26 10:24:08
*/

Ext.define('Koltiva.store.MainBuyerSurvey.CmbDisagreeAgree', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MainBuyerSurvey.CmbDisagreeAgree',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Disagree Strongly')
    },{
        "id": "2",
        "label": lang('Disagree')
    },{
        "id": "3",
        "label": lang('Neither Agree nor Disagree')
    },{
        "id": "4",
        "label": lang('Agree')
    },{
        "id": "5",
        "label": lang('Strongly Agree')
    }]
});