Ext.define('Koltiva.store.Traceability_new.Transaction.PalmoilType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction.PalmoilType',
    id: 'Koltiva.store.Traceability_new.Transaction.PalmoilType',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/transaction/palmoil_type',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, options) {
            store.proxy.extraParams.SupplyTransID = this.storeVar.SupplyTransID;
        }
    }
});