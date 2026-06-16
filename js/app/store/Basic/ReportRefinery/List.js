Ext.define('Koltiva.store.Basic.ReportRefinery.List',{
    extend: 'Ext.data.TreeStore',
    storeId:'koltiva-Basic-ReportRefinery-List',
    model: Koltiva.model.Basic.ReportRefinery.List,
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud,
    },
    folderSort: true
});
