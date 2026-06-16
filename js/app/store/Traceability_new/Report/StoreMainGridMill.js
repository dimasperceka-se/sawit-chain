 
Ext.define('Koltiva.store.Traceability_new.Report.StoreMainGridMill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.StoreMainGridMill',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.StoreMainGridMill',
    fields: ['SupplyTransID', 'DateTransaction','TransNumber','VolumeBruto',
              'VolumeNetto','BatchFrom','Status','SupplyBatchNumber','DestDriver',
            'DestPO' ],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Report_transaction_mill/fetch', //Tanpa menggunakan routing PHP
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners: {
		'beforeload': function(store, options) {
			//store.proxy.extraParams.ObjType = Ext.getCmp('sObjType').getValue();
			//store.proxy.extraParams.Name = Ext.getCmp('sName').getValue();
		}
    }
});