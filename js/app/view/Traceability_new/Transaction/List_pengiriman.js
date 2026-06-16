
var cmbSupplyType = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyType');
var cmbSupplyStatus = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus');
var MainGridPengirimanTransaction = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridPengirimanTransaction');
Ext.define('Koltiva.view.Traceability_new.Transaction.List_pengiriman' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.List_pengiriman', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;
		
		var contextMenuListPengiriman = Ext.create('Ext.menu.Menu',{
            items:[ 
			{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuViewItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransactionPengiriman(sm.get('SupplyBatchID'),sm.get('SupplyBatchStatus'),'view'); 
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-actioncolumn').hide();
	                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setReadOnly(true);
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCancel').hide(); 
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Proses Pengiriman'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getSelectionModel().getSelection()[0]; 
                    SetFormTransactionPengiriman(sm.get('SupplyBatchID'),sm.get('SupplyBatchStatus')); 
                    
	                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setReadOnly(true);
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').setDisabled(false); 
                }
            },
			// {
            //     icon: varjs.config.base_url + 'images/icons/silk/printer.png',
            //     text: lang('Surat Jalan'),
            //     itemId: 'Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuCetakSuratJalanItem', 
            //     handler: function() {
            //         var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getSelectionModel().getSelection()[0];
            //         preview_cetak_surat(m_api + '/web-traceability/cetak-suratjalan/' + sm.get('SupplyBatchID') +'/' + m_sid );  
            //     }
            // },
            {
                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                text: lang('Surat Jalan'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuCetakSuratJalanMIniItem', 
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_api + '/traceability_api/web_pengiriman/cetak_suratjalanmini/' + sm.get('SupplyBatchID') +'/' + m_sid );  
                }
            }]
        });
		
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: MainGridPengirimanTransaction,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar',
                                store: MainGridPengirimanTransaction,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [ 
								{
                                    xtype: 'combo',
                                    store: cmbSupplyStatus,
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-SupplyStatus',
                                    name: 'SupplyStatus',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    width: 100,
                                    valueField: 'id',
                                    emptyText: lang('Status Transaction')
                                }, {
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    emptyText: lang('Search by Name')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() { 
										var SupplyStatus = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-SupplyStatus').getValue(); 
										var SupplyKey = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-SupplyKey').getValue(); 
										MainGridPengirimanTransaction.load({ params : { SupplyKey : SupplyKey, SupplyStatus : SupplyStatus } })
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-BtnExport',
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
                                            url: m_api+'/traceability_api/web_pengiriman/export_excel/'+param_string,
                                        
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
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
								id :'Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuAction',
                                width:'10%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        contextMenuListPengiriman.showAt(e.getXY());
                                        var sm = record; 
                                        if(sm.data.SupplyBatchStatus == "Sent" || sm.data.SupplyBatchStatus == "Delivered"){
                                           contextMenuListPengiriman.getComponent('Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuUpdateItem').setVisible(false);
                                           contextMenuListPengiriman.getComponent('Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuViewItem').setVisible(true); 
                                        }else{
                                            contextMenuListPengiriman.getComponent('Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuUpdateItem').setVisible(true);
                                            contextMenuListPengiriman.getComponent('Koltiva.view.Traceability_new.Transaction.List_pengiriman-contextMenuViewItem').setVisible(false); 
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
                                text: 'Destination ID',
                                dataIndex: 'SupplyDestOrgID',
                                hidden: true
                            },{
                                text: lang('Destination Name'),
                                dataIndex: 'SupplyDestOrgName',
                                width:200,
                            },{
                                text: lang('Delivery Date'),
                                dataIndex: 'DeliveryDate',
                                width:100,
                            }, {
                                text: lang('Dest Weight'),
                                dataIndex: 'DestWeight',
                                width:150
                            },{
                                text: lang('Dest Package'),
                                dataIndex: 'DestNumberPackage',
                                width:90
                            }, {
                                text: lang('Driver'),
                                dataIndex: 'DestDriver',
                                width:100
                            } ] 
				}];
				this.callParent(arguments);
    }
});

function SetFormTransactionPengiriman(SupplyBatchID, SupplyBatchStatus, action) { 
		 
		Ext.Ajax.request({
			waitMsg: 'Please Wait',
			url: m_api + '/web-traceability/pengiriman-main-grid',
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
				pgInputEnabledUpdate() 
				
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').store.reload({ params : { SBID : SupplyBatchID },
					callback : function(record, index)
					{
						//console.log(record, index)
						var tGross =0, tNett=0, tPackage=0;
						for(i=0;i<record.length;i++)
						{
							tGross += parseFloat(record[i].data.VolumeBruto);
							tNett += parseFloat(record[i].data.VolumeNetto);
							tPackage += parseFloat(record[i].data.PackageNumber);
						}
						Ext.fly('labelGross').update('Total Gross : ' + tGross);
						Ext.fly('labelNett').update('Total Nett : ' + tNett);  
						Ext.fly('labelPackage').update('Total Package : ' + tPackage); 
					}
			    });
				 
				if(SupplyBatchStatus == 'Open'){
					pgButtonProcessEnabled();
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-actioncolumn').show();
				}
				if(SupplyBatchStatus == 'Closed'){
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').hide(); 
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSent').show();
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-actioncolumn').hide();
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').hide(true);  
				}
				 
				if(SupplyBatchStatus == 'Sent' || SupplyBatchStatus == 'Delivered' ){
					Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').hide(true);  
					if(action == 'view'){
						Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(false); 
						Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').hide(); 
					}
					else{
						pgButtonProcessHidden(); 
					}
				}
				
				
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight').setValue(r.data[0].DestWeight);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage').setValue(r.data[0].DestNumberPackage); 
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').setValue(r.data[0].SupplyBatchID);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setValue(r.data[0].DeliveryDate);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchNumber').setValue(r.data[0].SupplyBatchNumber); 
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO').setValue(r.data[0].DestPO);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setValue(r.data[0].Mill);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight').setValue(r.data[0].DestWeight);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage').setValue(r.data[0].DestNumberPackage);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID').setValue(r.data[0].DestTransportID);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber').setValue(r.data[0].DestContainerNumber);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver').setValue(r.data[0].DestDriver);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber').setValue(r.data[0].DestTransportNumber);	
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchStatus').setValue(r.data[0].SupplyBatchStatus);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMillName').setValue(r.data[0].SupplyDestMillOtherName);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setValue(r.data[0].SMESPCodeID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO').setValue(r.data[0].SupplyDestDoOrgID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType').setValue(r.data[0].SupplyDestProcessType);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType').setValue(r.data[0].SupplyType);
                if(r.data[0].SupplyDestMillOtherName === null || r.data[0].SupplyDestMillOtherName === ''){
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMill').setValue(false);
                }else{
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMill').setValue(true);
                }
			}
		})
}