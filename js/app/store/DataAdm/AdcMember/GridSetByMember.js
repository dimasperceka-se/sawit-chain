/*
* @Author: nikolius
* @Date:   2017-10-10 11:17:43
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-10 12:26:32
*/

Ext.define('Koltiva.store.DataAdm.AdcMember.GridSetByMember', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMember.GridSetByMember',
    storeId: 'Koltiva.store.DataAdm.AdcMember.GridSetByMember',
    fields: ['MemberIDInc','id', 'Name','Desa','Kecamatan','PartnerAccess'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_member/grid_set_by_member',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            //console.log(this.storeVar);
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
            store.proxy.extraParams.MemberName = this.storeVar.MemberName;
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
            store.proxy.extraParams.SubDistrictID = this.storeVar.SubDistrictID;
            store.proxy.extraParams.VillageID = this.storeVar.VillageID;
            store.proxy.extraParams.MemberType = this.storeVar.MemberType;
        }
    }
});