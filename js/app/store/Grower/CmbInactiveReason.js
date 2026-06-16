/*
* @Author: nikolius
* @Date:   2017-05-23 16:48:19
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 16:49:16
*/
Ext.define('Koltiva.store.Grower.CmbInactiveReason', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbInactiveReason',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Died')
    },{
        "id": "2",
        "label": lang('Moved/Left The Area')
    },{
        "id": "3",
        "label": lang('Stopped Farming')
    },{
        "id": "4",
        "label": lang('Duplicate ID')
    },{
        "id": "5",
        "label": lang('Others')
    }]
});