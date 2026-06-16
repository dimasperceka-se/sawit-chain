/*
* @Author: nikolius
* @Date:   2017-06-01 13:59:14
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-25 16:19:41
*/

Ext.define('Koltiva.store.MainBuyerSurvey.CmbBuyerType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MainBuyerSurvey.CmbBuyerType',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Palm Oil Mill')
    },{
        "id": "2",
        "label": lang('Supplier/SPK/SPB/DO')
    },{
        "id": "3",
        "label": lang('Middleman/Agent')
    },{
        "id": "4",
        "label": lang('Loading Ramp')
    },{
        "id": "5",
        "label": lang('Cooperative')
    }]
});