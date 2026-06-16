 

Ext.define('Koltiva.view.Traceability_new.report.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.report.MainGrid',
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
		var storeGridMainReport  = Ext.create('Koltiva.store.Traceability_new.Report.MainGrid'); 
		var ComboMill = Ext.create('Koltiva.store.Traceability_new.Report.comboMill');
		var comboAgent = Ext.create('Koltiva.store.Traceability_new.Report.comboAgent'); 

		var comboCertified = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('No'), "id":'no'},
                {"label":lang('Yes'), "id":'yes'}
                //...
            ]
        });
		
		 
		
        //items
        thisObj.items = 
		[{//1
				xtype: "panel",
				titleAlign: "center",
				title: "TRACEABILITY REPORT TRANSACTION",
				bodyStyle: "background:#fff;",
				layout:{
					type:"column"
				},
				items: [
				{ //2
					xtype: "form",
					columnWidth:0.2,
					layout: "vbox",
					id: "Koltiva.view.Traceability_new.report.MainGrid-form-filter",
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
											var frmval = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGrid-form-filter").getValues();
											frmval.xls = "true";
											var store = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGrid-form-Grid").getStore();
											var url = store.getProxy().url;
											var querystring = EncodeQueryData(frmval);
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
									name: "Koltiva.view.Traceability_new.report.MainGrid-form-Mill",
									width:210,
									emptyText: "All",
									store: ComboMill,
									queryMode: "local",
									displayField: 'Name',
									valueField: 'PartnerID',
									id: "Koltiva.view.Traceability_new.report.MainGrid-form-Mill",
									listeners: {
										select : function()
										{
											Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-BuyingUnit').setValue(''); 
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
									name: "Koltiva.view.Traceability_new.report.MainGrid-form-BuyingUnit",
									width:210,
									emptyText: "All",
									store: comboAgent,
									queryMode: "local",
									displayField: "label",
									valueField: "id",
									id: "Koltiva.view.Traceability_new.report.MainGrid-form-BuyingUnit",
									listeners: {
										
									}
							  }]
					  },
					  { 
                        xtype: "buttongroup",
                        title: "<b>"+lang('Certified')+"</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
									xtype: "combo",
									name: "Koltiva.view.Traceability_new.report.MainGrid-form-Certified",
									width:210,
									emptyText: "All",
									store: comboCertified,
									queryMode: "local",
									displayField: "label",
									valueField: "id",
									id: "Koltiva.view.Traceability_new.report.MainGrid-form-Certified",
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-date_from",
                            emptyText: "Start From",
                            format: 'Y-m-d',
                            //value: m_date_start
                        },{
                            xtype: "datefield",
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-date_to",
                            emptyText: "Until Date",
                            format: 'Y-m-d',
                            value: m_date_end
                        }]
                     },{
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-TransNumber",
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-BatchNumber",
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-ProvinceID",
                            width:210,
                            emptyText: "All",
                            store: ComboProvince,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGrid-form-ProvinceID",
                            listeners: {
                                select : function()
								{
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-DistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-SubDistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-VillageID').setValue('');
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-DistrictID",
                            width:210,
                            emptyText: "All",
                            store: ComboDistrict,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGrid-form-DistrictID",
                            listeners: {
                                select : function()
								{ 
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-SubDistrictID').setValue('');
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-VillageID').setValue('');
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-SubDistrictID",
                            width:210,
                            emptyText: "All",
                            store: ComboSubDistrict,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGrid-form-SubDistrictID",
                            listeners: {
                                select : function()
								{
									Ext.getCmp('Koltiva.view.Traceability_new.report.MainGrid-form-VillageID').setValue('');
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-VillageID",
                            width:210,
                            emptyText: "All",
                            store: ComboVillage,
                            queryMode: "local",
                            displayField : 'label',
							valueField : 'id',
                            id: "Koltiva.view.Traceability_new.report.MainGrid-form-VillageID",
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
                            name: "Koltiva.view.Traceability_new.report.MainGrid-form-FarmerID",
                            width:210,
                            emptyText: "All FarmerID"
                        }]
                     }
					]
				 }, //2
				 {
					xtype: "gridpanel",
					columnWidth:0.8,
					id: "Koltiva.view.Traceability_new.report.MainGrid-form-Grid",
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
						id: 'Koltiva.view.Traceability_new.report.MainGrid-form-gridToolbar',
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
								width: '10%'
							 },/*{
								text: lang('SupplyBatchID'),
								dataIndex: 'SupplyBatchID', 
								hidden:true,
								width: '10%'
							 },*/
							 {
								text: lang('Transaction Number'),
								dataIndex: 'TransNumber', 
								width: '20%'
							 },{
								text: lang('FarmerID'),
								dataIndex: 'FarmerID', 
								width: '20%'
							 },{
								text: lang('FarmerName'),
								dataIndex: 'FarmerName', 
								width: '15%'
							 },{
								text: lang('isCertified'),
								dataIndex: 'isCertified', 
								width: '15%'
							 },/*{
								text: lang('Village'),
								dataIndex: 'Village', 
								hidden :true,
								width: '15%'
							 },{
								text: lang('SubDistrict'),
								dataIndex: 'SubDistrict',
								hidden :true, 
								width: '15%'
							 },*/
							 {
								text: lang('District'),
								dataIndex: 'District', 
								width: '15%'
							 },
							 {
								text: lang('Province'),
								dataIndex: 'Province', 
								width: '15%'
							 },/*{
								text: lang('PlotNr'),
								dataIndex: 'PlotNr', 
								width: '15%'
							 },{
								text: lang('FarmingTypeName'),
								dataIndex: 'FarmingTypeName', 
								width: '15%'
							 },{
								text: lang('DetailTypeName'),
								dataIndex: 'DetailTypeName', 
								width: '15%'
							 },{
								text: lang('VolumeBruto'),
								dataIndex: 'VolumeBruto', 
								width: '15%'
							 },{
								text: lang('PackageNumber'),
								dataIndex: 'PackageNumber', 
								width: '15%'
							 },{
								text: lang('VolumeCutting'),
								dataIndex: 'VolumeCutting', 
								width: '15%'
							 },*/{
								text: lang('Nett Weight (kg)'),
								dataIndex: 'VolumeNetto', 
								width: '15%'
							 },
							 {
								text: lang('Price per kg'),
								dataIndex: 'NetPrice', 
								width: '15%',
								renderer: Ext.util.Format.numberRenderer('0,000.00'),
								align : 'right',
							 },
							 {
								text: lang('Total Payment'),
								dataIndex: 'TotalPayment', 
								width: '15%',
								renderer: Ext.util.Format.numberRenderer('0,000.00'),
								align : 'right',
							 },
							 {
								text: lang('Agent'),
								dataIndex: 'Agent', 
								width: 170
							 },{
								text: lang('Batch Status'),
								dataIndex: 'Status', 
								width: '15%'
							 },{
								text: lang('Batch Number'),
								dataIndex: 'SupplyBatchNumber', 
								width: '15%'
							 },{
								text: lang('Destination'),
								dataIndex: 'Destination', 
								width: '15%'
							 },
							 /*{
								text: lang('Latitude'),
								dataIndex: 'Latitude', 
								width: '15%'
							 },{
								text: lang('Longitude'),
								dataIndex: 'Longitude', 
								width: '15%'
							 }*/
							{
								text: lang('Total Hectare'),
								dataIndex: 'GardenAreaHa', 
								width: '15%'
							 },{
								text: lang('Total Hectare Polygon'),
								dataIndex: 'GardenAreaPolygon', 
								width: '15%'
							 }]
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
	var frmval = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGrid-form-filter").getValues();
	var store = Ext.getCmp("Koltiva.view.Traceability_new.report.MainGrid-form-Grid").getStore();
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