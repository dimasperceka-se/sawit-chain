/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 14:04:26
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.MainGrid',
    storeId: 'Koltiva.store.Staff.RegisterStaff.MainGrid',
    fields: ['RegID','Email','Username','Fullname','UserRole','ObjLabel','StatusRegistered','LastUpdatedLabel'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/registers_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.Role = this.storeVar.Role;
            store.proxy.extraParams.StringNameUsername = this.storeVar.StringNameUsername;
        }
    }
});