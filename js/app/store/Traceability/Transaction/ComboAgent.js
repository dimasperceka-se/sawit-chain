/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-30 14:16:32
*/

Ext.define('Koltiva.store.Traceability.Transaction.ComboAgent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Transaction.ComboAgent',
    storeId: 'Koltiva.store.Traceability.Transaction.ComboAgent',
    fields: ['NameAgent'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/comboAgent',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});