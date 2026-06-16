Ext.define('Koltiva.view.MasterTraining.ParticipantPanel', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.MasterTraining.ParticipantPanel',
    title: lang('Training Participants'),
    width: '100%',
    frame: true,
    cls: 'Sfr_PanelLayoutForm',
    collapsible: true,
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        console.log((thisObj.viewVar.opsiDisplay != 'view' && m_act_update));

        var store_participant = Ext.create('Koltiva.store.MasterTraining.ParticipantGrid');
        var store_staff = Ext.create('Koltiva.store.MasterTraining.CmbStaff');

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
            width: '100%',
            minHeight: '30%',
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
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: !(thisObj.viewVar.opsiDisplay != 'view' && m_act_add),
                    scope: this,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var r = Ext.create('Koltiva.model.MasterTraining.ParticipantGrid', {
                            id: '',
                            staf: '',
                            wstart: '',
                            wend: '',
                            bstart: '',
                            bend: ''
                        });
                        store_participant.insert(0, r);
                        RowEditing.startEdit(0, 0);
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    hidden: !(thisObj.viewVar.opsiDisplay != 'view' && m_act_update),
                    text: lang('Update'),
                    scope: this,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                        RowEditing.startEdit(sm[0].index, 0);
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls:'Sfr_BtnGridRed', 
                    overCls:'Sfr_BtnGridRed-Hover',
                    hidden: !(thisObj.viewVar.opsiDisplay != 'view' && m_act_delete),
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
                                                        training: thisObj.viewVar.trainMasterID
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
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    text: lang('Daftar Hadir'),
                    scope: this,
                    handler: function () {
                        preview_cetak_surat(m_cetak + thisObj.viewVar.trainMasterID);
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'participant_id',
                hidden: true
            }, {
                text: lang('ID'),
                dataIndex: 'id_staff',
                flex: 0.5,
            }, {
                text: lang('Staff'),
                flex: 2,
                dataIndex: 'staf',
                editor: {
                    xtype: 'combo',
                    displayField: 'label',
                    id: 'staf',
                    name: 'staf',
                    valueField: 'id',
                    queryMode: 'remote',
                    store: store_staff,
                    typeAhead: true,
                    listeners: {
                        change: function (cb, nv, ov) {
                            if (thisObj.isNumber(Ext.getCmp('staf').getValue())) {
                                Ext.Ajax.request({
                                    waitMsg: 'Check data...',
                                    url: m_check,
                                    method: 'GET',
                                    params: {
                                        trainingid: thisObj.viewVar.trainMasterID,
                                        staffid: Ext.getCmp('staf').getValue()
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        if (!obj.data) {
                                            Ext.MessageBox.alert('Warning', lang('Staff telah terdapat dalam list'));
                                            Ext.getCmp('staf').setValue('');
                                            return;
                                        }
                                    }
                                });
                            }
                        }
                    }
                }
            }, {
                text: lang('W. Awal'),
                flex: 1,
                dataIndex: 'wstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('W. Akhir'),
                flex: 1,
                dataIndex: 'wend',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Awal'),
                flex: 1,
                dataIndex: 'bstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Akhir'),
                flex: 1,
                dataIndex: 'bend',
                editor: {
                    xtype: 'textfield'
                }
            }],
            plugins: [RowEditing],
            listeners: {
                'itemdblclick': function () {
                    if (!m_act_update) {
                        RowEditing.cancelEdit();
                        return false;
                    }
                },
                'canceledit': function (editor, e, eOpts) {
                    store_participant.load({
                        params: {
                            training: thisObj.viewVar.trainMasterID
                        }
                    });
                },
                'edit': function (editor, e) {
                    if (e.record.data.participant_id.trim() == '') {
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_crud + '_participant',
                            method: 'POST',
                            params: {
                                training: thisObj.viewVar.trainMasterID,
                                staf: e.record.data.staf,
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
                                                training: thisObj.viewVar.trainMasterID
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
                                        stafid: e.record.data.id_staff,
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
                                                        training: thisObj.viewVar.trainMasterID
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