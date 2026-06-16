/*
* @Author: nikolius
* @Date:   2017-05-18 19:08:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:24
*/

/*
    Store ini memerlukan parameter
        1. ProvinceID
*/

Ext.define('Koltiva.store.Grower.CmbDistrict', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbDistrict',
    id: 'store.Grower.CmbDistrict',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_district',
        reader: {
            type: 'json'
        }
    }
});