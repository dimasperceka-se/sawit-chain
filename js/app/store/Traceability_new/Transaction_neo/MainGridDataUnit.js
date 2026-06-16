Ext.define('Koltiva.store.Traceability_new.Transaction_neo.MainGridDataUnit', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.MainGridDataUnit',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.MainGridDataUnit',
    fields: ['SupplyTransID','Bunches','VolumeBruto','VolumeNetto','DeductionPercentage', 'ContractPrice','TotalPayment'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/web_transaction/data_weight_unit_main_grid',
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