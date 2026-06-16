/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jul 14 2020
 *  File : ComboAccessStaff.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.ComboAccessStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.ComboAccessStaff',
    fields: ['id', 'name'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/combo_access_staff',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});