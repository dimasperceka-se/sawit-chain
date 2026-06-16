/*
* @Author: nikolius
* @Date:   2017-10-11 10:36:43
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 10:37:47
*/
Ext.define('Koltiva.store.DataAdm.AdcMember.GridSetDataControlByRegion', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMember.GridSetDataControlByRegion',
    storeId: 'Koltiva.store.DataAdm.AdcMember.GridSetDataControlByRegion',
    fields: ['MemberIDInc','id', 'Name','Desa','Kecamatan','PartnerAccess'],
    pageSize: 25,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_member/grid_set_data_control_by_region',
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
            store.proxy.extraParams.MemberType = this.storeVar.MemberType;
        }
    }
});