/*
* @Author: nikolius
* @Date:   2017-05-30 15:54:38
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-30 15:55:10
*/

Ext.define('Koltiva.store.Grower.CmbBank', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbBank',
    id: 'store.Grower.CmbBank',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_bank',
        reader: {
            type: 'json'
        }
    }
});