/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : CmbProvinceAccess.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.CmbProvinceAccess', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbProvinceAccess',
    id: 'Koltiva.store.ComboGeneral.CmbProvinceAccess',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_propinsi_access',
        reader: {
            type: 'json'
        }
    }
});