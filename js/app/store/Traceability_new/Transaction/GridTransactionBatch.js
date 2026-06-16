Ext.define('Koltiva.store.Traceability_new.Transaction.GridTransactionBatch', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.GridTransactionBatch',
    storeId: 'Koltiva.store.Traceability_new.Transaction.GridTransactionBatch',
    fields: [{name:'SupplyTransID'}, {name:'SupplyType'}, {name:'DateTransaction'}, {name:'FakturNumber'}, {name:'Name'}, {name:'SupplyStatus'}, {name:'VolumeNetto',type:'float'}, {name:'NumberPackage',type:'int'}],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        //url: m_api + '/tc_transaction/transaction_batch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.Role = this.storeVar.Role;
            store.proxy.extraParams.SupplyBatchID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.WinBatchFrom-Form-SupplyBatchID').getValue();
        }, 
        load: function(store, record) {
            var bruto = 0;
            var netto = 0;
            var tandan = 0;
            Ext.each(record, function(one){
                //bruto = bruto+one.data.VolumeBruto;
                netto = netto+one.data.VolumeNetto;
                tandan = tandan+one.data.NumberPackage;
            });
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.WinBatchFrom-Form-VolumeNetto').setValue(netto.toFixed(2));
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.WinBatchFrom-Form-DestNumberPackage').setValue(tandan);
            /*Ext.getCmp('VolumeBruto').setValue(bruto.toFixed(2));
            if (Ext.getCmp('DestWeight').getValue()=='')Ext.getCmp('DestWeight').setValue(bruto.toFixed(2));
            
            Ext.getCmp('VolumeNetto').setValue(netto.toFixed(2));
            Ext.getCmp('Package').setValue((bruto-netto).toFixed(2));
            
            Ext.getCmp('VolumeNettoHighBrix').setValue(nettoHighBrix.toFixed(2));
            Ext.getCmp('VolumeNettoLowBrix').setValue(nettoLowBrix.toFixed(2));*/
            
            //Ext.getCmp('BatchJumlahKarung').setValue(jumlahkarung);
            
         }
    }
});