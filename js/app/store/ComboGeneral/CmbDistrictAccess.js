/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : CmbDistrictAccess.js
 *******************************************/
/*
    Store ini memerlukan parameter
    * ProvinceID
*/

Ext.define('Koltiva.store.ComboGeneral.CmbDistrictAccess', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbDistrictAccess',
    id: 'Koltiva.store.ComboGeneral.CmbDistrictAccess',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_district_access',
        reader: {
            type: 'json'
        }
    }
});