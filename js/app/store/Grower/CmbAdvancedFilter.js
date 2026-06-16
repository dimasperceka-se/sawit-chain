/*
* @Author: nikolius
* @Date:   2017-05-17 16:09:44
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-29 16:15:58
*/

Ext.define('Koltiva.store.Grower.CmbAdvancedFilter', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbAdvancedFilter',
    fields: ['id', 'label'],
    data: [{
        "id": "Handphone",
        "label": lang('Handphone')
    },{
        "id": "Age",
        "label": lang('Age')
    },{
        "id": "MaritalStatus",
        "label": lang('Marital Status')
    },{
        "id": "DateCollection",
        "label": lang('Date Collection')
    },{
        "id": "DateCreated",
        "label": lang('Date Created')
    },{
        "id": "DateSynced",
        "label": lang('Date Synced')
    },{
        "id": "LastUpdatedDate",
        "label": lang('Last Updated Date')
    },{
        "id": "Enumerator",
        "label": lang('Enumerator')
    }]
});