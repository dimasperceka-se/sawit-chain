Ext.define('Koltiva.store.Traceability.Transaction.ComboDestination', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboDhisRole',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboDhisRole',
    fields: ['id', 'name'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/destination',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});