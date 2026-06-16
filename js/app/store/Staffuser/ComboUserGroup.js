/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jul 14 2020
 *  File : ComboUserGroup.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.ComboUserGroup', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.ComboUserGroup',
    fields: ['GroupId', 'GroupName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/system/grouplist',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});