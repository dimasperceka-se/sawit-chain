/*
* @Author: nikolius
* @Date:   2018-06-04 15:59:55
* @Last Modified by:   nikolius
* @Last Modified time: 2018-06-28 13:55:46
*/

Ext.define('Koltiva.store.IMS.AcqProGridTrainingApproved', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridTrainingApproved',
    id: 'Koltiva.store.IMS.AcqProGridTrainingApproved',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup','PercentageAttendance','TrainingReq','AppRemark','AppBy','DateApproval'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_training_approved',
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
            store.proxy.extraParams.DateApprovalSearch = this.storeVar.DateApprovalSearch;
        }
    }
});