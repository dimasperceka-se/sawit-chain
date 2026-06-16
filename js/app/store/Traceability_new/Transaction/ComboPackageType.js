Ext.define('Koltiva.store.Traceability_new.Transaction.ComboPackageType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboPackageType',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboPackageType',
    fields: ['PackageID','PackageType','PackageWeight','PackageCapacity'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/package-type', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
        beforeload: function (storeComboPackageType, operation) {
            storeComboPackageType.proxy.extraParams.SID = m_sid
        }
    }
});
 