Ext.define('Koltiva.store.Traceability.Reception.ComboSupplyStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reception.ComboSupplyStatus',
    storeId: 'Koltiva.store.Traceability.Reception.ComboSupplyStatus',
    fields: ['id','label'],
    autoLoad: true,
    fields: ['id', 'label'],
    data: [
        {'id': "Sent", 'label': lang('Sent')},
        {'id': "Received", 'label': lang('Received')}
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