/*
* @Author: nikolius
* @Date:   2018-06-07 13:55:30
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-07 14:17:09
*/

Ext.define('Koltiva.store.IMS.AcqProGridSelectionApproved', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridSelectionApproved',
    id: 'Koltiva.store.IMS.AcqProGridSelectionApproved',
    fields: ['DisplayID','DestObjID','Name','Gender','District','SubDistrict','Village','FarmerGroup','ApprovalRemark','ApprovalBy','IMSSocID','DateApproval','ParticipantType'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_selection_approved',
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