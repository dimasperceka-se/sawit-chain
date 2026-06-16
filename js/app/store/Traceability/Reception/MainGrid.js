Ext.define('Koltiva.store.Traceability.Reception.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reception.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reception.MainGrid',
    fields: ['DespatchID','DespatchNumber','Name','PackingDate','ShippingDate','ReceptionDate','DespatchVolume','Status','DestinationID'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/refinery/fetch',
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