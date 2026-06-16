Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboArea', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboArea',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboArea',
    fields: ['id','label'],
    autoLoad: true,
    fields: ['id', 'label'],
    data: [
        {'id': "farmer", 'label': lang('Farmer')},
        {'id': "district", 'label': lang('District')} 
    ],
    listeners : {
        load: function(store, record){
            
            
        }
    }
});