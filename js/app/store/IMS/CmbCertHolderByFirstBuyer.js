/*
* @Author: Nikolius Lau
* @Date:   2018-08-15 17:25:43
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-15 17:48:53
*/

Ext.define('Koltiva.store.IMS.CmbCertHolderByFirstBuyer', {
    extend: 'Ext.data.Store',
    storeId: 'store.IMS.CmbCertHolderByFirstBuyerce',
    id: 'store.IMS.CmbCertHolderByFirstBuyerce',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/cmb_cert_holder_by_first_buyer',
        reader: {
            type: 'json'
        }
    },
    listeners: {
    	beforeload: function(store, operation, options){
    		store.proxy.extraParams.FirstBuyerID = this.storeVar.FirstBuyerID;
    	}
    }
});