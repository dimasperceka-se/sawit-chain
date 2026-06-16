/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Feb 12 2020
 *  File : CmbAutoStaffModel.js
 *******************************************/
 Ext.define("Koltiva.store.ComboGeneral.CmbAutoStaff", {
	extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbAutoStaff',
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_auto_staff',
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
        name: 'partner',
        mapping: 'partner'
    }]
});