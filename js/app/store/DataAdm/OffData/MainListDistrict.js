/*
* @Author: nikolius
* @Date:   2017-04-07 10:45:46
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-07 11:17:22
*/
Ext.define('Koltiva.store.DataAdm.OffData.MainListDistrict', {
    extend: 'Ext.data.Store',
    storeId:'koltiva-store-DataAdm-OffData-MainListDistrict',
    fields: ['ProvinceID', 'Province', 'DistrictID', 'District', 'DhisSqlViewID', 'DhisSqlViewName', 'Query_Available', 'File_Available'],
    groupField: 'Province',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/off_data/main_list_district',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});