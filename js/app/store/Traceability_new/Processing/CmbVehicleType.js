Ext.define('Koltiva.store.Traceability_new.Processing.CmbVehicleType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Processing.CmbVehicleType',
    fields: ['VehicleTypeID','VehicleTypeName'],
    data: [{
        "VehicleTypeID": "1",
        "VehicleTypeName": lang('Truck')
    },{
        "VehicleTypeID": "2",
        "VehicleTypeName": lang('Mini Truck')
    },{
        "VehicleTypeID": "3",
        "VehicleTypeName": lang('Pick Up')
    },{
        "VehicleTypeID": "4",
        "VehicleTypeName": lang('Truck Colt Diesel')
    },{
        "VehicleTypeID": "5",
        "VehicleTypeName": lang('Dump Truck')
    },{
        "VehicleTypeID": "7",
        "VehicleTypeName": lang('Other')
    }],
    // autoLoad: true,
    // storeVar: false,
    // setStoreVar: function(value){
    //     this.storeVar = value;
    // },
    // proxy: {
    //     type: 'ajax', 
    //     url : m_api + '/processing/transaction/list_vehicle_type', 
	// 	reader: {
    //         type: 'json',  
    //         root: 'data'
    //     }
    // },
    // listeners: {
	// 	beforeload: function(store, operation, options){
            
    //     },  
    // }
});