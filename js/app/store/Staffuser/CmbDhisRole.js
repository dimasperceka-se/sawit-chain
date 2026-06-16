/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Jul 15 2020
 *  File : CmbDhisRole.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.CmbDhisRole', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.CmbDhisRole',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/cmb_dhis_role',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});