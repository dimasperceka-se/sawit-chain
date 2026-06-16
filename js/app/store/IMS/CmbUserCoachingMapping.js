
Ext.define('Koltiva.store.IMS.CmbUserCoachingMapping', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbUserCoachingMapping',
    id: 'Koltiva.store.IMS.CmbUserCoachingMapping',
    fields: ['id','label',],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_coaching/cmb_user',
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