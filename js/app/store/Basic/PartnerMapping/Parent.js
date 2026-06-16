Ext.define('Koltiva.store.Basic.PartnerMapping.Parent',{
    extend: 'Ext.data.Store',
    storeId: 'koltiva-Basic-PartnerMapping-Parent',
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
