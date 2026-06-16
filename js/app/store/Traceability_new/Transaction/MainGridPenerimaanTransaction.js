 
Ext.define('Koltiva.store.Traceability_new.Transaction.MainGridPenerimaanTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.MainGridPenerimaanTransaction',
    storeId: 'Koltiva.store.Traceability_new.Transaction.MainGridPenerimaanTransaction',
    fields: ['SupplyBatchID','SupplyTransID','SupplyBatchNumber','SupplyDestOrgID','SupplyOrgName','DeliveryDate','DestWeight','SupplyBatchDate','SupplyBatchStatus','Bruto','Net','PackageNumber','DestNumberPackage','SupplierName', 'agCompanyName'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/penerimaan-main-grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var ptextSearch, pstatusSearch;

            var palm_penerimaan_list_searchp = JSON.parse(localStorage.getItem('palm_penerimaan_list_searchp'));
            if(palm_penerimaan_list_searchp != null){
                ptextSearch = palm_penerimaan_list_searchp.ptextSearch;
                pstatusSearch = palm_penerimaan_list_searchp.pstatusSearch;
            }else{
                ptextSearch = "";
                pstatusSearch = "";
            }
            store.proxy.extraParams.SID             = m_sid; 
            store.proxy.extraParams.textSearch      = ptextSearch; 
            store.proxy.extraParams.statusSearch    = pstatusSearch; 
        }
    }
});