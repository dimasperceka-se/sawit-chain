/*
* @Author: yusuf
* @Date:   2018-12-13 15:54:38
* @Last Modified by:   nikolius
* @Last Modified time: 2018-12-13 15:55:10
*/

Ext.define('Koltiva.store.Traceability.Reference.Supplychain_quality_value.cmbObject', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_quality_value.cmbObject',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_quality_value.cmbObject',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/supplychain-quality-value-quality',
        reader: {
            type: 'json',
             root: 'data',
            totalProperty: 'total'
        }
    }
});