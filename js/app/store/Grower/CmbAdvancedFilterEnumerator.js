/*
* @Author: Fashah Darullah
* @Date:   2019-09-05 15:54:38
*/
Ext.define('Koltiva.store.Grower.CmbAdvancedFilterEnumerator', {
    extend: 'Ext.data.Store',
    storeId: 'store.Grower.CmbAdvancedFilterEnumerator',
    id: 'store.Grower.CmbAdvancedFilterEnumerator',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/grower/combo_enum',
        reader: {
            type: 'json'
        }
    }
});