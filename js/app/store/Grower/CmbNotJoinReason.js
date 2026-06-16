/*
* @Author: nikolius
* @Date:   2017-05-23 16:48:19
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 16:49:16
*/
Ext.define('Koltiva.store.Grower.CmbNotJoinReason', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbNotJoinReason',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Farmer Does Not Sell to The Certified Buyers')
    },{
        "id": "2",
        "label": lang('Farmer is Not Willing to Get Audited')
    },{
        "id": "3",
        "label": lang('Farmer Wants to Resign')
    },{
        "id": "4",
        "label": lang('Others')
    }]
});