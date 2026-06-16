Ext.define('Koltiva.store.Traceability_new.Reception.StoreComboPackage', {
    extend: 'Ext.data.Store',
    storeId:'Koltiva.store.Traceability_new.Reception.StoreComboPackage',
    fields: ['id','label', 'weight'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/fetch_combo_package',
        reader: {
            type: 'json',
            root: 'data'
        } 
    },
    listeners: {
        beforeload: function(store, operation, options){
            
        }
    }
});