/*
* @Author: nikolius
* @Date:   2017-10-13 15:35:41
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 15:37:16
*/

Ext.define('Koltiva.store.ComboGeneral.ComboDistrict', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboDistrict',
    id: 'Koltiva.store.ComboGeneral.ComboDistrict',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_district',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
        }
    }
});