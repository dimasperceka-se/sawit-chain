 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_quality_value.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_quality_value.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_quality_value.MainGrid',
    fields: ['ValueQualityID', 'QualityID', 'Name', 'Value', 'is_default', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_quality_value/fetch/',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
           store.proxy.extraParams.QualityID = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality_value.MainGrid-QualityID').getValue();      
        }
	}
});