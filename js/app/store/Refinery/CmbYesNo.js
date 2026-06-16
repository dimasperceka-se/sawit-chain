Ext.define('Koltiva.store.Refinery.CmbYesNo', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbYesNo',
    fields: ['id', 'label'],
    data: [{
        "id": "Yes",
        "label": lang('Yes')
    },{
        "id": "No",
        "label": lang('No')
    }]
});