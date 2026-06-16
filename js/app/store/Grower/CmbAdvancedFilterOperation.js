/*
* @Author: nikolius
* @Date:   2017-05-17 16:38:10
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-17 16:39:11
*/
Ext.define('Koltiva.store.Grower.CmbAdvancedFilterOperation', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbAdvancedFilterOperation',
    fields: ['id', 'label'],
    data: [{
        "id": "=",
        "label": "="
    }, {
        "id": "!=",
        "label": "!="
    }, {
        "id": ">=",
        "label": ">="
    }, {
        "id": "<=",
        "label": "<="
    }]
});