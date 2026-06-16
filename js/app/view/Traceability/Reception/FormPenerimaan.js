
 /**
* Ext.ux.grid.DynamicGridPanel
*/

var pComboQuality = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboQuality');
// var pmyStore = Ext.create('Ext.data.Store',{
// 		 model:'dynamicModel',
// 		 autoLoad:false,
// 		 autoSync: false

// });
 
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

// var pmyGridQuality = {
//        id:'pmyGridQuality',
// 	   plugins: [pcellEditing], 
//        xtype:'dynamicgrid', 
// 	   "autoHeight": true,
// 	   "columnLines": true,  
//        store:pmyStore,
// 	   anchor: '100%',
// }; 
Ext.define('Koltiva.view.Traceability.Reception.FormPenerimaan', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.FormPenerimaan',
    flex: 1,
    padding: 5,  
	margin: '0 0 0 0', 
	listeners:{
		afterRender: function(){
            //isikan variabel dari local storage
			var palm_penerimaan_list_searchp = JSON.parse(localStorage.getItem('palm_penerimaan_list_searchp'));
			console.log(palm_penerimaan_list_searchp);
            if(palm_penerimaan_list_searchp != null){
                // Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey').setValue(palm_penerimaan_list_searchp.ptextSearch);
                // Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus').setValue(palm_penerimaan_list_searchp.pstatusSearch);
            }

            //load storenya sebelum viewnya aktif
            this.setFilterLs();
            Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getStore().load();
        }	
	},
    setFilterLs: function(){
    	localStorage.setItem('palm_penerimaan_list_searchp', JSON.stringify({
            // ptextSearch: Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyKey').getValue(),
            // pstatusSearch: Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridToolbar-SupplyStatus').getValue()
        }));
    },
    initComponent: function () { 
         
        var thisObj = this;  
		
		var grid_dispatch_list 			= Ext.create('Koltiva.view.Traceability.Reception.GridDispatch');
		var grid_product_dispatch_list	= Ext.create('Koltiva.view.Traceability.Reception.GridProduct');
		
        thisObj.items = [ 
					{
                    columnWidth:1, 
                    layout:'form',
					xtype: 'form',
					id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form',
                    style: 'padding:15px;',
                    items:[
						{
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Receipt Dispatch')+'</div>'
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchID',
							name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchID' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionID',
							name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionID' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability.Reception.FormPenerimaan-act',
							name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-act' 
						}, 
						{
							xtype: 'hidden', 
							id	: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-Status',
							name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-Status' 
						},
					   {
							xtype: 'textfield',
							fieldLabel: lang('Shipping Date'),
							labelWidth:175, 
							id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ShippingDate',
							name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ShippingDate',
							value: m_now,
							readOnly: true,
							
						},
					   {
							xtype: 'fieldcontainer', 
							width : 450, 
							fieldLabel: lang('Dispatch Volume'),	
							labelWidth:175, 								
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchVolume',
										name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchVolume', 
										value:0
								  },
								  { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								 ]
					   },	
					   {
						xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Dispatch List')+'</div>'
					   }, 
				   		grid_dispatch_list,		   
					   {
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Receipt')+'</div>'
					   }, 
					   {
						xtype: 'datefield',
						id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionDate',
						name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionDate',
						format: 'Y-m-d',
						allowBlank: false,
						baseCls: 'Sfr_FormInputMandatory',
						fieldLabel: lang('Received Date')
					   }, 
					    {
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Received Weight'),
							labelWidth:175, 										
							defaults: {
								hideLabel: true,
								allowBlank: false, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto',
										name: 'Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto',   
										value:0,	
										listeners : { 
											change: function(record){ 
												// checkingWeight(record.getValue());
											} 							
										}	
								  },
								  { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								 ],
							
					 },	
					//  {
					//   xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Product List')+'</div>'
					//  }, 
					// 	 grid_product_dispatch_list,	
					 /*
				     {
						xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Quality')+'</div>'
				     },
					 pmyGridQuality 
					 */
					]
				}];
        
		 //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [  
		// {
        //     text: 'Close Dispatch',
        //     margin: '5 15 5 5',
        //     scale: 'large',
        //     ui: 's-button',
		// 	disabled : true,
        //     cls: 's-green',
        //     id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-BtnSave',
        //     handler: function () {
		// 		window.localStorage.clear();
		// 		var formNya = Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form').getForm();
		// 		if (formNya.isValid()) {					
		// 			formNya.submit({
		// 				url: m_api + '/dispatch/refinery/close_dispatch', 
		// 				//params : { quality : JSON.stringify(content) },
		// 				headers: { 
		// 					SID: m_sid,
		// 					PID: m_pid
		// 				},
		// 				method:'POST',
		// 				waitMsg: 'Saving data...',
		// 				success: function(fp, o) {
		// 					var obj = Ext.JSON.decode(o.response.responseText);
							
		// 					Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-ReceptionID').setValue(obj.ReceptionID);
							
		// 					Ext.MessageBox.show({
		// 						title: 'Information',
		// 						msg: lang('Data saved'),
		// 						buttons: Ext.MessageBox.OK,
		// 						animateTarget: 'mb9',
		// 						icon: 'ext-mb-success'
		// 					});

		// 					Ext.getCmp('Koltiva.view.Traceability.Reception.ReceptionList-gridTransaction').getStore().load({params : { SID : m_sid } });
		// 					pInputDisabled();  
		// 				},
		// 				failure: function(fp, o){
							
		// 					Ext.MessageBox.show({
		// 						title: 'Error',
		// 						msg: lang('Gagal Menyimpan Data'),
		// 						buttons: Ext.MessageBox.OK,
		// 						animateTarget: 'mb9',
		// 						icon: 'ext-mb-error'
		// 					});
		// 				}
		// 			});
		// 		}else{
		// 			Ext.MessageBox.show({
		// 				title: 'Attention',
		// 				msg: lang('Form not complete yet'),
		// 				buttons: Ext.MessageBox.OK,
		// 				animateTarget: 'mb9',
		// 				icon: 'ext-mb-info'
		// 			});
		// 		}						
		// 	}
		// },{
        //     text: 'Reset',
        //     margin: '5 15 5 5',
        //     scale: 'large',
        //     ui: 's-button', 
        //     cls: 's-black',
        //     id: 'Koltiva.view.Traceability.Reception.FormPenerimaan-BtnCancel',
        //     handler: function () {   
		// 		window.localStorage.clear();
		// 		pInputDisabled(); 
		// 		var formNya = Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form').getForm();
		// 		formNya.reset();	 
				 
		// 	}
		// }
	]
		
		this.callParent(arguments);
    }
});


// checkingWeight = function (VolumeNetto)
// {   
// 	var act = Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-act').getValue();
// 	var DestWeight = Ext.getCmp("Koltiva.view.Traceability.Reception.FormPenerimaan-Form-DespatchVolume").getValue();							
// 	var Persen20 = parseFloat(DestWeight)+parseFloat((20/100)*DestWeight);
	
// 	if(act !="Yes"){
		
// 		if(parseFloat(VolumeNetto) > Persen20){
// 			Ext.MessageBox.show({
// 				title: lang('Warning'),
// 				msg: lang("Oaps !, Weight could't more than 20% Dest Weight"),
// 				buttons: Ext.MessageBox.OK,
// 				animateTarget: 'mb9',
// 				icon: 'ext-mb-error'
// 			});
// 			Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto').setValue("");
// 			Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-Form-VolumeNetto').focus(false,200);
// 		}
// 	}else{
// 		Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-act').setValue("No");
// 	} 
// }

 
pInputEnabled = function ()
{   
	// Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnSave').setDisabled(false);  
}

pInputDisabled = function ()
{ 
	// Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnSave').setVisible(false);
	// Ext.getCmp('Koltiva.view.Traceability.Reception.FormPenerimaan-BtnCancel').setVisible(false); 
	//Ext.getCmp('pmyGridQuality').getStore().load({params : { STID : '', SID : '' } });	
}
 