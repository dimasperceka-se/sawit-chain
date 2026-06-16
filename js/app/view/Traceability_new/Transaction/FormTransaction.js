 	
		
/**
* Ext.ux.grid.DynamicGridPanel
*/
/*
var EnumCombo = Ext.create('Ext.form.field.ComboBox', {
	store: [[ '1', 'Yes' ], ['2', 'No' ]]
});
var ComboQuality = Ext.create('Koltiva.store.Traceability.Transaction.ComboQuality');
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
									field: Ext.create('Ext.form.field.Text',
									  {
										  listeners:{
										  blur:function(val)
											{
												if(record.get('Name') == 'Rubber Content (%)'){
													if(parseFloat(val.getValue()) < record.get('MinValue') ){
														Ext.MessageBox.show({
															title: 'Information',
															msg: record.get('Name') + ' ' + lang('Minimum value') + ' ' +  record.get('MinValue'),
															buttons: Ext.MessageBox.OK,
															animateTarget: 'mb9',
															icon: 'ext-mb-success'
														});  
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(true)
													}else{
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(false)
														
														var ContractPrice =  Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice').getValue();
														var netPrice = (parseFloat(ContractPrice) *  parseFloat(val.getValue())) / 100 ;
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice').setValue(netPrice);
														
														var Weight = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto').getValue();
														var totalPayment = parseFloat( Weight ) * parseFloat(netPrice);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment').setValue(totalPayment);
														
													}
												}
											}	
								        }
									  }) 
								} );
							} else if (type === 'combo') { 
								var ComboQualitys = Ext.create('Ext.form.field.ComboBox', {
									store: ComboQuality,
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
 
    onRender: function(ct, position){
            
            Ext.ux.grid.DynamicGridPanel.superclass.onRender.call(this, ct, position);
             
            this.store.on('load', this.storeLoad, this, {single:true});
    } 
});

*/
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

var myStore = Ext.create('Ext.data.Store',{
         model:'dynamicModel',
		 autoLoad:false,
		 autoSync: false

   }); 
var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1, 
        listeners: {
			beforeEdit : function(editor, e, opts )
			{  
				 localStorage.removeItem('QualityID');
				 window.localStorage.setItem("QualityID", e.record.get('QualityID') );	
				 ComboQuality.load({ params: { QualityID : e.record.get('QualityID')} });   
			},
            edit: function(editor, e, opts ){    
				 window.localStorage.setItem("QualityID", e.record.get('QualityID') );
				 ComboQuality.load({ params: { QualityID : e.record.get('QualityID')} });  
			} 			
		}
});
var myGridQuality = {
       id:'myGridQuality',
	   plugins: [cellEditing], 
       xtype:'dynamicgrid', 
	   "autoHeight": true,
	   "columnLines": true,  
       store:myStore,
	   anchor: '100%',
	   listeners : {
		   
	   }
};

function getCertifiedFarmer(MemberID){
	Ext.Ajax.request({
		url: m_api + '/traceability_api/web_transaction/getFarmerCertified',
		waitMsg: lang('Please Wait'),
		method : 'GET',
		params: {
			MemberID:  MemberID
		},
		success: function(result, opts) {
			var r = JSON.parse(result.responseText);
			// console.log(r.data);
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified').setValue(r.data.Certification);
		}
	});
}

Ext.define('Koltiva.view.Traceability_new.Transaction.FormTransaction', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction',
    flex: 1,
    padding: 5,  
	margin: '0 0 0 0', 
    initComponent: function () {
		var thisObj = this; 
		
		if(m_daerah_access.includes("43") || m_daerah_access.includes("44")){
			var Currency = lang("MYR");
		}else{
			var Currency = lang("IDR");
		};
		  
		var ComboFarmer 	= Ext.create('Koltiva.store.Traceability_new.Transaction.ComboFarmer');
		var ComboPlantation = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboPlantation'); 
		var ComboPlantationNonFarmer = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboPlantationNonFarmer');
		var ComboSellerMill = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerMill');
		var ComboSellerDO 	= Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerDO');
		var ComboSellerAgent = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerAgent');

		var ComboSellerType = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('External Estate'), "id":'external'},
                {"label":lang('Other Supplier'), "id":'other'}
                //...
            ]
        });
		
		var storeGridMainTransaction = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridTransaction');
        thisObj.items = [
					{
					xtype: 'toolbar',
					dock:'top',
					//style: 'border-style: none',
					style: 'margin-top: -10px',
					items: [{
								icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
								
								text: lang('Add New Transaction'),
								id :'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-AddnewTransaction', 
								handler: function() {
									var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form').getForm();
									InputEnabled();

									Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(false);
									formNya.reset();
									ComboFarmer.load();
									myStore.load({ params : { STID : 0 , SID : m_sid } });
									if(m_isFarmer == 1){
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setVisible(true);
									}
									if(m_IsNonFarmer == 1){
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setVisible(true);
									}
									if(m_IsCompany == 1){
										Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setVisible(true);
									}
								}
							}] 
					},
					{
                    columnWidth:1,
                    layout:'form',
					xtype: 'form',
					id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form',					
                    style: 'padding:15px;',
                    items:[{
						columnWidth:1,
						layout:'form',
						id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-Basic',					
						style: 'padding-right:10px;',
						items:[
								{
									xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Basic Information')+'</div>'
								}, 
								{
									xtype: 'datefield',
									fieldLabel: lang('Transaction Date'),
									labelWidth:175, 
									format: 'Y-m-d H:i:s',
									labelAlign:'top',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction',
									value: m_now,
									disabled:true								
								},
								{
									fieldLabel: lang('Sales Type'),
									xtype: 'radiogroup',
									columns: 3,
									labelAlign: 'top',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesTypeGroup',
									disabled:true,
									allowBlank:false,
									items:[{
										boxLabel: lang('Farmer'),
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesType',
										inputValue: '1',
										hidden:true,
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC',
										listeners:{
											change: function(){
												if(this.checked == true){
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-Farmer').setVisible(true);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified').setVisible(true);													
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-NonFarmer').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-DirectBatch').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').allowBlank = false;
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType').allowBlank = true;
													
												}
												return false;
											}
										}
									},{
										boxLabel: lang('Non Farmer'),
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesType',
										inputValue: '2',
										hidden:true,
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC',
										listeners:{
											change: function(){												
												if(this.checked == true){
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-Farmer').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-NonFarmer').setVisible(true);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-DirectBatch').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').allowBlank = true;
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNrNonFarmer').allowBlank = false;

													ComboPlantationNonFarmer.load({params : {'SupplychainID' : m_sid } });
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNrNonFarmer').setReadOnly(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType').allowBlank = true;
													return false;
												}
											}
										}
									},{
										boxLabel: lang('Direct Batch'),
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesType',
										inputValue: '3',
										hidden:true,
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC',
										listeners:{
											change: function(){												
												if(this.checked == true){
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-Farmer').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-NonFarmer').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified').setVisible(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-DirectBatch').setVisible(true);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').allowBlank = true;
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType').setReadOnly(false);
													Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType').allowBlank = false;
													return false;
												}
											}
										}
									}]
								},
								{
									xtype: 'hidden', 
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-STID',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-STID' 
								}
							]
						},
						{
							columnWidth:1,
							layout:'form',
							id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-Farmer',
							style: 'padding-right:10px;',
							hidden:true,
							items:[
								{
									xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Farmer')+'</div>'
								}, 						
								{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerID',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerID',
									allowBlank : true,
									readOnly:true,
									store: ComboFarmer, 
									minChars: 3,
									fieldLabel: lang('Farmer'),  
									displayField: 'MemberName',
									valueField: 'MemberID',   
									typeAhead: true, 
									queryCaching:false,
									emptyText: lang('Search by Name'),
									listConfig: {
										loadingText: 'Searching...',
										emptyText: 'No matching farmer found.',
										// Custom rendering template for each item
										getInnerTpl: function() {
											return '<div class="search-item">' + 
													'<br>{MemberID} - <b>{MemberName}</b><br>Member ID : <b>{MemberDisplayID}</b><br> Kelompok Tani : <b>{GroupName}</b><br>Desa : <b>{Village}, {SubDistrict}</b><br>Certified : <b>{CertProgName}</b>' + 
													'<hr>' +
													'</div>';
										}
									},
									pageSize: 10,							
									listeners : {
										change : function(val)
										{
											if(val.getValue()!= null){  
												ComboPlantation.load({params : {'MemberID' : val.getValue() } });

												getCertifiedFarmer(val.getValue());
												//var STID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-STID').getValue(); 
												//if( STID == '' ) { STID = 0; }
												//myStore.load({ params : { STID : STID , SID : m_sid } }); 										
											}
										}								
									}
									
								},{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr',
									allowBlank : false,
									readOnly:true,
									store: ComboPlantation, 
									labelWidth:200, 
									fieldLabel: lang('Farm Number'), 
									queryMode: 'local',
									displayField: 'PlantationName',
									valueField: 'PlantationNr',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){ 
										var records = combo.store.findRecord('PlantationNr', value); 
											//alert(rec.get('FarmingType'))
										
										} 							
									},onFocus: function() {
										var me = this;
									
										if (!me.isExpanded) {
											me.expand()
										}
										me.getPicker().focus();
									}
								},{
									xtype: 'textfield',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified',
									allowBlank : true,
									readOnly:true,
									labelWidth:200, 
									fieldLabel: lang('Certified')
								}
							]
						},
						{
							columnWidth:1,
							layout:'form',
							id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-NonFarmer',	
							hidden:true,				
							style: 'padding-right:10px;',
							items:[
								{
									xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Non Farmer')+'</div>'
								},{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNrNonFarmer',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNrNonFarmer',
									allowBlank : true,
									readOnly:true,
									store: ComboPlantationNonFarmer, 
									labelWidth:200, 
									fieldLabel: lang('Farm Number'), 
									queryMode: 'local',
									displayField: 'PlantationName',
									valueField: 'PlantationName',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){ 
										var records = combo.store.findRecord('PlantationName', value); 
											//alert(rec.get('FarmingType'))
										
										} 							
									},onFocus: function() {
										var me = this;
									
										if (!me.isExpanded) {
											me.expand()
										}
										me.getPicker().focus();
									}
								}
							]
						},{
							columnWidth:1,
							layout:'form',
							id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-DirectBatch',					
							style: 'padding-right:10px;',
							hidden:true,
							items:[
								{
									xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Batch')+'</div>'
								},{
									xtype: 'combobox',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType',
									allowBlank : false,
									readOnly:true,
									store: ComboSellerType, 
									labelWidth:200, 
									fieldLabel: lang('Seller Type'), 
									queryMode: 'local',
									displayField: 'label',
									labelAlign:'top',
									valueField: 'id',
									typeAhead: true, 
									disableKeyFilter : true,
									triggerAction : 'all', 
									listeners : { 
										change: function(combo, /* Array */ value){ 
											if (typeof combo.store.findRecord('id', value) !== 'undefined') {
												var records = combo.store.findRecord('id', value);
												if(records){
													if(records.data.id == "external"){
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setVisible(true);
														Ext.getCmp('OtherMill').setVisible(true);
														ComboSellerMill.load({params : {'SupplychainID' : m_sid } });
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setVisible(false);
														Ext.getCmp('OtherDO').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(false);
														Ext.getCmp('OtherAgentSurvey').setVisible(false);
														Ext.getCmp('OtherAgent').setVisible(false);
													}else{
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(false);
														Ext.getCmp('OtherMill').setVisible(false);
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setVisible(true);
														Ext.getCmp('OtherDO').setVisible(true);
														ComboSellerDO.load({params : {'SupplychainID' : m_sid } });
														Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setVisible(true);
														Ext.getCmp('OtherAgent').setVisible(true);
													}
												}
											}
										} 							
									},onFocus: function() {
										var me = this;
									
										if (!me.isExpanded) {
											me.expand()
										}
										me.getPicker().focus();
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
											id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill',
											name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill',
											allowBlank : true,
											hidden:true,
											labelAlign:'top',
											store: ComboSellerMill, 
											labelWidth:200, 
											fieldLabel: lang('Mill'), 
											queryMode: 'local',
											displayField: 'Name',
											valueField: 'ObjID',
											typeAhead: true, 
											disableKeyFilter : true,
											triggerAction : 'all', 
											listeners : { 
												change: function(combo, /* Array */ value){ 
													// var records = combo.store.findRecord('id', value); 
													// console.log(records.data.id);
												
												} 							
											},onFocus: function() {
												var me = this;
											
												if (!me.isExpanded) {
													me.expand()
												}
												me.getPicker().focus();
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
											id:'OtherMill',
											hidden: true,
											items: [
												{
													boxLabel  : lang('Yes'),
													name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMill',
													inputValue: '1',
													id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMill',
													listeners:{
														change: function(checkbox, newValue, oldValue, eOpts) {
															if(newValue){
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setReadOnly(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setValue('');
															}else{
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setReadOnly(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setValue('');
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
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName',
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
									items:[{
										columnWidth: 0.6,
										layout:'form',
										style:'padding-right:25px;',
										items:[{
											xtype: 'combobox',
											id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DO',
											name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DO',
											allowBlank : true,
											hidden:true,
											labelAlign:'top',
											store: ComboSellerDO, 
											labelWidth:200, 
											fieldLabel: lang('DO'), 
											queryMode: 'local',
											displayField: 'Name',
											valueField: 'ObjID',
											typeAhead: true, 
											disableKeyFilter : true,
											triggerAction : 'all', 
											listeners : { 
												change: function(combo, /* Array */ value){ 
													if (typeof combo.store.findRecord('ObjID', value) !== 'undefined') {
														var records = combo.store.findRecord('ObjID', value); 
														// console.log(records.data);
														
														ComboSellerAgent.load({params : {'SupplychainID' : records.data.ObjID } });
													}												
												} 							
											},onFocus: function() {
												var me = this;
											
												if (!me.isExpanded) {
													me.expand()
												}
												me.getPicker().focus();
											}
										}]
									},{
										columnWidth:0.38,
										layout:'form',				
										// style: 'padding-right:10px;',
										items:[{
											xtype: 'fieldcontainer',
											fieldLabel: lang('Other DO'),
											defaultType: 'checkboxfield',
											labelAlign:'top',
											id:'OtherDO',
											hidden: true,
											items: [
												{
													boxLabel  : lang('Yes'),
													name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDO',
													inputValue: '1',
													id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDO',
													listeners:{
														change: function(checkbox, newValue, oldValue, eOpts) {
															if(newValue){
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setReadOnly(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setValue('');
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent').setValue("1");
															}else{
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setReadOnly(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setValue('');
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
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName',
									fieldLabel: lang('Other DO Name'),
									labelAlign:'top',
									hidden:true,
									listeners :{
										change:function(val){
											
										}
									} 
									
								},{
									layout: 'column',
									border: false,
									items:[{
										columnWidth: 0.6,
										layout:'form',
										style:'padding-right:25px;',
										items:[{
											xtype: 'combobox',
											id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent',
											name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent',
											allowBlank : true,
											hidden:true,
											labelAlign:'top',
											store: ComboSellerAgent, 
											labelWidth:200, 
											fieldLabel: lang('Agent'), 
											queryMode: 'local',
											displayField: 'Name',
											valueField: 'ObjID',
											typeAhead: true, 
											disableKeyFilter : true,
											triggerAction : 'all', 
											listeners : { 
												change: function(combo, /* Array */ value){ 
													// var records = combo.store.findRecord('id', value); 
													// console.log(records.data.id);
												
												} 							
											},onFocus: function() {
												var me = this;
											
												if (!me.isExpanded) {
													me.expand()
												}
												me.getPicker().focus();
											}
										}]
									},{
										columnWidth:0.38,
										layout:'form',		
										// style: 'padding-right:10px;',
										items:[{
											xtype: 'fieldcontainer',
											fieldLabel: lang('Other Agent'),
											defaultType: 'checkboxfield',
											labelAlign:'top',
											id:'OtherAgent',
											hidden: true,
											items: [
												{
													boxLabel  : lang('Yes'),
													name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent',
													inputValue: '1',
													id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent',
													listeners:{
														change: function(checkbox, newValue, oldValue, eOpts) {
															if(newValue){
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(true);
																Ext.getCmp('OtherAgentSurvey').setVisible(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setReadOnly(true);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setValue('');
															}else{
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(false);
																Ext.getCmp('OtherAgentSurvey').setVisible(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setReadOnly(false);
																Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setValue('');
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
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName',
									fieldLabel: lang('Other Agent Name'),
									labelAlign:'top',
									hidden:true									
								},
								{
									xtype: 'textfield',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin',
									fieldLabel: lang('Agent Nin'),
									labelAlign:'top',
									hidden:true									
								},{
									xtype: 'fieldcontainer',
									fieldLabel: lang('Agents want to be surveyed later'),
									defaultType: 'checkboxfield',
									labelWidth:'250',
									id:'OtherAgentSurvey',
									labelAlign:'top',
									hidden: true,
									items: [
										{
											name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey',
											inputValue: '1',
											id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey'
										}
									]
								}
							]
						},
						{
							xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Weight and Price')+'</div>'
						}, 
						{
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Bunches'),										
							defaults: {
								hideLabel: true,
								allowBlank: true, 
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numberfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Bunches',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Bunches',   
										value:0, 
										listeners :{
											change:function(val){
												 
											}
										} 
										
								   },  
								   { xtype:'component',html : lang('PSC'), margin:'12 0 0 5 0' } 
								   ]
						},
						{
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Gross Weight'),										
							defaults: {
								hideLabel: true,
								allowBlank: true, 
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numberfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeBruto',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeBruto',   
										value:0, 
										listeners :{
											change:function(val){
												 
											}
										} 
										
								   },  
								   { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								   ]
						},
						{
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Nett Weight'),										
							defaults: {
								hideLabel: true,
								allowBlank: true, 
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numberfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto',   
										value:0, 
										listeners :{
											change:function(val){
												 var ContractPrice = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice').getValue();
												 var totalPayment = parseFloat(val.getValue()) * parseFloat(ContractPrice); 
												 Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPriceSebelumPengurangan').setValue(totalPayment);

												 
												 var ContractPrice = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice').getValue();
												 var totalPayment2 = (parseFloat(ContractPrice) * parseFloat(val.getValue())); 
												 Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment').setValue(totalPayment2);
											}
										} 
										
								   },  
								   { xtype:'component',html : lang('Kg'), margin:'12 0 0 5 0' } 
								   ]
						},{
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Price per Kg'),									
							defaults: {
								hideLabel: true,
								allowBlank: true, 
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numberfield',
										allowBlank: false, 
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice', 
										value:0,
										listeners :{
											change:function(val){
												 var VolumeNetto = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto').getValue();
												 var totalPayment = parseFloat(val.getValue()) * parseFloat(VolumeNetto); 
												 Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment').setValue(totalPayment);
											}
										} 	
								   }, { xtype:'component',html : Currency, margin:'12 0 0 5 0' } 
								   ]
						},
						{
							xtype: 'numericfield',
							hidden:true,
							readOnly:true, 
							id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPriceSebelumPengurangan', 
							value:0,
						},
						{
								xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('Payment')+'</div>'
						}, 
						/*{
							xtype: 'radiogroup', 
							id : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PanelOpsinpwp',  
							labelWidth:200,
							fieldLabel: lang('NPWP'), 						
							items:[{
									boxLabel: lang('Ya'), 
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Opsinpwp',
									inputValue: '1',  
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Opsinpwp1',
									listeners:{
										change: function(val){
											 if(val.getValue() == true){
												Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducPercentage').setValue('0.25');
												HitungNPWP('0.25') 
											 }
											 
										}
									}
								},{
									boxLabel: lang('Tidak'), 
									style: 'margin-left: -90px',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Opsinpwp',
									inputValue: '2',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Opsinpwp2',
									checked: true,
									listeners:{
										change: function(val){
											if(val.getValue() == true){
											Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducPercentage').setValue('0.5');  
												HitungNPWP('0.5') 
											}
										}
									}
								}]
						},
						{
							xtype: 'fieldcontainer',  
							width : 450, 
							labelStyle :'width:200px;',
							fieldLabel: lang('NPWP Percentage'),								
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										readOnly:true, 
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducPercentage',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducPercentage',
										value:0 
									}, { xtype:'component',html : lang('%'), margin:'12 0 0 5 0' } 
								   ]
						},
						{
							xtype: 'fieldcontainer',  							
							width : 450, 
							labelStyle :'width:200px;',
							fieldLabel: lang('NPWP Price'),								
							defaults: {
								hideLabel: true,
								allowBlank: true,  
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										readOnly:true, 
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducValue',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NpwpDeducValue',
										value:0 
									}, { xtype:'component',html : lang('IDR'), margin:'12 0 0 5 0' } 
								   ]
						},
						{
							xtype: 'radiogroup', 
							id : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-PanelStampdeduction',  
							labelWidth:200,
							fieldLabel: lang('Stamp deduction'), 						
							items:[{
									boxLabel: lang('Ya'), 
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OpsiStampdeduction',
									inputValue: '1',  
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OpsiStampdeduction1',
									listeners:{
										change: function(val){
											if(val.getValue() == true){  
												HitungSTAMP('ya') 
											}
										}
									}
								},{
									boxLabel: lang('Tidak'), 
									style: 'margin-left: -90px',
									name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OpsiStampdeduction',
									inputValue: '2',
									id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OpsiStampdeduction2',
									checked: true,
									listeners:{
										change: function(val){
											if(val.getValue() == true){  
												HitungSTAMP('no') 
											}
										}
									}
								}]
						},
						{
							xtype: 'numericfield',
							hidden:true,
							readOnly:true, 
							name :'Koltiva.view.Traceability_new.Transaction.FormTransaction-StampDeducValue', 
							id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-StampDeducValue', 
							value:0,
						},*/
						/*
						{
							xtype: 'fieldcontainer',  
							width : 450,
							fieldLabel: lang('Net Price'),									
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield',
										allowBlank: false,
										readOnly:true,
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice', 
										value:0,
										listeners : {
											change : function(){
												 
											}
										}						
								   }, { xtype:'component',html : lang('IDR'), margin:'12 0 0 5 0' } 
								   ]
						},
						*/
						{
							xtype: 'fieldcontainer',  
							width : 450, 
							labelStyle :'width:200px;',
							fieldLabel: lang('Invoice Number'),								
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								//readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'textfield',
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-InvoiceNumber',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-InvoiceNumber',
										emptyText : '.....',
							}]
						} ,
						{
							xtype: 'fieldcontainer',  
							width : 450, 
							labelStyle :'width:200px;',
							fieldLabel: lang('Total Amount'),								
							defaults: {
								hideLabel: true,
								allowBlank: true, 
								readOnly:true,
							}, 
							layout: 'hbox',
							msgTarget: 'side',
							items: [{
										xtype: 'numericfield', 
										id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment',
										name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment',
										value:0,
									}, { xtype:'component',html : Currency, margin:'12 0 0 5 0' } 
								   ]
						} 
					]
				}];
        
		 //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save Transaction',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
			disabled : true,
            id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave',
            handler: function () {
				window.localStorage.clear();
				/*
				var grid = Ext.getCmp('myGridQuality'); 
				var content = new Array();
				for(var x=0; x < grid.getStore().count(); x++){
					content[x] = grid.getStore().data.items[x].data;
				}
				*/
				var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form').getForm();
				 
				//console.log(JSON.stringify(content))  
				if(formNya.isValid()){					
					formNya.submit({
						url: m_api + '/web-traceability/main-submit',
						//params : { quality : JSON.stringify(content) },
						headers: { 
							 SID: m_sid,
							 PID: m_pid
						},
						method:'POST',
						waitMsg: 'Saving data...',
						success: function(fp, o) {
							Ext.MessageBox.show({
								title: 'Information',
								msg: lang('Data saved'),
								buttons: Ext.MessageBox.OK,
								animateTarget: 'mb9',
								icon: 'ext-mb-success'
							});  
							 
							Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(true);
							InputDisabled();
							Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainGrid-gridTransaction').getStore().load();	
							formNya.reset();
						},
						failure: function(fp, o){

							console.log(fp);
							console.log(o);
							 
							Ext.MessageBox.show({
								title: 'Error',
								msg: lang('Gagal Menyimpan Data'),
								buttons: Ext.MessageBox.OK,
								animateTarget: 'mb9',
								icon: 'ext-mb-error'
							});
						}
					}); 
			
				}else{
					Ext.MessageBox.show({
						title: 'Attention',
						msg: lang('Form not complete yet'),
						buttons: Ext.MessageBox.OK,
						animateTarget: 'mb9',
						icon: 'ext-mb-info'
					});
				}				
			}
		},
		{
            text: 'Reset',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button', 
            cls: 's-black',
            id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnCancel',
            handler: function () {  
				var formNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form').getForm();
				formNya.reset();	
				InputDisabled();  
				window.localStorage.clear();
			}
		}]
		
		this.callParent(arguments);
    }
});

 
InputDisabled = function()
{ 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-AddnewTransaction').setDisabled(false)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerID').setReadOnly(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').setReadOnly(true) 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction').setDisabled(true);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesTypeGroup').setDisabled(true);
	//Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice').setReadOnly(true) 
	
	var STID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-STID').getValue(); 
	if( STID == '' ) { STID = 0; }
	myStore.load({ params : { STID : STID , SID : m_sid } }); 
}

InputEnabled = function()
{ 
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SalesTypeGroup').setDisabled(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Form-AddnewTransaction').setDisabled(true)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-BtnSave').setDisabled(false)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction').setDisabled(false);
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerID').setReadOnly(false)
	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').setReadOnly(false)     
	//Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice').setReadOnly(false) 
}


// totalPayment = function()
// {
// 	var VolumeNetto = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto').getValue();
// 	var NetPrice = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NetPrice').getValue();
// 	var total = parseFloat(VolumeNetto) *  parseFloat(NetPrice);
// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment').setValue(total); 
// }
