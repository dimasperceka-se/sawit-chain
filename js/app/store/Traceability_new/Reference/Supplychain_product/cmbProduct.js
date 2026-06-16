/*
* @Author: muhammad hidayturrohman
* @Date:   2020-11-10
*/

Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbProduct', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbProduct',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbProduct',
    fields: ['ProductID', 'ProductName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_product/product_mill',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});