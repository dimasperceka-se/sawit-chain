/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : ComboWorkareaDistrict.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.ComboWorkareaDistrict', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboWorkareaDistrict',
    id: 'Koltiva.store.ComboGeneral.ComboWorkareaDistrict',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/workarea',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.prov = this.storeVar.prov;
        }
    }
});