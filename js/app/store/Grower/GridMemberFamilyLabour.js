/*
* @Author: nikolius
* @Date:   2017-05-25 14:54:58
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-04 14:10:01
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.Grower.GridMemberFamilyLabour', {
    extend: 'Ext.data.Store',
    id: 'store.Grower.GridMemberFamilyLabour',
    storeId: 'store.Grower.GridMemberFamilyLabour',
    fields: ['FamLabID','MemberID','FamLabName','FamLabRelation','Age','Gender'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/grid_family_labour',
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