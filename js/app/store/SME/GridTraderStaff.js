/*
* @Author: nikolius
* @Date:   2017-09-07 13:32:08
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 16:25:26
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.SME.GridTraderStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridTraderStaff',
    storeId: 'Koltiva.store.SME.GridTraderStaff',
    fields: ['StaffID','PersonID','UserID','Name','Position','Age'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/grid_trader_staff',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});