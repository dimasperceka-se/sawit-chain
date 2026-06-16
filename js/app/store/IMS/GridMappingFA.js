/*
* @Author: nikolius
* @Date:   2018-04-19 14:34:28
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-19 16:02:44
*/
Ext.define('Koltiva.store.IMS.GridMappingFA', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridMappingFA',
    id: 'Koltiva.store.IMS.GridMappingFA',
    fields: ['FieldAgent','Farmer','FarmerGroup','Province','District','SubDistrict','Village'],
    autoLoad: true,
    pageSize: 50,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_mapping_fa',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.UserId = this.storeVar.UserId;
        }
    }
});