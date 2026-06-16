/*
* @Author: nikolius
* @Date:   2017-04-07 15:40:08
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-07 15:45:55
*/
Ext.define('Koltiva.store.DataAdm.OffData.MainListSubdistrict', {
    extend: 'Ext.data.Store',
    storeId:'koltiva-store-DataAdm-OffData-MainListSubdistrict',
    fields: ['DistrictID', 'DistrictLabel', 'SubdistrictID', 'Subdistrict', 'DhisSqlViewID', 'DhisSqlViewName', 'Query_Available', 'File_Available'],
    groupField: 'DistrictLabel',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/off_data/main_list_subdistrict',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});