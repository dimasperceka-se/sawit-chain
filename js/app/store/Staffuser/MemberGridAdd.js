/*
    Param2 yg diperlukan ketika load Store ini
    - SupplierGroupID
*/

Ext.define('Koltiva.store.Staffuser.MemberGridAdd', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staffuser.MemberGridAdd',
    storeId: 'Koltiva.store.Staffuser.MemberGridAdd',
    fields: ['MemberID','MemberDisplayID','FarmerName','SubDistrict','Village'],
    autoLoad: false,
    remoteSort: true,
    pageSize: 30,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/members',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.StaffAssignmentID = this.storeVar.StaffAssignmentID;
            store.proxy.extraParams.StaffID = this.storeVar.StaffID;
            store.proxy.extraParams.textSearch = this.storeVar.textSearch;
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
            store.proxy.extraParams.SubdistrictID = this.storeVar.SubdistrictID;
            store.proxy.extraParams.VillageID = this.storeVar.VillageID;
        }
    }
});