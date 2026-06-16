/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-07-24 13:13:08
*/

Ext.define('Koltiva.store.Report.Transaction.MainGridTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Report.Transaction.MainGridTransaction',
    storeId: 'Koltiva.store.Report.Transaction.MainGridTransaction',
    fields: ['SupplyTransID','SupplyType','Name','District','SubDistrict','FarmerID','AgentBatchID','DateTransaction','Bruto','Netto','SupplyID','DeliveryDate','BatchFrom','SupplyBatchStatus'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/report_traceability/store_grid_mill_transaction',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MillID = Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboMill').getValue();
            store.proxy.extraParams.DOID = Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboDO').getValue();
            store.proxy.extraParams.AgentID = Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboAgent').getValue();
            store.proxy.extraParams.DateFrom = Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom').getRawValue();
            store.proxy.extraParams.DateTo = Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateTo').getRawValue();
        }
    }
});