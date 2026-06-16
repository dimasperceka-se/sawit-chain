/*
* @Author: nikolius
* @Date:   2017-06-01 16:41:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:31:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.HouseholdSurvey.HouseholdSurPanelSummary' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.HouseholdSurvey.HouseholdSurPanelSummary',
    title: lang('Household Surveys'),
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
            thisObj.storeGridHouseholdSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridHouseholdSurvey.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridHouseholdSurvey = Ext.create('Koltiva.store.HouseholdSurvey.GridHouseholdSurveySummary');

        //context menu
        var contextMenuGridHouseholdSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.HouseholdSurvey.HouseholdSurPanelSummary-gridHouseholdSurvey').getSelectionModel().getSelection()[0];

                    var winFormHouseholdSurvey = Ext.create('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey');
                    winFormHouseholdSurvey.setViewVar({
                        opsiDisplay:'view',
                        callerStore: thisObj.storeGridHouseholdSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:sm.get('SurveyNr'),
                        DateCollection:sm.get('DateCollection')
                    });
                    if (!winFormHouseholdSurvey.isVisible()) {
                        winFormHouseholdSurvey.center();
                        winFormHouseholdSurvey.show();
                    } else {
                        winFormHouseholdSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Grower.HouseholdSurvey.HouseholdSurPanelSummary-gridHouseholdSurvey').getSelectionModel().getSelection()[0];

                    var winFormHouseholdSurvey = Ext.create('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey');
                    winFormHouseholdSurvey.setViewVar({
                        opsiDisplay:'update',
                        callerStore: thisObj.storeGridHouseholdSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:sm.get('SurveyNr'),
                        DateCollection:sm.get('DateCollection')
                    });
                    if (!winFormHouseholdSurvey.isVisible()) {
                        winFormHouseholdSurvey.center();
                        winFormHouseholdSurvey.show();
                    } else {
                        winFormHouseholdSurvey.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Grower.HouseholdSurvey.HouseholdSurPanelSummary-gridHouseholdSurvey').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/household_survey/survey',
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
                                    thisObj.storeGridHouseholdSurvey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    thisObj.storeGridHouseholdSurvey.load();
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
                text: lang('List of Household Surveys')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var winFormHouseholdSurvey = Ext.create('Koltiva.view.HouseholdSurvey.WinFormHouseholdSurvey');
                    winFormHouseholdSurvey.setViewVar({
                        opsiDisplay:'insert',
                        callerStore: thisObj.storeGridHouseholdSurvey,
                        MemberID:thisObj.viewVar.MemberID,
                        SurveyNr:null,
                        DateCollection:null
                    });
                    if (!winFormHouseholdSurvey.isVisible()) {
                        winFormHouseholdSurvey.center();
                        winFormHouseholdSurvey.show();
                    } else {
                        winFormHouseholdSurvey.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Grower.HouseholdSurvey.HouseholdSurPanelSummary-gridHouseholdSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridHouseholdSurvey,
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
                        contextMenuGridHouseholdSurvey.showAt(e.getXY());
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