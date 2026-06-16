/*
* @Author: nikolius
* @Date:   2017-09-07 15:05:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-07 15:06:33
*/

Ext.define('Koltiva.store.Trader.CmbBrandVehicle', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Trader.CmbBrandVehicle',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/trader_mem/cmb_brand_vehicle',
        reader: {
            type: 'json'
        }
    }
});