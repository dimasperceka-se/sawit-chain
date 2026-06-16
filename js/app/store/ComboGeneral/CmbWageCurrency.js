/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jan 14 2019
 *  File : CmbWageCurrency.js
 *******************************************/

Ext.define('Koltiva.store.ComboGeneral.CmbWageCurrency', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbWageCurrency',
    id: 'Koltiva.store.ComboGeneral.CmbWageCurrency',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_wage_currency',
        reader: {
            type: 'json'
        }
    }
});