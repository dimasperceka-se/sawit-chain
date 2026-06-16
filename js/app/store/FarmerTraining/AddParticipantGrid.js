Ext.define('Koltiva.store.FarmerTraining.AddParticipantGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.AddParticipantGrid',
    fields: ['addFarmerID', 'addFarmerDisplayID', 'addFarmerName', 'Province', 'District', 'SubDistrict', 'Village'],
    //pageSize: 10,
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_store_participant + 's_add',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.FarmerTrainingID = Ext.getCmp('idt').getValue()
            store.proxy.extraParams.key = Ext.getCmp('keyAddPart').getValue()
            store.proxy.extraParams.prov = Ext.getCmp('provAddPart').getValue()
            store.proxy.extraParams.kab = Ext.getCmp('kabAddPart').getValue()
            // store.proxy.extraParams.cpg = Ext.getCmp('cpgAddPart').getValue()
        }
    }
});