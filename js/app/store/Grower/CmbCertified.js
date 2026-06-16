/*
* @Author: nikolius
* @Date:   2017-05-18 18:54:52
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:03
*/

Ext.define('Koltiva.store.Grower.CmbCertified', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbCertified',
    id: 'store.Grower.CmbCertified',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_certified',
        reader: {
            type: 'json'
        }
    }
});