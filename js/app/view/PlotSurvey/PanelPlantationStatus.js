/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue May 07 2019
 *  File : PanelPlantationStatus.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
    - CallFrom
*/

Ext.define('Koltiva.view.PlotSurvey.PanelPlantationStatus' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotSurvey.PanelPlantationStatus',
    style:'margin-left:15px;margin-top:15px;',
    title:lang('Plantation Status'),
    frame: true,
    collapsible: true,
    collapsed: true,
    margin:'0 0 20 8',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            if (thisObj.viewVar.CallFrom != 'Mill') {
                Ext.getCmp('Koltiva.view.PlotSurvey.PanelGardenStatus.btnAdd').setVisible(false);
            } else {
                Ext.getCmp('Koltiva.view.PlotSurvey.PanelGardenStatus.btnAdd').setVisible(true);
            }
            
        },
        expand: function() {
            var thisObj = this;
            thisObj.MainGrid.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.PlotSurvey.PanelPlantationStatusMainGrid',{
        	storeVar: {
                MemberID: thisObj.viewVar.MemberID,
                CallFrom: thisObj.viewVar.CallFrom
            }
        });

        var ContextMenuMain = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.PanelGardenStatus-MainGrid').getSelectionModel().getSelection()[0];
                    var WinFormPlotStatus = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotStatus', {
                    	viewVar: {
			                MemberID: thisObj.viewVar.MemberID,
                        	PlotNr: sm.get('PlotNr'),
                        	OpsiDisplay: 'view',
                            CallerStore: thisObj.MainGrid,
                            CallFrom: thisObj.viewVar.CallFrom
			            }
                    });
                    if (!WinFormPlotStatus.isVisible()) {
                        WinFormPlotStatus.center();
                        WinFormPlotStatus.show();
                    } else {
                        WinFormPlotStatus.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.PanelGardenStatus-MainGrid').getSelectionModel().getSelection()[0];
                    var WinFormPlotStatus = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotStatus', {
                    	viewVar: {
			                MemberID: thisObj.viewVar.MemberID,
                        	PlotNr: sm.get('PlotNr'),
                        	OpsiDisplay: 'update',
                            CallerStore: thisObj.MainGrid,
                            CallFrom: thisObj.viewVar.CallFrom
			            }
                    });
                    if (!WinFormPlotStatus.isVisible()) {
                        WinFormPlotStatus.center();
                        WinFormPlotStatus.show();
                    } else {
                        WinFormPlotStatus.close();
                    }
                }
            }]
        });

        thisObj.items = [{
        	xtype:'grid',
            id: 'Koltiva.view.PlotSurvey.PanelGardenStatus-MainGrid',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.MainGrid,
            minHeight:125,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            }, 
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id: 'Koltiva.view.PlotSurvey.PanelGardenStatus.btnAdd',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    handler: function() {
                        var WinFormPlotStatus = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotStatus', {
                            viewVar: {
                                MemberID: thisObj.viewVar.MemberID,
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.MainGrid,
                                CallFrom: thisObj.viewVar.CallFrom
                            }
                        });
                        if (!WinFormPlotStatus.isVisible()) {
                            WinFormPlotStatus.center();
                            WinFormPlotStatus.show();
                        } else {
                            WinFormPlotStatus.close();
                        }
                    }
                },{
                    xtype: 'splitbutton',
                    text: lang('Export'),
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    cls:'Sfr_BtnGridPaleBlue',
                    menu: {
                        items: [
                            {
                                text: lang('Export Data'),
                                hidden: m_act_export,
                                handler: function() {
                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-download', //custom class in msg-box.html
                                        animateTarget: 'mb7'
                                    });

                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}

                                    Ext.Ajax.request({
                                        url: m_api+'/plot_survey/export_plot_status?MemberID='+thisObj.viewVar.MemberID+'&CallFrom='+thisObj.viewVar.CallFrom,
                                    
                                        method: 'GET',
                                        waitMsg: lang('Please Wait'),
                                        timeout: 360000,
                                        success: function(data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);
                                            window.location = jsonResp.filenya;
                                        },
                                        failure: function() {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });

                                }
                            }
                        ]
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.5,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        ContextMenuMain.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'MemberID',
                hidden:true
            },{
                text: lang('Plantation Nr'),
                dataIndex: 'PlotNr',
                flex:1
            },{
                text: lang('Size (ha)'),
                dataIndex: 'GardenAreaHa',
                flex:1
            },{
                text: lang('Annual Production')+' (ton)',
                dataIndex: 'AnnualProduction',
                flex:1.5,
                renderer: function (value) {
                    var RetVal;

                    if(value != null && value != ''){
                        RetVal = value;
                    }else{
                        RetVal = '-';
                    }

                    return RetVal;
                }
            },{
                text: lang('Plantation Status'),
                dataIndex: 'ActiveStatus',
                flex:2,
                renderer: function (value) {
                    var RetVal;

                    if(value != null && value != ''){
                        switch(value){
                            case '1':
                                RetVal = '<span class="Sfr_GridColGreenRounded">'+lang('Active')+'</span>';
                            break;
                            case '2':
                                RetVal = '<span class="Sfr_GridColRedRounded">'+lang('Inactive')+'</span>';
                            break;
                            default:
                                RetVal = '-';
                            break;
                        }
                    }else{
                        RetVal = '-';
                    }

                    return RetVal;
                }
            }]
        }];

        this.callParent(arguments);
    }
});