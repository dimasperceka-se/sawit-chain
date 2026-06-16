/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Jan 17 2020
 *  File : CmbFilterProvince.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.CmbFilterProvince', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbFilterProvince',
    id: 'Koltiva.store.ComboGeneral.CmbFilterProvince',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_filter_province',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.CountryID = this.storeVar.CountryID;
        }
    }
});