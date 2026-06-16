Ext.define('Koltiva.store.Traceability.Transaction.ComboSupplyID', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Transaction.ComboSupplyID',
    storeId: 'Koltiva.store.Traceability.Transaction.ComboSupplyID',
    fields: [
        {name: 'id', mapping: 'id'},
        {name: 'displayid', mapping: 'displayid'},
        {name: 'name', mapping: 'name'},
        {name: 'noktp', mapping: 'noktp'},
        {name: 'village', mapping: 'village'},
        {name: 'subdistrict', mapping: 'subdistrict'},
        {name: 'district', mapping: 'district'},
        {name: 'handphone', mapping: 'handphone'},
        {name: 'gender', mapping: 'gender'},
        {name: 'groupname', mapping: 'groupname'}
    ],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/supplyid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
        beforeload: function (storeComboSupplyID, operation) {
            storeComboSupplyID.proxy.extraParams.tipe = Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyType').getValue()
        }
    }
});