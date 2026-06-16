Ext.define('Koltiva.store.Traceability_new.Processing.RefHaveOer', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.RefHaveOer',
    id: 'Koltiva.store.Traceability_new.Processing.RefHaveOer',
    fields: [
         'date'
        ,'nett'
        ,'flag'
        ,'setProduction'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/getBatchHaveNotOer/',
        reader: {
            type: 'json',
            root: 'data',
            // totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/processing/transaction/information_grid_have_oer',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        var data = JSON.parse(data.responseText);

                        if (data.checkDataExist == 0) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer-ButtonSubmit').hide();
                        }
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProductID = this.storeVar.ProductID;
        }
    }
});