/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.PolygonOver.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.PolygonOver.MainGrid',
    storeId: 'Koltiva.store.DataAdm.PolygonOver.MainGrid',
    fields: ['MemberID','MemberDisplayID','MemberName','PlotNr','Revision','StatusCheck','Function','FunctionDescription','OGR_FID','FunctionCode'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    remoteSort: true,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/polygon_over/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});