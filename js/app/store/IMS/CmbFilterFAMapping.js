/*
* @Author: nikolius
* @Date:   2018-04-19 14:24:31
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-19 14:25:17
*/
Ext.define('Koltiva.store.IMS.CmbFilterFAMapping', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbFilterFAMapping',
    id: 'Koltiva.store.IMS.CmbFilterFAMapping',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/cmb_filter_fa_mapping',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
        }
    }
});