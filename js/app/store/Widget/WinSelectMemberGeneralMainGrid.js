/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri May 03 2019
 *  File : WinSelectMemberGeneralMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.Widget.WinSelectMemberGeneralMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Widget.WinSelectMemberGeneralMainGrid',
    fields: ['MemberID','MemberDisplayID','MemberName','Gender','Age','Province','District','SubDistrict','Village'],
    pageSize: 25,
    autoLoad: true,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tools/widget_select_member',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ListType = this.storeVar.ListType;
            store.proxy.extraParams.ExceptionID = this.storeVar.ExceptionID;
            store.proxy.extraParams.TextSearch = this.storeVar.TextSearch;
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
            store.proxy.extraParams.DistrictID = this.storeVar.DistrictID;
            store.proxy.extraParams.SubDistrictID = this.storeVar.SubDistrictID;
            store.proxy.extraParams.VillageID = this.storeVar.VillageID;
        }
    }
});