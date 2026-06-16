/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : WinGridPolygonCompareMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.PolygonOver.WinGridPolygonCompareMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.PolygonOver.WinGridPolygonCompareMainGrid',
    fields: ['SupplierID','ID','Name','FarmNr','Revision','StatusCheck','SupplierIDOver','IDOver','NameOver','FarmNrOver','RevisionOver','StatusCheckOver'],
    pageSize: 20,
    autoLoad: true,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/polygon_over/polygon_compare_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.UserId = this.storeVar.UserId;
        }
    }
});