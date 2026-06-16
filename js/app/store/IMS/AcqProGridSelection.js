/*
* @Author: nikolius
* @Date:   2018-03-19 10:28:40
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-07 14:11:39
*/

Ext.define('Koltiva.store.IMS.AcqProGridSelection', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridSelection',
    id: 'Koltiva.store.IMS.AcqProGridSelection',
    fields: ['DisplayID','DestObjID','Name','Gender','District','SubDistrict','Village','FarmerGroup','Recommendation','SelectionStatus','IMSSocID','DateGenerated','ParticipateInSocialization','ParticipantType'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_selection',
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
            store.proxy.extraParams.Participate = this.storeVar.Participate;
            store.proxy.extraParams.Recommendation = this.storeVar.Recommendation;
            store.proxy.extraParams.Selection = this.storeVar.Selection;
        }
    }
});