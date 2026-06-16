/**
 * Grid IMS Training Event Mapping (Training Days Mapping)
 */

Ext.define('Koltiva.view.IMS.WinGridImsTrainingEventMapping', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinGridImsTrainingEventMapping',
    title: lang('Training Days Maping'),
    modal: true,
    width: '70%',
    height: 550,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGridStore = Ext.create('Koltiva.store.IMS.GridImsTrainingEventMapping', {
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.contextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: false,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.GridImsTrainingEventMapping-Grid').getSelectionModel().getSelection()[0];
                        if (Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping') == undefined) {
                            Ext.create('Koltiva.view.IMS.WinFormImsTrainingEventMapping', {
                                viewVar: {
                                    opsiDisplay: 'view',
                                    IMSID: thisObj.viewVar.IMSID,
                                    caller: Ext.getCmp('Koltiva.view.IMS.WinGridImsTrainingEventMapping')
                                }
                            });
                        }
                        if (sm.get('ActivityType') === 'Full')
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Full').setValue(true)
                        else
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Remidial').setValue(true);
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ParticipantType').setValue(sm.get('ParticipantType'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikGAP').setValue(sm.get('TopikGAP'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikCOC').setValue(sm.get('TopikCOC'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping').show();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.GridImsTrainingEventMapping-Grid').getSelectionModel().getSelection()[0];
                        if (Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping') == undefined) {
                            Ext.create('Koltiva.view.IMS.WinFormImsTrainingEventMapping', {
                                viewVar: {
                                    opsiDisplay: 'update',
                                    IMSID: thisObj.viewVar.IMSID,
                                    caller: Ext.getCmp('Koltiva.view.IMS.WinGridImsTrainingEventMapping')
                                }
                            });
                        }
                        if (sm.get('ActivityType') === 'Full')
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Full').setValue(true)
                        else
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ActivityType-Remidial').setValue(true);
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-ParticipantType').setValue(sm.get('ParticipantType'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikGAP').setValue(sm.get('TopikGAP'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping-TopikCOC').setValue(sm.get('TopikCOC'));
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping').show();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.GridImsTrainingEventMapping-Grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda yakin akan reset data ini?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_api + '/ims_training/training_event_mapping',
                                    method: 'DELETE',
                                    params: {
                                        IMSID: sm.get('IMSID'),
                                        TrainingType: sm.get('TrainingType'),
                                        ActivityType: sm.get('ActivityType'),
                                        ParticipantType: sm.get('ParticipantType')
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        if (obj.success) {
                                            Ext.MessageBox.alert('Info', obj.message);
                                        } else {
                                            Ext.MessageBox.alert('Info', obj.message);
                                        }
                                        thisObj.MainGridStore.load({
                                            params: {
                                                IMSID: thisObj.viewVar.IMSID,
                                            }
                                        });
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }]
        });

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.IMS.GridImsTrainingEventMapping-Grid',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.MainGridStore,
                overflowY: 'scroll',
                maxHeight: 500,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [
                            {
                                xtype: 'button',
                                name: 'Koltiva.view.IMS.WinGridImsTrainingEventMapping.AddButton',
                                id: 'Koltiva.view.IMS.WinGridImsTrainingEventMapping.AddButton',
                                text: lang('Add'),
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    var WinFormImsTrainingEventMapping;
                                    if (Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping') == undefined) {
                                        WinFormImsTrainingEventMapping = Ext.create('Koltiva.view.IMS.WinFormImsTrainingEventMapping', {
                                            viewVar: {
                                                opsiDisplay: 'add',
                                                IMSID: thisObj.viewVar.IMSID,
                                                caller: Ext.getCmp('Koltiva.view.IMS.WinGridImsTrainingEventMapping')
                                            }
                                        });
                                        WinFormImsTrainingEventMapping.show();
                                    } else {
                                        WinFormImsTrainingEventMapping.show();
                                    }
                                }
                            }
                        ]
                    }],
                columns: [
                    {
                        text: 'IMSID',
                        dataIndex: 'IMSID',
                        hidden: true
                    },
                    {
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '6%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    thisObj.contextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    },
                    {
                        text: lang('Training Type'),
                        flex: 2,
                        dataIndex: 'TrainingType'
                    },
                    {
                        text: lang('Activity Type'),
                        flex: 2,
                        dataIndex: 'ActivityType'
                    },
                    {
                        text: lang('Participant Type'),
                        flex: 1,
                        dataIndex: 'ParticipantType'
                    },
                    {
                        text: lang('Topic GAP'),
                        flex: 1,
                        dataIndex: 'TopikGAP'
                    },
                    {
                        text: lang('Topic COC'),
                        flex: 1,
                        dataIndex: 'TopikCOC'
                    }
                ]
            }];

        this.callParent(arguments);
    },
    reloadGridAndCloseWin: function () {
        var thisObj = this;
        thisObj.MainGridStore.load({
            params: {
                IMSID: thisObj.viewVar.IMSID,
            }
        });
        Ext.getCmp('Koltiva.view.IMS.WinFormImsTrainingEventMapping').close();
    }
});