Ext.define('Koltiva.store.Traceability_new.Processing.RefProcessing', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.RefProcessing',
    id: 'Koltiva.store.Traceability_new.Processing.RefProcessing',
    fields: ['ProcessingProductID','ProcessingNumber', 'RemainingVolume','ProcessingDate','ProductVolume','ProductID','ProductName','PickedVolume','ProductPercentage'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/RefProcessing/',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/processing/transaction/information_grid_refinery',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        var data = JSON.parse(data.responseText);
                        
                        let labelCpo = lang('CPO Remaining : ');

                        Ext.getCmp('CPOTotal').update(labelCpo +data.CPO);

                        let labelPko = lang('PKO Remaining : ');

                        Ext.getCmp('PKOTotal').update(labelPko +data.PKO);

                        // if(data.TotalCapacity != null){
                        //     let labelTotalCapacity = lang('FFB Unproccesed : ');
                        //     Ext.getCmp('FFBUnproccesed').update(labelTotalCapacity + data.TotalCapacity);
                        // } else {
                        //     let labelTotalCapacity = lang('FFB Unproccesed :');
                        //     Ext.getCmp('FFBUnproccesed').update(labelTotalCapacity + '0');
                        // }
                        
                        // Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').setValue(parseFloat(data.ProductPercentageCpo));
                        Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').setValue();

                        // Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').setValue(parseFloat(data.ProductPercentagePk));
                        Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').setValue();

                        Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.HaveOer').setValue(parseInt(data.HaveOer));
                        Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.flagPk').setValue(parseInt(data.flagPk));
                        Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.flagCpo').setValue(parseInt(data.flagCpo));

                        if (parseInt(data.HaveOer) == 1) {
                            Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').hide();
                            Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').hide();
                        } else {
                            if (data.flagPk == 0) {
                                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').hide();
                            }

                            if (data.flagCpo == 0) {
                                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').hide();
                            }
                        }
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ProductID;

            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if(patchouli_grower_ls != null){
                ProductID        = patchouli_grower_ls.ProductID;
            }else{
                ProductID        = "";
            }

            // store.proxy.extraParams.ProductID = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductTypeSearch').getValue();

            store.proxy.extraParams.ProductID = this.storeVar.ProductID;
        }
    }
});