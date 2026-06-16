/*
* @Author: nikolius
* @Date:   2017-10-13 17:52:21
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 17:52:56
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboDhisRole', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboDhisRole',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboDhisRole',
    fields: ['id', 'name'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/app_ref_role_cmb',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});