/*
* @Author: nikolius
* @Date:   2018-03-19 16:43:44
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-19 17:04:55
*/

Ext.define('Koltiva.store.IMS.GridDetailTrainingInfo', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridDetailTrainingInfo',
    id: 'Koltiva.store.IMS.GridDetailTrainingInfo',
    fields: ['Topic','BatchNumber','Start','End','AttendancePercentage','CpgBatchTrainingID'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_training_info_detail',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.FarmerID = this.storeVar.FarmerID;
        }
    }
});