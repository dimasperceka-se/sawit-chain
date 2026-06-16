/**************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Thu June 23 2022
 * File : MainGrid.js
 ************************************** */
 Ext.define('Koltiva.store.TraceabilitySetting.FuelType.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.TraceabilitySetting.FuelType.MainGrid',
    storeId: 'Koltiva.store.TraceabilitySetting.FuelType.MainGrid',
    fields: ['GHGFuelTypeID', 'FuelTypeName', 'FuelTypeCoefficient', 'StatusCode'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_setting/fuel_type/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
						document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        sort: function(store, records, success){
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
						document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;
                    }
                });
            }
        },
        beforeload: function(store, operation, options) {
            store.proxy.extraParams.textSearch = Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid-textFuelTypeNameSearch').getValue();
            // store.proxy.extraParams.textSearch = this.storeVar.textSearch;
        }
    }
});