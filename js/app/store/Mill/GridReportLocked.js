/*
* @Author: nikolius
* @Date:   2017-08-03 15:28:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:11:43
*/

Ext.define('Koltiva.store.Mill.GridReportLocked', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridReportLocked',
    fields: ['SupplyBatchDate', 'SupplyOrgID', 'SupplyDestOrgID', 'SupplyBatchNumber', 
    'DeliveryDate', 'DestPO', 'DestWeight','DestDriver','DestTransportID','DestTransportNumber',
    'Notes','VolumeBruto','VolumeNetto','PackageNumber','SupplyBatchStatus','ObjID','SupplierName'],
    pageSize: 20,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/mill/get_grid_report_locked',
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