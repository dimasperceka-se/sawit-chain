/*
* @Author: nikolius
* @Date:   2017-07-24 10:15:22
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-24 11:31:37
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.TraderSurvey.TraderSurveyPanelSummary' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.TraderSurvey.TraderSurveyPanelSummary',
    title: lang('Trader Survey Summary'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderSurvey = Ext.create('Koltiva.store.TraderSurvey.GridTraderSurveySummary');

        //context menu
        var contextMenuGridTraderSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.TraderSurvey.TraderSurveyPanelSummary-gridTraderSurvey').getSelectionModel().getSelection()[0];

                    //window form
                    var winFormTraderSurvey = Ext.create('Koltiva.view.TraderSurvey.WinFormTraderSurvey');

                    winFormTraderSurvey.setViewVar({
                        opsiDisplay:'view',
                        callerStore: storeGridTraderSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        BusinessNr: sm.get('BusinessNr'),
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection')
                    });
                    if (!winFormTraderSurvey.isVisible()) {
                        winFormTraderSurvey.center();
                        winFormTraderSurvey.show();
                    } else {
                        winFormTraderSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.TraderSurvey.TraderSurveyPanelSummary-gridTraderSurvey').getSelectionModel().getSelection()[0];

                    //window form
                    var winFormTraderSurvey = Ext.create('Koltiva.view.TraderSurvey.WinFormTraderSurvey');

                    winFormTraderSurvey.setViewVar({
                        opsiDisplay:'update',
                        callerStore: storeGridTraderSurvey,
                        MemberID: thisObj.viewVar.MemberID,
                        BusinessNr: sm.get('BusinessNr'),
                        SurveyNr: sm.get('SurveyNr'),
                        DateCollection: sm.get('DateCollection')
                    });
                    if (!winFormTraderSurvey.isVisible()) {
                        winFormTraderSurvey.center();
                        winFormTraderSurvey.show();
                    } else {
                        winFormTraderSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.TraderSurvey.TraderSurveyPanelSummary-gridTraderSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/trader_survey/survey',
                                method: 'DELETE',
                                params: {
                                    MemberID: thisObj.viewVar.MemberID,
                                    BusinessNr: sm.get('BusinessNr'),
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
                                    storeGridTraderSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    storeGridTraderSurvey.load();
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
                text: lang('List of Trader Survey')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    //window form
                    var winFormTraderSurvey = Ext.create('Koltiva.view.TraderSurvey.WinFormTraderSurvey');

                    winFormTraderSurvey.setViewVar({
                        opsiDisplay:'insert',
                        callerStore: storeGridTraderSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        BusinessNr:null,
                        SurveyNr:null,
                        DateCollection:null
                    });
                    if (!winFormTraderSurvey.isVisible()) {
                        winFormTraderSurvey.center();
                        winFormTraderSurvey.show();
                    } else {
                        winFormTraderSurvey.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.TraderSurvey.TraderSurveyPanelSummary-gridTraderSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridTraderSurvey,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '10%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridTraderSurvey.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Business Nr'),
                dataIndex: 'BusinessNr',
                width: '15%'
            },{
                text: lang('SurveyNr'),
                dataIndex: 'SurveyNr',
                hidden:true
            },{
                text: lang('Survey'),
                dataIndex: 'Survey',
                width: '30%'
            },{
                text: lang('DateCollection'),
                dataIndex: 'DateCollection',
                width: '20%'
            },{
                text: lang('Created By'),
                dataIndex: 'CreatedBy',
                width: '20%'
            }]
        }];

        this.callParent(arguments);
    }
});