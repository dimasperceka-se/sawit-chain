var cmbSupplyType = Ext.create('Ext.data.Store', {
    fields: ['id', 'label'],
    data : [
        {"label":lang('Mill'), "id":'mill'},
        {"label":lang('Agent'), "id":'agent'}
    ]
});

var storeMainGridPenjualanTransaction = Ext.create('Koltiva.store.Traceability_new.Report.MainGridRecapSales');

Ext.define('Koltiva.view.Traceability_neo.Report.List_penjualan' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_neo.Report.List_penjualan', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    listeners: {
        afterRender: function(){
            //isikan variabel dari local storage
            var palm_penerimaan_list_searchp = JSON.parse(localStorage.getItem('palm_penerimaan_list_searchp'));
            if(palm_penerimaan_list_searchp != null){
                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').setValue(palm_penerimaan_list_searchp.ptextSearch);
                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').setValue(palm_penerimaan_list_searchp.pstatusSearch);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability_neo.Report.List_penjualan-gridTransaction').getStore().load();

            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').hide();
        }
    },
    submitOnEnterGrid: function(field, event){
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Traceability_neo.Report.List_penjualan').setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability_neo.Report.List_penjualan-gridTransaction').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
        localStorage.setItem('palm_penerimaan_list_searchp', JSON.stringify({
            // ptextSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').getValue(),
            // pstatusSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').getValue()
        }));
    },
    initComponent: function() {
        var thisObj = this;
        
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_neo.Report.List_penjualan-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeMainGridPenjualanTransaction,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar',
                                store: storeMainGridPenjualanTransaction,
                                dock: 'bottom',
                                displayInfo: true
                            },
                            {
                                xtype: 'toolbar',
                                dock:'top',
                                items: [ 
                                // {
                                //     xtype: 'combo',
                                //     store: cmbSupplyType,
                                //     id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus',
                                //     name: 'SupplyStatus',
                                //     queryMode: 'local',
                                //     displayField: 'label',
                                //     width: 100,
                                //     valueField: 'id',
                                //     emptyText: lang('Seller Type'),
                                //     listeners: {
                                //         specialkey: thisObj.submitOnEnterGrid
                                //     }
                                // }, 
                                // {
                                //     name: 'SupplyKey',
                                //     id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey',
                                //     xtype: 'textfield',
                                //     width: 250,
                                //     emptyText: lang('Search by Name'),
                                //     listeners: {
                                //         specialkey: thisObj.submitOnEnterGrid
                                //     }
                                // },
                                // {
                                //     xtype: 'button',
                                //     id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-BtnSearch',
                                //     icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                //     margin: '0px 10px 0px 6px',
                                //     text: lang('Search'),
                                //     handler: function() {
                                //         Ext.getCmp('Koltiva.view.Traceability_neo.Report.List_penjualan').setFilterLs();
                                //         Ext.getCmp('Koltiva.view.Traceability_neo.Report.List_penjualan-gridTransaction').getStore().loadPage(1);                                   
                                //     }
                                // }, 
                                {
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar-BtnExports',
                                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Export'),
                                    cls:'Sfr_BtnGridGreen',
                                    overCls:'Sfr_BtnGridGreen-Hover',
                                    handler: function() {

                                        Ext.MessageBox.show({
                                            msg: 'Please wait...',
                                            progressText: 'Exporting...',
                                            width: 300,
                                            wait: true,
                                            waitConfig: {
                                                interval: 200
                                            },
                                            icon: 'ext-mb-download', //custom class in msg-box.html
                                            animateTarget: 'mb7'
                                        });

                                        var param_string    = '?sid='+m_sid;
        
                                        try {
                                            Ext.destroy(Ext.get('downloadIframe'));
                                        }
                                        catch(e) {}
        
                                        Ext.Ajax.request({
                                            url: m_api+'/traceability_api/web_transaction/export_excel_report/'+param_string,
                                        
                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function(data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function() {
                                                Ext.MessageBox.hide();
                                                Ext.MessageBox.show({
                                                    title: 'Notifications',
                                                    msg: 'Failed to export, Please try again.',
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                        
                                    }
                                },
                                {
                                    xtype: 'container',
                                    flex: 1
                                }]
                            }],
                            columns: [
                            {
                                text: lang('Date'),
                                dataIndex: 'Tanggal_pengiriman',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                width:100,
                            },
                            {
                                text: 'Buyer Name',
                                dataIndex: 'Nama_agen',
                                width:100
                            },
                            {
                                text: 'Destination Name',
                                dataIndex: 'Tujuan_Mill',
                                flex:1
                            },
                            {
                                text: lang('Delivery to Mill'),
                                dataIndex: 'Berat_kotor_pengiriman',
                                flex:1
                            },{
                                text: lang('Validate From Mill'),
                                dataIndex: 'Berat_bersih_dijual',
                                flex:1
                            }, 
                            {
                                text: lang('Total Price'),
                                dataIndex: 'Total_harga',
                                flex:1,
                                renderer: Ext.util.Format.numberRenderer('0,000,000')
                            } 
                         ] 
                }];
                this.callParent(arguments);
    }
});

function SetFormTransactionPenerimaan(SupplyBatchID) { 
         
        Ext.Ajax.request({
            waitMsg: 'Please Wait',
            url: m_api + '/web-traceability/data-edit-penerimaan',
            method: 'get',
            params: {
                SBID : SupplyBatchID,
                SID : m_sid,
                page : 1,
                start : 0,
                limit :1
            },
            success: function(response, opts) {
                var r = Ext.decode(response.responseText);  
                pInputEnabled()                  
                 
                //Ext.getCmp('pmyGridQuality').getStore().load({ params : { STID : r.data[0].SupplyTransID, SBID : r.data[0].SupplyBatchID , SID : m_sid } });
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID').setValue(r.data[0].SupplyTransID); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID').setValue(r.data[0].SupplyBatchID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DeliveryDate').setValue(r.data[0].DeliveryDate);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchNumber').setValue(r.data[0].SupplyBatchNumber); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestPO').setValue(r.data[0].DestPO); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight').setValue(r.data[0].DestWeight); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-Weather').setValue(r.data[0].Weather); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportID').setValue(r.data[0].DestTransportID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestDriver').setValue(r.data[0].DestDriver);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportNumber').setValue(r.data[0].DestTransportNumber);    
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchStatus').setValue(r.data[0].SupplyBatchStatus);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-act').setValue("Yes");
                  
                var strDate = Ext.util.Format.date(r.data[0].DateTransaction, "Y-m-d"); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction').setValue(strDate);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setValue(r.data[0].VolumeNetto);
                
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction').setReadOnly(false);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setReadOnly(false);

                let getValueVolumeNetto = r.data[0].VolumeNetto;

                if (isNaN(parseFloat(getValueVolumeNetto))) {
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').hide();
                } else {
                    let setMessage
                    let getDestWeight   = parseFloat(r.data[0].DestWeight);
                    let getPersen20     = parseFloat(getDestWeight) - parseFloat((20/100) * getDestWeight);
                    let getPersen20plus = parseFloat(getDestWeight) + parseFloat((20/100) * getDestWeight);

                    if (parseFloat(getValueVolumeNetto) < parseFloat(getPersen20)) {
                        setMessage = lang("Less than 20%");

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').show();
                        Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").update(`<div style="margin-left:180px;color:#ED2F0D;">${lang(setMessage)}</div>`);
                    } else if (parseFloat(getValueVolumeNetto) > parseFloat(getPersen20plus)) {
                        setMessage = lang("More than 20%");

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').show();
                        Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").update(`<div style="margin-left:180px;color:#ED2F0D;">${lang(setMessage)}</div>`);
                    } else {
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').hide();
                    }
                }
                
            }
        })
}