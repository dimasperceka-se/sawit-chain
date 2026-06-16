Ext.define('Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryReceiving', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryReceiving',
    id: 'Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryReceiving',
    fields: [
        'TransDetailID'
        ,'SupplyTransID'
        ,'DeliveryDetailID'
        ,'DetailNumber'
        ,{name : 'Weight', type: 'float'}
        ,'TotalCapacity'
        ,'PaymentPaid'
        ,'PaymentStatusID'
        ,'PaymentMethodID'
        ,'BankCode'
        ,'BankName'
        ,'AccountNumber'
        ,'AccountName'
        ,'statusWeight'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/data_delivery_receiving_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.SupplyTransID = this.storeVar.SupplyTransID;
        }
    }
});