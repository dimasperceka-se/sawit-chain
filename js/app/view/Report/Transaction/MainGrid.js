/*
* @Author: nikolius
* @Date:   2017-10-13 13:12:01
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-07-24 16:06:56
*/

Ext.define('Koltiva.view.Report.Transaction.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Report.Transaction.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
      var thisObj = this;

        //store yg dipakai (begin)
      var storeComboFarmer = Ext.create('Koltiva.store.Report.Transaction.ComboFarmer');   
      var storeComboMill = Ext.create('Koltiva.store.Report.Transaction.ComboMill');
      var storeComboDO = Ext.create('Koltiva.store.Report.Transaction.ComboDO');
      var storeComboAgent = Ext.create('Koltiva.store.Report.Transaction.ComboAgent');
      var cmbSupplyID = Ext.create('Koltiva.store.Report.Transaction.ComboSupplyID');
      var storeMill = Ext.create('Koltiva.store.Report.Transaction.MainGridTransaction');
      var storeDO = Ext.create('Koltiva.store.Report.Transaction.GridTransactionDO');
      var storeAgent = Ext.create('Koltiva.store.Report.Transaction.GridTransactionAgent');
      /*
      function filterRecord() {
        storeGridMain.load({
              params: {
                  start: 0,
                  keySearch: Ext.getCmp('keySearch').getValue(),
              }
          });
    }*/
    
      Var1=  Ext.define('MyGrid1', {
            extend: 'Ext.grid.Panel',
            xtype: 'grid1',
            id: 'Koltiva.view.Report.Transaction.MainGrid-gridRegisterStaff',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeMill,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        //buka popup form
                        var WinRegisterStaffForm = Ext.create('Koltiva.view.Report.Transaction.WinRegisterStaffForm',{
                            viewVar: {
                                opsiDisplay: 'insert'
                            }
                        });
                        if (!WinRegisterStaffForm.isVisible()) {
                            WinRegisterStaffForm.center();
                            WinRegisterStaffForm.show();
                        } else {
                            WinRegisterStaffForm.close();
                        }
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    id: 'Koltiva.view.Report.Transaction.MainGrid-SearchComboMill',
                    name: 'NameMill',
                    xtype: 'combobox',
                    width: 120,
                    store: storeComboMill,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    emptyText: lang('Mill'),
                    allowBlank: false
                },{
                    id: 'Koltiva.view.Report.Transaction.MainGrid-SearchComboDO',
                    name: 'NameDO',
                    xtype: 'combobox',
                    width: 120,
                    store: storeComboDO,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    emptyText: lang('DO'),
                },{
                    id: 'Koltiva.view.Report.Transaction.MainGrid-SearchComboAgent',
                    name: 'NameAgent',
                    xtype: 'combobox',
                    width: 150,
                    store: storeComboAgent,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    emptyText: lang('Agent'),
                },

                {
                    id: 'Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom',
                    name: 'SearchDateFrom',
                    xtype: 'datefield', 
                    emptyText: lang('From'),          
                    format: 'Y-m-d',
                    allowBlank: false
                },
                {
                    id: 'Koltiva.view.Report.Transaction.MainGrid-SearchDateTo',
                    name: 'SearchDateTo',
                    xtype: 'datefield',
                    emptyText: lang('To'),       
                    format: 'Y-m-d',
                    allowBlank: false
                },
                {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    margin: '0px 10px 0px 6px',
                    text: lang('Search'),
                    handler: function() {
                    var mill_search=Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboMill').getValue()
                    var from_search=Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom').getRawValue()                 
                    var to_search=Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateTo').getRawValue()
                    
                    if(mill_search==null) {alert('Mill Not Blank')}
                    if(from_search=='') {alert('From Not Blank')} 
                    if(to_search=='') {alert('To Not Blank')}        

                    if(mill_search !== 'null' || from_search !== '' || mill_search !== ''){
                        storeMill.load();
                        storeAgent.load();
                        storeDO.load();
                        }
                    }
                }]
            }],
            columns: [//
            ]
        })

        Var2=  Ext.define('MyGrid2', {
            extend: 'Ext.grid.Panel',
            xtype: 'grid2',
            store: storeMill,
            title: 'MILL',
            width: '100%',

            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeMill,
                dock: 'bottom',
                displayInfo: true
              },{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'splitbutton',
                    text: 'Export',
                    menu: {
                        items: [{
                            text: 'Excel',
                            handler: function () {                            
                                url     = 'api/index.php/report_traceability/store_grid_do_transaction_Excellmill?'
                                    +'&MillID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboMill').getValue()
                                    +'&DOID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboDO').getValue()
                                    +'&AgentID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboAgent').getValue()
                                    +'&DateFrom='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom').getRawValue()                 
                                    +'&DateTo='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateTo').getRawValue()
                                  
                                window.open(url);
                            }
                        }]
                    }
                }, {
                    xtype: 'label',
                    id: 'premiumWarehouse',
                    text: ''
                }]
            },],
            columns: [{
                text: 'ID',
                dataIndex: 'SupplyTransID',
                hidden: true
            },{
                text: lang('Type'),
                dataIndex: 'SupplyType',
                width:'7%'
            },{
                text: lang('District'),
                dataIndex: 'District',
                width:'10%'
            },{
                text: lang('Sub District'),
                dataIndex: 'SubDistrict',
                width:'10%'
            },{
                text: lang('Farmer ID'),
                dataIndex: 'FarmerID',
                width:'10%'
            },{
                text: lang('Mill Name'),
                dataIndex: 'Name',
                width:'20%'
            },{
                text: lang('Date'),
                dataIndex: 'DateTransaction',
                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                width:'10%'
            },{
                text: lang('Gross'),
                dataIndex: 'Bruto',
                width:'8%'
            },{
                text: lang('Netto'),
                dataIndex: 'Netto',
                width:'8%'
            },{
                text: lang('Supply ID'),
                dataIndex: 'SupplyID',
                width:'12%'
            },{
                text: lang('Delivery Date'),
                dataIndex: 'DeliveryDate',
                width:'11%'
            },{
                text: lang('Batch From'),
                dataIndex: 'BatchFrom',
                width:'15%'
            },{
                text: lang('Agent Batch ID'),
                dataIndex: 'AgentBatchID',
                width:'9%'
            },{
                text: lang('Status'),
                dataIndex: 'SupplyBatchStatus',
                width:'7%'
            }]
        })

        Var3=  Ext.define('MyGrid3', {
            extend: 'Ext.grid.Panel',
            xtype: 'grid3',
            store: storeDO,
            title: 'DO',
            width: '100%',

            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeDO,
                dock: 'bottom',
                displayInfo: true
                },{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'splitbutton',
                    text: 'Export',
                    menu: {
                        items: [{
                            text: 'Excel',
                            handler: function () {                            
                                url     = 'api/index.php/report_traceability/store_grid_do_transaction_Excelldo?'
                                    +'&MillID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboMill').getValue()
                                    +'&DOID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboDO').getValue()
                                    +'&AgentID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboAgent').getValue()
                                    +'&DateFrom='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom').getRawValue()                 
                                    +'&DateTo='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateTo').getRawValue()
                                  
                                window.open(url);
                            }
                        }]
                    }
                }, {
                    xtype: 'label',
                    id: 'premiumWarehouse',
                    text: ''
                }]
            },],
            columns: [{
                text: 'ID',
                dataIndex: 'SupplyTransID',
                hidden: true
            },{
                text: lang('Type'),
                dataIndex: 'SupplyType',
                width:'7%'
            },{
                text: lang('District'),
                dataIndex: 'District',
                width:'10%'
            },{
                text: lang('Sub District'),
                dataIndex: 'SubDistrict',
                width:'10%'
            },{
                text: lang('DO ID'),
                dataIndex: 'DoID',
                width:'10%'
            },{
                text: lang('Batch Number'),
                dataIndex: 'SupplyBatchNumber',
                width:'15%'
            },{
                text: lang('DO Name'),
                dataIndex: 'Name',
                width:'7%'
            },{
                text: lang('Date'),
                dataIndex: 'DateTransaction',
                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                width:'10%'
            },{
                text: lang('Gross'),
                dataIndex: 'Bruto',
                width:'8%'
            },{
                text: lang('Netto'),
                dataIndex: 'Netto',
                width:'8%'
            },{
                text: lang('Supply ID'),
                dataIndex: 'SupplyID',
                width:'7%'
            },{
                text: lang('Delivery Date'),
                dataIndex: 'DeliveryDate',
                width:'11%'
            },{
                text: lang('Batch From'),
                dataIndex: 'BatchFrom',
                width:'10%'
            },
            {
                text: lang('Mill Batch ID'),
                dataIndex: 'MillBatchID',
                width:'10%'
            },
            {
                text: lang('Status'),
                dataIndex: 'SupplyBatchStatus',
                width:'7%'
            },{
                text: lang('Sent To'),
                dataIndex: 'BatchTo',
                width:'7%'
            }]
        })

        Var4=  Ext.define('MyGrid4', {
            extend: 'Ext.grid.Panel',
            xtype: 'grid4',
            store: storeAgent,
            title: 'AGENT',
            width: '100%',

            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeAgent,
                dock: 'bottom',
                displayInfo: true
                },{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'splitbutton',
                    text: 'Export',
                    menu: {
                        items: [{
                            text: 'Excel',
                            handler: function () {                            
                                url     = 'api/index.php/report_traceability/store_grid_do_transaction_Excellagent?'
                                    +'&MillID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboMill').getValue()
                                    +'&DOID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboDO').getValue()
                                    +'&AgentID='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchComboAgent').getValue()
                                    +'&DateFrom='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateFrom').getRawValue()                 
                                    +'&DateTo='+ Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid-SearchDateTo').getRawValue()
                                  
                                window.open(url);
                            }
                        }]
                    }
                }, {
                    xtype: 'label',
                    id: 'premiumWarehouse',
                    text: ''
                }]
            },],
            columns: [{
                text: 'ID',
                dataIndex: 'SupplyTransID',
                hidden: true
            },{
                text: lang('Type'),
                dataIndex: 'SupplyType',
                width:'7%'
            },{
                text: lang('District'),
                dataIndex: 'District',
                width:'10%'
            },{
                text: lang('Sub District'),
                dataIndex: 'SubDistrict',
                width:'10%'
            },{
                text: lang('Agent ID'),
                dataIndex: 'AgentID',
                width:'10%'
            },{
                text: lang('Batch Number'),
                dataIndex: 'SupplyBatchNumber',
                width:'15%'
            },{
                text: lang('Agent Name'),
                dataIndex: 'Name',
                width:'7%'
            },{
                text: lang('Date'),
                dataIndex: 'DateTransaction',
                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                width:'10%'
            },{
                text: lang('Gross'),
                dataIndex: 'Bruto',
                width:'8%'
            },{
                text: lang('Netto'),
                dataIndex: 'Netto',
                width:'8%'
            },{
                text: lang('Supply ID'),
                dataIndex: 'SupplyID',
                width:'7%'
            },{
                text: lang('Delivery Date'),
                dataIndex: 'DeliveryDate',
                width:'11%'
            },{
                text: lang('Batch From'),
                dataIndex: 'BatchFrom',
                width:'10%'
            },{
                text: lang('DO Batch ID'),
                dataIndex: 'DoBatchID',
                width:'10%'
            },{
                text: lang('Status'),
                dataIndex: 'SupplyBatchStatus',
                width:'7%'
            },{
                text: lang('Sent To'),
                dataIndex: 'BatchFrom',
                width:'7%'
            }]
        })

       
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            
            items:[{
                id:'Koltiva.view.Company.Form-title',
                html:''
            },{
                id: 'Koltiva.view.Company.Form-labelInfoInsert',
                html:'',
            }]
        },{
            html:'<br />'
        },{
            layout: 'auto',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                items:[Var1]
            }, {
                columnWidth: 1,
                layout: 'form',
                items:[Var2]
            }, {
                columnWidth: 1,
                layout: 'form',
                items:[Var3]
            },{
                columnWidth: 1,
                layout: 'form',
                items:[Var4]
            }]
        }];

        this.callParent(arguments);
    }
});