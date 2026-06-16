/*
* @Author: nikolius
* @Date:   2017-09-07 15:20:33
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-07 15:25:39
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.Trader.CmbStaffTrader', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Trader.CmbStaffTrader',
    fields: ['id','label'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/trader_mem/cmb_staff_trader',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});