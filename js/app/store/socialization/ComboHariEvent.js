/*
* @Author: nikolius
* @Date:   2017-10-13 15:27:46
* @Last Modified by:   nikolius
* @Last Modified time: 2018-02-05 13:46:20
*/

Ext.define('Koltiva.store.socialization.ComboHariEvent', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.socialization.ComboProvince',
    id: 'Koltiva.store.socialization.ComboProvince',
    fields: [ 'hari'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/socialization/application_store/comboharievent',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
          
        }
    }
});