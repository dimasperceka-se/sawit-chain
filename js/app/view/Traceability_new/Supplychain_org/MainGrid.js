var cmb_objtype = Ext.create('Ext.data.Store', {
			extend: 'Ext.data.Model',
			fields: ['id', 'label'],
			data: [{
                "id": "mill",
                "label": lang("Mill")
            }, {
                "id": "agent",
                "label": lang("SME")
            },{
				"id": "refinery",
                "label": lang("Refinery")
			},{
				"id": "kcp",
                "label": lang("KCP")
			},{
				"id": "bulking",
                "label": lang("Bulking")
			},{
				"id": "farmer_group",
                "label": lang("Farmer Group")
			},{
				"id": "cooperative",
                "label": lang("Cooperative")
			}]
			// autoLoad: true,
			// proxy: {
			// 	type: 'ajax',
			// 	url: m_api + '/traceability_api/Supplychain_org/objtype_list',
			// 	reader: {
			// 		type: 'json',
			// 		root: 'data'
			// 	}
			// }
		});
var cmb_storePatner = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org.cmbPartner'); 
var cmbObjID = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboStaffObjID'); 
var cmbArea = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org.ComboArea');
var cmbCurrency = Ext.create('Koltiva.store.ComboGeneral.CmbWageCurrency');

var MainSetting = Ext.create('Ext.form.Panel', {
    frame: false,
	height: 1000,
	autoScroll: true,
	width: '100%',
	bodyPadding: 5,
    id: 'Koltiva.view.Traceability_new.Supplychain_org.MainGrid.TabSetting-form', 
    items: [{
        xtype: 'panel',
        title: lang('Setting Transaction'),
        items:[{
            layout: 'column',
            items:[{
                columnWidth: 0.485,
                layout:'form',
                style:'padding-left:5px;margin-top:5px;',
                items: [{
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
                                    id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsFarmer',
                                    msgTarget: 'side',
                                    columns :2, 
                                    padding :'8 10 0 0',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsFarmer',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsFarmerYes',
                                        style: 'margin-top:-10px;',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsFarmer',
                                        inputValue: '0',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsFarmerNo',
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
                            fieldLabel: lang('Non Farmer'),									
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsNonFarmer',
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsNonFarmer',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsNonFarmerYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsNonFarmer',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsNonFarmerNo',
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsBatch',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsBatch',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsBatchYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsBatch',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsBatchNo',
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
                            fieldLabel: lang('Storage'),									
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsStorage',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsStorage',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsStorageYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsStorage',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsStorageNo',
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsSent',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSent',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSentYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSent',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSentNo',
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
                            fieldLabel: lang('Company'),									
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsCompany',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsCompany',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsCompanyYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsCompany',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsCompanyNo',
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
                            fieldLabel: lang('GHG Emission'),									
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
                                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsGHGEmissions',
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        columns :2, 
                                        padding :'8 10 0 0',
                                        items:[{
                                            boxLabel: lang('Yes'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsGHGEmissions',
                                            inputValue: '1',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsGHGEmissionsYes',
                                            style: 'margin-top:-10px;',
                                            listeners:{
                                                change: function(){
                                                    return false;
                                                }
                                            }
                                        },{
                                            boxLabel: lang('No'),
                                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsGHGEmissions',
                                            inputValue: '0',
                                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsGHGEmissionsNo',
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
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-AccessBy',
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-AccessBy',
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
                        },
                        {
                            xtype: 'fieldcontainer', 
                            width : 350, 
                            fieldLabel: lang('Currency'),									
                            defaults: {
                                hideLabel: true,
                                allowBlank: false, 
                            }, 
                            layout: 'hbox',
                            msgTarget: 'side',
                            items: [{
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-CurrID',
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-CurrID',
                                        xtype: 'combo', 
                                        fieldLabel: lang('Currency'),
                                        store:cmbCurrency,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        listeners: { 
                                            'change': function(fb, v){
                                                
                                            }					   
                                        }
                                    }]
                        },{
                            xtype: 'fieldcontainer', 
                            width : 350, 
                            fieldLabel: lang('Production Capacity (MT/Hour)'),							
                            defaults: {
                                hideLabel: true,
                                allowBlank: false, 
                            }, 
                            layout: 'hbox',
                            msgTarget: 'side',
                            items: [{
                                    xtype: 'numberfield',	
                                    id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ProductionCapacity',
                                    name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ProductionCapacity',
                                    allowDecimals: true,
                                    value:0,
                                }]
                        },{
                            xtype: 'fieldcontainer', 
                            width : 350, 
                            fieldLabel: lang('Work Hour(Per Hour)'),								
                            defaults: {
                                hideLabel: true,
                                allowBlank: false, 
                            }, 
                            layout: 'hbox',
                            msgTarget: 'side',
                            items: [{
                                    xtype: 'numberfield',	
                                    id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-WorkHour',
                                    name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-WorkHour',  
                                    value:0, 
                                }]
                        },
                ]
            },
            {
                columnWidth: 0.485,
                layout:'form',
                style:'padding-left:15px;margin-top:5px;',
                items:[{
                    xtype: 'fieldcontainer', 
                    width : 350, 
                    fieldLabel: lang('Sent SMS Notif'),                                 
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
                        id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsSMS',
                        allowBlank: false,
                        msgTarget: 'side',
                        columns :2, 
                        padding :'8 10 0 0',
                        items:[{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSMS',
                            inputValue: '1',
                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSMSYes',
                            style: 'margin-top:-10px;',
                            width : 50,
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSMS',
                            inputValue: '0',
                            id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSMSNo',
                            style: 'margin-top:-10px; margin-left:50px;',
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
                        xtype: 'datefield',
                        labelWidth: 230,
                        width:300,
                        fieldLabel: lang('Active SMS Date'),
                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SMSDate',
                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SMSDate',
                        format: 'Y-m-d',
                        allowBlank: true,  
                    },
                    {
                        xtype: 'fieldcontainer', 
                        width : 350, 
                        fieldLabel: lang('Payment'),									
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
                                    id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsPaymentMethod',
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    columns :2, 
                                    padding :'8 10 0 0',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPaymentMethod',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPaymentMethodYes',
                                        style: 'margin-top:-10px;',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPaymentMethod',
                                        inputValue: '0',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPaymentMethodNo',
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
                        xtype: 'datefield',
                        labelWidth: 230,
                        width:300,
                        fieldLabel: lang('Active Payment Date'),
                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PaymentDate',
                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PaymentDate',
                        format: 'Y-m-d',
                        allowBlank: true,  
                    },
                    {
                        xtype: 'fieldcontainer', 
                        width : 350, 
                        fieldLabel: lang('Processing Detail'),									
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
                                    id : 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsPickProcessingDetail',
                                    allowBlank: false,
                                    msgTarget: 'side',
                                    columns :2, 
                                    padding :'8 10 0 0',
                                    items:[{
                                        boxLabel: lang('Auto'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPickProcessingDetail',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPickProcessingDetailYes',
                                        style: 'margin-top:-10px;',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('Manual'),
                                        name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPickProcessingDetail',
                                        inputValue: '0',
                                        id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPickProcessingDetailNo',
                                        style: 'margin-top:-10px; margin-left:20px;',
                                        width : 160,
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                            }]
                    }
            ]
        }]
        }]
    }]
});


function submitOnEnterGridGrower(field, event) {
	if (event.getKey() == event.ENTER) {
		Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid').getStore().loadPage(1);
	}
}
		
Ext.define('Koltiva.view.Traceability_new.Supplychain_org.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Supplychain_org.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org.MainGrid'); 
		
		var contextMenuSuppGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
				cls:'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuViewItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                    SetFormTransaction(sm.get('SupplychainID')); 
					setDisabledButtonView(true)
					Ext.getCmp('setVarParameters').setValue('view');//edit mode
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
				cls:'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
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
					var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
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
								   Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid').getStore().load();
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
		
		var generated	= true;
		if(m_daerah_access.includes("73") || m_daerah_access.includes("61")){
			generated	= false;
		}else if(m_daerah_access.includes("43") || m_daerah_access.includes("44")){
			generated	= true;
		}		
		
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
			minHeight:125,
            selType: 'rowmodel',
            store: storeGridMain, 
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            }, 
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Supplychain_org.MainGrid-gridToolbar',
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
						var storeGridMainRel = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org_rel.MainGrid'); 
						storeGridMainRel.load();
						tambah();
					}
				},{
					xtype:'button',
					icon: varjs.config.base_url + 'images/icons/new/reload.png',
					text: lang('Generate Access Farmer'), 
					cls:'Sfr_BtnGridGreen',
                    hidden: generated,
					overCls:'Sfr_BtnGridGreen-Hover',
					handler: function() {
						Ext.MessageBox.confirm('Message', 'Generating Access Farmer Data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/traceability_api/Supplychain_areafarmer/generate_farmer_access_all',
                                    method: 'GET',
                                    success: function(response, o) {
                                        var obj = Ext.JSON.decode(response.responseText);
                                        
                                        if(obj.code == "400"){
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: obj.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-warning'
                                            });
                                            return false;
                                        }
                                        
                                        if(obj.code == 200){
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data Generated'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });
                                        }
                                        Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
                                    },
                                    failure: function(response, opts) {
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
                        });
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
							specialkey: submitOnEnterGridGrower
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

var MainGridRelated = Ext.create('Koltiva.view.Traceability_new.Supplychain_org_rel.MainGrid'); 	
var MainGridQuality = Ext.create('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid'); 
var MainGridPackage = Ext.create('Koltiva.view.Traceability_new.Supplychain_package.MainGrid'); 
var MainGridAreaDistrict = Ext.create('Koltiva.view.Traceability_new.Supplychain_area.MainGrid'); 
var MainGridAreaFarmer = Ext.create('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid');
var MainGridProduct = Ext.create('Koltiva.view.Traceability_new.Supplychain_product.MainGrid');
	
var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 170,
            anchor: '100%'
        },
        items: [
			{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[
							{
								columnWidth: 0.495,
								style:'padding-right:25px;',
								layout:'form',
								items:[
									{
										xtype: 'fieldset',
										title: lang('Unit Pembelian'),
										items: [{
												xtype: 'hidden',
												id: 'setVarParameters',//Importan to setvar view mode
												},
												{
														xtype: 'hidden',
														id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID',
														name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID', 
												},
												{ 
													id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PartnerID',
													name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PartnerID',
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
													id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType',
													name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType',
													xtype: 'combo', 
													fieldLabel: lang('Role'),
													store:cmb_objtype,
													displayField: 'label',
													valueField: 'id',
													queryMode: 'local',
													listeners: {
														'select' : function()
														{
														Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID').setValue('')
														},
														'change': function(fb, v){
															if(fb.getValue() != null && fb.getValue() !='' ){
															
															var SupplyChainID = DataForm.getForm().findField("Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID").getValue();						 
															cmbObjID.setStoreVar({
															SupplyChainID : SupplyChainID,
															ObjType: fb.getValue(),
															DistrictID: null,
															PartnerID : Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PartnerID').getValue()
															});
															cmbObjID.load();
															}
														}					   
													}
												},{
													fieldLabel: lang('Obj ID'),
													id: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID',
													name: 'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID',
													xtype: 'combo', 
													store: cmbObjID ,
													queryMode: 'local',
													displayField: 'label',
													valueField: 'id', 
												}]
									}]
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
					items: [
							{
								xtype: 'panel',
								autoScroll: true, 
								//disabled:true,
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Setting',
								title: lang('Setting'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items:[MainSetting]
							},
							{
								xtype: 'panel',
								autoScroll: true, 
								//disabled:true,
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Relasi',
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
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Quality',
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
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Package',
								title: lang('Package'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridPackage]
							},{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Area',
								title: lang('Access District'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridAreaDistrict]
							},{
								xtype: 'panel',
								autoScroll: true,
								disabled:true,
								id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Farmer',
								title: lang('Access Farmer'),
								width:'100%',
								padding:5,
								style: 'border:2px solid #ADD2ED', 
								items: [MainGridAreaFarmer]
							},
							// {
							// 	xtype: 'panel',
							// 	autoScroll: true,
							// 	disabled:true,
							// 	id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Product',
							// 	title: lang('Product Output'),
							// 	width:'100%',
							// 	padding:5,
							// 	style: 'border:2px solid #ADD2ED', 
							// 	items: [MainGridProduct]
							// }
							],
							listeners: { 
								'tabchange': function (tabPanel, tab) { 
									if(tab.title == lang('Quality') ){
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getStore().load();
									}
									if(tab.title == lang('Package') ){
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_package.MainGrid-gridMainGrid').getStore().load();
									}
									 
									if(tab.title == lang('Access District') ){
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
									}
									
									if(tab.title == lang('Access Farmer') ){
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
									}

									// if(tab.title == lang('Product Output') ){
									// 	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getStore().load();
									// }
									
								}
							}
			    }
				/*END TAB*/
		],
		buttons: [{
            id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
			icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            handler: function() {               
			    var form = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm').getForm();  
				if(Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-AccessBy').getValue() == null )
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
								Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').setValue(o.result.SupplyTransID);
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
        id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        autoScroll: true,
        width: '90%',
        height: '90%', 
        listeners:{
            hide: function(){
                //supaya di reset lg form + gridnya
                Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType').setValue('');
                Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID').setValue('');
                Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm').getForm().reset(); 
            }
        },
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
	
function tambah() { 
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType').setValue('');
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID').setValue('');
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm').getForm().reset(); 
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
		    
		    Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').setValue(SupplychainID);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PartnerID').setValue(obj.data[0].PartnerID);
		    Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType').setValue(obj.data[0].ObjType);
            Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID').setValue(obj.data[0].ObjID);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-AccessBy').setValue(obj.data[0].AccessBy);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-CurrID').setValue(obj.data[0].CurrID);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ProductionCapacity').setValue(obj.data[0].ProductionCapacity);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-WorkHour').setValue(obj.data[0].WorkHour);
			
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SMSDate').setValue(obj.data[0].SMSDate);

            Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PaymentDate').setValue(obj.data[0].PaymentDate);

			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsSMS').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSMS' : obj.data[0].IsSMS }
			);

			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsFarmer').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsFarmer' : obj.data[0].IsFarmer }
		  	);
			
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsNonFarmer').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsNonFarmer' : obj.data[0].IsNonFarmer }
			);
			
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsBatch').setValue(
			  	{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsBatch' : obj.data[0].IsBatch }
			);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsStorage').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsStorage' : obj.data[0].IsStorage }
			);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsSent').setValue(
			  	{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsSent' : obj.data[0].IsSent }
			); 
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsCompany').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsCompany' : obj.data[0].IsCompany }
			);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsPaymentMethod').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPaymentMethod' : obj.data[0].IsPaymentMethod }
			);
			Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsGHGEmissions').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsGHGEmissions' : obj.data[0].IsGHGEmissions }
			);
            Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PanelOpsiIsPickProcessingDetail').setValue(
				{'Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-IsPickProcessingDetail' : obj.data[0].IsPickProcessingDetail }
			);
			
			setDisabledTabs(false)//Aktifkan Tabs
			
			//Load Store Tab 1, karena tab yg lain diload berdasarkan klik tab masing". biar gak berat
			//var storeGridMainRel = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_org_rel.MainGrid'); 
			//storeGridMainRel.load();
			Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org_rel.MainGrid-gridMainGrid').getStore().load();
			
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
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Relasi').setDisabled(st);			 
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Quality').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Package').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Area').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Farmer').setDisabled(st);
	// Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-panel_Product').setDisabled(st);  
	
	var cmp = Ext.getCmp('all_panel');
	cmp.setActiveTab(0);
}

setDisabledButtonView = function (st)
{ 
	if(st == true){
		Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-saveButton').hide();
	}
	else{
		Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-saveButton').show();
	}
	
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_package.MainGrid-gridMainGrid-Btn').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_org_rel.MainGrid-gridMainGrid-Btn').setDisabled(st);
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid-Btn').setDisabled(st);
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality_value.MainGrid-gridMainGrid-Btn').setDisabled(st); 
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_package.MainGrid-gridMainGrid-Btn').setDisabled(st);
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid-Btn').setDisabled(st); 
	
}


 