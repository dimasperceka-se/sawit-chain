/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-07-03 09:57:56
*/

Ext.define('Koltiva.store.Report.Transaction.ComboMill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Report.Transaction.ComboMill',
    storeId: 'Koltiva.store.Report.Transaction.ComboMill',
    fields: ['id','label'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/combomill',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});