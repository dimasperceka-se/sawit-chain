/*
* @Author: nikolius
* @Date:   2018-03-15 16:13:04
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-16 11:00:41
*/

Ext.define('Koltiva.store.IMS.GridIMSCoaching', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridIMSCoaching',
    id: 'Koltiva.store.IMS.GridIMSCoaching',
    fields: ['FarmerID','FarmerName','Gender','NCMajor','NCMinor','NCMajorAct','NCMinorAct','FarmerGroup', 'Village', 'SubDistrict', 'District', 'Province'],
    autoLoad: false,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_ims_coaching',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.textSearch = this.storeVar.textSearch;
        }
    }
});