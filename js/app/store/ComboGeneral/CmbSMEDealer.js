/*
* @Author: nikolius
* @Date:   2017-11-09 10:08:22
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-09 10:21:56
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - FarmerGroupID
*/

Ext.define('Koltiva.store.ComboGeneral.CmbSMEDealer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbSMEDealer',
    storeId: 'Koltiva.store.ComboGeneral.CmbSMEDealer',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_sme_dealer',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
        }
    }
});