Ext.define('Koltiva.store.Dboard.CmbAdvancedFilter', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Dboard.CmbAdvancedFilter',
    fields: ['id', 'label'],
    data: [{
        "id": "DateTransaction",
        "label": lang('Date Transaction')
    }]
});