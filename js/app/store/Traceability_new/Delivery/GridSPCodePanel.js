
Ext.define('Koltiva.store.Traceability_new.Delivery.GridSPCodePanel', {
    extend: 'Ext.data.Store',
    id: 'store.Traceability_new.Delivery.GridSPCodePanel',
    storeId: 'store.Traceability_new.Delivery.GridSPCodePanel',
    fields: ['id','name'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/sp_code',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});