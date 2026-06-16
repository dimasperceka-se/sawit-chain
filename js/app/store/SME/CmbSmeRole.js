
Ext.define('Koltiva.store.SME.CmbSmeRole', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbSmeRole',
    storeId: 'Koltiva.store.SME.CmbSmeRole',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/cmb_sme_role',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RoleType = 'Agent';
        }
    }
});