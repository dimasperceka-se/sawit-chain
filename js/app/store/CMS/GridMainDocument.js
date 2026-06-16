/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 17 2018
 *  File : GridMainDocument.js
 *******************************************/

Ext.define('Koltiva.store.CMS.GridMainDocument', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.CMS.GridMainDocument',
    storeId: 'Koltiva.store.CMS.GridMainDocument',
    fields: ['DocID','Name','Description','PostedBy','LastUpdated','StatusType'],
    pageSize: 20,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/cms/grid_main_document',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {        
        beforeload: function(store, operation, options){
            //store.proxy.extraParams.TopID = this.storeVar.TopID;
        }
    }
});