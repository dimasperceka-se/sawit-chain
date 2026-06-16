/*
* @Author: nikolius
* @Date:   2017-06-01 14:06:58
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-25 16:21:52
*/

Ext.define('Koltiva.store.MainBuyerSurvey.CmbTransportMode', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MainBuyerSurvey.CmbTransportMode',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Transported by buyer')
    },{
        "id": "2",
        "label": lang('Transported by rented vehicle, respondent chooses buyer')
    },{
        "id": "3",
        "label": lang('Transported by rented vehicle, driver chooses buyer')
    },{
        "id": "4",
        "label": lang('Use own vehicle')
    }]
});