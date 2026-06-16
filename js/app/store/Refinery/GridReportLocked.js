Ext.define('Koltiva.store.Refinery.GridReportLocked', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridReportLocked',
    fields: ['SupplyBatchDate', 'SupplyOrgID', 'SupplyDestOrgID', 'SupplyBatchNumber', 
    'DeliveryDate', 'DestPO', 'DestWeight','DestDriver','DestTransportID','DestTransportNumber',
    'Notes','VolumeBruto','VolumeNetto','PackageNumber','SupplyBatchStatus','ObjID','SupplierName'],
    pageSize: 20,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/get_grid_report_locked',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }
    }
});