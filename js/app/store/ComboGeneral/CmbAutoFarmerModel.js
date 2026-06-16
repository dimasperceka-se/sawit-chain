/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Feb 12 2020
 *  File : CmbAutoFarmerModel.js
 *******************************************/
Ext.define("Koltiva.store.ComboGeneral.CmbAutoFarmerModel", {
    extend: 'Ext.data.Model',
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_auto_farmer',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    fields: [{
        name: 'id',
        mapping: 'id'
    }, {
        name: 'label',
        mapping: 'label'
    }, {
        name: 'name',
        mapping: 'name'
    }, {
        name: 'displayid',
        mapping: 'displayid'
    }]
});