 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.MainGrid',
    fields: ['SupplychainID', 'ObjType', 'ObjID','Name','PartnerName','rel','quality','package','quality_value','AccessBy','ProductionCapacity','WorkHour'],
    pageSize: 25,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_org/fetch', //Tanpa menggunakan routing PHP
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners: {
		'beforeload': function(store, options) {
			store.proxy.extraParams.ObjType = Ext.getCmp('sObjType').getValue();
			store.proxy.extraParams.Name = Ext.getCmp('sName').getValue();
		}
    }
});