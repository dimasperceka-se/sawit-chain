/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

Ext.define('Koltiva.store.FarmCloud.MessagesManagementGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmCloud.MessagesManagementGrid',
    storeId: 'Koltiva.store.FarmCloud.MessagesManagementGrid',
    fields: ['MessagesID','Title','Content','StatusType','CreatedBy','LastUpdated'],
    pageSize: 10,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_grid_mains,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {        
        beforeload: function(store, operation, options){
            //store.proxy.extraParams.TopID = this.storeVar.TopID;
        }
    }
});