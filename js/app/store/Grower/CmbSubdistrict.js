/*
* @Author: nikolius
* @Date:   2017-05-18 19:38:45
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-24 17:42:38
*/

/*
    Store ini memerlukan parameter
        1. DistrictID
*/

Ext.define('Koltiva.store.Grower.CmbSubdistrict', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbSubdistrict',
    id: 'store.Grower.CmbSubdistrict',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_subdistrict',
        reader: {
            type: 'json'
        }
    }
});