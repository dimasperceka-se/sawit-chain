 
/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.SME.GridTraderNurseery', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridTraderNurseery',
    storeId: 'Koltiva.store.SME.GridTraderNurseery',
    fields: ['VehID','BrandName','VehName','VehPoliceNr','VehCapacity','Driver'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        //url: m_api + '/sme/grid_trader_vehicle',
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