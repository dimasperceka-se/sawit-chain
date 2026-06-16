/*
* @Author: nikolius
* @Date:   2017-05-18 18:54:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:03
*/

Ext.define('Koltiva.store.Grower.CmbDealerAssign', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbDealerAssign',
    id: 'store.Grower.CmbDealerAssign',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_dealer_Assign',
        reader: {
            type: 'json'
        }
    }
});