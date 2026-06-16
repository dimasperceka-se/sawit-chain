Ext.define('Koltiva.store.Traceability_new.Transaction_neo.ComboTransType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.ComboTransType',
    fields: ['id','label'],
    data: [{
        "id": "1",
        "label": lang('Farmer')
    },{
        "id": "2",
        "label": lang('Batch')
    },{
        "id": "3",
        "label": lang('Nonfarmer')
    },{
        "id": "4",
        "label": lang('Delivery')
    }],
});