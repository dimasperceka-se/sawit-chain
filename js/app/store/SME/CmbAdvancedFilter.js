/*
* @Author: nikolius
* @Date:   2017-07-19 13:41:26
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-19 13:42:01
*/

Ext.define('Koltiva.store.SME.CmbAdvancedFilter', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbAdvancedFilter',
    fields: ['id', 'label'],
    data: [{
        "id": "Handphone",
        "label": lang('Handphone')
    },{
        "id": "Age",
        "label": lang('Age')
    }]
});