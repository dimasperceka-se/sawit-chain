Ext.define('Koltiva.store.Traceability_new.Transaction.ComboDestinationDO', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboDestinationDO',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboDestinationDO',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/get-do',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});