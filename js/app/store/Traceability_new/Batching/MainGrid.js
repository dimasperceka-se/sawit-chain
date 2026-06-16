Ext.define('Koltiva.store.Traceability_new.Batching.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Batching.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Batching.MainGrid',
    fields: [
        'SupplyBatchID'
        ,'SupplyBatchNumber'
        ,'AgentName'
        ,'DestinationName'
        ,'VolumeBruto'
        ,'SupplyBatchStatusID'
        , 'Status'
        , 'DateCreated'
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
        url: m_api + '/traceability_api/batching/grid_main',
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
            var cof_gridprocessing_params = JSON.parse(localStorage.getItem('cof_gridprocessing_params'));
            if (cof_gridprocessing_params != null) {
                store.proxy.extraParams.ArrFilter                      = cof_gridprocessing_params.ArrFilter.join(',');
                store.proxy.extraParams.TextFilterSupplyBatchNumber    = cof_gridprocessing_params.TextFilterSupplyBatchNumber;
                store.proxy.extraParams.TextFilterSupplyBatchStatusID  = cof_gridprocessing_params.TextFilterSupplyBatchStatusID;
                store.proxy.extraParams.TextFilterStartSupplyBatchDate = cof_gridprocessing_params.TextFilterStartSupplyBatchDate;
                store.proxy.extraParams.TextFilterEndSupplyBatchDate   = cof_gridprocessing_params.TextFilterEndSupplyBatchDate;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter                      = null;
                store.proxy.extraParams.TextFilterSupplyBatchNumber    = null;
                store.proxy.extraParams.TextFilterSupplyBatchStatusID  = null;
                store.proxy.extraParams.TextFilterStartSupplyBatchDate = null;
                store.proxy.extraParams.TextFilterEndSupplyBatchDate   = null;
            }
        }
    },
    loadInfoFilter: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_grid',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;

                var cof_gridprocessing_params = JSON.parse(localStorage.getItem('cof_gridprocessing_params'));
                if (cof_gridprocessing_params != null) {
                    // if (cof_gridprocessing_params.ArrFilterLang.length > 0)
                    //     // document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '<strong>' + lang('Data filter by') + ':</strong>&nbsp;&nbsp;<span style="color:#895608;">' + cof_gridprocessing_params.ArrFilterLang.join(', ') + '</span>';
                    // else
                    //     document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
                } else {
                    // document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
                }
            }
        });
    },
    loadInfoStatus: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_status',
            params: {
                statusFor: 'batching',
            },
            method:'GET',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoStatus').innerHTML = data.responseText;
            }
        });
    }
});