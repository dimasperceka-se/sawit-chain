/*
* @Author: nikolius
* @Date:   2018-02-05 14:49:02
* @Last Modified by:   nikolius
* @Last Modified time: 2018-02-05 14:49:46
*/

Ext.define('Koltiva.store.ComboGeneral.ComboVillage', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboVillage',
    id: 'Koltiva.store.ComboGeneral.ComboVillage',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_village',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.SubDistrictID = this.storeVar.SubDistrictID;
            store.proxy.extraParams.loadAll = this.storeVar.loadAll;
        }
    }
});