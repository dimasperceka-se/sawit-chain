


Ext.define('Koltiva.view.Traceability_new.Transaction.FormPengiriman', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman',
    flex: 1,
    padding: 5,  
    margin: '0 0 0 0', 
    initComponent: function () { 
         
        var thisObj = this; 
        
		var ComboDestination = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboDestination'); 
		var ComboDestinationDO = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboDestinationDO'); 
		var ComboTransport = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboTransport'); 
		var MainGridPengirimanTransaction = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridPengirimanTransaction');
		var Grid_transact_pengiriman = Ext.create('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman');
		var ComboSPB = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSPB');
		var ComboDestType = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Mill'), "id":'mill'},
                {"label":lang('Mill & DO'), "id":'do'},
                {"label":lang('Agent'), "id":'agent'}
                //...
            ]
		});
		var ComboProcDestination = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Processing in Mill'), "id":'mill'},
                {"label":lang('Processing in  DO'), "id":'do'}
                //...
            ]
		});
        thisObj.items = [
					{
					xtype: 'toolbar',
					dock:'top',
					//style: 'border-style: none',
					style: 'margin-top: -10px',
					items: [{
								icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
								id :'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction', 
								text: lang('Add New Delivery'),
								handler: function() { 
									 var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form').getForm();
									 formNya.reset();	
									 pgInputEnabled() 	
									 
										//reset semua
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').store.reload({ params : { SBID : 0 },
											callback : function(record, index)
											{
												//console.log(record, index)
												var tGross =0, tNett=0, tPackage=0; 
												Ext.fly('labelGross').update('Total Gross : ' + tGross);
												Ext.fly('labelNett').update('Total Nett : ' + tNett);  
												Ext.fly('labelPackage').update('Total Package : ' + tPackage); 
											}
										});
								}
							}] 
					},
					{
                    columnWidth:1, 
                    layout:'form',
					xtype: 'form',
					id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form',
                    style: 'padding:15px;',
                    items:[
						{
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Delivery Batch')+'</div>'
						},
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchStatus',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchStatus' 
						},
						{
							xtype: 'datefield',
							labelAlign:'top',
							fieldLabel: lang('Delivery Date'),
							labelWidth:175, 
							format: 'Y-m-d',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate',
							value: m_now,
							readOnly: true,
							
						},
						{
							xtype: 'textfield',
							labelAlign:'top',
							fieldLabel: lang('Batch Number'),
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchNumber',
							readOnly :true,
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchNumber', 
				       },
					   {
						xtype: 'textfield',
						labelAlign:'top',
						fieldLabel: lang('Dest PO'),
						allowBlank : false,
						readOnly:true,
						id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO',
						name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO', 
				       },  
					   {
							xtype: 'combobox',
							labelAlign:'top',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType',
							allowBlank : false,
							store: ComboDestType,
							fieldLabel: lang('Delivery Type'),
							queryMode: 'local',
							readOnly:true,
							displayField: 'label',
							valueField: 'id',
							listeners : {
								change : function(val)
								{  
									var MillID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').getValue();
									if(val.getValue() == 'mill'){  
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setVisible(true);
										Ext.getCmp('MillOther').setVisible(true);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO').setVisible(false);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType').setVisible(false);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setVisible(true);
										ComboDestination.load({params : {'SID':m_sid, 'type':val.getValue() } });
										ComboSPB.load({params : {'MillID' : MillID,'SID':m_sid, 'type':val.getValue() } });
									}
									if(val.getValue() == 'do'){  
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setVisible(true);
										Ext.getCmp('MillOther').setVisible(true); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO').setVisible(true);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType').setVisible(true);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setVisible(true);
										ComboDestination.load({params : {'SID':m_sid, 'type':'mill' } });
										ComboDestinationDO.load({params : {'MillID' : m_sid} });
										ComboSPB.load({params : {'MillID' : MillID,'SID':m_sid, 'type':val.getValue() } });
									}
									if(val.getValue() == 'agent'){  
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setVisible(false);
										Ext.getCmp('MillOther').setVisible(false);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO').setVisible(true);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType').setVisible(false);
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setVisible(false);
										ComboDestination.load({params : {'SID':m_sid, 'type':'mill' } });
										ComboDestinationDO.load({params : {'MillID' : m_sid} });
									}
								}								
							} 
					   },{
							layout: 'column',
							border: false,
							columnWidth:1,
							items:[{
								columnWidth: 0.6,
								layout:'form',
								style:'padding-right:25px;',
								items:[{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill',
									name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill',
									allowBlank : true,
									hidden:true,
									labelAlign:'top',
									store: ComboDestination, 
									labelWidth:200, 
									fieldLabel: lang('Mill'), 
									queryMode: 'local',
									displayField: 'Name',
									valueField: 'SupplychainID',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){
											var records = combo.store.findRecord('SupplychainID', value);
											console.log(records);

											if(typeof records !== 'undefined' && records != null){
												var type = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType').getValue();
												if(type == 'mill' || type == 'do'){
													ComboSPB.load({params : {'MillID' : records.data.SupplychainID,'SID':m_sid, 'type':type } });
													ComboDestinationDO.load({params : {'MillID' : records.data.SupplychainID,'SID':m_sid, 'type':type } });
												}
											}
										
										} 							
									}
								}]
							},{
								columnWidth:0.38,
								layout:'column',			
								// style: 'padding-right:10px;',
								items:[{
									xtype: 'fieldcontainer',
									fieldLabel: lang('Other MIll'),
									defaultType: 'checkboxfield',
									labelAlign:'top',
									id:'MillOther',
									hidden: true,
									items: [
										{
											boxLabel  : lang('Yes'),
											name      : 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMill',
											inputValue: '1',
											id        : 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMill',
											listeners:{
												change: function(checkbox, newValue, oldValue, eOpts) {
													if(newValue){
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMillName').setVisible(true);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setReadOnly(true);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setValue('');
													}else{
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMillName').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setReadOnly(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setValue('');
													}
												}
											}
										}
									]
								}]
							}]
						},
						{
							xtype: 'textfield',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMillName',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-OtherMillName',
							fieldLabel: lang('Other MIll Name'),
							labelAlign:'top',
							hidden:true,
							listeners :{
								change:function(val){
									 
								}
							} 
							
						},{
							layout: 'column',
							border: false,
							columnWidth:1,
							items:[{
								columnWidth: 0.6,
								layout:'form',
								style:'padding-right:25px;',
								items:[{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB',
									name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SPB',
									allowBlank : true,
									hidden:true,
									labelAlign:'top',
									store: ComboSPB, 
									labelWidth:200, 
									fieldLabel: lang('SPB'), 
									queryMode: 'local',
									displayField: 'label',
									valueField: 'id',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){ 
											// var records = combo.store.findRecord('id', value); 
											// console.log(records.data.id);
										
										} 							
									}
								}]
							}]
						},{
							layout: 'column',
							border: false,
							columnWidth:1,
							items:[{
								columnWidth: 0.6,
								layout:'form',
								style:'padding-right:25px;',
								items:[{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO',
									name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DO',
									allowBlank : true,
									hidden:true,
									labelAlign:'top',
									store: ComboDestinationDO, 
									labelWidth:200, 
									fieldLabel: lang('DO'), 
									queryMode: 'local',
									displayField: 'label',
									valueField: 'id',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){
										
										} 							
									}
								}]
							}]
						},{
							layout: 'column',
							border: false,
							columnWidth:1,
							items:[{
								columnWidth: 0.6,
								layout:'form',
								style:'padding-right:25px;',
								items:[{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType',
									name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestProcessType',
									allowBlank : true,
									hidden:true,
									labelAlign:'top',
									store: ComboProcDestination, 
									labelWidth:200, 
									fieldLabel: lang('Processing Destination'), 
									queryMode: 'local',
									displayField: 'label',
									valueField: 'id',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){
										
										} 							
									}
								}]
							}]
						},
					   	{
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Transaction')+'</div>'
					   	}, 
					   Grid_transact_pengiriman,
					   {
							xtype: 'fieldcontainer', 
							width : 450, 
							labelAlign:'top',
							fieldLabel: lang('Dest Weight'),										
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight',
									    name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight', 
										value:0
								  },
								  { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								 ]
					   },
						
					   {
							xtype: 'fieldcontainer', 
							width : 450, 
							labelAlign:'top',
							fieldLabel: lang('Dest Package'),									
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage',
										name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage', 
										fieldStyle:'text-align:right;', 
										value:0, 
								  } 
								 ]
					   },	 
					   {
							xtype: 'combobox',
							labelAlign:'top',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID',
							allowBlank : true,
							readOnly:true,
							store: ComboTransport,
							fieldLabel: lang('Transport type'),
							queryMode: 'local',
							displayField: 'DestTransportName',
							valueField: 'DestTransportID',
							listeners : {
								select :  function(a, record)
								{
									var r =  record[0]; 
									if(r.get('IsDetail') == 1){
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber').show();
									}else{
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber').hide();
									}
									
								},
								change :  function(val)
								{ 
									 DestTransportID = this.getValue(); // Value From Combo
									 var index = this.getStore().findExact('DestTransportID', DestTransportID); // Compares Value from combo to field invoiceid and returns the index
									 var record = this.getStore().getAt(index); // Gets the record at that index
									if(typeof records !== 'undefined' && records != null){
										IsDetail = record.get('IsDetail'); // Returns the value of the field 'processed'
										if(IsDetail == 1){
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber').show();
										}else{
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber').hide();
										}
									}
								}
							}	
					   }, 
					   {
							xtype: 'textfield',
							labelAlign:'top',
							fieldLabel: lang('Nomor Kontainer'), 
							labelWidth:175,
							hidden: true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestContainerNumber', 
				       }, 
					   {
							xtype: 'textfield',
							labelAlign:'top',
							fieldLabel: lang('Driver Name'),
							readOnly:true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver', 
				       },
					   {
							xtype: 'textfield',
							labelAlign:'top',
							fieldLabel: lang('Transport Number'),
							labelWidth:175,  
							readOnly:true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber', 
				       },
					]
				}];
        
		 //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Sent',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
			hidden : true,
            cls: 's-blue',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSent',
            handler: function () {
				var SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue();
				Ext.Ajax.request({
						url: m_api + '/web-traceability/sent-pengiriman',
						method: 'POST',
						waitMsg: lang('Sending data...'),
						params: {  
							SBID : SBID
						},
						success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							switch (obj.success) {
								case true:   
									Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').setDisabled(true);
									pgButtonProcessHidden()
									Ext.MessageBox.show({
										title: 'Information',
										msg: lang('Sent Batch berhasil'),
										buttons: Ext.MessageBox.OK,
										animateTarget: 'mb9',
										icon: 'ext-mb-success'
									});
									Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getStore().load({ params : { status: 1, SBID : SBID}}); 
									Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getStore().load();
									break;
								default:
									Ext.MessageBox.alert('Warning', obj.message);
									break;
							}
						}
					});
			}
		},{
            text: 'Close Batch',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
			hidden : true,
            cls: 's-blue',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch',
            handler: function () {
				
						var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form').getForm(); 
						var SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue(); 
						formNya.submit({
							url: m_api + '/web-traceability/pengiriman-submit', 
							headers: { 
								 SID: m_sid,
								 PID: m_pid,
								 SBID : SBID
							},
							method:'POST',
							waitMsg: 'Saving data...',
							success: function(fp, o) {
								var obj = Ext.JSON.decode(o.response.responseText);
								//console.log(obj.SBID) 
								 
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').setValue(obj.SBID)
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchNumber').setValue(obj.SupplyBatchNumber) 
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getStore().load({ params : { SBID : obj.SBID}});
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getStore().load();								
								 
							},
							failure: function(fp, o){
								 
								Ext.MessageBox.show({
									title: 'Error',
									msg: lang('Gagal Menyimpan Data'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-error'
								});
							}
						}); 
						
				var cekAdaGrid = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getStore().count();
				if(cekAdaGrid > 0){ 
					Ext.Ajax.request({
							url: m_api + '/web-traceability/close-pengiriman',
							method: 'POST',
							waitMsg: lang('Sending data...'),
							params: {  
								SBID : SBID
							},
							success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								switch (obj.success) {
									case true:   
										Ext.MessageBox.show({
											title: 'Information',
											msg: lang('Close Batch berhasil'),
											buttons: Ext.MessageBox.OK,
											animateTarget: 'mb9',
											icon: 'ext-mb-success'
										}); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').hide(); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSent').show(); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-BtnAdd').hide(); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getStore().load();	
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getStore().load({ params : { status: 1, SBID : SBID}}); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-actioncolumn').hide(); 
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').hide();
										break;
									default:
										Ext.MessageBox.alert('Warning', obj.message);
										break;
								}
							}
						}); 
						
				}	  
			}
		},{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
			disabled : true,
            cls: 's-green',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave',
            handler: function () {
						var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form').getForm();
						
						var SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue(); 
						formNya.submit({
							url: m_api + '/web-traceability/pengiriman-submit', 
							headers: { 
								 SID: m_sid,
								 PID: m_pid,
								 SBID : SBID
							},
							method:'POST',
							waitMsg: 'Saving data...',
							success: function(fp, o) {
								var obj = Ext.JSON.decode(o.response.responseText);
								//console.log(obj.SBID) 
								 
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').setValue(obj.SBID)
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyBatchNumber').setValue(obj.SupplyBatchNumber)
								Ext.MessageBox.show({
									title: 'Information',
									msg: lang('Data saved'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-success'
								});  
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getStore().load({ params : { SBID : obj.SBID}});
								Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getStore().load();								
								pgButtonProcessEnabled();
							},
							failure: function(fp, o){
								 
								Ext.MessageBox.show({
									title: 'Error',
									msg: lang('Gagal Menyimpan Data'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-error'
								});
							}
						}); 
			}
		},{
            text: 'Reset',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button', 
            cls: 's-black',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCancel',
            handler: function () {   
				pgInputDisabled(); 
				var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form').getForm();
				formNya.reset();	 
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').hide();
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(false);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-BtnAdd').setVisible(false);
				//reset semua
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').store.reload({ params : { SBID : 0 },
					callback : function(record, index)
					{
						//console.log(record, index)
						var tGross =0, tNett=0, tPackage=0; 
						Ext.fly('labelGross').update('Total Gross : ' + tGross);
						Ext.fly('labelNett').update('Total Nett : ' + tNett);  
						Ext.fly('labelPackage').update('Total Package : ' + tPackage); 
					}
				});
			}
		}]
		
		this.callParent(arguments);
    }
});

pgButtonProcessEnabled = function ()
{  
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSent').hide();
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').show();	  
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-BtnAdd').show();
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(true); 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').show();
	
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage').setReadOnly(false);
}

pgButtonProcessHidden = function ()
{ 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCloseBatch').hide();
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSent').hide(); 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(true);
}
 
pgInputEnabled = function ()
{ 
	
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').show(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCancel').show(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').setDisabled(false); 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setReadOnly(false);
	
}
 
pgInputEnabledUpdate = function ()
{ 
	
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').show(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnCancel').show(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').setDisabled(false); 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber').setReadOnly(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setReadOnly(true);
	
}
 
pgInputDisabled = function ()
{ 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-BtnSave').setDisabled(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-AddnewTransaction').setDisabled(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DeliveryDate').setReadOnly(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestPO').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-Mill').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestWeight').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SupplyDestType').setReadOnly(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestNumberPackage').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportID').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestDriver').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-DestTransportNumber').setReadOnly(true)
}

 