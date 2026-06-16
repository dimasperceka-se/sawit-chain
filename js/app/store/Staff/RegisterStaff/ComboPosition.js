/*
* @Author: nikolius
* @Date:   2017-10-13 16:30:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 16:31:27
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboPosition', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboPosition',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboPosition',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/position',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ObjType = this.storeVar.ObjType;
        }
    }
});