/*
* @Author: nikolius
* @Date:   2017-09-14 14:12:27
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-14 14:15:47
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.Grower.GridMemberLabour', {
    extend: 'Ext.data.Store',
    id: 'store.Grower.GridMemberLabour',
    storeId: 'store.Grower.GridMemberLabour',
    fields: ['LaboID','MemberID','LaboName','Age','Gender','WageAmount','WagePeriod'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/grid_labour',
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