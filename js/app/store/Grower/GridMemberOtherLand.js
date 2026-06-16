/*
* @Author: nikolius
* @Date:   2017-08-18 14:55:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-18 15:15:30
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.Grower.GridMemberOtherLand', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.GridMemberOtherLand',
    storeId: 'Koltiva.store.Grower.GridMemberOtherLand',
    fields: ['MemOtherID','MemberID','Commodity','GardenHa','Remark'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/grid_other_land',
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