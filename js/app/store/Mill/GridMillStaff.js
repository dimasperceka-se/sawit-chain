/*
* @Author: nikolius
* @Date:   2017-08-22 11:05:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-22 12:04:43
*/

/*
    Store ini memerlukan parameter
        1. MillID
*/

Ext.define('Koltiva.store.Mill.GridMillStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridMillStaff',
    storeId: 'Koltiva.store.Mill.GridMillStaff',
    fields: ['StaffID','PersonID','UserID','Name','Position','Age'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/mill/grid_mill_staff',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MillID = this.storeVar.MillID;
        }
    }
});