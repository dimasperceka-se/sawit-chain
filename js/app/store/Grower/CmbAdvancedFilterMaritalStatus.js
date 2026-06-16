/*
* @Author: nikolius
* @Date:   2017-05-17 17:09:08
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-17 17:09:57
*/
Ext.define('Koltiva.store.Grower.CmbAdvancedFilterMaritalStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbAdvancedFilterMaritalStatus',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Married')
    },{
        "id": "2",
        "label": lang('Single')
    },{
        "id": "3",
        "label": lang('Janda/Duda')
    }]
});