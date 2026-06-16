Ext.define('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin-top:10px;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //ngeload main grid pertama kali
            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-CmbFilterYear').getStore().load({
                callback: function(records, operation, success){
                    var combo = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-CmbFilterYear');
                    combo.select(combo.getStore().getAt(0));

                    thisObj.StoreGridMain.storeVar.FilterYear = combo.getValue();
                    thisObj.StoreGridMain.load();
                }
            });
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.Dboard.MainGridKpiTargetSawitTerampil',{
            storeVar: {
                FilterYear: null
            }
        });

        thisObj.CmbFilterYear = Ext.create('Koltiva.store.Dboard.CmbFilterYearKpiSawitTarget');

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-Grid').getSelectionModel().getSelection()[0];

                    var WinFormInputKpiTargetGeneral = Ext.create('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil');
                    WinFormInputKpiTargetGeneral.setViewVar({
                        opsiDisplay : 'update',
                        TargetID    : sm.get('TargetID')
                        
                    });
                    if (!WinFormInputKpiTargetGeneral.isVisible()) {
                        WinFormInputKpiTargetGeneral.center();
                        WinFormInputKpiTargetGeneral.show();
                    } else {
                        WinFormInputKpiTargetGeneral.close();
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-Grid',
            style: 'border:1px solid #CCC;margin-top:2px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            minHeight:1000,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            features: [{
                ftype: 'summary'
            }],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-gridToolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function () {
                        var WinFormInputKpiTargetGeneral = Ext.create('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil');
                        WinFormInputKpiTargetGeneral.setViewVar({
                            opsiDisplay:'insert'
                        });
                        if (!WinFormInputKpiTargetGeneral.isVisible()) {
                            WinFormInputKpiTargetGeneral.center();
                            WinFormInputKpiTargetGeneral.show();
                        } else {
                            WinFormInputKpiTargetGeneral.close();
                        }
                    }
                },{
                    store: thisObj.CmbFilterYear,
                    editable: false,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    editable: false,
                    id: 'Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-CmbFilterYear',
                    name: 'Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-CmbFilterYear',
                    style: 'margin-left:5px;margin-top:5px;',
                    listeners: {
                        change: function (cb, nv, ov) {
                        }
                    }
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    text: lang('Search'),
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.FilterYear = Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil-CmbFilterYear').getValue();
                        thisObj.StoreGridMain.load();
                    }
                }]
            }],
            columns: [{
                text: '',
                xtype:'actioncolumn',
                width: '5%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                dataIndex: 'TargetID',
                hidden:true
            },{
                text: lang('Cluster Name'),
                dataIndex: 'ClusterName',
                width:'10%'
            },{
                text: lang('Program Name'),
                dataIndex: 'ProgramName',
                width:'10%'
            },{
                text: lang('Year'),
                dataIndex: 'Year',
                width:'5%'
            },{
                text: lang('Province'),
                dataIndex: 'Province',
                width:'10%',
                summaryRenderer: function(value, summaryData, dataIndex) {
                    return 'Total';
                }
            },{
                xtype: 'numbercolumn',
                text: lang('Ksatria Mill'),
                dataIndex: 'KsMill',
                width:'8%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('S. Terampil Mill'),
                dataIndex: 'StMill',
                width:'9%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmer Reg'),
                dataIndex: 'FarmerReg',
                width:'8%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farm Reg'),
                dataIndex: 'FarmReg',
                width:'8%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farm Ha'),
                dataIndex: 'Ha',
                width:'8%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Soc & Sel'),
                dataIndex: 'SocSel',
                width:'8%',
                format:'0,000',
                summaryType: 'sum'
            }, {
                xtype: 'numbercolumn',
                text: lang('Farmer Survey'),
                dataIndex: 'FarmerSurveyBP',
                width:'7%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farm Survey'),
                dataIndex: 'FarmSurvey',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Polygon'),
                dataIndex: 'Polygon',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Farmer Coach'),
                dataIndex: 'FarmerCoach',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('Coaching Session'),
                dataIndex: 'CoachingSess',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('SMS Broad'),
                dataIndex: 'Sms',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('ID Card'),
                dataIndex: 'IdCard',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmXtenstion'),
                dataIndex: 'FarmX',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmGate'),
                dataIndex: 'FarmG',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmRetail'),
                dataIndex: 'FarmR',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            },{
                xtype: 'numbercolumn',
                text: lang('FarmC'),
                dataIndex: 'FarmC',
                width:'5%',
                format:'0,000',
                summaryType: 'sum'
            }]
        }];

        this.callParent(arguments);
    }
});