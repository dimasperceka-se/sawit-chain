/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 11-11-2020
 *  File : CoachingTask.js
 *******************************************/
Ext.define('Koltiva.store.Ext_staff.FarmerList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Ext_staff.FarmerList',
    storeId: 'Koltiva.store.Ext_staff.FarmerList',
    fields: ['StaffAssignmentMemberID', 'MemberID','MemberDisplayID', 'MemberName', 'Gender', 'Province', 'District'],
    pageSize: 20,
    autoLoad: false,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/ext_staff/farmer_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.StaffID = this.storeVar.StaffID;
            store.proxy.extraParams.StaffAssignmentID = this.storeVar.StaffAssignmentID;
            store.proxy.extraParams.textSearch = this.storeVar.textSearch;            
        }
    }
});