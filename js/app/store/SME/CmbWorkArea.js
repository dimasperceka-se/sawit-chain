
Ext.define('Koltiva.store.SME.CmbWorkArea', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbWorkArea',
    storeId: 'Koltiva.store.SME.CmbWorkArea',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/cmb_work_area',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }
    }
});