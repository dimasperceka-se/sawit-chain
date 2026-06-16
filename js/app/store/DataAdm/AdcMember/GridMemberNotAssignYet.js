/*
* @Author: nikolius
* @Date:   2017-10-11 15:31:50
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 15:42:15
*/
Ext.define('Koltiva.store.DataAdm.AdcMember.GridMemberNotAssignYet', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.AdcMember.GridMemberNotAssignYet',
    storeId: 'Koltiva.store.DataAdm.AdcMember.GridMemberNotAssignYet',
    fields: ['id', 'Name','Desa','Kecamatan', 'MemberType','Province','District'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/adc_member/grid_member_not_assign_yet',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});