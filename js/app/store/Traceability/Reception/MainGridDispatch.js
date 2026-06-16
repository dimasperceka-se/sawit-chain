//digunakan di Form pengiriman  untuk menampung data hasil cheklist transaction di form 
Ext.define('Koltiva.store.Traceability.Reception.MainGridDispatch', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reception.MainGridDispatch',
    storeId: 'Koltiva.store.Traceability.Reception.MainGridDispatch',
    fields: ['DespatchID','DespatchNumber','CompanyName','ProductName','DespatchVolume'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/refinery/grid_dispatch_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            //store.proxy.extraParams.status = 2; 
        }
    }
});