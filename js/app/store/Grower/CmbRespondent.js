/*
* @Author: nikolius
* @Date:   2017-05-23 16:48:19
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-23 16:49:16
*/
Ext.define('Koltiva.store.Grower.CmbRespondent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbRespondent',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Registered Farmer')
    },{
        "id": "2",
        "label": lang('Family Member')
    },{
        "id": "3",
        "label": lang('Plantation Manager')
    },{
        "id": "4",
        "label": lang('Worker')
    },{
        "id": "5",
        "label": lang('Others')
    }]
});