/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : MainGrid.js
 *******************************************/

Ext.define('Koltiva.store.Coaching.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Coaching.MainGrid',
    storeId: 'Koltiva.store.Coaching.MainGrid',
    fields: ['CoachingID', 'SupplierID', 'UserID', 'PersonNm', 'MemberDisplayID','CoachingRecipient', 'CoachingRecipientName', 'CoachingDate', 'FarmerName','GroupName','sesi'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function (store, records, success) {
            if (success == true) {
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function (data) {
                        document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        sort: function (store, records, success) {
            if (success == true) {
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function (data) {
                        document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.KeySearch = this.storeVar.KeySearch;
            store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID;
            store.proxy.extraParams.StartDate = this.storeVar.StartDate;
            store.proxy.extraParams.EndDate = this.storeVar.EndDate;
        }
    }
});