/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Thu Sep 06 2018
 *  File : CmbPartnerCommon.js
 *******************************************/

Ext.define('Koltiva.store.ComboGeneral.CmbPartnerCommon', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbPartnerCommon',
    storeId: 'Koltiva.store.ComboGeneral.CmbPartnerCommon',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_partner_common',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});