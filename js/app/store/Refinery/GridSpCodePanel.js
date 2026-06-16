Ext.define('Koltiva.store.Refinery.GridSpCodePanel', {
    extend: 'Ext.data.Store',
    id: 'store.Refinery.GridSpCodePanel',
    storeId: 'store.Refinery.GridSpCodePanel',
    fields: ['SPCodeID','SuratNr','Note'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/grid_sp_code',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RefineryID = this.storeVar.RefineryID;
            store.proxy.extraParams.CallFrom = this.storeVar.CallFrom;
        }
    }
});