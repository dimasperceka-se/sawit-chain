/*
* @Author: nikolius
* @Date:   2017-10-11 18:20:20
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 18:21:35
*/
Ext.define('Koltiva.store.DataAdm.AdcMill.GridMillNotAssignYet', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMill.GridMillNotAssignYet',
    storeId: 'Koltiva.store.DataAdm.AdcMill.GridMillNotAssignYet',
    fields: ['id', 'Name','Desa','Kecamatan'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_mill/grid_mill_not_assign_yet',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});