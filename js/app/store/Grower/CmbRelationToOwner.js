/*
* @Author: nikolius
* @Date:   2017-07-25 14:08:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-25 14:09:22
*/

Ext.define('Koltiva.store.Grower.CmbRelationToOwner', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbRelationToOwner',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Family Member')
    },{
        "id": "2",
        "label": lang('Labor')
    },{
        "id": "3",
        "label": lang('Foreman')
    },{
        "id": "4",
        "label": lang('Other')
    }]
});