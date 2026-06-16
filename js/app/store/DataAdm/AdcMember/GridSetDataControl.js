/*
* @Author: nikolius
* @Date:   2017-10-10 15:33:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-10 15:37:27
*/
Ext.define('Koltiva.store.DataAdm.AdcMember.GridSetDataControl', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMember.GridSetDataControl',
    storeId: 'Koltiva.store.DataAdm.AdcMember.GridSetDataControl',
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
        url: m_api + '/data_adm/adc_member/grid_set_data_control',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberIDSelected = this.storeVar.MemberIDSelected;
        }
    }
});