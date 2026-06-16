Ext.onReady(function() {
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
    var store_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_farmer,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var store_family = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_family,
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
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_store_kabupaten,
            extraParams: {prov: m_param},
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
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    Ext.define('Participant.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id', 'farmer_id', 'farmer_display_id', 'farmer', 'participant', 'PetaniKakao', 'if_no', 'FamilyID', 'wstart', 'wend', 'bstart', 'bend']
    });
    var store_participant = Ext.create('Ext.data.Store', {
        model: 'Participant.Model',
        //pageSize: 10000,
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
    var store_ya_tidak = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Ya"},
            {"id": "2", "label": "Tidak"},
        ]
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
            'beforeload': function(store, options) {
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
            Ext.getCmp('Kabupaten').setValue();
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
            win.hide(this, function() {
            });
            win.toFront();
        }
        Ext.getCmp('win').center()
    }
    function setFormValue(r) {
        // Ext.getCmp('cpg').setValue(r.CpgBatchID);
        Ext.getCmp('training').setValue(r.CPGtrainingsID);
        Ext.getCmp('idt').setValue(r.CpgKaderTrainingID);
        Ext.getCmp('location').setValue(r.TotLocation);
        // Ext.getCmp('fasilitator_scpp').setValue(r.StaffID);
        // Ext.getCmp('fasilitator_mitra').setValue(r.PrivateStaffID);
        Ext.getCmp('fasilitator_scpp').setValue(r.FacProgramPersonID);
        Ext.getCmp('fasilitator_mitra').setValue(r.FacPrivatePersonID);
        Ext.getCmp('TrainingStart').setValue(r.TrainingStart);
        Ext.getCmp('TrainingEnd').setValue(r.TrainingEnd);
        Ext.getCmp('days').setValue(r.TrainingDays);
        Ext.getCmp('Provinsi').setValue(r.Province);
        Ext.getCmp('Kabupaten').setValue(r.District);

        Ext.getCmp('Provinsi').setReadOnly(false);
        Ext.getCmp('Kabupaten').setReadOnly(false);

        if(r.TrainingDayStatus == 'half')
            Ext.getCmp('TrainingDayStatusHalf').setValue(true);
        if(r.TrainingDayStatus == 'full')
            Ext.getCmp('TrainingDayStatusFull').setValue(true);

        mc_sub_topic.load({
            callback : function(records, options, success) {
                if(r.subtopics != null){
                    var setSubtopic = r.subtopics.split(',');
                    //console.log(setSubtopic);
                    Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                }else{
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
    var store_CekSurvey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'surveya'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_CekSurvey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var SurveyID;
    var jenis;

    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [{
                xtype: 'combobox',
                id: 'survey',
                name: 'id',
                store: store_CekSurvey,
                fieldLabel: lang('Survey'),
                displayField: 'surveya',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        SurveyID = nv
                        //console.log(SurveyID);
                    }
                }
            },
            {
                xtype: 'container',
                height: 43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
                    {
                        id: 'h_p1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_farmer + 'CpgKaderTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    }, {
                        id: 'h_f1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_aff + 'CpgKaderTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_n1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            // winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_nutrisi + 'CpgKaderTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_ppi',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_ppi2012 + 'CpgKaderTrainingID/' + Ext.getCmp('idt').getValue() + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        xtype: 'button',
                        text: lang('Batal'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            winBeforeCetak.hide();
                        }
                    }
                ]
            }
        ]
    });
    var winBeforeCetak = Ext.create('widget.window', {
        id: 'print',
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: 430,
        height: 130,
        items: [DataBeforeCetak]
    });
    function displayBeforeCetak() {
        if (!winBeforeCetak.isVisible()) {
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {
            });
            winBeforeCetak.toFront();
        }
        Ext.getCmp('h_p1').hide();
        Ext.getCmp('h_f1').hide();
        Ext.getCmp('h_n1').hide();
        Ext.getCmp('h_ppi').hide();
        if (jenis == 'P1') {
            Ext.getCmp('h_p1').show();
            Ext.getCmp('print').setTitle('Cetak GAP');
        } else if (jenis == 'F1') {
            Ext.getCmp('h_f1').show();
            Ext.getCmp('print').setTitle('Cetak GFP');
        } else if (jenis == 'N1') {
            Ext.getCmp('h_n1').show();
            Ext.getCmp('print').setTitle('Cetak GNP');
        } else if (jenis == 'PPI') {
            Ext.getCmp('h_ppi').show();
            Ext.getCmp('print').setTitle('Cetak PPI');
        }
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        width: 1010,
        height: 560,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
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
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'id',
                                name: 'id',
                                inputType: 'hidden'
                            }, {
                                xtype: 'textfield',
                                id: 'idt',
                                name: 'idt',
                                inputType: 'hidden'
                            }, 
                            {
                                xtype: 'combo',
                                store: store_training,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Topic'),
                                queryMode: 'local',
                                id: 'training',
                                name: 'training',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
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
                            },{
                                id: 'Provinsi',
                                name: 'Provinsi',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                store: store_provinsi,
                                displayField: 'label',
                                valueField: 'label',
                                readOnly: false,
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        store_kabupaten.load({
                                            params: {
                                                key: Ext.getCmp('Provinsi').getValue()
                                            }});
                                        //Ext.getCmp('Kabupaten').enable();
                                    }
                                }
                            }, {
                                id: 'Kabupaten',
                                name: 'Kabupaten',
                                xtype: 'combo',
                                fieldLabel: lang('Kabupaten'),
                                store: store_kabupaten,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                /*                        listeners: {
                                 change: function (cb, nv, ov) {
                                 store_farmer.load({
                                 params: {
                                 kab: Ext.getCmp('Kabupaten').getValue()
                                 }});
                                 //Ext.getCmp('Kabupaten').enable();
                                 }
                                 }                      */
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('ToT Location'),
                                id: 'location',
                                name: 'location'
                            }]
                    }, {
                        columnWidth: 0.5,
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
                                id: 'fasilitator_scpp',
                                name: 'fasilitator_scpp',
                                allowBlank: true,
                            }, {
                                xtype: 'combo',
                                store: store_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator 2'),
                                queryMode: 'local',
                                id: 'fasilitator_mitra',
                                name: 'fasilitator_mitra'
                            }, {
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
                                },{
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusFull',
                                    boxLabel: lang('Full day'),
                                    inputValue: 'full'
                                }]
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Training Days'),
                                id: 'days',
                                name: 'days'
                            }]
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
                                handler: function() {
                                    RowEditing.cancelEdit();
                                    var r = Ext.create('Participant.Model', {
                                        participant_id: '', farmer_id: '', farmer: '', participant: '', PetaniKakao: '', if_no: '',
                                        FamilyID: '', wstart: '', wend: '', bstart: '', bend: ''
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
                                handler: function() {
                                    RowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection();
                                    RowEditing.startEdit(sm[0].index, 0);
                                    console.log(sm[0].get('farmer_id'))
                                    store_family.load({
                                        params: {
                                            farmerid: sm[0].get('farmer_id')
                                        }});
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/decline.png',
                                cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var sma = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_crud + '_participant',
                                                method: 'DELETE',
                                                params: {id: sma.get('participant_id')},
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_participant.load({
                                                                params: {
                                                                    training: Ext.getCmp('id').getValue()
                                                                }});
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
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
                                handler: function() {
                                    //win.hide();
                                    preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue());

                                    //window.location = m_cetak+Ext.getCmp('idt').getValue();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GAP'),
                                scope: this,
                                cls:'hide-icon',
                                handler: function() {
                                    jenis = 'P1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GFP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'F1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                id: 't_n1',
                                text: lang('GNP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'N1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('PPI'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
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
                        text: '',
                        dataIndex: 'farmer_id',
                        hidden: true,
                    }, {
                        text: lang('ID'),
                        dataIndex: 'farmer_display_id',
                        width: '10%'
                    }, {
                        text: lang('Registered Farmer'),
                        width: '30%',
                        dataIndex: 'farmer',
                        editor: {
                            xtype: 'combo',
                            displayField: 'label',
                            id: 'farmer',
                            name: 'farmer',
                            valueField: 'id',
                            queryMode: 'remote',
                            store: store_farmer,
                            listeners: {
                                change: function(cb, nv, ov) {
                                    if (isNumber(Ext.getCmp('farmer').getValue())) {
                                        Ext.Ajax.request({
                                            waitMsg: 'Check data...',
                                            url: m_check,
                                            method: 'GET',
                                            params: {
                                                trainingid: Ext.getCmp('id').getValue(),
                                                farmerid: Ext.getCmp('farmer').getValue()
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                if (!obj.data) {
                                                    Ext.MessageBox.alert('Warning', lang('Farmer telah terdapat dalam list'));
                                                    Ext.getCmp('farmer').setValue('');
                                                    return;
                                                }
                                            }
                                        });

                                        store_family.load({
                                            params: {
                                                farmerid: Ext.getCmp('farmer').getValue()
                                            }});
                                        //Ext.getCmp('Kabupaten').enable();
                                    }
                                }
                            }
                        }
                    }, {
                        text: lang('Participant'),
                        width: '10%',
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
                        text: lang('Pengganti'),
                        width: '15%',
                        dataIndex: 'if_no',
                        editor: {
                            xtype: 'combo',
                            displayField: 'label',
                            id: 'if_no',
                            name: 'if_no',
                            valueField: 'id',
                            queryMode: 'local',
                            store: store_family
                        }
                    }, {
                        text: lang('W. Awal'),
                        width: '9%',
                        dataIndex: 'wstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('W. Akhir'),
                        width: '8%',
                        dataIndex: 'wend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Awal'),
                        width: '8%',
                        dataIndex: 'bstart',
                        editor: {
                            xtype: 'textfield'
                        }
                    }, {
                        text: lang('B. Akhir'),
                        width: '8%',
                        dataIndex: 'bend',
                        editor: {
                            xtype: 'textfield'
                        }
                    }],
                plugins: [RowEditing],
                listeners: {
                    'itemdblclick': function(dv, record, item, index, e) {
                        if (!m_act_update) {
                            RowEditing.cancelEdit();
                            return false;
                        } else {
                            var sm = record;
                            store_family.load({
                                params: {
                                    farmerid: sm.get('farmer_id')
                                }});
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        if (m_act_save == 'hide-icon') {return false}
                        store_participant.load({
                            params: {
                                training: Ext.getCmp('id').getValue()
                            }});
                    },
                    'edit': function(editor, e) {
                        if (m_act_save == 'hide-icon') {return false}
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
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_participant.load({
                                                params: {
                                                    training: Ext.getCmp('id').getValue()
                                                }});
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
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
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_participant.load({
                                                        params: {
                                                            training: Ext.getCmp('id').getValue()
                                                        }});
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
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
                cls: 's-blue '+m_act_save,
                handler: function() {
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
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                            }
                        });
                        win.hide(this, function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    kab: Ext.getCmp('Kab').getValue()
                                }});
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
                handler: function() {
                    win.hide();
                }
            }]
    });
    var win = Ext.create('widget.window', {
        id: 'win',
        title: lang('Kader Training'),
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 1024,
        height: 600,
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
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            extraParams: {prov: m_param,dist: m_DistrictID,subdist: m_SubDistrictID},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
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
            extraParams: {prov: m_param},
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
                }});
        }
    }
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: lang('Kader Training'),
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                store_participant.load({
                    params: {
                        training: sm.get('id')
                    }});
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function(fp, o) {
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
                        handler: function() {
                            displayFormWindow();
                            if (m_param) Ext.getCmp('Provinsi').setValue(m_Province).setReadOnly(true);
                            else Ext.getCmp('Provinsi').setValue('').setReadOnly(false);
                            if (m_District) Ext.getCmp('Kabupaten').setValue(m_District).setReadOnly(true);
                            else Ext.getCmp('Kabupaten').setValue('').setReadOnly(false);
                            hideSave();
                            store_participant.load();
                            Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                        },
                        hidden: !m_act_add,
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            if (typeof(sm) === 'undefined') {
                                Ext.MessageBox.alert('Info', lang('Please select data to update'));
                                return false;
                            }
                            displayFormWindow();
                            store_participant.load({
                                params: {
                                    training: sm.get('id')
                                }});
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {id: sm.get('id')},
                                success: function(fp, o) {
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
                        handler: function() {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {id: smb.raw.id},
                                        success: function(response, opts) {
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
                                        failure: function(response, opts) {
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
                        handler: function() {
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
                width: '30%',
                dataIndex: 'training'
            }, {
                text: lang('District'),
                width: '25%',
                dataIndex: 'tot'
            }, {
                text: lang('Participants'),
                width: '10%',
                dataIndex: 'participant'
            }, {
                text: lang('Start'),
                width: '10%',
                dataIndex: 'start'
            }, {
                text: lang('End'),
                width: '10%',
                dataIndex: 'end'
            }, {
                text: lang('Days'),
                width: '5%',
                dataIndex: 'days'
            }]
    });
    /*    mc_Kabupaten.on('load',function(st) {
     if (Ext.getCmp('Kab').getValue()==null) Ext.getCmp('Kab').setValue(st.getAt('0').get('label'));
     store.load({
     params: {
     key: Ext.getCmp('key').getValue(),
     kab: Ext.getCmp('Kab').getValue()
     }});
     });*/
//end of view



});
