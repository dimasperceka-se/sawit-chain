/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 05 2018
 *  File : ImsAssetRcpGridFarmerAPDFarmerSearch.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerAPDFarmerSearch', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerAPDFarmerSearch',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerAPDFarmerSearch',
    pageSize: 10,
    fields: [{
        name: 'FarmerID',
        mapping: 'FarmerID'
    }, {
        name: 'FarmerName',
        mapping: 'FarmerName'
    }, {
        name: 'FarmerGroup',
        mapping: 'FarmerGroup'
    }, {
        name: 'DisplayField',
        mapping: 'DisplayField'
    }],
    proxy: {
        type: 'ajax',
        url: m_crud + '_farmers',
        url: m_api + '/ims_asset_rcp/farmer_apd_farmer_search',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
        }
    }
});