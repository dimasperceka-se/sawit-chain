/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Apr 29 2019
 *  File : PlotSurPanelSummaryGar.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

Ext.define('Koltiva.view.PlotSurvey.PlotSurPanelSummaryGar' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotSurvey.PlotSurPanelSummaryGar',
    title: lang('Plantation Surveys'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible: true,
    collapsed: true,
    margin:'0 0 20 8',
    listeners: {
        afterRender: function () {
            var thisObj = this;
        },
        expand: function() {
            var thisObj = this;
            thisObj.storeGridPlotSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridPlotSurvey.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridPlotSurvey = Ext.create('Koltiva.store.PlotSurvey.GridPlotSurveySummary');

        //context menu
        var contextMenuGridPlotSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.PlotSurvey.PlotSurPanelSummary-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotSurveyGar');

                    winFormPlotSurvey.setViewVar({
                        opsiDisplay:'view',
                        callerStore: thisObj.storeGridPlotSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        PlotNr: sm.get('PlotNr'),
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection')
                    });
                    if (!winFormPlotSurvey.isVisible()) {
                        winFormPlotSurvey.center();
                        winFormPlotSurvey.show();
                    } else {
                        winFormPlotSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.PlotSurvey.PlotSurPanelSummary-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotSurveyGar');

                    winFormPlotSurvey.setViewVar({
                        opsiDisplay:'update',
                        callerStore: thisObj.storeGridPlotSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        PlotNr: sm.get('PlotNr'),
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection')
                    });
                    if (!winFormPlotSurvey.isVisible()) {
                        winFormPlotSurvey.center();
                        winFormPlotSurvey.show();
                    } else {
                        winFormPlotSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.PlotSurvey.PlotSurPanelSummary-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/plot_survey/survey',
                                method: 'DELETE',
                                params: {
                                    MemberID: thisObj.viewVar.MemberID,
                                    PlotNr: sm.get('PlotNr'),
                                    SurveyNr: sm.get('SurveyNr'),
                                    DateCollection: sm.get('DateCollection')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.storeGridPlotSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    thisObj.storeGridPlotSurvey.load();

                                    //refresh store Plot Status (spesial)
                                    var grid_plot_status = Ext.data.StoreManager.lookup('store.Grower.GridPlotStatus');
                                    grid_plot_status.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    grid_plot_status.load();
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
            }]
        });

        thisObj.dockedItems = [{
            xtype: 'toolbar',
            baseCls: 'bgToolbarTitlePanel',
            dock: 'top',
            items:[{
                xtype: 'tbtext',
                style:'font-weight:bold;text-decoration:underline;',
                text: lang('List of Plantation Surveys')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    //window form plot survey
                    var WinFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormPlotSurveyGar');

                    WinFormPlotSurvey.setViewVar({
                        opsiDisplay:'insert',
                        callerStore: thisObj.storeGridPlotSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        PlotNr:null,
                        SurveyNr:null,
                        DateCollection:null
                    });
                    if (!WinFormPlotSurvey.isVisible()) {
                        WinFormPlotSurvey.center();
                        WinFormPlotSurvey.show();
                    } else {
                        WinFormPlotSurvey.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Grower.PlotSurvey.PlotSurPanelSummary-gridPlotSurvey',
            loadMask: true,
            minHeight:'200',
            selType: 'rowmodel',
            store: thisObj.storeGridPlotSurvey,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.5,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridPlotSurvey.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Garden Nr'),
                dataIndex: 'PlotNr',
                flex: 1,
            },{
                text: lang('SurveyNr'),
                dataIndex: 'SurveyNr',
                hidden:true
            },{
                text: lang('Survey'),
                dataIndex: 'Survey',
                flex: 1,
            },{
                text: lang('DateCollection'),
                dataIndex: 'DateCollection',
                flex: 1,
            },{
                text: lang('Enumerator'),
                dataIndex: 'Enumerator',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});