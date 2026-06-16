Ext.define('Koltiva.store.Traceability_new.Report.comboAgent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.comboAgent',
    storeId: 'Koltiva.store.Traceability_new.Report.comboAgent',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Report_transaction/ComboAgent',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            //store.proxy.extraParams.SupplyChainID = this.storeVar.SupplyChainID;
        }
    }
});