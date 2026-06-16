/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Jul 15 2020
 *  File : CmbDhisGroup.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.CmbDhisGroup', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.CmbDhisGroup',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/cmb_dhis_group',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});