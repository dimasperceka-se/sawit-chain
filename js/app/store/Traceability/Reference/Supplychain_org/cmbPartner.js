/*
* @Author: yusuf
* @Date:   2018-12-13 15:54:38
* @Last Modified by:   nikolius
* @Last Modified time: 2018-12-13 15:55:10
*/

Ext.define('Koltiva.store.Traceability.Reference.Supplychain_org.cmbPartner', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_org.cmbPartner',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_org.cmbPartner',
    fields: ['PartnerID', 'PartnerName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/supplychain-org-partner',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});