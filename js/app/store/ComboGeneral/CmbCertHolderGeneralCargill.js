/*
* @Author: nikolius
* @Date:   2017-11-08 14:17:10
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-08 14:18:45
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - DistrictID
*/

Ext.define('Koltiva.store.ComboGeneral.CmbCertHolderGeneralCargill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbCertHolderGeneralCargill',
    storeId: 'Koltiva.store.ComboGeneral.CmbCertHolderGeneralCargill',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_holder',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){ 
            store.proxy.extraParams.CertProgID = this.storeVar.CertProgID; 
        }
    }
});