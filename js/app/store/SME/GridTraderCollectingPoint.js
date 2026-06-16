/*
* @Author: nikolius
* @Date:   2017-09-07 14:13:59
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-07 16:43:08
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.SME.GridTraderCollectingPoint', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridTraderCollectingPoint',
    storeId: 'Koltiva.store.SME.GridTraderCollectingPoint',
    fields: ['CollectpointID','CollectpointDisplayID','Name','OrgIDLabel','SubDistrict','Village','Latitude','Longitude','LastUpdated'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/tph/grid_trader_tph',
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