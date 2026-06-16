
 /**
* Ext.ux.grid.DynamicGridPanel
*/

var pComboQuality = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboQuality');
var pmyStore = Ext.create('Ext.data.Store',{
		 model:'dynamicModel',
		 autoLoad:false,
		 autoSync: false

});
 
Ext.define('Ext.ux.grid.DynamicGridPanel', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.dynamicgrid', 
    initComponent: function(){ 
        var config = {
            columns:[],
            rowNumberer: false,
			viewConfig: {forceFit: true},
			enableColLock: false,
			loadMask: true,
			border: false,
			stripeRows: true,
        };
 
        Ext.apply(this, config); 
        Ext.apply(this.initialConfig, config); 
        this.callParent(arguments);
    },
    /**
    * When the store is loading then reconfigure the column model of the grid
    */
    storeLoad: function()
    { 
        if(typeof(this.store.proxy.reader.jsonData.columns) === 'object') {
            var columns = []; 
            if(this.rowNumberer) { columns.push(Ext.create('Ext.grid.RowNumberer')); }
		  
            Ext.each(this.store.proxy.reader.jsonData.columns, function(column){
				column['getEditor'] = function( record ) { 
						var type = record.get('Type');  
						if( column.dataIndex === 'Value' ){ //filter dibawah ini hanya bekerja pada coloumn Value
							if (type === 'text') {
								return new Ext.create('Ext.grid.CellEditor', {
									field: Ext.create('Ext.form.field.Text')
								});
							} else if (type === 'combo') { 
								var ComboQualitys = Ext.create('Ext.form.field.ComboBox', {
									store: pComboQuality,
									displayField : 'Value',
									valueField : 'Value',	
									labelSeparator: '', 
									queryMode: 'local', 
									fieldStyle : 'text-align:left;' 
								});
								return new Ext.create('Ext.grid.CellEditor', {
									field: ComboQualitys
								});
							} else if (type === 'decimal') {
								return new Ext.create('Ext.grid.CellEditor', {
									field: Ext.create('Ext.form.field.Number')
								});
							}else if (type === 'enum') {
								return new Ext.create('Ext.grid.CellEditor', {
									field: EnumCombo
								});
							}
					   } 
				} 
				columns.push(column);
            }); 
            this.reconfigure(this.store, columns);
        }
    },

    /**
    * assign the event to itself when the object is initialising
    */
    onRender: function(ct, position){
            /**
            *  well, old fashion way, but works well.
            */
            Ext.ux.grid.DynamicGridPanel.superclass.onRender.call(this, ct, position);
            /**
            * hook the store load event to our function
            */
           this.store.on('load', this.storeLoad, this, {single:true});
    }
});

//STID digunakan Saat Edit Data
Ext.define('dynamicModel', {
     extend: 'Ext.data.Model',
     //set the proxy
     proxy: {
       type: 'rest',
       url : m_api + '/web-traceability/quality-grid',
	   reader: {
            type: 'json',  
            root: 'data'
       },   
     }
});


var pcellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1, 
        listeners: {
			beforeEdit : function(editor, e, opts )
			{  
				 localStorage.removeItem('QualityID');
				 window.localStorage.setItem("QualityID", e.record.get('QualityID') );	
				 pComboQuality.load({ params: { QualityID : e.record.get('QualityID')} });   
			},
            edit: function(editor, e, opts ){    
				 window.localStorage.setItem("QualityID", e.record.get('QualityID') );
				 pComboQuality.load({ params: { QualityID : e.record.get('QualityID')} });  
			} 	
		}
});

var pmyGridQuality = {
       id:'pmyGridQuality',
	   plugins: [pcellEditing], 
       xtype:'dynamicgrid', 
	   "autoHeight": true,
	   "columnLines": true,  
       store:pmyStore,
	   anchor: '100%',
}; 
Ext.define('Koltiva.view.Traceability_new.Transaction.FormPenerimaan', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan',
    flex: 1,
    padding: 5,  
	margin: '0 0 0 0', 
	listeners:{
		afterRender: function(){
            //isikan variabel dari local storage
			var palm_penerimaan_list_searchp = JSON.parse(localStorage.getItem('palm_penerimaan_list_searchp'));
			console.log(palm_penerimaan_list_searchp);
            if(palm_penerimaan_list_searchp != null){
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').setValue(palm_penerimaan_list_searchp.ptextSearch);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').setValue(palm_penerimaan_list_searchp.pstatusSearch);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().load();
        }	
	},
    setFilterLs: function(){
    	localStorage.setItem('palm_penerimaan_list_searchp', JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyKey').getValue(),
            pstatusSearch: Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPenerimaan-gridToolbar-SupplyStatus').getValue()
        }));
    },
    initComponent: function () { 
         
        var thisObj = this;  
        var ComboTransport = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboTransport');  
		var ComboPackageType = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboPackageType');

		let setValueNettoOriginal = 0;
		let setValueLabel = ""; 
		
        thisObj.items = [ 
					{
                    columnWidth:1, 
                    layout:'form',
					xtype: 'form',
					id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form',
                    style: 'padding:15px;',
                    items:[
						{
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Receipt Batch')+'</div>'
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-act',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-act' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchStatus',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchStatus' 
						},  
						{
							xtype: 'hidden', 			
							id	: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID', 
						}, 
						{
							xtype: 'textfield',
							fieldLabel: lang('Batch Number'),
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchNumber',
							readOnly :true,
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchNumber', 
				       },
					   {
							xtype: 'textfield',
							fieldLabel: lang('Delivery Date'),
							labelWidth:175, 
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DeliveryDate',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DeliveryDate',
							value: m_now,
							readOnly: true,
							
						},
					   {
						xtype: 'textfield',
						fieldLabel: lang('Dest PO'),
						allowBlank : true,
						readOnly:true,
						id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestPO',
						name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestPO', 
				       }, 
					   {
							xtype: 'fieldcontainer', 
							width : 450, 
							fieldLabel: lang('Dest Weight Estimation'),									
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight',
										name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight', 
										value:0
								  },
								  { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								 ]
					   },   
					   {
							xtype: 'combobox',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportID',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportID',
							allowBlank : true,
							readOnly:true,
							store: ComboTransport,
							fieldLabel: lang('Transport type'),
							queryMode: 'local',
							displayField: 'DestTransportName',
							valueField: 'DestTransportID' 
					   },  
					   {
							xtype: 'textfield',
							fieldLabel: lang('Driver Name'),
							readOnly:true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestDriver',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestDriver', 
				       },
					   {
							xtype: 'textfield',
							fieldLabel: lang('Transport Number'),
							labelWidth:175, 
							readOnly:true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportNumber',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportNumber', 
				       },  				   
					   {
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Wheater')+'</div>'
					   }, 
					   {
							xtype: 'textfield',
							fieldLabel: lang('Weather Forecast'),
							labelWidth:175, 
							readOnly:true,
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-Weather',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-Weather', 
				       }, 				   
					   {
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Timbangan')+'</div>'
					   }, 
					  {
							xtype: 'datefield',
							fieldLabel: lang('Transaction Date'),
							labelWidth:175, 
							format: 'Y-m-d', 
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction',
							name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction', 
							allowBlank: false, 
							readOnly: true,
							listeners: {
				               render: function(){
				                    var picker = this.getPicker();
				                    picker.on("select",function(){ this.hide(); });
				                    this.inputCell.on("click",function(){                                                  
				                        if (picker.hidden)                               
				                            picker.show(); 
				                        else
				                            picker.hide();
				                     });                                    
				                }
				            }
							
						}, 
					   {
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Weight'),										
							defaults: {
								hideLabel: true,
								allowBlank: false, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto',
										name: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto',   
										value:0,	
										listeners : { 
											change: function(record){
												let value        = parseFloat(record.getValue());

												if (!isNaN(value)) {
													if (value != 0) {
														if (parseFloat(setValueNettoOriginal) > 0) {
															if (parseFloat(value) != parseFloat(setValueNettoOriginal)) {
																Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").hide();
															} else {
																Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").show();
															}
														}
													} else {
														Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").hide();
													}
												} else {
													Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").hide();
												}

												// remarks 8-4-2021

												// checkingWeight(record.getValue());
											}						
										}	
								  },
								  { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' }
								 ],
							
					 },
					 {
							xtype: 'label',
							id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto', 
							margin:0, 
							padding:0
					 }, 
					 /*
				     {
						xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Quality')+'</div>'
				     },
					 pmyGridQuality 
					 */
					]
				}];
        
		 //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [  {
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
			disabled : true,
            cls: 's-green',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave',
            handler: function () {
						window.localStorage.clear();
						/*
						var grid = Ext.getCmp('pmyGridQuality'); 
						var content = new Array();
						for(var x=0; x < grid.getStore().count(); x++){
							content[x] = grid.getStore().data.items[x].data;
						}
						*/

						// penambahan label warning dibawah volume netto
						// berdasarkan validasi sebelumnya
						// 8-4-2021

						let VolumeNetto     = Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto").getValue();
						let DestWeight      = Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight").getValue();							
						let Persen20        = parseFloat(DestWeight) - parseFloat((20/100) * DestWeight);
						let Persen20plus    = parseFloat(DestWeight) + parseFloat((20/100) * DestWeight);
						let DateTransaction = Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction").getValue();

						if (DateTransaction == null) {
							Ext.MessageBox.show({
								title: lang('Warning'),
								msg: lang("Please input date correctly"),
								buttons: Ext.MessageBox.OK,
								animateTarget: 'mb9',
								icon: 'ext-mb-error'
							});

							return;
						}

						if (isNaN(parseFloat(VolumeNetto)) || parseFloat(VolumeNetto) == 0) {
							Ext.MessageBox.show({
								title: lang('Warning'),
								msg: lang("Please input weight correctly"),
								buttons: Ext.MessageBox.OK,
								animateTarget: 'mb9',
								icon: 'ext-mb-error'
							});

							return;
						} else {

							let DestWeight   = Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight").getValue();
							let Persen20     = parseFloat(DestWeight) - parseFloat((20/100) * DestWeight);
							let Persen20plus = parseFloat(DestWeight) + parseFloat((20/100) * DestWeight);
							let message 
							
							if (parseFloat(VolumeNetto) < parseFloat(Persen20)) {
								message = lang("Weight couldn't less that 20% dest weight estimation");

								if (Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").isVisible() == false) {
									Ext.MessageBox.show({
								        title: 'Warning',
								        msg: message + '<br/>' + lang('Are you sure to continue?'),
								        buttons: Ext.MessageBox.OKCANCEL,
								        icon: Ext.MessageBox.WARNING,
								        fn: function(btn){
								            if (btn == 'ok'){
								            	Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").show();

								            	setValueNettoOriginal = parseFloat(VolumeNetto);
								            	setValueLabel         = lang('Less than 20%');

								            	Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").update(`<div style="margin-left:180px;color:#ED2F0D;">${lang(setValueLabel)}</div>`);

								            	return;
								            } else {
								                return;
								            }
								        }
								    });
								} else {
									formSubmitProcess();
								}
							} else if (parseFloat(VolumeNetto) > parseFloat(Persen20plus)) {
								message = lang("Weight couldn't more that 20% dest weight estimation");

								if (Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").isVisible() == false) {
									Ext.MessageBox.show({
								        title: 'Warning',
								        msg: message + '<br/>' + lang('Are you sure to continue?'),
								        buttons: Ext.MessageBox.OKCANCEL,
								        icon: Ext.MessageBox.WARNING,
								        fn: function(btn){
								            if (btn == 'ok'){
								            	Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").show();

								            	setValueNettoOriginal = parseFloat(VolumeNetto);
								            	setValueLabel         = lang('More than 20%');

								            	Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-labelCheckVolumeNetto").update(`<div style="margin-left:180px;color:#ED2F0D;">${lang(setValueLabel)}</div>`);

								            	return;
								            } else {
								                return;
								            }
								        }
								    });
								} else {
									formSubmitProcess();
								}
							} else {
								formSubmitProcess();
							}
							
						}

						// validasi diremarks
						// 8-4-2021

						// if(parseFloat(VolumeNetto) < Persen20){
						// 	Ext.MessageBox.show({
						// 		title: lang('Warning'),
						// 		msg: lang("Oaps !, Weight could't less than 20% from Dest Weight"),
						// 		buttons: Ext.MessageBox.OK,
						// 		animateTarget: 'mb9',
						// 		icon: 'ext-mb-error'
						// 	});
						// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setValue("");
						// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').focus(false,200);
						// }else if(parseFloat(VolumeNetto) > Persen20plus){
						// 	Ext.MessageBox.show({
						// 		title: lang('Warning'),
						// 		msg: lang("Oaps !, Weight could't more than 20% from Dest Weight"),
						// 		buttons: Ext.MessageBox.OK,
						// 		animateTarget: 'mb9',
						// 		icon: 'ext-mb-error'
						// 	});
						// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setValue("");
						// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').focus(false,200);
						// }else{
							// var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form').getForm();
							// var SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID').getValue();

							// formNya.submit({
							// 	url: m_api + '/web-traceability/penerimaan-submit', 
							// 	//params : { quality : JSON.stringify(content) },
							// 	headers: { 
							// 		SID: m_sid,
							// 		PID: m_pid,
							// 		SBID : SBID
							// 	},
							// 	method:'POST',
							// 	waitMsg: 'Saving data...',
							// 	success: function(fp, o) {
							// 		var obj = Ext.JSON.decode(o.response.responseText);
									
							// 		Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID').setValue(SBID);
							// 		if(obj.SupplyBatchNumber != '' &&  typeof obj.SupplyBatchNumber != 'undefined'){
							// 			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchNumber').setValue(obj.SupplyBatchNumber)
							// 		}
							// 		if(obj.SupplyTransID != '' &&  typeof obj.SupplyTransID != 'undefined'){
							// 			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID').setValue(obj.SupplyTransID);
							// 		}
							// 		Ext.MessageBox.show({
							// 			title: 'Information',
							// 			msg: lang('Data saved'),
							// 			buttons: Ext.MessageBox.OK,
							// 			animateTarget: 'mb9',
							// 			icon: 'ext-mb-success'
							// 		});
							// 		//Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridTransaction').getStore().load();
							// 		var STID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID').getValue();			
							// 		//Ext.getCmp('pmyGridQuality').getStore().load({params : { STID : STID, SID : m_sid } });	
							// 		Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().load({params : { SID : m_sid } });
							// 		pInputDisabled();  
							// 	},
							// 	failure: function(fp, o){
									
							// 		Ext.MessageBox.show({
							// 			title: 'Error',
							// 			msg: lang('Gagal Menyimpan Data'),
							// 			buttons: Ext.MessageBox.OK,
							// 			animateTarget: 'mb9',
							// 			icon: 'ext-mb-error'
							// 		});
							// 	}
							// }); 
						
						// }

						
			}
		},{
            text: 'Reset',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button', 
            cls: 's-black',
            id: 'Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnCancel',
            handler: function () {   
				window.localStorage.clear();
				pInputDisabled(); 
				var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form').getForm();
				formNya.reset();	 
				 
			}
		}]
		
		this.callParent(arguments);
    }
});


checkingWeight = function (VolumeNetto)
{   
	var act = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-act').getValue();
	var DestWeight = Ext.getCmp("Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight").getValue();							
	var Persen20 = parseFloat(DestWeight)+parseFloat((20/100)*DestWeight);
	
	if(act !="Yes"){
		
		if(parseFloat(VolumeNetto) > Persen20){
			Ext.MessageBox.show({
				title: lang('Warning'),
				msg: lang("Oaps !, Weight could't more than 20% Dest Weight"),
				buttons: Ext.MessageBox.OK,
				animateTarget: 'mb9',
				icon: 'ext-mb-error'
			});
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setValue("");
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').focus(false,200);
		}
	}else{
		Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-act').setValue("No");
	} 
}

 
pInputEnabled = function ()
{   
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave').setDisabled(false);  
}

pInputDisabled = function ()
{ 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave').setDisabled(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestPO').setReadOnly(true) 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestWeight').setReadOnly(true) 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportID').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestDriver').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DestTransportNumber').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-DateTransaction').setReadOnly(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-VolumeNetto').setReadOnly(true); 
	//Ext.getCmp('pmyGridQuality').getStore().load({params : { STID : '', SID : '' } });	
}

formSubmitProcess = function ()
{
	let formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form').getForm();
	let SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID').getValue();

	formNya.submit({
		url: m_api + '/web-traceability/penerimaan-submit',
		headers: { 
			SID: m_sid,
			PID: m_pid,
			SBID : SBID
		},
		method:'POST',
		waitMsg: 'Saving data...',
		success: function(fp, o) {
			let obj = Ext.JSON.decode(o.response.responseText);
			
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SBID').setValue(SBID);

			if(obj.SupplyBatchNumber != '' &&  typeof obj.SupplyBatchNumber != 'undefined'){
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-SupplyBatchNumber').setValue(obj.SupplyBatchNumber)
			}

			if(obj.SupplyTransID != '' &&  typeof obj.SupplyTransID != 'undefined'){
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID').setValue(obj.SupplyTransID);
			}

			Ext.MessageBox.show({
				title: 'Information',
				msg: lang('Data saved'),
				buttons: Ext.MessageBox.OK,
				animateTarget: 'mb9',
				icon: 'ext-mb-success'
			});

			let STID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-Form-STID').getValue();
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPenerimaan-BtnSave').hide();
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.List_penerimaan-gridTransaction').getStore().load({params : { SID : m_sid } });

			pInputDisabled();  
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
