Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSupplyType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSupplyType',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSupplyType',
    fields: ['id','label'],
    autoLoad: true,
    fields: ['id', 'label'],
    data: [
        {'id': "Farmer", 'label': lang('Farmer')},
        {'id': "Nonfarmer", 'label': lang('Non farmer')},
        {'id': "Batch", 'label': lang('Batch')}
    ],
    listeners : {
        load: function(store, record){
            /*if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability_new.Transaction.TransactionForm-Form-SupplyType'))){
                var combo = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.TransactionForm-Form-SupplyType');
                combo.select(combo.getStore().getAt(0));
            }*/
            
        }
    }
});