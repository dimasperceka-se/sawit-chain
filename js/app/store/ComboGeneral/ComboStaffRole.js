/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : ComboStaffRole.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.ComboStaffRole', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboStaffRole',
    id: 'Koltiva.store.ComboGeneral.ComboStaffRole',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/objtype_list',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});