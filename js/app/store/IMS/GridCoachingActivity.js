/*
* @Author: nikolius
* @Date:   2018-03-15 16:13:04
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-16 11:00:41
*/

Ext.define('Koltiva.store.IMS.GridCoachingActivity', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridCoachingActivity',
    id: 'Koltiva.store.IMS.GridCoachingActivity',
    fields: ['ActivityID','FarmerID','FarmerName','EventDate','TimeStart','TimeEnd','DateCreated','CreatedBy','UserName'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_coaching_activity',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            var ct_farmer_ls = JSON.parse(localStorage.getItem('ct_farmer_ls'));
            if(ct_farmer_ls != null){
                ptextSearch = ct_farmer_ls.ptextSearch;
            }else{
                ptextSearch = "";
            }
            store.proxy.extraParams.textSearch = ptextSearch;
        }
    }
});