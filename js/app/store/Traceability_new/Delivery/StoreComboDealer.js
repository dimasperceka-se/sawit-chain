Ext.define('Koltiva.store.Traceability_new.Delivery.StoreComboDealer', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.StoreComboDealer',
    id: 'Koltiva.store.Traceability_new.Delivery.StoreComboDealer',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/comboDealer',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});