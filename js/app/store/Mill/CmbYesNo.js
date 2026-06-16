/*
* @Author: nikolius
* @Date:   2017-08-04 11:22:23
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 11:23:34
*/

Ext.define('Koltiva.store.Mill.CmbYesNo', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.CmbYesNo',
    fields: ['id', 'label'],
    data: [{
        "id": "Yes",
        "label": lang('Yes')
    },{
        "id": "No",
        "label": lang('No')
    }]
});