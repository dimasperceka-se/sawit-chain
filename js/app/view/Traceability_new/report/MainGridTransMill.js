 

Ext.define('Koltiva.view.Traceability_new.report.MainGridTransMill' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.report.MainGridTransMill',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        
        var thisObj = this;
		
		var ComboProvince = Ext.create('Koltiva.store.ComboGeneral.ComboProvince'); 
		var ComboDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
		var ComboSubDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');
		var ComboVillage = Ext.create('Koltiva.store.ComboGeneral.ComboVillage');  
		var storeGridMainReport  = Ext.create('Koltiva.store.Traceability_new.Report.StoreMainGridMill'); 
		var ComboMill = Ext.create('Koltiva.store.Traceability_new.Report.ComboReportMill');
		var comboAgent = Ext.create('Koltiva.store.Traceability_new.Report.comboAgent'); 
		
		 
		
        //items
        thisObj.items = 
		[{//1
				xtype: "panel",
				titleAlign: "center",
				title: "TRACEABILITY REPORT TRANSACTION MILL",
				bodyStyle: "background:#fff;",
				layout:{
					type:"column"
				},
				items: [
				{ //2
					xtype: "form",
					columnWidth:0.2,
					layout: "vbox",
					id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-filter",
					padding: 5,
					height: 900,
					titleAlign:"center",
					style:"background:#424141 !important;",
					bodyStyle:"background:#424141 !important;",
					defaults: {
						labelAlign: "top",
						labelWidth: 100,
						labelStyle: "margin-top:3px;",
						margin: 3
					},
					width:200,
					items: [
						{//3
							xtype:"container",
							layout:{
								type:"hbox"
							},
							items:[
								{
									xtype: "button",
									margin: "5",
									text: "Generate Grid",
									handler: function () {
										Cari();
									}
								},
								{
									xtype: "button",
									margin: "5",
									text: lang("Export Excel"), 
									handler: function () { 
                                        var frmval = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGridTransMill-form-filter").getValues();
                                        frmval.xls = "true";
                                        var store = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGridTransMill-form-Grid").getStore();
                                        var url = m_api +'/traceability_api/Report_transaction_mill/export_excel/'
                                        var querystring = EncodeQueryData(frmval);
                                        console.log(url);
                                        console.log(querystring);
                                        window.open(url + "?" + querystring+'&Spout=true');
									}
								} 
							]
					   },//3 
					   { 
                        xtype: "buttongroup",
                        title: "<b>"+lang('Mill')+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
									xtype: "combo",
									name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-Mill",
									width:210,
									emptyText: "All",
									store: ComboMill,
									queryMode: "local",
									displayField: 'Name',
									valueField: 'SupplychainID',
									id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-Mill",
									listeners: {
										select : function()
										{
											Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-BuyingUnit').setValue(''); 
										},
										change: function (record) {
											comboAgent.proxy.extraParams.SupplyChainID = record.getValue();
											comboAgent.load();  
											
										} 
									}
							  }]
					  },
					  { 
                        xtype: "buttongroup",
                        title: "<b>"+lang('Agent')+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
									xtype: "combo",
									name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-BuyingUnit",
									width:210,
									emptyText: "All",
									store: comboAgent,
									queryMode: "local",
									displayField: "label",
									valueField: "id",
									id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-BuyingUnit",
									listeners: {
										
									}
							  }]
					  },
					  {
                        xtype: "buttongroup",
                        title: "<b>Transaction Date</b>",
                        columns: 2,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "datefield",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-date_from",
                            emptyText: "Start From",
                            format: 'Y-m-d',
                            //value: m_date_start
                        },{
                            xtype: "datefield",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-date_to",
                            emptyText: "Until Date",
                            format: 'Y-m-d',
                            value: m_date_end
                        }]
                     },/*{
                        xtype: "buttongroup",
                        title: "<b>"+lang("Transaction Number")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-TransNumber",
                            width:210,
                            emptyText: "All"
                        }]
                     },
					  {
                        xtype: "buttongroup",
                        title: "<b>"+lang("Batch Number")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-BatchNumber",
                            width:210,
                            emptyText: "All"
                        }]
                      },
					  {
                        xtype: "buttongroup",
                        title: "<b>"+lang("Province")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-ProvinceID",
                            width:210,
                            emptyText: "All",
                            store: ComboProvince,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-ProvinceID",
                            listeners: {
                                select : function()
								{
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-DistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-SubDistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-VillageID').setValue('');
								},
								change: function (record) {
                                    ComboDistrict.setStoreVar({'ProvinceID':record.getValue()}); 
								    ComboDistrict.load(); 
                                }
                            }
                        }]
                     },
					  {
                        xtype: "buttongroup",
                        title: "<b>"+lang("District")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-DistrictID",
                            width:210,
                            emptyText: "All",
                            store: ComboDistrict,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-DistrictID",
                            listeners: {
                                select : function()
								{ 
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-SubDistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-VillageID').setValue('');
								},
								change: function (record) {
                                    ComboSubDistrict.setStoreVar({'DistrictID':record.getValue()}); 
									ComboSubDistrict.load();   
                                }
                            }
                        }]
                     },
                     {
                        xtype: "buttongroup",
                        title: "<b>"+lang("Sub District")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-SubDistrictID",
                            width:210,
                            emptyText: "All",
                            store: ComboSubDistrict,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-SubDistrictID",
                            listeners: {
                                select : function()
								{
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill-form-VillageID').setValue('');
								},
								change : function(record){
								   ComboVillage.setStoreVar({'SubDistrictID':record.getValue()}); 
								   ComboVillage.load();  
								}
                            }
                        }]
                     },
                     {
                        xtype: "buttongroup",
                        title: "<b>"+lang("Village")+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-VillageID",
                            width:210,
                            emptyText: "All",
                            store: ComboVillage,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-VillageID",
                            listeners:{
                                 
                            }
                        }]
                     },
                     {
                        xtype: "buttongroup",
                        title: "<b>FarmerID</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-FarmerID",
                            width:210,
                            emptyText: "All FarmerID"
                        }]
                     }*/
					]
				 }, //2
				 {
					xtype: "gridpanel",
					columnWidth:0.8,
					id: "Koltiva.view.Traceability_new.report.MainGridTransMill-form-Grid",
					height: 900,
					loadMask: true,
					selType: 'rowmodel',
					store: storeGridMainReport, 
					viewConfig: {
						deferEmptyText: false,
						emptyText: lang('No data Available'),
					}, 
					dockedItems: [{
						xtype: 'pagingtoolbar',
						id: 'Koltiva.view.Traceability_new.report.MainGridTransMill-form-gridToolbar',
						store: storeGridMainReport,
						dock: 'bottom',
						displayInfo: true
					}],
					columns: [{
								text: lang('Transaction Date'),
								dataIndex: 'DateTransaction', 
								width: '15%'
							 },{
								text: lang('TransactionID'),
								dataIndex: 'SupplyTransID', 
                                hidden:true,
                                width: '10%'
							 },
							 {
								text: lang('Transaction Number'),
								dataIndex: 'TransNumber', 
								width: '20%'
                             },
                             {
								text: lang('Gross Weight (kg)'),
								dataIndex: 'VolumeBruto', 
								width: '15%'
							 },
							 {
								text: lang('Nett Weight (kg)'),
								dataIndex: 'VolumeNetto', 
								width: '15%'
							 },
							 {
								text: lang('Agent'),
								dataIndex: 'BatchFrom', 
								width: 170
							 },{
								text: lang('Batch Status'),
								dataIndex: 'Status', 
								width: '15%'
							 },{
								text: lang('Batch Number'),
								dataIndex: 'SupplyBatchNumber', 
								width: '15%'
                             },
                             {
								text: lang('Driver'),
								dataIndex: 'DestDriver', 
								width: '15%'
							 },
                             {
								text: lang('PO Number'),
								dataIndex: 'DestPO', 
								width: '15%'
							 },
							]
				 }
				] 
		        
		}]; //1

        this.callParent(arguments);
    }, 
    listeners: {
        afterlayout: function (c, v) {
            
        }
    },
});


function Cari(){
	var frmval = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGridTransMill-form-filter").getValues();
	var store = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGridTransMill-form-Grid").getStore();
	store.getProxy().extraParams = frmval;
	store.load();
}

function EncodeQueryData(data)
{
	var ret = [];
	var d;
	for (d in data) {
		ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
		}
	return ret.join("&");
	
}