/*
* @Author: nikolius
* @Date:   2017-11-09 16:09:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-09 18:33:50
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - FarmerGroupID
*/

Ext.define('Koltiva.store.Cooperatives.CooperativesMemberInputGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Cooperatives.CooperativesMemberInputGrid',
    storeId: 'Koltiva.store.Cooperatives.CooperativesMemberInputGrid',
    fields: ['MemberID','MemberDisplayID','MemberName','SubDistrict','Village','Enumerator'],
    autoLoad: false,
    remoteSort: true,
    pageSize: 30,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/cooperatives/coop_member_input_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            console.log(this.storeVar);
            store.proxy.extraParams.CoopID = this.storeVar.CoopID;
            store.proxy.extraParams.textSearch = this.storeVar.textSearch;
            store.proxy.extraParams.villageSearch = this.storeVar.villageSearch;
            store.proxy.extraParams.Enumerator = this.storeVar.Enumerator;
        }
    }
});