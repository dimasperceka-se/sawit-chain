/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-30 14:40:37
*/

Ext.define('Koltiva.store.Report.Transaction.ComboFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Report.Transaction.ComboFarmer',
    storeId: 'Koltiva.store.Report.Transaction.ComboFarmer',
    fields: ['NameFarmer'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/comboFarmer',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});