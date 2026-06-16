/*
* @Author: nikolius
* @Date:   2018-03-19 14:53:17
* @Last Modified by:   nikolius
* @Last Modified time: 2018-06-04 15:01:18
*/

Ext.define('Koltiva.store.IMS.AcqProGridTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridTraining',
    id: 'Koltiva.store.IMS.AcqProGridTraining',
    fields: ['ApplicantID','FarmerID','FarmerName','Gender','Province','District','SubDistrict','Village','FarmerGroup','PercentageAttendance','TrainingReq','DateGenerated','EligibleStatus'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_training',
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