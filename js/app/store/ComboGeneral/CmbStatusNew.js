/*
* @Author: Yusuf
* @Date:   2018-08-24 13:50:18
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-24 13:52:43
*/

Ext.define('Koltiva.store.ComboGeneral.CmbStatusNew', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbStatusNew',
    fields: ['id', 'label'],
    data: [{
        "id": 'active',
        "label": lang('Active')
    },{
        "id": "inactive",
        "label": lang("Inactive")
    }]
});