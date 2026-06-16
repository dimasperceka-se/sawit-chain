
// var cmbReceptionStatus = Ext.create('Koltiva.store.Traceability.Reception.ComboSupplyStatus');
var storeMainGridReception = Ext.create('Koltiva.store.Traceability.Reception.MainGrid');
Ext.define('Koltiva.view.Traceability.Reception.ReceptionList' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.ReceptionList', 
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
                // Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey').setValue(palm_penerimaan_list_searchp.ptextSearch);
                // Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus').setValue(palm_penerimaan_list_searchp.pstatusSearch);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getStore().load();
        }
    },
    submitOnEnterGrid: function(field, event){
    	if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList').setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
    	localStorage.setItem('palm_penerimaan_list_searchp', JSON.stringify({
            // ptextSearch: Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey').getValue(),
            // pstatusSearch: Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus').getValue()
        }));
    },
    initComponent: function() {
        var thisObj = this;
		
		var contextMenuListPenerimaan = Ext.create('Ext.menu.Menu',{
            items:[
			{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                id: 'Koltiva.view.Traceability.Reception.ReceptionList-contextMenuViewItem',
                handler: function() {
                   var sm = Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getSelectionModel().getSelection()[0];
                //    console.log(sm);
                    SetFormTransactionPenerimaan(sm.get('ShippingDate'));  
					// Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnSave').hide();
					// Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnCancel').hide();
                }
            },
			{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Proses Penerimaan'),
                id: 'Koltiva.view.Traceability.Reception.ReceptionList-contextMenuAddItem',
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransactionPenerimaan(sm.get('ShippingDate'));  
                    Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnSave').show();
					Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnCancel').show();
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/document_link.png',
                id: 'Koltiva.view.Traceability_new.Reception.MainGrid.ContextMenu-Detail',
                text: lang('Reception Detail'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getSelectionModel().getSelection()[0];
                    console.log(sm)
                    Ext.getCmp('Koltiva.view.Traceability.Reception.MainGrid').destroy(); //destory current view

                    var MainFormDispatch = [];
                    if (Ext.getCmp('Koltiva.view.Traceability.Reception.MainGridTransaction') == undefined) {
                        MainFormDispatch = Ext.create('Koltiva.view.Traceability.Reception.MainGridTransaction', {
                            viewVar: {
                                ShippingDate: sm.raw.ShippingDate
                            }
                        });
                    } else {
                        MainFormDispatch = Ext.create('Koltiva.view.Traceability.Reception.MainGridTransaction', {
                            viewVar: {
                                ShippingDate: sm.raw.ShippingDate
                            }
                        });
                    }
                },
            }
        ]
        });
		
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            minHeight:300,
                            selType: 'rowmodel',
                            store: storeMainGridReception,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar',
                                store: storeMainGridReception,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [ 
								// {
                                    // xtype: 'combo',
                                    // store: cmbReceptionStatus,
                                    // id: 'Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus',
                                    // name: 'SupplyStatus',
                                    // queryMode: 'local',
                                    // displayField: 'label',
                                    // width: 100,
                                    // valueField: 'id',
                                    // emptyText: lang('Status Transaction'),
                                    // listeners: {
                                    //     specialkey: thisObj.submitOnEnterGrid
                                    // }
                                // }, 
                                // {
                                //     name: 'SupplyKey',
                                //     id: 'Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey',
                                //     xtype: 'textfield',
                                //     hidden:true,
                                //     width: 250,
                                //     emptyText: lang('Search by Name/Dispatch Number'),
                                //     listeners: {
                                //         specialkey: thisObj.submitOnEnterGrid
                                //     }
                                // },
                                // {
                                //     xtype: 'button',
                                //     id: 'Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-BtnSearch',
                                //     icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                //     margin: '0px 10px 0px 6px',
                                //     text: lang('Search'),
                                //     handler: function() {
                                //         Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList').setFilterLs();
                                //         Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getStore().loadPage(1);									  
                                //     }
                                // },
                                {
                                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                    text: lang('Export'),
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
                    
                                        try {
                                            Ext.destroy(Ext.get('downloadIframe'));
                                        } catch (e) {}

                                        // var status  = Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus').getValue();
                                        // var key     = Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey').getValue();
                    
                                        Ext.Ajax.request({
                                            // url: m_api + '/dispatch/refinery/export_reception?searchstatus='+status+'&key='+key,
                                            url: m_api + '/dispatch/refinery/export_reception',
                    
                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function (data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function () {
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
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }]
                            }],
                            columns: [
                            {
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width:'10%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        contextMenuListPenerimaan.showAt(e.getXY());
                                        var sm = record;   
										if(sm.data.Status == "Sent"){  
                                            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuViewItem').show();
                                            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuAddItem').show(); 
										}
										else if(sm.data.Status == "Received"){
                                            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuViewItem').show();
                                            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuAddItem').hide();  
										}
										else
										{
											Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuViewItem').show();
                                            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-contextMenuAddItem').hide();  
										}
                                    }
                                }]
                            },
							{
                                text: lang('Status'),
                                dataIndex: 'Status',
                                flex:1 
                            },{
                                text: lang('Shipping Date'),
                                dataIndex: 'ShippingDate',
                                flex:1
                            }, {
                                text: lang('Dispatch Volume'),
                                dataIndex: 'DespatchVolume',
                                flex:1
                            }, 
							{
                                text: lang('Receipt Date'),
                                dataIndex: 'ReceptionDate',
                                flex:1
                            }] 
				}];
				this.callParent(arguments);
    }
});

function SetFormTransactionPenerimaan(ShippingDate) { 
		 
		Ext.Ajax.request({
			waitMsg: 'Please Wait',
			url: m_api + '/dispatch/refinery/getPernerimaan',
			method: 'get',
			params: {
				ShippingDate : ShippingDate,
				SID : m_sid,
				page : 1,
				start : 0,
				limit :1
			},
			success: function(response, opts) {
				var r = Ext.decode(response.responseText);  
                pInputEnabled()
                
                Ext.getCmp('Koltiva.view.Traceability.Reception.GridDispatch-Grid').store.reload({ params : { ShippingDate : ShippingDate }});
                Ext.getCmp('Koltiva.view.Traceability.Reception.GridProduct-Grid').store.reload({ params : { ShippingDate : ShippingDate }});

                // Ext.getCmp('Koltiva.view.Traceability.Reception.GridDispatch-Grid').store.reload({ params : { DespatchID : DespatchID }});
                // Ext.getCmp('Koltiva.view.Traceability.Reception.GridProduct-Grid').store.reload({ params : { DespatchID : DespatchID }});
				 
				//Ext.getCmp('pmyGridQuality').getStore().load({ params : { STID : r.data[0].SupplyTransID, SBID : r.data[0].DespatchID , SID : m_sid } });
		 		Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-Status').setValue(r.data[0].STATUS);
				Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ShippingDate').setValue(r.data[0].ShippingDate); 
                Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchID').setValue(r.data[0].DespatchID); 
				Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchVolume').setValue(r.data[0].DespatchVolume); 
                Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-act').setValue("Yes");
				  
				var strDate = Ext.util.Format.date(r.data[0].ReceptionDate, "Y-m-d"); 
                Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionDate').setValue(strDate);
                Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto').setValue(r.data[0].DespatchVolume);
                
                // Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionDate').setReadOnly(true);
                Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto').setReadOnly(true);
                
			}
		})
}