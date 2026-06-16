 

Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboRefineryjID', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboRefineryjID',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboRefineryjID',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_org/objectid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.SupplyChainID = this.storeVar.SupplyChainID;
			store.proxy.extraParams.ObjType = this.storeVar.ObjType;
			store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
        }
    }
});