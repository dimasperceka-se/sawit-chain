/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : WinFormAddPolygonCompareMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.PolygonOver.WinFormAddPolygonCompareMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.PolygonOver.WinFormAddPolygonCompareMainGrid',
    fields: ['SupplierID','ID','Name','FarmNr','Revision','StatusCheck','DateCreated'],
    pageSize: 20,
    autoLoad: true,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/polygon_over/add_polygon_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.UserId = this.storeVar.UserId;
            store.proxy.extraParams.TxtSearchLabel = this.storeVar.TxtSearchLabel;
            store.proxy.extraParams.CmbStatusCheck = this.storeVar.CmbStatusCheck;
        }
    }
});