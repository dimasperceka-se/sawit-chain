/*
* @Author: nikolius
* @Date:   2017-10-11 17:09:26
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 17:10:22
*/
Ext.define('Koltiva.store.DataAdm.AdcMill.GridSetDataControl', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMill.GridSetDataControl',
    storeId: 'Koltiva.store.DataAdm.AdcMill.GridSetDataControl',
    fields: ['MillID','id', 'Name','Desa','Kecamatan','PartnerAccess'],
    pageSize: 25,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_mill/grid_set_data_control',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MillIDSelected = this.storeVar.MillIDSelected;
        }
    }
});