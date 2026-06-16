Ext.define('Koltiva.store.Traceability_new.Transaction.ComboFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboFarmer',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboFarmer',
    fields: ['MemberID','MemberDisplayID','MemberName','MemberNames','District','SubDistrict','Village','GroupName','CertProgName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    }, 
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/farmer', 
		reader: {
            type: 'json',  
            root: 'data',
			totalProperty: 'total'
        }
    },
    pageSize: 10, 
    listeners: {
        beforeload: function (storeComboFarmer, operation) {
            storeComboFarmer.proxy.extraParams.PID = m_pid;
			storeComboFarmer.proxy.extraParams.SID = m_sid;
        }
    }
});
 