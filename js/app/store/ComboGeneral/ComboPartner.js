/*
* @Author: nikolius
* @Date:   2017-10-10 16:14:05
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-10 16:15:07
*/
Ext.define('Koltiva.store.ComboGeneral.ComboPartner', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboPartner',
    id: 'Koltiva.store.ComboGeneral.ComboPartner',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_partner',
        reader: {
            type: 'json'
        }
    }
});