Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org.StoreComboObjType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.StoreComboObjType',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.StoreComboObjType',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_org/objtype_list',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});