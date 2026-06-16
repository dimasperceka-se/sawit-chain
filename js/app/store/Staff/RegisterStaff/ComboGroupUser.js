/*
* @Author: nikolius
* @Date:   2017-10-13 16:45:57
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 16:52:53
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboGroupUser', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboGroupUser',
    storeId: 'Koltiva.store.Staff.RegisterStaff.ComboGroupUser',
    fields: ['GroupId', 'GroupName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/system/grouplist',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});