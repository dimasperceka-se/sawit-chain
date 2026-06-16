/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Fri Mar 13 2020
 *  File : WinFormSelectStaffMultipleMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.UserDashboard.WinFormSelectStaffMultipleMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserDashboard.WinFormSelectStaffMultipleMainGrid',
    fields: ['UserID', 'UserRealName', 'UserName', 'GroupName', 'PositionName', 'RoleName'],
    pageSize: 20,
    autoLoad: true,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/user_dashboard/select_staff_multiple_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.DashID = this.storeVar.DashID;
            store.proxy.extraParams.TxtSearchLabel = this.storeVar.TxtSearchLabel;
            store.proxy.extraParams.CmbGroup = this.storeVar.CmbGroup;
        }
    }
});