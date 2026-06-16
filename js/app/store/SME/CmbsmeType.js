Ext.define('Koltiva.store.SME.CmbsmeType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbsmeType',
    storeId: 'Koltiva.store.SME.CmbsmeType',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/cmb_sme_type',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            
        }
    }
});