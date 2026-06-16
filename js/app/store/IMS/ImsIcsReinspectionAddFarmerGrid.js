/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jan 29 2019
 *  File : ImsIcsReinspectionAddFarmerGrid.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsIcsReinspectionAddFarmerGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsIcsReinspectionAddFarmerGrid',
    id: 'Koltiva.store.IMS.ImsIcsReinspectionAddFarmerGrid',
    fields: ['FarmerID','FarmerName','FarmerGroup','Village','AFLStatus','CertGardenNr','ICSDate','CertNextHarvest','CertHarvest','CertHectare'],
    autoLoad: true,
    pageSize: 50,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/ims_ics_reinspection_add_farmer_list',
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