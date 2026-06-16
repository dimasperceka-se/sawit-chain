Ext.define('Koltiva.store.Traceability_new.Transaction_neo.Farmers', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.Farmers',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.Farmers',
    fields: ['MemberID','MemberDisplayID','MemberName','MemberNames','District','SubDistrict','Village','GroupName','CertProgName','FarmerCategory'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url : m_api + '/web-traceability/new-farmer', 
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});