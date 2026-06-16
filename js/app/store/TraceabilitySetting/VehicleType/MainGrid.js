/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : MainGrid.js
********************************************/
Ext.define('Koltiva.store.TraceabilitySetting.VehicleType.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.TraceabilitySetting.VehicleType.MainGrid',
    storeId: 'Koltiva.store.TraceabilitySetting.VehicleType.MainGrid',
    fields: ['GHGVehicleTypeID', 'VehicleTypeName', 'FuelConsumption', 'StatusCode'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    setStorevar: function(value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_setting/vehicle_type/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        } 
    },
    listeners: {
        load: function(store, records, success) {
            if (success == true) {
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        beforelaod: function(store, operation, options) {
            store.proxy.extraParams.textSearch = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-textVehicleTypeNameSearch').getValue();
        }
    }
});