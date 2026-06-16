

var cmb_objtype = Ext.create('Ext.data.Store', {
			extend: 'Ext.data.Model',
			fields: ['id', 'label'],
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url: m_api + '/traceability_api/Supplychain_org/objtype_list',
				reader: {
					type: 'json',
					root: 'data'
				}
			}
		});
var cmb_storePatner = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org.cmbPartner'); 
var cmbObjID = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org.ComboStaffObjID'); 
var cmbArea = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org.ComboArea'); 
		
Ext.define('Koltiva.view.Traceability.Supplychain_org.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Supplychain_org.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org.MainGrid'); 
		
		var contextMenuSuppGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
				cls:'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Traceability.Transaction.List_transaction-contextMenuViewItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                    SetFormTransaction(sm.get('SupplychainID')); 
					setDisabledButtonView(true)
					Ext.getCmp('setVarParameters').setValue('view');//edit mode
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
				cls:'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Traceability.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                    SetFormTransaction(sm.get('SupplychainID'));
					setDisabledButtonView(false)	
					Ext.getCmp('setVarParameters').setValue('edit');//edit mode 
                }
            },
			{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
					var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
					Ext.Ajax.request({
							waitMsg: lang('Please Wait'),
							url:  m_api + '/traceability_api/Supplychain_org/del',
							method : 'POST',
							params: {
							   SupplychainID: sm.get('SupplychainID')
							},
							success: function(response, opts){
							   var obj = Ext.decode(response.responseText);  
							   if(obj.success == true){
								   Ext.getCmp('Koltiva.view.Traceability.Supplychain_org.MainGrid-gridMainGrid').getStore().load();
								   Ext.MessageBox.show({
										title: 'Success',
										msg: lang('Successfully Deleted'),
										buttons: Ext.MessageBox.OK,
										animateTarget: 'mb9',
										icon: 'ext-mb-success'
									});
							   }else{
								   Ext.MessageBox.show({
										title: 'Error',
										msg: lang('Delete Failed'),
										buttons: Ext.MessageBox.ERROR,
										animateTarget: 'mb9',
										icon: 'ext-mb-error'
									});
							   }
							}
					});
				} 
			}]
        });

		
		
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability.Supplychain_org.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain, 
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            }, 
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability.Supplychain_org.MainGrid-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
						xtype:'button',
						icon: varjs.config.base_url + 'images/icons/new/add.png',
						text: lang('Add'), 
						cls:'Sfr_BtnGridGreen',
						overCls:'Sfr_BtnGridGreen-Hover',
						handler: function() {
							var storeGridMainRel = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid'); 
							storeGridMainRel.load();
							tambah();
						}
				  },{
                    xtype:'tbspacer',
                    flex:1
                  }, 
				  {
						id: 'sObjType',
						name: 'sObjType',
						xtype: 'combo', 
						width: 190,
						store: cmb_objtype,
						displayField: 'label',
						valueField: 'id',
						queryMode: 'local',
						selectOnFocus: true,
						emptyText: lang('Role'),
						listeners: {
							  
						}
					},{
						name: 'sName',
						id: 'sName',
						xtype: 'textfield', 
						width: 300,
						emptyText: lang('Name'),
						listeners: {
							 
						}
					},{
						xtype: 'button',
						icon: varjs.config.base_url + 'images/icons/silk/search.png', 
						cls:'Sfr_BtnGridPaleBlue',
						overCls:'Sfr_BtnGridPaleBlue-Hover',
						handler: function() {
							storeGridMain.load({
								params: {
									page: 1,
									start: 0,
									limit: 50
								}
							});
						}
					}
				]
            }],
            columns: [
			{ 
				text: lang('Action'),
				xtype:'actioncolumn',
				width:'5%',
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						contextMenuSuppGrid.showAt(e.getXY());
						var sm = record; //sm.data.SupplyStatus
					 
					}
				}]
			},{
                text: lang('ID'),
                dataIndex: 'SupplychainID', 
				width: '5%'
            }, {
                text: lang('Type'),
                dataIndex: 'ObjType', 
				width: '13%'
            },{
                text: lang('Name'),
                dataIndex: 'Name', 
				flex:1
            },
			{
                text: lang('Relation'),
                dataIndex: 'rel', 
				width: '8%',
				renderer: function (value, meta) {
					if(value == 0){ meta.style = "background-color:#ED2F0D; color:white; text-align:center;"; return 'N'; } 
					else { meta.style = "background-color:#23B80C; color:white; text-align:center;"; return 'Y'; } 
				}
            },
			{
                text: lang('Quality'),
                dataIndex: 'quality', 
				width: '8%',
				renderer: function (value, meta) {
					if(value == 0){ meta.style = "background-color:#ED2F0D; color:white; text-align:center;"; return 'N'; } 
					else { meta.style = "background-color:#23B80C; color:white; text-align:center;"; return 'Y'; } 
				}
            },
			{
                text: lang('Quality Value'),
                dataIndex: 'quality_value', 
				width: '8%',
				renderer: function (value, meta) {
					if(value == 0){ meta.style = "background-color:#ED2F0D; color:white; text-align:center;"; return 'N'; } 
					else { meta.style = "background-color:#23B80C; color:white; text-align:center;"; return 'Y'; } 
				}
            }, 
			{
                text: lang('Package'),
                dataIndex: 'package', 
				width: '8%',
				renderer: function (value, meta) {
					if(value == 0){ meta.style = "background-color:#ED2F0D; color:white; text-align:center;"; return 'N'; } 
					else { meta.style = "background-color:#23B80C; color:white; text-align:center;"; return 'Y'; } 
				}
            }], 
            listeners: {
            
			}			
        }];
        this.callParent(arguments);
    }
}); 

var MainGridRelated = Ext.create('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid'); 	
var MainGridQuality = Ext.create('Koltiva.view.Traceability.Supplychain_quality.MainGrid'); 
var MainGridPackage = Ext.create('Koltiva.view.Traceability.Supplychain_package.MainGrid'); 
var MainGridAreaDistrict = Ext.create('Koltiva.view.Traceability.Supplychain_area.MainGrid'); 
var MainGridAreaFarmer = Ext.create('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid');
	
var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 170,
            anchor: '100%'
        },
        items: [{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
								columnWidth: 0.495,
								style:'padding-right:25px;',
								layout:'form',
								items:[
								  /*LEFT*/
										{
											xtype: 'fieldset',
											title: lang('Unit Pembelian'),
											items: [{
													xtype: 'hidden',
													id: 'setVarParameters',//Importan to setvar view mode
													},
													{
														  xtype: 'hidden',
														  id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID',
														  name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID', 
													},
													{ 
													   id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PartnerID',
													   name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PartnerID',
													   xtype: 'combo', 
													   fieldLabel: lang('Partner'),
													   store: cmb_storePatner,
													   displayField: 'PartnerName',
													   valueField: 'PartnerID',
													   queryMode: 'local',
													   listeners: {
														  'change': function(fb, v){
														   
														  }					   
													  }
													},
													{
													   id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjType',
													   name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjType',
													   xtype: 'combo', 
													   fieldLabel: lang('Role'),
													   store:cmb_objtype,
													   displayField: 'label',
													   valueField: 'id',
													   queryMode: 'local',
													   listeners: {
														  'select' : function()
														  {
															Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID').setValue('')
														  },
														  'change': function(fb, v){
															 if(fb.getValue() != null && fb.getValue() !='' ){
															 
															 var SupplyChainID = DataForm.getForm().findField("Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID").getValue();						 
															 cmbObjID.setStoreVar({
																SupplyChainID : SupplyChainID,
																ObjType: fb.getValue(),
																DistrictID: null,
																PartnerID : Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PartnerID').getValue()
															 });
															 cmbObjID.load();
															 }
														  }					   
													  }
													},{
													   fieldLabel: lang('Obj ID'),
													   id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID',
													   name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID',
													   xtype: 'combo', 
													   store: cmbObjID ,
													   queryMode: 'local',
													   displayField: 'label',
													   valueField: 'id', 
													}]
										}
								  /*LEFT*/ 
								]
							  },
							  {
								columnWidth: 0.5,
								layout:'form',
								style:'padding-left:15px;',
								items:[
								   /*RIGHT*/
								   {
											xtype: 'fieldset',
											title: lang('Setting Transaction'),
											items: [
													{
														xtype: 'fieldcontainer', 
														width : 350, 
														fieldLabel: lang('Farmer'),									
														defaults: {
															hideLabel: true,
															allowBlank: true, 
															readOnly:true,
														}, 
														layout: 'hbox',
														msgTarget: 'side',
														items: [{ 
																	labelAlign:'top',
																	xtype: 'radiogroup',
																	allowBlank: false,
																	id : 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsFarmer',
																	msgTarget: 'side',
																	columns :2, 
																	padding :'8 10 0 0',
																	items:[{
																		boxLabel: lang('Yes'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsFarmer',
																		inputValue: '1',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsFarmerYes',
																		style: 'margin-top:-10px;',
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	},{
																		boxLabel: lang('No'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsFarmer',
																		inputValue: '0',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsFarmerNo',
																		style: 'margin-top:-10px; margin-left:20px;',
																		width : 160,
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	}]
																}]
													},
													{
														xtype: 'fieldcontainer', 
														width : 350, 
														fieldLabel: lang('Batch'),									
														defaults: {
															hideLabel: true,
															allowBlank: true, 
															readOnly:true,
														}, 
														layout: 'hbox',
														msgTarget: 'side',
														items: [{ 
																	labelAlign:'top',
																	xtype: 'radiogroup',
																	id : 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsBatch',
																	allowBlank: false,
																	msgTarget: 'side',
																	columns :2, 
																	padding :'8 10 0 0',
																	items:[{
																		boxLabel: lang('Yes'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsBatch',
																		inputValue: '1',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsBatchYes',
																		style: 'margin-top:-10px;',
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	},{
																		boxLabel: lang('No'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsBatch',
																		inputValue: '0',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsBatchNo',
																		style: 'margin-top:-10px; margin-left:20px;',
																		width : 160,
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	}]
																}]
													},
													{
														xtype: 'fieldcontainer', 
														width : 350, 
														fieldLabel: lang('Sent'),									
														defaults: {
															hideLabel: true,
															allowBlank: true, 
															readOnly:true,
														}, 
														layout: 'hbox',
														msgTarget: 'side',
														items: [{ 
																	labelAlign:'top',
																	xtype: 'radiogroup',
																	id : 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsSent',
																	allowBlank: false,
																	msgTarget: 'side',
																	columns :2, 
																	padding :'8 10 0 0',
																	items:[{
																		boxLabel: lang('Yes'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsSent',
																		inputValue: '1',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsSentYes',
																		style: 'margin-top:-10px;',
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	},{
																		boxLabel: lang('No'),
																		name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsSent',
																		inputValue: '0',
																		id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsSentNo',
																		style: 'margin-top:-10px; margin-left:20px;',
																		width : 160,
																		listeners:{
																			change: function(){
																				return false;
																			}
																		}
																	}]
																}]
													},
													{
														xtype: 'fieldcontainer', 
														width : 350, 
														fieldLabel: lang('Area'),									
														defaults: {
															hideLabel: true,
															allowBlank: false, 
														}, 
														layout: 'hbox',
														msgTarget: 'side',
														items: [{
																   id: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-AccessBy',
																   name: 'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-AccessBy',
																   xtype: 'combo', 
																   fieldLabel: lang('Area'),
																   store:cmbArea,
																   displayField: 'label',
																   valueField: 'id',
																   queryMode: 'local',
																   listeners: { 
																	  'change': function(fb, v){
																		  
																	  }					   
																  }
																}]
													}
											]
								   }
								   /*RIGHT*/
								]
							  }]
						}]
				   }]   
			   },
			    
			 /*TAB PANEL*/
				{
					xtype: 'tabpanel',
					id:'all_panel', 
					flex: 1,
					margin: 2,
					activeTab: 0,
					plain: true,
					cls:'tabSce',
					items: [{
								xtype: 'panel',
								autoScroll: true, 
								//disabled:true,
								id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Relasi',
								title: lang('Relasi'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items:[MainGridRelated]
							},
							{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Quality',
								title: lang('Quality'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridQuality]
							}, 
							{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Package',
								title: lang('Package'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridPackage]
							},{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Area',
								title: lang('Access District'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridAreaDistrict]
							},{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Farmer',
								title: lang('Access Farmer'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridAreaFarmer]
							}],
							listeners: { 
								'tabchange': function (tabPanel, tab) { 
									if(tab.title == lang('Quality') ){
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality.MainGrid-gridMainGrid').getStore().load();
									}
									if(tab.title == lang('Package') ){
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_package.MainGrid-gridMainGrid').getStore().load();
									}
									 
									if(tab.title == lang('Access District') ){
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
									}
									
									if(tab.title == lang('Access Farmer') ){
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
									}
									
								}
							}
			    }
				/*END TAB*/
		],
		buttons: [{
            id:'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
			icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            handler: function() {               
			    var form = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm').getForm();  
				if(Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-AccessBy').getValue() == null )
				{
					Ext.MessageBox.show({
									title: 'Error',
									msg: lang('Belum Memilih Area'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-error'
								});
				}else{
					form.submit({
							url: m_api + '/traceability_api/Supplychain_org/submit',
							method:'POST',
							waitMsg: lang('Sending data...'),
							success: function(fp, o) {
								Ext.MessageBox.show({
									title: 'Information',
									msg: lang('Data saved'),
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-success'
								});  
								Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').setValue(o.result.SupplyTransID);
								Ext.getCmp('all_panel').show() 
								
								 /*Enable Tab after Save*/ 		
								setDisabledTabs(false)
							},
							failure: function(fp, o){
								var pesanNya;
								 
								if(o.result.message != undefined){
									pesanNya = o.result.message;
								}else{
									pesanNya = lang('Connection error');
								}
								Ext.MessageBox.show({
									title: 'Error',
									msg: pesanNya,
									buttons: Ext.MessageBox.OK,
									animateTarget: 'mb9',
									icon: 'ext-mb-error'
								});
								 
							}
						});
				}
                
				
			  
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
			icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            disabled: false,
            handler: function() {
                win.hide();
            }
        }]
		
});

var win = Ext.create('widget.window', {
        title: lang('Buying Unit'),
        id:'Koltiva.view.Traceability.Reference.Supplychain_org-win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        autoScroll: true,
        width: '90%',
        height: '90%', 
        listeners:{
            hide: function(){
                //supaya di reset lg form + gridnya
                Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjType').setValue('');
                Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID').setValue('');
                Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm').getForm().reset(); 
            }
        },
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
	
function tambah() { 
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjType').setValue('');
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID').setValue('');
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm').getForm().reset(); 
	displayFormWindow(); 
}
function displayFormWindow(){
	DataForm.getForm().reset();
	setDisabledTabs(true)
	if(!win.isVisible()){
		win.show();
	} else {
		win.show();
	}
}

SetFormTransaction = function(SupplychainID)
{ 
	
	Ext.Ajax.request({
		waitMsg: lang('Please Wait'),
		url:  m_api + '/traceability_api/Supplychain_org/fetch_supplyorg',
		method : 'GET',
		params: {
		   SupplychainID: SupplychainID
		},
		success: function(response, opts){
		   var obj = Ext.decode(response.responseText);  
		    //alert(obj.data[0].IsFarmer)
		    Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').setValue(SupplychainID);
			Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PartnerID').setValue(obj.data[0].PartnerID);
		    Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjType').setValue(obj.data[0].ObjType);
            Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID').setValue(obj.data[0].ObjID);
			Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-AccessBy').setValue(obj.data[0].AccessBy);
			
			Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsFarmer').setValue(
			  {'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsFarmer' : obj.data[0].IsFarmer }
			);
			Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsBatch').setValue(
			  {'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsBatch' : obj.data[0].IsBatch }
			);
			Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PanelOpsiIsSent').setValue(
			  {'Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-IsSent' : obj.data[0].IsSent }
			); 
			
			setDisabledTabs(false)//Aktifkan Tabs
			
			//Load Store Tab 1, karena tab yg lain diload berdasarkan klik tab masing". biar gak berat
			//var storeGridMainRel = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid'); 
			//storeGridMainRel.load();
			Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getStore().load();
			
			if(!win.isVisible()){
				win.show();
			} else {
				win.show();
			}
		},
		failure: function(response, opts){
		   Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
		}
	 });  
}

setDisabledTabs = function(st)
{
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Relasi').setDisabled(st);			 
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Quality').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Package').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Area').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-panel_Farmer').setDisabled(st); 
	var cmp = Ext.getCmp('all_panel');
	cmp.setActiveTab(0);
}

setDisabledButtonView = function (st)
{ 
	if(st == true){
		Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-saveButton').hide();
	}
	else{
		Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-saveButton').show();
	}
	
	Ext.getCmp('Koltiva.view.Traceability.Supplychain_package.MainGrid-gridMainGrid-Btn').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid-Btn').setDisabled(st);
	Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality.MainGrid-gridMainGrid-Btn').setDisabled(st);
	Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid-Btn').setDisabled(st); 
	
}


 