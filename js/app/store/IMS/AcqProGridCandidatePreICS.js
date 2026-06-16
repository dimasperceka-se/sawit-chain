/*
* @Author: Nikolius Lau
* @Date:   2018-08-27 13:48:44
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-27 13:50:34
*/

Ext.define('Koltiva.store.IMS.AcqProGridCandidatePreICS', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridCandidatePreICS',
    id: 'Koltiva.store.IMS.AcqProGridCandidatePreICS',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup','TrainingPercentage','StatusComply','AuditRemark'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_candidate_preics',
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