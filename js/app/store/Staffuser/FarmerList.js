/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 11-11-2020
 *  File : CoachingTask.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.FarmerList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.FarmerList',
    storeId: 'Koltiva.store.Staffuser.FarmerList',
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
        url: m_api + '/staffuser/farmer_list',
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
        }
    }
});