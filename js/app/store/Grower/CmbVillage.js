/*
* @Author: nikolius
* @Date:   2017-05-23 15:41:35
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:49
*/
/*
    Store ini memerlukan parameter
        1. SubdistrictID
*/

Ext.define('Koltiva.store.Grower.CmbVillage', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbVillage',
    id: 'store.Grower.CmbVillage',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_village',
        reader: {
            type: 'json'
        }
    }
});