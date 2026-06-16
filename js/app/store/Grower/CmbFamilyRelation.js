/*
* @Author: nikolius
* @Date:   2017-05-25 17:12:43
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-14 13:45:53
*/

Ext.define('Koltiva.store.Grower.CmbFamilyRelation', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbFamilyRelation',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Spouse')
    },{
        "id": "2",
        "label": lang('Child')
    },{
        "id": "3",
        "label": lang('Parent')
    },{
        "id": "4",
        "label": lang('Other')
    }]
});