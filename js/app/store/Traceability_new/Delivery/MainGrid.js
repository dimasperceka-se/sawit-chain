Ext.define('Koltiva.store.Traceability_new.Delivery.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Delivery.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Delivery.MainGrid',
    fields: [
        'DeliveryID'
        ,'DeliveryNumber'
        ,'SellingWeight'
        ,'DeliveryStatusID'
        ,'Status'
        ,'DeliveryDate'
        ,'ExternalCode'
        ,'DeliveryNumber'
        ,'Destination'
        ,'DestWeight'
        ,'PackageWeight'
        ,'PackageNumber'
        ,'ArrivalEstimation'
        ,'DateCreated'
        ,'SupplyBatchNumber'
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
        url: m_api + '/traceability_api/delivery/grid_main',
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
            var cof_griddelivery_params = JSON.parse(localStorage.getItem('cof_griddelivery_params'));
            if (cof_griddelivery_params != null) {
                store.proxy.extraParams.ArrFilter                      = cof_griddelivery_params.ArrFilter.join(',');
                store.proxy.extraParams.TextFilterDeliveryNumber    = cof_griddelivery_params.TextFilterDeliveryNumber;
                store.proxy.extraParams.TextFilterExernalCode  = cof_griddelivery_params.TextFilterExernalCode;
                store.proxy.extraParams.TextFilterDestinationID = cof_griddelivery_params.TextFilterDestinationID;
                store.proxy.extraParams.TextFilterDeliveryStatusID   = cof_griddelivery_params.TextFilterDeliveryStatusID;
                store.proxy.extraParams.TextFilterStartDeliveryDate   = cof_griddelivery_params.TextFilterStartDeliveryDate;
                store.proxy.extraParams.TextFilterEndDeliveryDate   = cof_griddelivery_params.TextFilterEndDeliveryDate;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter                      = null;
                store.proxy.extraParams.TextFilterDeliveryNumber    = null;
                store.proxy.extraParams.TextFilterExernalCode  = null;
                store.proxy.extraParams.TextFilterDestinationID = null;
                store.proxy.extraParams.TextFilterDeliveryStatusID   = null;
                store.proxy.extraParams.TextFilterStartDeliveryDate   = null;
                store.proxy.extraParams.TextFilterEndDeliveryDate   = null;
            }
        }
    },
    loadInfoFilter: function () {
        // Ext.Ajax.request({
        //     url: m_api + '/tools/information_grid',
        //     waitMsg: lang('Please Wait'),
        //     success: function (data) {
        //         document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;

        //         var cof_griddelivery_params = JSON.parse(localStorage.getItem('cof_griddelivery_params'));
        //         if (cof_griddelivery_params != null) {
        //             if (cof_griddelivery_params.ArrFilterLang.length > 0)
        //                 document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '<strong>' + lang('Data filter by') + ':</strong>&nbsp;&nbsp;<span style="color:#895608;">' + cof_griddelivery_params.ArrFilterLang.join(', ') + '</span>';
        //             else
        //                 document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
        //         } else {
        //             document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
        //         }
        //     }
        // });
    },
    loadInfoStatus: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_status',
            params: {
                statusFor: 'Delivery',
            },
            method:'GET',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoStatus').innerHTML = data.responseText;
            }
        });
    }
});