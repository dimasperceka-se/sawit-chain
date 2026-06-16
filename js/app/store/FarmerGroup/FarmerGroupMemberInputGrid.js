/*
* @Author: nikolius
* @Date:   2017-11-09 16:09:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-09 18:33:50
*/

/*
    Param2 yg diperlukan ketika load Store ini
    - FarmerGroupID
*/

Ext.define('Koltiva.store.FarmerGroup.FarmerGroupMemberInputGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerGroup.FarmerGroupMemberInputGrid',
    storeId: 'Koltiva.store.FarmerGroup.FarmerGroupMemberInputGrid',
    fields: ['MemberID','MemberDisplayID','MemberName','SubDistrict','Village','Enumerator'],
    autoLoad: false,
    remoteSort: true,
    pageSize: 30,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer_group/farmer_group_member_input_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var patchouli_farmer_group_ls = JSON.parse(localStorage.getItem('patchouli_farmer_group_ls'));

            if(patchouli_farmer_group_ls != null){
                pSearch = patchouli_farmer_group_ls.pSearch;
                ProvinceID = patchouli_farmer_group_ls.ProvinceID;
                DistrictID = patchouli_farmer_group_ls.DistrictID;
                SubdistrictID = patchouli_farmer_group_ls.SubdistrictID;
                VillageID = patchouli_farmer_group_ls.VillageID;
                Enumerator = patchouli_farmer_group_ls.Enumerator;
            }else{
                pSearch = '';
                ProvinceID = '';
                DistrictID = '';
                SubdistrictID = '';
                VillageID = '';
                Enumerator = '';
            }

            store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID;
            store.proxy.extraParams.textSearch = pSearch;
            store.proxy.extraParams.ProvinceID = ProvinceID;
            store.proxy.extraParams.DistrictID = DistrictID;
            store.proxy.extraParams.SubdistrictID = SubdistrictID;
            store.proxy.extraParams.VillageID = VillageID;
            store.proxy.extraParams.Enumerator = Enumerator;
        }
    }
});