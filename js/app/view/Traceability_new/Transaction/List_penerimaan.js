
var cmbSupplyType = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyType');
var cmbSupplyStatus = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus');
var storeMainGridPenerimaanTransaction = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridPenerimaanTransaction');
Ext.define('Koltiva.view.Traceability_new.Transaction.List_penerimaan' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan', 
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
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').setValue(palm_penerimaan_list_searchp.ptextSearch);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').setValue(palm_penerimaan_list_searchp.pstatusSearch);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().load();

            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto').hide();
        }
    },
    submitOnEnterGrid: function(field, event){
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan').setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
        localStorage.setItem('palm_penerimaan_list_searchp', JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').getValue(),
            pstatusSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').getValue()
        }));
    },
    initComponent: function() {
        var thisObj = this;
        
        var contextMenuListPenerimaan = Ext.create('Ext.menu.Menu',{
            items:[
            {
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem',
                handler: function() {
                   var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransactionPenerimaan(sm.get('SupplyBatchID'));  
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave').hide();
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnCancel').hide();
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Proses Penerimaan'),
                id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem',
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransactionPenerimaan(sm.get('SupplyBatchID'));   
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave').show();
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnCancel').show();
                }
            }, 
            {
                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                text: lang('Print'),
                id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem', 
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_api + '/web-traceability/cetak-penerimaankuitansi/' +  sm.get('SupplyTransID'));  
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('Transaction Detail'),
                id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuListTransaction', 
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getSelectionModel().getSelection()[0];
                    
                    var WinListTrasactionBatch = Ext.create('Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan',{
                        viewVar: {
                            opsiDisplay : 'insert', 
                            SupplyBatchID        :  sm.get('SupplyBatchID')
                        }
                     }); 
                    if (!WinListTrasactionBatch.isVisible()) {
                        WinListTrasactionBatch.center();
                        WinListTrasactionBatch.show();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-grid').getStore().load();
                    } else {
                        WinListTrasactionBatch.close();
                    }   
                }
            },
        ]
        });
        
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeMainGridPenerimaanTransaction,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar',
                                store: storeMainGridPenerimaanTransaction,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [ 
                                {
                                    xtype: 'combo',
                                    store: cmbSupplyStatus,
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus',
                                    name: 'SupplyStatus',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    width: 100,
                                    valueField: 'id',
                                    emptyText: lang('Status Transaction'),
                                    listeners: {
                                        specialkey: thisObj.submitOnEnterGrid
                                    }
                                }, {
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 250,
                                    emptyText: lang('Search by Name/Batch Number'),
                                    listeners: {
                                        specialkey: thisObj.submitOnEnterGrid
                                    }
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan').setFilterLs();
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().loadPage(1);                                   
                                    }
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width:'10%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        contextMenuListPenerimaan.showAt(e.getXY());
                                        var sm = record;
                                        
                                        if(sm.data.SupplyBatchStatus == "Received"){
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem').show();
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem').hide(); 
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem').show(); 
                                         } else {
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem').show();
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem').show(); 
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem').hide();
                                         }

                                        if(sm.data.SupplyDestOrgID == m_sid){
                                            if(sm.data.SupplyBatchStatus == "Pending"){  
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem').show();
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem').show(); 
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem').hide();
                                            }
                                            else if(sm.data.SupplyBatchStatus == "Sent"){
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem').show();
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem').hide(); 
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem').show(); 
                                            }
                                            else
                                            {
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuViewItem').show();
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuAddItem').hide();  
                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-contextMenuCetakKwitansiItem').show();
                                            }
                                        }
                                    }
                                }]
                            },
                            {
                                text: lang('Status'),
                                dataIndex: 'SupplyBatchStatus',
                                width:100 
                            },
                            {
                                text: lang('Batch Number'),
                                dataIndex: 'SupplyBatchNumber',
                                width:200
                            }, 
                            {
                                text: 'SupplierID',
                                dataIndex: 'SupplyDestOrgID',
                                hidden: true
                            },{
                                text: lang('Company Name'),
                                dataIndex: 'SupplierName',
                                width:200,
                            },{
                                text: lang('Delivery Date'),
                                dataIndex: 'DeliveryDate',
                                width:100,
                            }, {
                                text: lang('Dest Weight'),
                                dataIndex: 'DestWeight',
                                width:150
                            }, 
                            {
                                text: lang('Receipt Date'),
                                dataIndex: 'SupplyBatchDate',
                                width:120
                            }, 
                            {
                                text: lang('Package'),
                                dataIndex: 'PackageNumber',
                                width:150
                            },{
                                text: lang('Gross'),
                                dataIndex: 'Bruto',
                                width:100
                            }, 
                            {
                                text: lang('Net'),
                                dataIndex: 'Net',
                                width:100
                            } ] 
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