/*
* @Author: nikolius
* @Date:   2018-03-16 11:31:31
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-07 13:57:34
*/

Ext.define('Koltiva.store.IMS.AcqProGridSocialization', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.AcqProGridSocialization',
    id: 'Koltiva.store.IMS.AcqProGridSocialization',
    fields: ['DisplayID','DestObjID','Name','Gender','SubDistrict','Village','FarmerGroup','SocEventName','DateOfSocialization','DateGenerated','IMSSocID'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/acq_pro_grid_socialization',
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