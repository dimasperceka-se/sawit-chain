/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : GridMainAnnouncement.js
 *******************************************/

Ext.define('Koltiva.store.CMS.GridMainAnnouncement', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.CMS.GridMainAnnouncement',
    storeId: 'Koltiva.store.CMS.GridMainAnnouncement',
    fields: ['AnnID','Title','Content','StatusType','CreatedBy','LastUpdated'],
    pageSize: 10,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/cms/grid_main_announcement',
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