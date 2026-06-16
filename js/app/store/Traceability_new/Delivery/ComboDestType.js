Ext.define('Koltiva.store.Traceability_new.Delivery.ComboDestType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.ComboDestType',
    id: 'Koltiva.store.Traceability_new.Delivery.ComboDestType',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/comboDestType',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});