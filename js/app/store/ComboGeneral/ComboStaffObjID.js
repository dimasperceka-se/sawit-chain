/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : ComboStaffObjID.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.ComboStaffObjID', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboStaffObjID',
    id: 'Koltiva.store.ComboGeneral.ComboStaffObjID',
    storeVar: null,
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic_staff/objectid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.ObjType = this.storeVar.ObjType;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
        }
    }
});