/*
* @Author: nikolius
* @Date:   2017-08-22 11:05:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-22 12:04:43
*/

/*
    Store ini memerlukan parameter
        1. PartnerID
*/

Ext.define('Koltiva.store.Mill.GridOtherSupplier', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridOtherSupplier',
    storeId: 'Koltiva.store.Mill.GridOtherSupplier',
    fields: ['SupplierName','GardenType','FFBSupply','Tracebility','Generated','MillTCID','MillID','Generated','GardenAreaHa','AnnualProduction'],
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
        url: m_api + '/mill/grid_other',
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
            store.proxy.extraParams.MillTCDID = this.storeVar.MillTCDID;
            store.proxy.extraParams.Year = Year;
            store.proxy.extraParams.Period = Period;
        }
    }
});