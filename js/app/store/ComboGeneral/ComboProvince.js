/*
* @Author: nikolius
* @Date:   2017-10-13 15:27:46
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 15:31:17
*/

Ext.define('Koltiva.store.ComboGeneral.ComboProvince', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.ComboProvince',
    id: 'Koltiva.store.ComboGeneral.ComboProvince',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_province',
        reader: {
            type: 'json'
        }
    }
});