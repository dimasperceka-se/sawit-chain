/*
    Store ini memerlukan parameter
        1. PartnerID
*/

Ext.define('Koltiva.store.Refinery.GridExternalEstate', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridExternalEstate',
    storeId: 'Koltiva.store.Refinery.GridExternalEstate',
    fields: ['SupplierName','GardenType','FFBSupply','Tracebility','Generated','RefineryTCID','RefineryID','Generated','GardenAreaHa','AnnualProduction'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/grid_external',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var palm_trdec_list_searchp = JSON.parse(localStorage.getItem('palm_trdec_list_searchp'));
            if(palm_trdec_list_searchp != null){
                Year   = palm_trdec_list_searchp.Year;
                Period     = palm_trdec_list_searchp.Period;
            }else{
                Year        = m_year;
                Period      = m_period;
            }
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
            store.proxy.extraParams.RefineryTCDID = this.storeVar.RefineryTCDID;
            store.proxy.extraParams.Year = Year;
            store.proxy.extraParams.Period = Period;
        }
    }
});