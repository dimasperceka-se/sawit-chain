Ext.define('Koltiva.store.Traceability_new.Transaction.ComboQuality', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboQuality',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboQuality',
    fields: ['ValueQualityID','Value'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/quality-value', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
		beforeload: function(store, operation, options){
           var QualityID = window.localStorage.getItem("QualityID");
		   if(QualityID != ''){
				store.proxy.extraParams.QualityID = QualityID;
		   }
        }
         
		 
    }
});



 