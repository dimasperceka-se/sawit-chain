  

Ext.define('Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman' ,{ 
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman',
    title: lang('Transaction'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var storeWindowTransactionPengiriman = Ext.create('Koltiva.store.Traceability_new.Transaction.window.storeWindowTransactionPengiriman');
		var MainGridTransactionPengiriman = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridTransactionPengiriman');
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman-grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeWindowTransactionPengiriman,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman-gridToolbar',
                                store: storeWindowTransactionPengiriman,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    emptyText: lang('Search by Name / ID')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        storeWindowTransactionPengiriman.load()
                                    }
                                } ]
                            }],
							selModel: {
									selType: 'checkboxmodel',
									checkOnly: true,
									multiSelect: true,
									mode: "MULTI",
									headerWidth: 50,
									listeners: { 
										select: function(model, record, index) { 
												id = record.get('SupplyTransID');  
												Ext.Ajax.request({
													url: m_api + '/web-traceability/submit-transaction',
													method: 'POST',
													waitMsg: lang('Sending data...'),
													params: { 
														STID: id,
														Status : 1,
														SBID : Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue()
													},
													success: function(response, opts) {
														var obj = Ext.decode(response.responseText);
														switch (obj.success) {
															case true:   
															    
																//reload grid Transaksi yang di form transaksi Pengiriman
																var SupplyBatchID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue();
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').store.reload({ params : { status: 2,SBID : SupplyBatchID,  },
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
																		Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight').setValue(tNett);
																		Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage').setValue(tPackage);
																	}
																});
																
																storeWindowTransactionPengiriman.load();//reload Window grid 
																 
																break;
															default:
																Ext.MessageBox.alert('Warning', obj.message);
																break;
														}
													}
												});
										 
										}
									}					
								}, 
                            columns: [
							{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },
							{
                                text: 'ID',
                                dataIndex: 'SupplyID',
                                hidden: true
                            },{
                                text: lang('Supply Type'),
                                dataIndex: 'SupplyType',
                                width:100,
                            },{
                                text: lang('Supplier ID'),
                                dataIndex: 'SupplyID',
                                width:100,
                            },{
                                text: lang('Supplier Name'),
                                dataIndex: 'SupplierName',
                                width:250,
                            },{
                                text: lang('Certified'),
                                dataIndex: 'Certified',
                                width:250,
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                width:100,
                            },{
                                text: lang('Gross'),
                                dataIndex: 'VolumeBruto',
                                width:100
                            },{
                                text: lang('Package'),
                                dataIndex: 'PackageNumber',
                                width:90
                            }, {
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                width:150
                            } ]  
            }];
        //items --------------------------------------------------------------------------------------------------------------- (end)

        //buttons --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [ {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons --------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this; 

        }
    }
});

  