Ext.define('Koltiva.store.Basic.Kml.list',{
    extend: 'Ext.data.Store',
    storeId:'koltiva-Basic.Kml-list',
    fields: ['ID', 'Name', 'FileName', 'Color', 'Province', 'District', 'SubDistrict', 'Village', 'category'],
    autoLoad: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: m_api + '/basic/kmls',
        extraParams: {
            prov: m_param
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
