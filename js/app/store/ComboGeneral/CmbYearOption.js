/*
* @Author: nikolius
* @Date:   2017-08-21 10:53:56
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-04 13:51:53
*/
Ext.define('Koltiva.store.ComboGeneral.CmbYearOption', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbYearOption',
    id: 'Koltiva.store.ComboGeneral.CmbYearOption',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_year_option',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.yearRange = this.storeVar.yearRange;
            store.proxy.extraParams.yearOrder = this.storeVar.yearOrder;
        }
    }
});