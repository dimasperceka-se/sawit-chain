/*
* @Author: nikolius
* @Date:   2018-03-15 16:13:04
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-16 11:00:41
*/

Ext.define('Koltiva.store.IMS.AcqProGridFarmerIdentification', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridFarmerIdentification',
    id: 'Koltiva.store.IMS.AcqProGridFarmerIdentification',
    fields: ['ApplicantID','DisplayID','ApplicantName','Gender','District','SubDistrict','Village','FarmerGroup','ApplicantStatus'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_farmer_identification',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.StringSearch = this.storeVar.StringSearch;
        }
    }
});