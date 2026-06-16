/*
* @Author: nikolius
* @Date:   2017-10-13 17:33:06
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 17:34:04
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboAccessArea', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboAccessArea',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboAccessArea',
    fields: ['id', 'name'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer/access_staffs',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});