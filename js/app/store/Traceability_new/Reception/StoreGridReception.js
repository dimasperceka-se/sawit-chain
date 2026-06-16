Ext.define('Koltiva.store.Traceability_new.Reception.StoreGridReception', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reception.StoreGridReception',
    storeId: 'Koltiva.store.Traceability_new.Reception.StoreGridReception',
    fields: [
        'DeliveryID', 
        'DeliveryID',
        'SupplychainID',
        'DestinationID',
        'DeliveryStatusID',
        'DeliveryStatus',
        'DeliveryNumber',
        'DeliveryDate',
        'DateReceipt',
        'ExternalCode',
        'TotalWeight',
        'PackageNumber',
        'DestWeight',
        'ArrivalEstimation',
        'DestDriver',
        'AgentName',
        'DestinationName',
        'SupplyTransID',
        'PaymentMethodID',
        'PaymentAmount',
        'PaymentStatusID',
        'PaymentStatus',
        'uid',
        'SupplychainIDSelf',
        'Weight',
        'DestTransportNumber'
    ],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/fetch_data',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function (store, records, success) {
            var thisObj = this;
            if (success == true) {
                thisObj.loadInfoFilter();
                thisObj.loadInfoStatus();
            }
        },
        sort: function (store, records, success) {
            var thisObj = this;
            if (success == true) {
                thisObj.loadInfoFilter();
            }
        },
        beforeload: function (store, operation, options) {
            var cof_gridreception_params = JSON.parse(localStorage.getItem('cof_gridreception_params'));
            if (cof_gridreception_params != null) {
                store.proxy.extraParams.ArrFilter                      = cof_gridreception_params.ArrFilter.join(',');
                store.proxy.extraParams.TextFilterKeyword              = cof_gridreception_params.TextFilterKeyword;
                store.proxy.extraParams.TextFilterWarehouseID          = cof_gridreception_params.TextFilterWarehouseID;
                store.proxy.extraParams.TextFilterCollectorID          = cof_gridreception_params.TextFilterCollectorID;
                store.proxy.extraParams.TextFilterStartShipmentDate    = cof_gridreception_params.TextFilterStartShipmentDate;
                store.proxy.extraParams.TextFilterEndShipmentDate     = cof_gridreception_params.TextFilterEndShipmentDate;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter                       = null;
                store.proxy.extraParams.TextFilterKeyword               = null;
                store.proxy.extraParams.TextFilterWarehouseID           = null;
                store.proxy.extraParams.TextFilterCollectorID           = null;
                store.proxy.extraParams.TextFilterStartShipmentDate     = null;
                store.proxy.extraParams.TextFilterEndShipmentDate      = null;
            }
        }
    },
    loadInfoFilter: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_grid',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;

                var cof_gridreception_params = JSON.parse(localStorage.getItem('cof_gridreception_params'));
                if (cof_gridreception_params != null) {
                    if (cof_gridreception_params.ArrFilterLang.length > 0)
                        document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '<strong>' + lang('Data filter by') + ':</strong>&nbsp;&nbsp;<span style="color:#895608;">' + cof_gridreception_params.ArrFilterLang.join(', ') + '</span>';
                    else
                        document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
                } 
                // else {
                //     document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
                // }
            }
        });
    },
    loadInfoStatus: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_status',
            params: {
                statusFor: 'reception',
            },
            method:'GET',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoStatus').innerHTML = data.responseText;
            }
        });
    }
});