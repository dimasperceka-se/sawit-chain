Ext.define('Koltiva.store.Basic.MillRefinery.Parent',{
    extend: 'Ext.data.Store',
    storeId: 'koltiva-Basic-MillRefinery-Parent',
    model: 'Koltiva.model.combo',
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_parent,
        reader: {
            type: 'json',
            root: 'data',
            // totalProperty: 'total'
        }
    }
});
