/*
* @Author: nikolius
* @Date:   2017-05-18 18:54:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:03
*/

Ext.define('Koltiva.store.Grower.CmbProvince', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbProvince',
    id: 'store.Grower.CmbProvince',
    fields: ['id', 'label'],
    remoteSort: true,
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_propinsi',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID;
        }
    }
});