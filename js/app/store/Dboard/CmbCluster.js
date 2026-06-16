Ext.define('Koltiva.store.Dboard.CmbCluster', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.CmbCluster',
    id: 'Koltiva.store.Dboard.CmbCluster',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_cluster',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});