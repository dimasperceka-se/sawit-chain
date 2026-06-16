/*
* @Author: nikolius
* @Date:   2017-10-11 17:51:23
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 17:54:29
*/
Ext.define('Koltiva.store.DataAdm.AdcMill.GridSetDataControlByRegion', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMill.GridSetDataControlByRegion',
    storeId: 'Koltiva.store.DataAdm.AdcMill.GridSetDataControlByRegion',
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
        url: m_api + '/data_adm/adc_mill/grid_set_data_control_by_region',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
            store.proxy.extraParams.SubDistrictID = this.storeVar.SubDistrictID;
            store.proxy.extraParams.VillageID = this.storeVar.VillageID;
        }
    }
});