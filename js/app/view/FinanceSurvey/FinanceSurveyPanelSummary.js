/*
* @Author: nikolius
* @Date:   2017-11-02 16:09:05
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-03 11:14:48
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.FinanceSurvey.FinanceSurveyPanelSummary' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FinanceSurvey.FinanceSurveyPanelSummary',
    title: lang('Finance Survey Summary'),
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
            thisObj.storeGridFinanceSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridFinanceSurvey.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridFinanceSurvey = Ext.create('Koltiva.store.FinanceSurvey.GridFinanceSurveySummary');

        var contextMenuGridFinanceSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FinanceSurvey.FinanceSurveyPanelSummary-gridFinanceSurvey').getSelectionModel().getSelection()[0];

                    var WinFormFinanceSurvey = Ext.create('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey');
                    WinFormFinanceSurvey.setViewVar({
                        opsiDisplay:'view',
                        callerStore: thisObj.storeGridFinanceSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:sm.get('SurveyNr'),
                        DateCollection:sm.get('DateCollection')
                    });
                    if (!WinFormFinanceSurvey.isVisible()) {
                        WinFormFinanceSurvey.center();
                        WinFormFinanceSurvey.show();
                    } else {
                        WinFormFinanceSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.FinanceSurvey.FinanceSurveyPanelSummary-gridFinanceSurvey').getSelectionModel().getSelection()[0];

                    var WinFormFinanceSurvey = Ext.create('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey');
                    WinFormFinanceSurvey.setViewVar({
                        opsiDisplay:'update',
                        callerStore: thisObj.storeGridFinanceSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:sm.get('SurveyNr'),
                        DateCollection:sm.get('DateCollection')
                    });
                    if (!WinFormFinanceSurvey.isVisible()) {
                        WinFormFinanceSurvey.center();
                        WinFormFinanceSurvey.show();
                    } else {
                        WinFormFinanceSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.FinanceSurvey.FinanceSurveyPanelSummary-gridFinanceSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/finance_survey/survey',
                                method: 'DELETE',
                                params: {
                                    MemberID: thisObj.viewVar.MemberID,
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
                                    thisObj.storeGridFinanceSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    thisObj.storeGridFinanceSurvey.load();
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
                text: lang('List of Finance Survey')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormFinanceSurvey = Ext.create('Koltiva.view.FinanceSurvey.WinFormFinanceSurvey');
                    WinFormFinanceSurvey.setViewVar({
                        opsiDisplay:'insert',
                        callerStore: thisObj.storeGridFinanceSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:null,
                        DateCollection:null
                    });
                    if (!WinFormFinanceSurvey.isVisible()) {
                        WinFormFinanceSurvey.center();
                        WinFormFinanceSurvey.show();
                    } else {
                        WinFormFinanceSurvey.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Grower.FinanceSurvey.FinanceSurveyPanelSummary-gridFinanceSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridFinanceSurvey,
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
                        contextMenuGridFinanceSurvey.showAt(e.getXY());
                    }
                }]
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
                flex: 2,
            },{
                text: lang('Enumerator'),
                dataIndex: 'Enumerator',
                flex: 2,
            }]
        }];

        this.callParent(arguments);
    }
});