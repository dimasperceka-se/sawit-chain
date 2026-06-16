Ext.define('Koltiva.store.Traceability_new.Processing.CmbProduct', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.CmbProduct',
    id: 'Koltiva.store.Traceability_new.Processing.CmbProduct',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/list_product/',
        reader: {
            type: 'json',  
            root: 'data'
        }
    }
});