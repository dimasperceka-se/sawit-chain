/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : ComboStaffPosition.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.ComboStaffPosition', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboStaffPosition',
    id: 'Koltiva.store.ComboGeneral.ComboStaffPosition',
    fields: ['id', 'label'],
    storeVar: null,
    autoLoad: false,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/position',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.ObjType = this.storeVar.ObjType;
        }
    }
});