Ext.define('Koltiva.view.FarmerTraining.ParticipantPanel', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerTraining.ParticipantPanel',
    title: lang('Training Participants'),
    width: '100%',
    frame: true,
    cls: 'Sfr_PanelLayoutForm',
    collapsible: true,
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        var store_family = Ext.create('Koltiva.store.FarmerTraining.CmbFamily');
        var store_participant = Ext.create('Koltiva.store.FarmerTraining.ParticipantGrid');
        var store_ya_tidak = Ext.create('Koltiva.store.FarmerTraining.CmbYesNo');

        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        store_participant.load({
            params: {
                training: thisObj.viewVar.trainMasterID
            }
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'gtraining',
            store: store_participant,
            minHeight: '300px',
            loadMask: true,
            selType: 'rowmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function () {
                        var WinAddParticipant = Ext.create('Koltiva.view.FarmerTraining.WinAddParticipant', {
                            viewVar: {
                                FarmerTrainingID: thisObj.viewVar.FarmerTrainingID,
                                Grid: store_participant
                            }
                        });
                        if (!WinAddParticipant.isVisible()) {
                            WinAddParticipant.center();
                            // WinAddParticipant.setPosition(100, 0);
                            WinAddParticipant.show();
                        } else {
                            WinAddParticipant.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    hidden: !m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                        RowEditing.startEdit(sm[0].index, 0);
                        store_family.load({
                            params: {
                                farmerid: sm[0].get('farmer_id')
                            }
                        });
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: 'Sfr_BtnGridRed',
                    overCls: 'Sfr_BtnGridRed-Hover',
                    hidden: !m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function () {
                        var sma = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud + '_participant',
                                    method: 'DELETE',
                                    params: {
                                        id: sma.get('participant_id')
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_participant.load({
                                                    params: {
                                                        training: Ext.getCmp('id').getValue()
                                                    }
                                                });
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                },
                {
                    xtype: 'splitbutton',
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    text: lang('Daftar Hadir'),
                    menu: {
                        items: [{
                            text: lang('Form Kosong'),
                            handler: function () {
                                preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue());
                            }
                        }, {
                            text: lang('Form Hasil'),
                            handler: function () {
                                var winBeforeCetakAttendanceList = Ext.create('Koltiva.view.FarmerTraining.WinBeforePrint', {
                                    viewVar: {
                                        farmerTrainingID: Ext.getCmp('idt').getValue()
                                    }
                                });
                                if (!winBeforeCetakAttendanceList.isVisible()) {
                                    winBeforeCetakAttendanceList.center();
                                    winBeforeCetakAttendanceList.show();
                                } else {
                                    winBeforeCetakAttendanceList.close();
                                }
                                Ext.getCmp('Koltiva.view.FarmerTraining.WinBeforePrint').setTitle(lang('Print Attendance List'));
                            }
                        }]
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/folder_table.png',
                    text: lang('Attachment Training Files'),
                    scope: this,
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    handler: function () {
                        //console.log(Ext.getCmp('idt').getValue());
                        var GridTrainAttachmentFiles = Ext.create('Koltiva.view.Train.GridTrainAttachmentFiles', {
                            viewVar: {
                                TrainID: Ext.getCmp('idt').getValue(),
                                TrainType: 'farmer'
                            }
                        });
                        if (!GridTrainAttachmentFiles.isVisible()) {
                            GridTrainAttachmentFiles.center();
                            GridTrainAttachmentFiles.show();
                        } else {
                            GridTrainAttachmentFiles.close();
                        }
                    }
                },
                {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    text: lang('GAP'),
                    scope: this,
                    cls: 'hide-icon',
                    handler: function () {
                        jenis = 'P1';
                        displayBeforeCetak();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    text: lang('GFP'),
                    cls: 'hide-icon',
                    scope: this,
                    handler: function () {
                        jenis = 'F1';
                        displayBeforeCetak();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    id: 't_n1',
                    text: lang('GNP'),
                    cls: 'hide-icon',
                    scope: this,
                    handler: function () {
                        jenis = 'N1';
                        displayBeforeCetak();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    text: lang('PPI'),
                    cls: 'hide-icon',
                    scope: this,
                    handler: function () {
                        jenis = 'PPI';
                        displayBeforeCetak();
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'participant_id',
                width: '5%',
                hidden: true
            }, {
                text: lang('ID'),
                dataIndex: 'farmer_id',
                flex:1
            }, {
                text: lang('Registered Farmer'),
                flex:1,
                dataIndex: 'farmer',
            }, {
                text: lang('Participant'),
                flex:1,
                dataIndex: 'participant',
                editor: {
                    xtype: 'combo',
                    store: store_ya_tidak,
                    id: 'participant',
                    name: 'participant',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }
            }, {
                text: lang('W. Awal'),
                flex:1,
                dataIndex: 'wstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('W. Akhir'),
                flex:1,
                dataIndex: 'wend',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Awal'),
                flex:1,
                dataIndex: 'bstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Akhir'),
                flex:1,
                dataIndex: 'bend',
                editor: {
                    xtype: 'textfield'
                }
            }],
            plugins: [RowEditing],
            listeners: {
                'itemdblclick': function (dv, record, item, index, e) {
                    if (!m_act_update) {
                        RowEditing.cancelEdit();
                        return false;
                    } else {
                        var sm = record;
                        store_family.load({
                            params: {
                                farmerid: sm.get('farmer_id')
                            }
                        });
                    }
                },
                'canceledit': function (editor, e, eOpts) {
                    if (m_act_save == 'hide-icon') {
                        return false
                    }
                    store_participant.load({
                        params: {
                            training: Ext.getCmp('id').getValue()
                        }
                    });
                },
                'edit': function (editor, e) {
                    if (m_act_save == 'hide-icon') {
                        return false
                    }
                    if (e.record.data.participant_id.trim() == '') {
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_crud + '_participant',
                            method: 'POST',
                            params: {
                                training: Ext.getCmp('id').getValue(),
                                farmer: e.record.data.farmer,
                                participant: e.record.data.participant,
                                if_no: e.record.data.if_no,
                                wstart: e.record.data.wstart,
                                wend: e.record.data.wend,
                                bstart: e.record.data.bstart,
                                bend: e.record.data.bend,
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_participant.load({
                                            params: {
                                                training: Ext.getCmp('id').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            },
                            failure: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                            }
                        });
                    } else {
                        Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_crud + '_participant',
                                    method: 'PUT',
                                    params: {
                                        id: e.record.data.participant_id,
                                        staf: e.record.data.staf,
                                        farmer: e.record.data.farmer,
                                        farmer_id: e.record.data.farmer_id,
                                        participant: e.record.data.participant,
                                        PetaniKakao: e.record.data.PetaniKakao,
                                        if_no: e.record.data.if_no,
                                        FamilyID: e.record.data.FamilyID,
                                        wstart: e.record.data.wstart,
                                        wend: e.record.data.wend,
                                        bstart: e.record.data.bstart,
                                        bend: e.record.data.bend,
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_participant.load({
                                                    params: {
                                                        training: Ext.getCmp('id').getValue()
                                                    }
                                                });
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                }
            }
        }];

        this.callParent(arguments);
    }
});