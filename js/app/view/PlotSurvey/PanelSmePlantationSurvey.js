/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 15 2019
 *  File : PanelSmePlantationSurvey.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
*/

Ext.define('Koltiva.view.PlotSurvey.PanelSmePlantationSurvey' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotSurvey.PanelSmePlantationSurvey',
    style:'margin-left:15px;margin-top:15px;',
    title:lang('Plantation Surveys'),
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridPlotSurvey = Ext.create('Koltiva.store.PlotSurvey.GridSmePlotSurveySummary', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        //context menu
        var contextMenuGridPlotSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.PanelSmePlantationSurvey-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormSmePlotSurvey', {
                        viewVar: {
                            opsiDisplay:'view',
                            callerStore: storeGridPlotSurvey,
                            MemberID: thisObj.viewVar.MemberID,
                            PlotNr: sm.get('PlotNr'),
                            SurveyNr: sm.get('SurveyNr'),
                            DateCollection: sm.get('DateCollection')
                        }
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
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.PanelSmePlantationSurvey-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormSmePlotSurvey', {
                        viewVar: {
                            opsiDisplay:'update',
                            callerStore: storeGridPlotSurvey,
                            MemberID: thisObj.viewVar.MemberID,
                            PlotNr: sm.get('PlotNr'),
                            SurveyNr: sm.get('SurveyNr'),
                            DateCollection: sm.get('DateCollection')
                        }
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
                    var sm = Ext.getCmp('Koltiva.view.PlotSurvey.PanelSmePlantationSurvey-gridPlotSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/plot_survey/survey_sme',
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
                                    storeGridPlotSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    storeGridPlotSurvey.load();

                                    //refresh store Plot Status (spesial)
                                    if(Ext.data.StoreManager.lookup('store.PlotSurvey.PanelPlantationStatusMainGrid') != undefined) {
                                        Ext.data.StoreManager.lookup('store.PlotSurvey.PanelPlantationStatusMainGrid').load();
                                    }
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
                    var WinFormPlotSurvey = Ext.create('Koltiva.view.PlotSurvey.WinFormSmePlotSurvey', {
                        viewVar: {
                            opsiDisplay:'insert',
                            callerStore: storeGridPlotSurvey,
                            MemberID:thisObj.viewVar.MemberID,
                            PlotNr:null,
                            SurveyNr:null,
                            DateCollection:null
                        }
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
            id: 'Koltiva.view.PlotSurvey.PanelSmePlantationSurvey-gridPlotSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridPlotSurvey,
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