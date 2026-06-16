/*
* @Author: nikolius
* @Date:   2017-10-10 11:09:14
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-10 11:10:14
*/
Ext.define('Koltiva.store.ComboGeneral.CmbMemberType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbMemberType',
    fields: ['id', 'label'],
    data: [{
        "id": "Farmer",
        "label": lang('Farmer')
    },{
        "id": "Agent",
        "label": lang('Agent')
    }]
});