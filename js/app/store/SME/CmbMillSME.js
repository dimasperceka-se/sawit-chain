/*
* @Author: nikolius
* @Date:   2017-07-28 10:40:24
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:26:55
*/

Ext.define('Koltiva.store.SME.CmbMillSME', {
    extend: 'Ext.data.Store',
    id: 'store.SME.CmbMillSME',
    storeId: 'store.SME.CmbMillSME',
    fields: ['id','label'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/cmb_mill_sme',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});