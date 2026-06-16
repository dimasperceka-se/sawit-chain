/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 17-03-2020
 *  File : GridFormFarmerCandidate.js
 *******************************************/
Ext.define('Koltiva.store.IMS.GridFormFarmerCandidate', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.IMS.GridFormFarmerCandidate',
    fields: ['FarmerID','FarmerName','Gender','Age','Province','District','SubDistrict','Village'],
    pageSize: 20,
    autoLoad: false,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_form_farmer_candidate',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.TxtSearchLabel = this.storeVar.TxtSearchLabel;
            store.proxy.extraParams.CmbFilterProvince = this.storeVar.CmbFilterProvince;
            store.proxy.extraParams.CmbFilterDistrict = this.storeVar.CmbFilterDistrict;
            store.proxy.extraParams.CmbFilterSubDistrict = this.storeVar.CmbFilterSubDistrict;
        }
    }
});