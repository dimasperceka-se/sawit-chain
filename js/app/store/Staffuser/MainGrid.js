/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.MainGrid',
    storeId: 'Koltiva.store.Staffuser.MainGrid',
    fields: ['PersonID','StaffID','ObjType','PersonNm','Gender','Country','Role','Status','Email','UserName','AccountGroup','AccountActive','AccountCognito'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
						document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        sort: function(store, records, success){
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
						document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            store.proxy.extraParams.KeySearch = this.storeVar.KeySearch;
            store.proxy.extraParams.CmbSearchRole = this.storeVar.CmbSearchRole;
        }
    }
});