/*
* @Author: yusuf
* @Date:   2018-12-13 15:54:38
* @Last Modified by:   nikolius
* @Last Modified time: 2018-12-13 15:55:10
*/

Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org.cmbObject', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.cmbObject',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org.cmbObject',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/supplychain-org-obj/0',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var thisObj = this;
            store.proxy.url = m_api + "/reference/supplychain-org-obj/" + thisObj.viewVar.tb ;
        },
    }
});