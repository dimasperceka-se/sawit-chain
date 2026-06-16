Ext.define('Koltiva.store.Traceability_new.Report.MainGridReportSms', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.MainGridReportSms',
    storeId: 'Koltiva.store.Traceability_new.Report.MainGridReportSms',
    fields: ['SupplyTransID',
    'SupplychainID',
    'SupplyBatchID',
    'TransNumber',
    'SupplyType',
    'DateTransaction',
    'MemberDisplayID',
    'SupplierName',
    'Bunches',
    'VolumeBruto',
    'VolumeNetto',
    'PlantationNr' ,
    'FarmingTypeID',
    'DetailTypeID',
    'PackageWeight',
    'SupplyStatus',
    'ContractPrice',
    'isTraceable',
    'FarmerCategory',
    'uid',
    'Status','SalesType',
    'SMSStatus',
    'SMSType',
    'SendDate',
    'Handphone',
    'GroupName',
    'Province',
    'District',
    'SubDistrict',
    'VillageID'
    ,'Village'
    ,'AutoID'
    ,'response'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/main-grid-sms',
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
            var cof_gridtransaction_params = JSON.parse(localStorage.getItem('cof_gridtransaction_params'));
            if (cof_gridtransaction_params != null) {
                store.proxy.extraParams.ArrFilter                      = cof_gridtransaction_params.ArrFilter.join(',');
                store.proxy.extraParams.TextFilterTransTypeName        = cof_gridtransaction_params.TextFilterTransTypeName;
                store.proxy.extraParams.TextFilterTransSupplyID        = cof_gridtransaction_params.TextFilterTransSupplyID;
                store.proxy.extraParams.TextFilterMemberName           = cof_gridtransaction_params.TextFilterMemberName;
                store.proxy.extraParams.TextFilterStartDateTransaction = cof_gridtransaction_params.TextFilterStartDateTransaction;
                store.proxy.extraParams.TextFilterEndDateTransaction   = cof_gridtransaction_params.TextFilterEndDateTransaction;
                store.proxy.extraParams.TextFilterProvince             = cof_gridtransaction_params.TextFilterProvince;
                store.proxy.extraParams.TextFilterDistrict             = cof_gridtransaction_params.TextFilterDistrict;

            } else {
                //reset params
                store.proxy.extraParams.ArrFilter                      = null;
                store.proxy.extraParams.TextFilterTransTypeName        = null;
                store.proxy.extraParams.TextFilterTransSupplyID        = null;
                store.proxy.extraParams.TextFilterMemberName           = null;
                store.proxy.extraParams.TextFilterStartDateTransaction = null;
                store.proxy.extraParams.TextFilterEndDateTransaction   = null;
                store.proxy.extraParams.TextFilterProvince             = null;
                store.proxy.extraParams.TextFilterDistrict             = null;
            }
        }
    },
    loadInfoFilter: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_grid',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoDataGrid').innerHTML = data.responseText;

                var cof_gridtransaction_params = JSON.parse(localStorage.getItem('cof_gridtransaction_params'));
                if (cof_gridtransaction_params != null) {
                    // if (cof_gridtransaction_params.ArrFilterLang.length > 0)
                    //     document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '<strong>' + lang('Data filter by') + ':</strong>&nbsp;&nbsp;<span style="color:#895608;">' + cof_gridtransaction_params.ArrFilterLang.join(', ') + '</span>';
                    // else
                    //     document.getElementById('Sfr_IdBoxInfoFilterGrid').innerHTML = '';
                } 
            }
        });
    },
    loadInfoStatus: function () {
        Ext.Ajax.request({
            url: m_api + '/tools/information_status',
            params: {
                // statusFor: 'report.maingridreport',
            },
            method:'GET',
            waitMsg: lang('Please Wait'),
            success: function (data) {
                document.getElementById('Sfr_IdBoxInfoStatus').innerHTML = data.responseText;
            }
        });
    }
});