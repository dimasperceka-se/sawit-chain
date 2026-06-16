/*
* @Author: nikolius
* @Date:   2017-09-07 15:05:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-07 15:06:33
*/

Ext.define('Koltiva.store.SME.CmbBrandVehicle', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.CmbBrandVehicle',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/cmb_brand_vehicle',
        reader: {
            type: 'json'
        }
    }
});