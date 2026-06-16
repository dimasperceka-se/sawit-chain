Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus',
    fields: ['id','label'],
    autoLoad: true,
    fields: ['id', 'label'],
    data: [
        {'id': "Open", 'label': lang('Open')},
        {'id': "Pending", 'label': lang('Pending')},
        {'id': "Sent", 'label': lang('Sent')}
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