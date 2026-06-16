/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : ComboWorkareaProvince.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.ComboWorkareaProvince', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboWorkareaProvince',
    id: 'Koltiva.store.ComboGeneral.ComboWorkareaProvince',
    fields: ['id', 'label','PhoneCode'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/propinsi',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});