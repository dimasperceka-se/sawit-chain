Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSPB', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSPB',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSPB',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/spbcode',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});