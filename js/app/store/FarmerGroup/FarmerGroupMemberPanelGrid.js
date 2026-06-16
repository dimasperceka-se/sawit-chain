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

Ext.define('Koltiva.store.FarmerGroup.FarmerGroupMemberPanelGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerGroup.FarmerGroupMemberPanelGrid',
    storeId: 'Koltiva.store.FarmerGroup.FarmerGroupMemberPanelGrid',
    fields: ['FarmerGroupID','MemberDisplayID','MemberName','Village','MemberID','Enumerator'],
    autoLoad: false,
    remoteSort: true,
    pageSize: 25,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer_group/farmer_group_member_panel_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID;
        }
    }
});