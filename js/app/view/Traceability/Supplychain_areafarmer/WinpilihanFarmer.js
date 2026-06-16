  

Ext.define('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer' ,{ 
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer',
    title: lang('Farmer List'),
    closable: false,
    modal: true,
    closeAction: 'destroy',
    width: '80%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        var ComboProvince = Ext.create('Koltiva.store.ComboGeneral.ComboProvince'); 
		var ComboDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
		var ComboSubDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');
		var ComboVillage = Ext.create('Koltiva.store.ComboGeneral.ComboVillage');
		var storewinGridFarmer = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_areafarmer.winGridFarmer');
		//var MainGridTransactionPengiriman = Ext.create('Koltiva.store.Traceability.Transaction.MainGridTransactionPengiriman');
        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [
								{
								layout: 'column',
								border: false,
								items:[{
										columnWidth: 0.495,
										style:'padding:5px 10px 0px 10px;',
										layout:'form',
										items:[{
											xtype: 'fieldset',
											title: lang('Setting Date'),
											items: [
											   /*Awal*/
											   {
													xtype: 'datefield',
													fieldLabel: lang('Start Date'),
													labelWidth:175,  
													id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateStart',
													name: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateStart',  
												},  
												{
													xtype: 'datefield',
													fieldLabel: lang('End Date'),
													labelWidth:175, 
													id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateEnd',
													name: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateEnd',  
												}
											   /*akhir*/
											]
										}]
									  }]
							},
							{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true, 
                            selType: 'rowmodel',
                            store: storewinGridFarmer,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar',
                                store: storewinGridFarmer,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    emptyText: lang('Search by Name / ID')
                                },{
									xtype: 'combobox',
									typeAhead: true, 
									queryMode: 'local',
									id:'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboProvince',
									store : ComboProvince,
									emptyText: lang('Select a Province...'),
									displayField : 'label',
									valueField : 'id',
									allowBlank: true,
									listeners : {
										select : function()
										{
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboDistrict').setValue('');
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboSubDistrict').setValue('');
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboVillage').setValue('');
										},
										change: function(record) {  
										   ComboDistrict.setStoreVar({'ProvinceID':record.getValue()}); 
										   ComboDistrict.load();  
										}
									}
								},{
									xtype: 'combobox',
									typeAhead: true, 
									id :'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboDistrict',
									store : ComboDistrict,
									queryMode: 'local',
									displayField : 'label',
									valueField : 'id',
									allowBlank: true,
									listeners : {
										select : function()
										{
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboSubDistrict').setValue('');
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboVillage').setValue('');
										},
										change : function(record){
										   ComboSubDistrict.setStoreVar({'DistrictID':record.getValue()}); 
										   ComboSubDistrict.load();  
										}
									}
								},{
									xtype: 'combobox',
									typeAhead: true, 
									id :'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboSubDistrict',
									store : ComboSubDistrict,
									queryMode: 'local',
									displayField : 'label',
									valueField : 'id',
									allowBlank: true,
									listeners : {
										select : function()
										{
											Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboVillage').setValue('');
										},
										change : function(record){
										   ComboVillage.setStoreVar({'SubDistrictID':record.getValue()}); 
										   ComboVillage.load();  
										}
									}
								},{
									xtype: 'combobox',
									typeAhead: true, 
									id :'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboVillage',
									store : ComboVillage,
									queryMode: 'local',
									displayField : 'label',
									valueField : 'id',
									allowBlank: true,
									listeners : {
										change : function(record){
										    
										}
									}
								},{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png', 
									cls:'Sfr_BtnGridPaleBlue',
									overCls:'Sfr_BtnGridPaleBlue-Hover', 
                                    text: lang('Search'),
                                    handler: function() {
										storewinGridFarmer.load( )
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
												var DateStart = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateStart').getValue();
												var DateEnd = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateEnd').getValue(); 
												console.log(DateStart)
												if(DateStart != null && DateEnd != null){
													id = record.get('MemberID');  
													Ext.Ajax.request({
														url: m_api + '/traceability/Supplychain_areafarmer/sentcheckeddata',
														method: 'POST',
														waitMsg: lang('Sending data...'),
														params: { 
															FarmerID: id,
															DateStart : DateStart,
															DateEnd : DateEnd,
															SupplychainID : Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue()
														},
														success: function(response, opts) {
															var obj = Ext.decode(response.responseText);
															switch (obj.success) {
																case true:    
																	storewinGridFarmer.load();//reload Window grid  
																	break;
																default:
																	Ext.MessageBox.alert('Warning', obj.message);
																	break;
															}
														}
													});
												}else{
													Ext.MessageBox.alert('Warning', lang('Start Date dan End Date Masih Kosong') );
												}
										}
									}					
								}, 
                            columns: [
							{
                                text: 'ID',
                                dataIndex: 'FarmerID',
                                hidden: true
                            },
							{
								text: lang('Farmer ID'),
								dataIndex: 'MemberDisplayID',
								width:'10%' 
							},
							{
								text: lang('Farmer Name'),
								dataIndex: 'MemberName',
								width:'15%' 
							},
							{
								text: lang('Village'),
								dataIndex: 'Desa',
								 flex:1, 
							},
							{
								text: lang('SubDistrict'),
								dataIndex: 'Kecamatan',
								flex:1,
							},
							{
								text: lang('District'),
								dataIndex: 'District',
								flex:1,
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
                Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
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

  