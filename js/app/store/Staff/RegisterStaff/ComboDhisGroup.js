/*
* @Author: nikolius
* @Date:   2017-10-13 17:54:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 17:54:36
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboDhisGroup', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboDhisGroup',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboDhisGroup',
    fields: ['id', 'name'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/app_ref_group_cmb',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});