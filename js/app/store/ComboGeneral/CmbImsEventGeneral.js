/*
* @Author: nikolius
* @Date:   2018-02-05 14:38:55
* @Last Modified by:   nikolius
* @Last Modified time: 2018-02-05 14:41:38
*/

Ext.define('Koltiva.store.ComboGeneral.CmbImsEventGeneral', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbImsEventGeneral',
    id: 'Koltiva.store.ComboGeneral.CmbImsEventGeneral',
    fields: ['id', 'label','IMSMasterID'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_imsevent',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.CertHolderID = this.storeVar.CertHolderID;
        }
    }
});
 