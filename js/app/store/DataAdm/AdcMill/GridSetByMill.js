/*
* @Author: nikolius
* @Date:   2017-10-11 16:45:04
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 16:46:54
*/

Ext.define('Koltiva.store.DataAdm.AdcMill.GridSetByMill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMill.GridSetByMill',
    storeId: 'Koltiva.store.DataAdm.AdcMill.GridSetByMill',
    fields: ['MillID','id', 'Name','Desa','Kecamatan','PartnerAccess'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_mill/grid_set_by_mill',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            //console.log(this.storeVar);
            store.proxy.extraParams.MillID = this.storeVar.MillID;
            store.proxy.extraParams.MillName = this.storeVar.MillName;
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
            store.proxy.extraParams.SubDistrictID = this.storeVar.SubDistrictID;
            store.proxy.extraParams.VillageID = this.storeVar.VillageID;
        }
    }
});