 
Ext.define('Koltiva.store.Traceability_new.Report.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.MainGrid',
    fields: ['SupplyTransID', 'SupplyBatchID', 'DateTransaction','TransNumber','FarmerID','FarmerName','Village','SubDistrict','District','Province','PlantationNr',
			  'PlotNr', 'VolumeBruto','PackageNumber','VolumeCutting','Latitude','Longitude',
              'VolumeNetto','NetPrice','TotalPayment','Agent','BuyingUnit','PartnerName','Status','SupplyBatchNumber','Destination',
            'GardenAreaHa','GardenAreaPolygon','isCertified' ],
    pageSize: 25,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Report_transaction/fetch', //Tanpa menggunakan routing PHP
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