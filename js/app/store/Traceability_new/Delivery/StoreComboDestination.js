Ext.define('Koltiva.store.Traceability_new.Delivery.StoreComboDestination', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.StoreComboDestination',
    id: 'Koltiva.store.Traceability_new.Delivery.StoreComboDestination',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/comboDestination',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});