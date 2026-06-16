/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 05-02-2020
 *  File : PanelEmployessMainGrid.js
 *******************************************/
Ext.define('Koltiva.store.Staffuser.FarmerAssignGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Staffuser.FarmerAssignGrid',
    id: 'Koltiva.store.Staffuser.FarmerAssignGrid',
    fields: ['StaffAssignmentID','StaffAssignmentExtID','StartDate','EndDate','FarmerNr','StatusCode'],
    pageSize: 20,
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/staffuser/farmer_assignment_grid',
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