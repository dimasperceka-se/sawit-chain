/*
    Store ini memerlukan parameter
        1. RefineryID
*/

Ext.define('Koltiva.store.Refinery.GridRefineryStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridRefineryStaff',
    storeId: 'Koltiva.store.Refinery.GridRefineryStaff',
    fields: ['StaffID','PersonID','UserID','Name','Position','Age'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/grid_refinery_staff',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RefineryID = this.storeVar.RefineryID;
        }
    }
});