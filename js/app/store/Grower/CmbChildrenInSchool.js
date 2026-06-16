/*
* @Author: nikolius
* @Date:   2017-07-25 14:46:01
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 13:20:00
*/

Ext.define('Koltiva.store.Grower.CmbChildrenInSchool', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbChildrenInSchool',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('All attending school')
    },{
        "id": "2",
        "label": lang('Some attending school')
    },{
        "id": "3",
        "label": lang('All not attending school')
    }]
});