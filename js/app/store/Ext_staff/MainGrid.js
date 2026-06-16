/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Jan 17 2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.store.Ext_staff.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Ext_staff.MainGrid',
    storeId: 'Koltiva.store.Ext_staff.MainGrid',
    fields: ['PersonID','StaffID','PersonNm','UserName','Province','District','StaffPositionLabel','UserCreated','ReferenceStaff','ModifiedBy','Gender','UserRole'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/ext_staff/grid_main_ext_staff',
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
        }
    }
});