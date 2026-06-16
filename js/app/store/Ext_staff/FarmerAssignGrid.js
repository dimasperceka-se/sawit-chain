/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 05-02-2020
 *  File : PanelEmployessMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.Ext_staff.FarmerAssignGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Ext_staff.FarmerAssignGrid',
    id: 'Koltiva.store.Ext_staff.FarmerAssignGrid',
    fields: ['StaffAssignmentID','StaffAssignmentExtID','StartDate','EndDate','FarmerNr','StatusCode'],
    pageSize: 20,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/ext_staff/farmer_assignment_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.StaffID = this.storeVar.StaffID;
        }
    }
});