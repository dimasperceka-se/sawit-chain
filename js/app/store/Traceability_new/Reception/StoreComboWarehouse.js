Ext.define('Koltiva.store.Traceability_new.Reception.StoreComboWarehouse', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reception.StoreComboWarehouse',
    id: 'Koltiva.store.Traceability_new.Reception.StoreComboWarehouse',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/reception/fetch_combo_warehouse',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){

        },
        load: function(store, record){
            var total = 0;
            var id = '';
            store.each(function(record){
                id = record.get('id');
                total += 1;
            });
            /*if(total > 1){
                if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability.Transaction.Reception_neo.GridReception-toolbar-Warehouse'))){
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.Reception_neo.GridReception-toolbar-Warehouse').show();
                }
            }else{
                if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability.Transaction.Reception_neo.GridReception-toolbar-Warehouse'))){
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.Reception_neo.GridReception-toolbar-Warehouse').setValue(id);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.Reception_neo.GridReception-toolbar-Warehouse').hide();
                }
            }*/
        }
    }
});