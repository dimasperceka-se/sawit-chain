/*
* @Author: nikolius
* @Date:   2017-04-12 11:48:56
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-12 11:50:19
*/
Ext.define('Koltiva.store.DataAdm.OffData.MainListMetadataFg', {
    extend: 'Ext.data.Store',
    storeId:'koltiva-store-DataAdm-OffData-MainListMetadata-FG',
    fields: ['MdoffID', 'Filename', 'DateCreated', 'CreatedBy'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/off_data/main_list_metadata_fg',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});