/*
* @Author: nikolius
* @Date:   2017-10-13 16:12:15
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 16:17:19
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboStaffObjID', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboStaffObjID',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboStaffObjID',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/objectid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ObjType = this.storeVar.ObjType;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
        }
    }
});