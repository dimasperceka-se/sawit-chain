Ext.define('Koltiva.store.Traceability_new.Reception.StoreCollector', {
    extend: 'Ext.data.Store',
    storeId:'Koltiva.store.Traceability_new.Reception.StoreCollector',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/fetch_collector',
        reader: {
            type: 'json',
            root: 'data'
        } 
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.id = Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').getValue() == undefined ? null : Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').getValue();
        }
    }
});