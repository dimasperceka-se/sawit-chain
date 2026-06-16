/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 25 2019
 *  File : GridExternalAuditInput.js
 *******************************************/
Ext.define('Koltiva.store.IMS.GridExternalAuditInput', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridExternalAuditInput',
    id: 'Koltiva.store.IMS.GridExternalAuditInput',
    fields: ['FarmerID','FarmerName','FarmerGroup','Village','AFLStatus','CertFirstYear','ICSDate','CertNextHarvest','CertHarvest','CertHectare','CertFarmNr','TotalCocoaFarm','TotalHa'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/external_audit_input_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.TextSearch = this.storeVar.TextSearch;
        }
    }
});