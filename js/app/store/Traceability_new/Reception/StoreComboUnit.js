Ext.define('Koltiva.store.Traceability_new.Reception.StoreComboUnit', {
    extend: 'Ext.data.Store',
    storeId:'Koltiva.store.Traceability_new.Reception.StoreComboUnit',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/fetch_combo_ref_unit',
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