/*
* @Author: nikolius
* @Date:   2017-06-01 13:13:42
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:30:47
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.MainBuyerSurvey.MainBuyerSurPanelSummary' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.MainBuyerSurvey.MainBuyerSurPanelSummary',
    title: lang('Main Buyer Survey Summary'),
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
            thisObj.storeGridMainBuyerSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridMainBuyerSurvey.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridMainBuyerSurvey = Ext.create('Koltiva.store.MainBuyerSurvey.GridMainBuyerSurveySummary');

        //context menu
        var contextMenuGridMainBuyerSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.MainBuyerSurvey.MainBuyerSurPanelSummary-gridMainBuyerSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormMainBuyerSurvey = Ext.create('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey');

                    winFormMainBuyerSurvey.setViewVar({
                        opsiDisplay:'view',
                        callerStore: thisObj.storeGridMainBuyerSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection'),
                        PlotNr: sm.get('PlotNr')
                    });
                    if (!winFormMainBuyerSurvey.isVisible()) {
                        winFormMainBuyerSurvey.center();
                        winFormMainBuyerSurvey.show();
                    } else {
                        winFormMainBuyerSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.MainBuyerSurvey.MainBuyerSurPanelSummary-gridMainBuyerSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormMainBuyerSurvey = Ext.create('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey');

                    winFormMainBuyerSurvey.setViewVar({
                        opsiDisplay:'update',
                        callerStore: thisObj.storeGridMainBuyerSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection'),
                        PlotNr: sm.get('PlotNr')
                    });
                    if (!winFormMainBuyerSurvey.isVisible()) {
                        winFormMainBuyerSurvey.center();
                        winFormMainBuyerSurvey.show();
                    } else {
                        winFormMainBuyerSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.MainBuyerSurvey.MainBuyerSurPanelSummary-gridMainBuyerSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/main_buyer_survey/survey',
                                method: 'DELETE',
                                params: {
                                    MemberID: thisObj.viewVar.MemberID,
                                    SurveyNr: sm.get('SurveyNr'),
                                    DateCollection: sm.get('DateCollection'),
                                    PlotNr: sm.get('PlotNr')
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
                                    thisObj.storeGridMainBuyerSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    thisObj.storeGridMainBuyerSurvey.load();
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
                text: lang('List of Main Buyer Survey')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    //window form plot survey
                    var winFormMainBuyerSurvey = Ext.create('Koltiva.view.MainBuyerSurvey.WinFormMainBuyerSurvey');

                    winFormMainBuyerSurvey.setViewVar({
                        opsiDisplay:'insert',
                        callerStore: thisObj.storeGridMainBuyerSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:null,
                        DateCollection:null,
                        PlotNr:null
                    });
                    if (!winFormMainBuyerSurvey.isVisible()) {
                        winFormMainBuyerSurvey.center();
                        winFormMainBuyerSurvey.show();
                    } else {
                        winFormMainBuyerSurvey.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Grower.MainBuyerSurvey.MainBuyerSurPanelSummary-gridMainBuyerSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridMainBuyerSurvey,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '7%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridMainBuyerSurvey.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('SurveyNr'),
                dataIndex: 'SurveyNr',
                hidden:true
            },{
                text: lang('Garden Nr'),
                dataIndex: 'PlotNr',
                width: '12%'
            },{
                text: lang('Survey'),
                dataIndex: 'Survey',
                width: '30%'
            },{
                text: lang('DateCollection'),
                dataIndex: 'DateCollection',
                width: '25%'
            },{
                text: lang('Enumerator'),
                dataIndex: 'Enumerator',
                width: '22%'
            }]
        }];

        this.callParent(arguments);
    }
});