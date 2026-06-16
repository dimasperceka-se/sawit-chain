Ext.define('Koltiva.store.Basic.PartnerMapping.List',{
    extend: 'Ext.data.TreeStore',
    storeId:'koltiva-Basic-PartnerMapping-List',
    model: Koltiva.model.Basic.PartnerMapping.List,
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud,
    },
    folderSort: true
});
