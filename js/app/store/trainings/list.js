Ext.define('Koltiva.store.trainings.list',{
    extend: 'Ext.data.TreeStore',
    storeId:'koltiva-trainings-list',
    model: Koltiva.model.trainings.list,
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud + 's',
    },
    folderSort: true
});
