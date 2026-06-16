Ext.define('Koltiva.store.Traceability_new.Dispatch.MainGridVehicle', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Dispatch.MainDispatch',
    storeId: 'Koltiva.store.Traceability_new.Dispatch.MainDispatch',
    fields: [
        'DespatchVehicleID'
        ,'DriverName'
        ,'VehicleTypeName'
        ,'DeliveryOrderNumber'
        ,'ContainerNumber'
        ,'VehicleNumber'
        ,'ProductName'
        ,'VehicleWeight'
        ,'OwnerStatusName'
        ,'DespatchID'
    ],
    pageSize: 10,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/fetchvehicle',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.DespatchID = this.storeVar.DespatchID;
            store.proxy.extraParams.ProductID = this.storeVar.ProductID;
        }, 
    }
});