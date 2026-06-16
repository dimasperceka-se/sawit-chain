/*
 * @Author: sonny.fitriawan 
 * @Date: 2017-12-07 16:37:35 
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2017-12-08 11:00:58
 */

Ext.define('Koltiva.store.Reference.Vehicle.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Reference.Vehicle.MainGrid',
    storeId: 'Koltiva.store.Reference.Vehicle.MainGrid',
    model:'Koltiva.model.Reference.Vehicle.ListGrid',
    //fields: ['BrandID','BrandName','StatusCode'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_crud + 's',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.BrandID = this.storeVar.BrandID;
            store.proxy.extraParams.StringNameBrandName = this.storeVar.StringNameBrandName;
        }
    }
});