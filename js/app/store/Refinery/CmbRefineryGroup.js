Ext.define('Koltiva.store.Refinery.CmbRefineryGroup', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.CmbRefineryGroup',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/combo_refinery_group',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});