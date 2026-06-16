Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    //add/update
    var store_service_provider = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/training_master/service_provider',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_store_training,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var store_provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'district'],
        proxy: {
            type: 'ajax',
            url: m_district_data,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_fasilitator = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_fasilitator,
            extraParams: {
                workarea: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_staff,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //fields: ['participant_id','farmer','participant','if_no','wstart','wend','bstart','bend']
    Ext.define('Participant.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id', 'id_staff', 'staf', 'wstart', 'wend', 'bstart', 'bend']
    });
    var store_participant = Ext.create('Ext.data.Store', {
        model: 'Participant.Model',
        //pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_participant + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var mc_sub_topic = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/training_subtopic',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function (store, options) {
                store.proxy.extraParams.CpgTrainingsID = Ext.getCmp('training').getValue();
            }
        }
    });

    function hideSave() {
        Ext.getCmp('saveButton').hide();
        if (Ext.getCmp('id').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
        }
        if (Ext.getCmp('id').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
        }
    }

    function displayFormWindow() {
        if (!win.isVisible()) {
            store_participant.load();
            DataForm.getForm().reset();
            // Ext.Ajax.request({
            //     url: m_label_provinsi,
            //     method: 'GET',
            //     params: {id: m_param},
            //     success: function(fp, o) {
            //         var r = Ext.decode(fp.responseText);
            //         Ext.getCmp('Provinsi').setValue(r.id);
            //     }
            // });
            win.show();
        } else {
            win.hide(this, function () {});
            win.toFront();
        }
        Ext.getCmp('win').center()
    }

    function setFormValue(r) {
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('location').setValue(r.TotLocation);
        Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramPersonID);
        Ext.getCmp('fasilitator_mitra').setValue(r.FacPrivatePersonID);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('DistrictID').setValue(r.DistrictID);
        Ext.getCmp('ServiceProvID').setValue(r.ServiceProvID);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('DistrictID').setReadOnly(false);

        if (r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if (r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        if (r.TrainingPurpose == 'Core')
            Ext.getCmp('TrainingPurposeCore').setValue(true);
        if (r.TrainingPurpose == 'General')
            Ext.getCmp('TrainingPurposeGeneral').setValue(true);

        mc_sub_topic.load({
            callback: function (records, options, success) {
                if (r.subtopics != null) {
                    var setSubtopic = r.subtopics.split(',');
                    //console.log(setSubtopic);
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                } else {
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                }
            }
        });
    }

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        width: 1010,
        height: 660,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 180,
            anchor: '95%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                        xtype: 'textfield',
                        id: 'id',
                        name: 'id',
                        inputType: 'hidden'
                    }, {
                        xtype: 'combo',
                        store: store_training,
                        displayField: 'label',
                        valueField: 'id',
                        fieldLabel: lang('Topic'),
                        queryMode: 'local',
                        allowBlank: false,
                        id: 'training',
                        name: 'training',
                        listeners: {
                            change: function (cb, nv, ov) {
                                mc_sub_topic.load();
                            }
                        }
                    }, {
                        xtype: 'boxselect',
                        id: 'CpgTrainingsIDSubTopic',
                        name: 'CpgTrainingsIDSubTopic[]',
                        store: mc_sub_topic,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        fieldLabel: lang('Subtopics'),
                        stacked: true,
                        pinList: false,
                        triggerOnClick: false,
                        filterPickList: true
                    }, {
                        id: 'Provinsi',
                        name: 'Provinsi',
                        xtype: 'combo',
                        fieldLabel: lang('Provinsi'),
                        store: store_provinsi,
                        displayField: 'label',
                        valueField: 'label',
                        queryMode: 'local',
                        readOnly: false,
                        listeners: {
                            change: function (cb, nv, ov) {
                                ProvinceID = nv
                                store_District.load({
                                    params: {
                                        id: nv
                                    }
                                });
                            }
                        }
                    }, {
                        id: 'DistrictID',
                        name: 'DistrictID',
                        xtype: 'combo',
                        fieldLabel: lang('District'),
                        store: store_District,
                        displayField: 'district',
                        valueField: 'id',
                        queryMode: 'local'
                    }, {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Training Purpose'),
                        allowBlank: false,
                        msgTarget: 'side',
                        items: [{
                            name: 'TrainingPurpose',
                            id: 'TrainingPurposeCore',
                            boxLabel: lang('Core'),
                            inputValue: 'Core'
                        }, {
                            name: 'TrainingPurpose',
                            id: 'TrainingPurposeGeneral',
                            boxLabel: lang('General'),
                            inputValue: 'General'
                        }]
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Location'),
                        id: 'location',
                        name: 'location'
                    },
                    {
                        xtype: 'combo',
                        store: store_service_provider,
                        displayField: 'label',
                        valueField: 'id',
                        fieldLabel: lang('Service Provider'),
                        queryMode: 'local',
                        id: 'ServiceProvID',
                        name: 'ServiceProvID'
                    },
                ]
            }, {
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                        xtype: 'combo',
                        store: store_fasilitator,
                        displayField: 'label',
                        valueField: 'id',
                        fieldLabel: lang('Fasilitator 1'),
                        queryMode: 'local',
                        allowBlank: false,
                        id: 'fasilitator_scpp',
                        name: 'fasilitator_scpp'
                    },
                    {
                        xtype: 'combo',
                        store: store_fasilitator,
                        displayField: 'label',
                        valueField: 'id',
                        fieldLabel: lang('Fasilitator 2'),
                        queryMode: 'local',
                        id: 'fasilitator_mitra',
                        name: 'fasilitator_mitra'
                    },
                    {
                        xtype: 'datefield',
                        format: 'Y-m-d',
                        fieldLabel: lang('Training Start'),
                        id: 'TrainingStart',
                        name: 'TrainingStart'
                    }, {
                        xtype: 'datefield',
                        fieldLabel: lang('Training End'),
                        format: 'Y-m-d',
                        id: 'TrainingEnd',
                        name: 'TrainingEnd'
                    }, {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Day Status'),
                        items: [{
                            name: 'TrainingDayStatus',
                            id: 'TrainingDayStatusHalf',
                            boxLabel: lang('Half day'),
                            inputValue: 'half'
                        }, {
                            name: 'TrainingDayStatus',
                            id: 'TrainingDayStatusFull',
                            boxLabel: lang('Full day'),
                            inputValue: 'full'
                        }]
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Training Days'),
                        id: 'days',
                        name: 'days'
                    }
                ]
            }]
        }, {
            xtype: 'gridpanel',
            id: 'gtraining',
            title: lang('Training Participants'),
            store: store_participant,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    cls: m_act_save,
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var r = Ext.create('Participant.Model', {
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
                    cls: m_act_save,
                    hidden: !m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function () {
                        RowEditing.cancelEdit();
                        var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                        RowEditing.startEdit(sm[0].index, 0);
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/decline.png',
                    cls: m_act_save,
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
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                    text: lang('Daftar Hadir'),
                    scope: this,
                    handler: function () {
                        win.hide();
                        preview_cetak_surat(m_cetak + Ext.getCmp('id').getValue());

                        //window.location = m_cetak+Ext.getCmp('idt').getValue();
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'participant_id',
                width: '10%',
                hidden: true
            }, {
                text: lang('ID'),
                dataIndex: 'id_staff',
                width: '10%',
            }, {
                text: lang('Staff'),
                width: '50%',
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
                            if (isNumber(Ext.getCmp('staf').getValue())) {
                                Ext.Ajax.request({
                                    waitMsg: 'Check data...',
                                    url: m_check,
                                    method: 'GET',
                                    params: {
                                        trainingid: Ext.getCmp('id').getValue(),
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
                width: '10%',
                dataIndex: 'wstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('W. Akhir'),
                width: '10%',
                dataIndex: 'wend',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Awal'),
                width: '10%',
                dataIndex: 'bstart',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: lang('B. Akhir'),
                width: '10%',
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
                            training: Ext.getCmp('id').getValue()
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
                                training: Ext.getCmp('id').getValue(),
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
        }],
        buttons: [{
            id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue() != '')
                    methode = 'PUT';
                else
                    methode = 'POST';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                        }
                    });
                    win.hide(this, function () {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                kab: Ext.getCmp('Kab').getValue()
                            }
                        });
                    });
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                win.hide();
            }
        }]
    });
    var win = Ext.create('widget.window', {
        id: 'win',
        title: lang('Master Training'),
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 1024,
        height: 700,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
    // end of add/update

    //view
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'training', 'batch', 'tot', 'participant', 'start', 'end', 'days', 'partner_name'],
        autoLoad: true,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            extraParams: {
                prov: m_param,
                dist: m_DistrictID,
                subdist: m_SubDistrictID
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
            }
        }
    });
    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_kabupaten,
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    kab: Ext.getCmp('Kab').getValue()
                }
            });
        }
    }
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        id: 'grid',
        width: '100%',
        minHeight: 250,
        //title: lang('Master Training'),
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function (dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                store_participant.load({
                    params: {
                        training: sm.get('id')
                    }
                });
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {
                        id: sm.get('id')
                    },
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(sm.get('id'));
                        setFormValue(r);
                        hideSave();
                    }
                });
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                handler: function () {
                    displayFormWindow();
                    if (m_param) Ext.getCmp('Provinsi').setValue(m_Province).setReadOnly(true);
                    else Ext.getCmp('Provinsi').setValue('').setReadOnly(false);
                    if (m_District) Ext.getCmp('DistrictID').setValue(m_DistrictID).setReadOnly(true);
                    else Ext.getCmp('DistrictID').setValue('').setReadOnly(false);
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                    hideSave();
                },
                hidden: !m_act_add
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                handler: function () {
                    displayFormWindow();
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    store_participant.load({
                        params: {
                            training: sm.get('id')
                        }
                    });
                    Ext.Ajax.request({
                        url: m_crud,
                        method: 'GET',
                        params: {
                            id: sm.get('id')
                        },
                        success: function (fp, o) {
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('id').setValue(sm.get('id'));
                            setFormValue(r);
                            hideSave();
                        }
                    });
                },
                hidden: !m_act_update
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                hidden: !m_act_delete,
                text: lang('Hapus'),
                scope: this,
                handler: function () {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_crud,
                                method: 'DELETE',
                                params: {
                                    id: smb.raw.id
                                },
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            store.load();
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
                xtype: 'textfield',
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'key',
                listeners: {
                    specialkey: submitOnEnter
                }
            }, {
                id: 'Kab',
                name: 'Kab',
                xtype: 'combo',
                store: mc_Kabupaten,
                displayField: 'label',
                valueField: 'label',
                queryMode: 'local',
                selectOnFocus: true,
                hidden: true,
                listeners: {
                    specialkey: submitOnEnter
                }
            }, {
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function () {
                    store.load({
                        params: {
                            key: Ext.getCmp('key').getValue(),
                            kab: Ext.getCmp('Kab').getValue()
                        }
                    });
                }
            }]
        }],
        columns: [{
            text: lang('ID'),
            dataIndex: 'id',
            width: '5%'
        }, {
            text: lang('Trainings'),
            flex: 3,
            dataIndex: 'training'
        }, {
            text: lang('District'),
            flex: 2,
            dataIndex: 'tot'
        }, {
            text: lang('Participants'),
            flex: 1,
            dataIndex: 'participant'
        }, {
            text: lang('Start'),
            flex: 1,
            dataIndex: 'start'
        }, {
            text: lang('End'),
            flex: 1,
            dataIndex: 'end'
        }, {
            text: lang('Days'),
            flex: 1,
            dataIndex: 'days'
        }]
    });
    mc_Kabupaten.on('load', function (st) {
        if (Ext.getCmp('Kab').getValue() == null)
            Ext.getCmp('Kab').setValue(st.getAt('0').get('label'));
        store.load({
            params: {
                key: Ext.getCmp('key').getValue(),
                kab: Ext.getCmp('Kab').getValue()
            }
        });
    });
    //end of view

});