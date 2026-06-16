/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon May 06 2019
 *  File : PanelTphCollectiveMemberGrid.js
 *******************************************/

Ext.define('Koltiva.store.Tph.PanelTphCollectiveMemberGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Tph.PanelTphCollectiveMemberGrid',
    fields: ['CollectpointID','MemberID','MemberDisplayID','MemberName','Age','Village'],
    autoLoad: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tph/collective_member_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.CollectpointID = this.storeVar.CollectpointID;
        }
    }
});