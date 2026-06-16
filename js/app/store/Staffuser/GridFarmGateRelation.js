/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed May 27 2020
 *  File : GridFarmGateRelation.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.GridFarmGateRelation', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Staffuser.GridFarmGateRelation',
    id: 'Koltiva.store.Staffuser.GridFarmGateRelation',
    fields: ['Type','IPAddress','Timestamp','Remark'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/grid_log_user_login',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.PersonID = this.storeVar.PersonID;
        }
    }
});