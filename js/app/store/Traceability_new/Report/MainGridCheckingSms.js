Ext.define('Koltiva.store.Traceability_new.Report.MainGridCheckingSms', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Report.MainGridCheckingSms',
    id: 'Koltiva.store.Traceability_new.Report.MainGridCheckingSms',
    fields: [
        'AutoID',
        'request',
        'ResponseStatus',
        'DateCreated',
        'Status',
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/web_transaction/sms_checking',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.AutoID = this.storeVar.AutoID;
        }
    }
});