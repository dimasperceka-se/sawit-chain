 

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.SME.GridTraderWarehouses', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridTraderWarehouses',
    storeId: 'Koltiva.store.SME.GridTraderWarehouses',
    fields: ['WarehousesNr','MemberID','Photo', 'Warehousetype', 'Latitude','Longitude'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/grid_trader_warehouses',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});