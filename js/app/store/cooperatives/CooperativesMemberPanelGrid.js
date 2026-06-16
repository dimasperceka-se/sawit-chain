/*
* @Author: nikolius
* @Date:   2017-11-09 14:01:53
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-10 10:41:54
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - FarmerGroupID
*/

Ext.define('Koltiva.store.Cooperatives.CooperativesMemberPanelGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Cooperatives.CooperativesMemberPanelGrid',
    storeId: 'Koltiva.store.Cooperatives.CooperativesMemberPanelGrid',
    fields: ['CoopID','MemberDisplayID','MemberName','Village','MemberID','Enumerator'],
    autoLoad: false,
    remoteSort: true,
    pageSize: 25,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/cooperatives/coop_member_panel_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.CoopID = this.storeVar.CoopID;
        }
    }
});