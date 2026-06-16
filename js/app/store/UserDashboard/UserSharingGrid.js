/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 2020-11-17
 *  File : UserSharingGrid.js
 *******************************************/
Ext.define('Koltiva.store.UserDashboard.UserSharingGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserDashboard.UserSharingGrid',
    storeId: 'Koltiva.store.UserDashboard.UserSharingGrid',
    fields: ['DashSetID', 'DashID', 'UserID', 'UserName', 'UserRealName', 'GroupName', 'PositionName', 'RoleName', 'DateCreated'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/user_dashboard/user_sharing_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.DashID = this.storeVar.DashID;
            store.proxy.extraParams.KeySearch = this.storeVar.KeySearch;
        }
    }
});