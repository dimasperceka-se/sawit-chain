/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jan 28 2019
 *  File : ImsCflTakeOutFarmerGrid.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsCflTakeOutFarmerGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsCflTakeOutFarmerGrid',
    id: 'Koltiva.store.IMS.ImsCflTakeOutFarmerGrid',
    fields: ['FarmerID','FarmerName','FarmerGroup','Village','CertFirstYear','ICSDate','CertNextHarvest','CertHarvest','SalesQuota','CertHectare','CertFarmNr','TotalCocoaFarm','TotalHa'],
    autoLoad: true,
    pageSize: 50,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/ims_cfl_takeout_farmer_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.SearchStringParam = this.storeVar.SearchStringParam;
            store.proxy.extraParams.SearchCpgParam = this.storeVar.SearchCpgParam;
        }
    }
});